<?php

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    private readonly string $rootDir;
    private readonly KernelInterface $appKernel;

    /**
     * AssetExtension constructor.
     */
    public function __construct(private readonly Packages $packages, KernelInterface $appKernel)
    {
        $this->rootDir = $appKernel->getProjectDir();
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_with_version', $this->getAssetUrl(...)),
        ];
    }

    /**
     * Returns the public url/path of an asset.
     *
     * If the package used to generate the path is an instance of
     * UrlPackage, you will always get a URL and not a path.
     *
     * @param string $path        A public path
     * @param null   $packageName The name of the asset package to use
     *
     * @return string|null The public path of the asset
     */
    public function getAssetUrl($path, $packageName = null): ?string
    {
        if (mb_strlen($path) === 0) {
            return $path;
        }

        if ($path[0] !== '/') {
            $path = "/$path";
        }
        $filePath = $this->rootDir . "/web$path";

        $version = '';
        if (file_exists($filePath)) {
            $version = filemtime($filePath);
        }

        $url = $this->packages->getUrl($path, $packageName);
        if (mb_strlen($version) > 0) {
            $url .= '?v=' . $version;
        }

        return $url;
    }
}
