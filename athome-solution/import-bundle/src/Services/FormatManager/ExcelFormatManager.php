<?php


namespace AthomeSolution\ImportBundle\Services\FormatManager;


use Port\Excel\ExcelReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ExcelFormatManager
 * @package AthomeSolution\ImportBundle\Services\FormatManager
 */
class ExcelFormatManager extends AbstractFormatManager
{
    /**
     * @param \SplFileObject  $file
     * @return mixed
     */
    public function getReader(\SplFileObject $file)
    {
        $reader = new ExcelReader($file, 0);

        return $reader;
    }
}