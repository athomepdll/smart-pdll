<?php


namespace App\Factory;

use App\Entity\ImportExport;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImportExportFactory
 * @package App\Factory
 */
class ImportExportFactory
{
    const INSEE_TYPE = 'insee';
    const DATA_TYPE = 'data';

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $inseeImportDir;

    /**
     * ImportExportFactory constructor.
     * @param string $projectDir
     * @param string $inseeImportDir
     */
    public function __construct(string $projectDir, string $inseeImportDir)
    {
        $this->projectDir = $projectDir;
        $this->inseeImportDir = $inseeImportDir;
    }

    /**
     * @param string $type
     * @param UploadedFile $uploadedFile
     * @param string|null $name
     * @param int|null $year
     * @return ImportExport
     * @throws \Exception
     */
    public function createImportExport(
        string $type,
        UploadedFile $uploadedFile,
        string $name = null,
        int $year = null
    ): ImportExport
    {
        $importExport = new ImportExport();

        list($filePath, $fileName) = $this->moveFile($uploadedFile, $name);

        $importExport->setType($type);

        if ($name !== null) {
            $importExport->setName($name);
        }

        if ($year !== null) {
            $importExport->setYear($year);
        }

        $importExport->setCurrentPage(1);
        $importExport->setFilePath($filePath);
        $importExport->setFileName($fileName);
        $importExport->setState(ImportExport::STATE_NEW);
        $importExport->setIncrement(3000);
        $importExport->createdAt = new \DateTime();
        $importExport->updatedAt = new \DateTime();
        $importExport->setEnabled(true);

        return $importExport;
    }

    /**
     * @param ImportExport $template
     * @param string $name
     * @return ImportExport
     * @throws \Exception
     */
    public function createFromImportExport(
        ImportExport $template,
        string $name
    ): ImportExport
    {
        $importExport = new ImportExport();
        $importExport->setType($template->getType());
        $importExport->setName($name);
        $importExport->setYear($template->getYear());

        $importExport->setCurrentPage(1);
        $importExport->setFilePath($template->getFilePath());
        $importExport->setFileName($template->getFileName());
        $importExport->setState(ImportExport::STATE_NEW);
        $importExport->setIncrement(3000);
        $importExport->createdAt = new \DateTime();
        $importExport->updatedAt = new \DateTime();
        $importExport->setEnabled(true);

        return $importExport;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $name
     * @return array
     * @throws \Exception
     */
    public function moveFile(UploadedFile $uploadedFile, string $name)
    {
        $date = new \DateTime('now');

        $filePath = $this->projectDir . $this->inseeImportDir;
        $fileName = $date->format('Y-m-d-H:i:s') . '-' . $name . '.csv';
        $uploadedFile->move($filePath, $fileName);

        return [$filePath, $fileName];
    }
}
