<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUploader
{
    private string $signatureFolder;
    private string $logoFolder;
    private string $receiptFolder;
    private string $profilePhotoFolder;
    private string $articleFolder;

    /**
     * FileUploader constructor.
     *
     * @param string $signatureFolder
     * @param string $logoFolder
     * @param string $receiptFolder
     * @param string $profilePhotoFolder
     * @param string $articleFolder
     */
    public function __construct(string $signatureFolder,
                                string $logoFolder,
                                string $receiptFolder,
                                string $profilePhotoFolder,
                                string $articleFolder)
    {
        $this->signatureFolder = $signatureFolder;
        $this->logoFolder = $logoFolder;
        $this->receiptFolder = $receiptFolder;
        $this->profilePhotoFolder = $profilePhotoFolder;
        $this->articleFolder = $articleFolder;
    }

    /**
     * @param Request $request
     *
     * @return string absolute file path
     */
    public function uploadSponsor(Request $request): string
    {
        $file = $this->getAndVerifyFile($request, array('image/*'));
        return $this->uploadFile($file, $this->logoFolder);
    }

    /**
     * @param Request $request
     *
     * @return string absolute file path
     */
    public function uploadSignature(Request $request): string
    {
        $file = $this->getAndVerifyFile($request, array('image/*'));
        return $this->uploadFile($file, $this->signatureFolder);
    }


    /**
     * @param Request $request
     *
     * @return string
     */
    public function uploadReceipt(Request $request): string
    {
        $file = $this->getAndVerifyFile($request, array('image/*', 'application/pdf'));
        return $this->uploadFile($file, $this->receiptFolder);
    }

    /**
 * @param Request $request
 * @return string
 */
    public function uploadProfileImage(Request $request): string
    {
        $file = $this->getAndVerifyFile($request, array('image/*'));

        $mimeType = $file->getMimeType();
        $fileType = explode('/', $mimeType)[0];
        if ($fileType === 'image') {
            return $this->uploadFile($file, $this->profilePhotoFolder);
        } else {
            throw new BadRequestHttpException('Filtypen må være et bilde.');
        }
    }

    /**
     * @param Request $request
     * @param string $id
     * @return string
     */
    public function uploadArticleImage(Request $request, string $id): ?string
    {
        $file = $this->getAndVerifyFile($request, array('image/*'), $id);
        if (!$file) {
            return null;
        }
        return $this->uploadFile($file, $this->articleFolder);
    }

    /**
     * @param Request $request
     * @param array $valid_mime_types
     * @param null $id
     * @return false|mixed
     */
    public static function getAndVerifyFile(Request $request, array $valid_mime_types, $id=null) {
        // e.g: array('image/*') for valid_mime_types to accept all subtypes of file image

        $file = FileUploader::getFileFromRequest($request, $id);
        $mimeType = $file->getMimeType();

        $fileType = explode('/', $mimeType)[0];
        $fileSubType = explode('/', $mimeType)[1];

        foreach ($valid_mime_types as $valid_mime_type) {
            $validType = explode('/', $valid_mime_type)[0];
            $validSubType = explode('/', $valid_mime_type)[1];

            if ($fileType === $validType &&
                ($fileSubType === $validSubType || $validSubType === "*")) {
                    return $file;
                }
            }

        throw new BadRequestHttpException('Filtypen er ikke gyldig.');
    }

    /**
     * @param UploadedFile $file
     * @param string       $targetFolder
     *
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
        } catch (FileException $e) {
            $originalFileName = $file->getClientOriginalName();
            $relativePath = $this->getRelativePath($targetFolder, $fileName);

            throw new UploadException('Could not copy the file '.$originalFileName.' to '.$relativePath);
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
                throw new FileException('Could not remove file '.$path);
            }
        }
    }

    private static function getFileFromRequest(Request $request, $id = null)
    {
        $fileKey = $id !== null ? $id : current($request->files->keys());
        $file = $request->files->get($fileKey);

        if (is_array($file)) {
            return current($file);
        }

        return $file;
    }

    private function generateRandomFileNameWithExtension(string $fileExtension): string
    {
        return uniqid().'.'.$fileExtension;
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
            $absoluteTargetDir = '/'.$absoluteTargetDir;
        }

        return "$absoluteTargetDir/$fileName";
    }

    private function getFileNameFromPath(string $path)
    {
        return substr($path, strrpos($path, '/') + 1);
    }
}
