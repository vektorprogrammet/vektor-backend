<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUploader
{
    /**
     * FileUploader constructor.
     */
    public function __construct(
        private readonly string $signatureFolder,
        private readonly string $logoFolder,
        private readonly string $receiptFolder,
        private readonly string $profilePhotoFolder
    ) {
    }

    /**
     * @return string absolute file path
     */
    public function uploadSponsor(Request $request): string
    {
        $file = static::getAndVerifyFile($request, ['image/*']);

        return $this->uploadFile($file, $this->logoFolder);
    }

    /**
     * @return string absolute file path
     */
    public function uploadSignature(Request $request): string
    {
        $file = static::getAndVerifyFile($request, ['image/*']);

        return $this->uploadFile($file, $this->signatureFolder);
    }

    public function uploadReceipt(Request $request): string
    {
        $file = static::getAndVerifyFile($request, ['image/*', 'application/pdf']);

        return $this->uploadFile($file, $this->receiptFolder);
    }

    public function uploadProfileImage(Request $request): string
    {
        $file = static::getAndVerifyFile($request, ['image/*']);

        $mimeType = $file->getMimeType();
        $fileType = explode('/', (string) $mimeType)[0];
        if ($fileType === 'image' || $mimeType === 'application/pdf') {
            return $this->uploadFile($file, $this->profilePhotoFolder);
        }
        throw new BadRequestHttpException('Filtypen må være et bilde eller PDF.');
    }

    /**
     * @param null $id
     *
     * @return false|mixed
     */
    public static function getAndVerifyFile(Request $request, array $valid_mime_types, $id = null)
    {
        // e.g: array('image/*') for valid_mime_types to accept all subtypes of file image

        $file = FileUploader::getFileFromRequest($request, $id);
        $mimeType = $file->getMimeType();

        $fileType = explode('/', (string) $mimeType)[0];
        $fileSubType = explode('/', (string) $mimeType)[1];

        foreach ($valid_mime_types as $valid_mime_type) {
            $validType = explode('/', (string) $valid_mime_type)[0];
            $validSubType = explode('/', (string) $valid_mime_type)[1];

            if ($fileType === $validType
                && ($fileSubType === $validSubType || $validSubType === '*')) {
                return $file;
            }
        }

        throw new BadRequestHttpException('Filtypen er ikke gyldig.');
    }

    /**
     * @return string absolute file path
     */
    public function uploadFile(UploadedFile $file, string $targetFolder): string
    {
        $fileExt = $file->guessExtension();
        $fileName = $this->generateRandomFileNameWithExtension($fileExt);

        if (!is_dir($targetFolder)) {
            mkdir($targetFolder, 0775, true);
        }

        try {
            $file->move($targetFolder, $fileName);
        } catch (FileException) {
            $originalFileName = $file->getClientOriginalName();
            $relativePath = $this->getRelativePath($targetFolder, $fileName);

            throw new UploadException('Could not copy the file ' . $originalFileName . ' to ' . $relativePath);
        }

        return $this->getAbsolutePath($targetFolder, $fileName);
    }

    public function deleteSponsor(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);
        $this->deleteFile("$this->logoFolder/$fileName");
    }

    public function deleteSignature(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);
        $this->deleteFile("$this->signatureFolder/$fileName");
    }

    public function deleteReceipt(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);
        $this->deleteFile("$this->receiptFolder/$fileName");
    }

    public function deleteProfileImage(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);
        $this->deleteFile("$this->profilePhotoFolder/$fileName");
    }

    public function deleteFile(string $path)
    {
        if (file_exists($path)) {
            if (!unlink($path)) {
                throw new FileException('Could not remove file ' . $path);
            }
        }
    }

    private static function getFileFromRequest(Request $request, $id = null)
    {
        $fileKey = $id ?? current($request->files->keys());
        $file = $request->files->get($fileKey);

        if (is_array($file)) {
            return current($file);
        }

        return $file;
    }

    private function generateRandomFileNameWithExtension(string $fileExtension): string
    {
        return uniqid() . '.' . $fileExtension;
    }

    private function getRelativePath(string $targetDir, string $fileName): string
    {
        return "$targetDir/$fileName";
    }

    private function getAbsolutePath(string $targetDir, string $fileName): string
    {
        // Removes ../, ./, //
        $absoluteTargetDir = preg_replace('/\.+\/|\/\//i', '', $targetDir);

        if ($absoluteTargetDir[0] !== '/') {
            $absoluteTargetDir = '/' . $absoluteTargetDir;
        }

        return "$absoluteTargetDir/$fileName";
    }

    private function getFileNameFromPath(string $path)
    {
        return mb_substr($path, mb_strrpos($path, '/') + 1);
    }
}
