<?php


namespace App\Services;

use Doctrine\Migrations\Configuration\Exception\FileNotFound;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class DownloadFileService
 * @package App\Services
 */
class DownloadFileService
{
    /** @var string */
    private $exportDir;
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * DownloadFileService constructor.
     * @param KernelInterface $kernel
     * @param $exportDir
     */
    public function __construct(
        KernelInterface $kernel,
        $exportDir
    ) {
        $this->kernel = $kernel;
        $this->exportDir = $exportDir;
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getFileToDownload(string $filename)
    {
        $filePath = $this->kernel->getProjectDir() . $this->exportDir;

        if (!file_exists($filePath . $filename)) {
            throw new FileNotFound("Le fichier n'a pas été trouvé {$filename}");
        }

        return $filePath  . $filename;
    }
}
