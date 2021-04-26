<?php


namespace AthomeSolution\ImportBundle\Services\Detector;


use Symfony\Component\HttpFoundation\File\UploadedFile;

interface DetectorInterface
{
    /**
     * @param UploadedFile $file
     * @return FormatManagerInterface
     */
    public function getFormatManager(\SplFileObject $file);

    /**
     * @param UploadedFile $file
     * @param string $configName
     * @return ConfigManagerInterface
     */
    public function getConfigManager(\SplFileObject $file, $configName = 'default');
}