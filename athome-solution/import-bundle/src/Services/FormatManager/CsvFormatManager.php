<?php


namespace AthomeSolution\ImportBundle\Services\FormatManager;


use Port\Csv\CsvReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CsvFormatManager
 * @package AthomeSolution\ImportBundle\Services\FormatManager
 */
class CsvFormatManager extends AbstractFormatManager
{
    /**
     * @param \SplFileObject  $file
     * @return CsvReader
     */
    public function getReader(\SplFileObject $file)
    {
        $reader = new CsvReader($file,',');
        $reader->setHeaderRowNumber(0);

        return $reader;
    }
}