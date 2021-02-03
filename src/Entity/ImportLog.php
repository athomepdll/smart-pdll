<?php


namespace App\Entity;

use App\Entity\Behavior\TimestampableBehavior;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImportLog
 * @package App\Entity
 */
class ImportLog extends Entity
{
    use TimestampableBehavior;

    const STATE_NEW = 'new';
    const STATE_RUNNING = 'running';
    const STATE_SUCCESS = 'success';
    const STATE_ERROR = 'error';

    /** @var Collection */
    protected $dataLines;
    /** @var Department */
    protected $department;
    /** @var bool */
    protected $isDisabled;
    /** @var string */
    protected $filePath;
    /** @var ImportMetadata */
    protected $importMetadata;
    /** @var ImportModel */
    protected $importModel;
    /** @var bool */
    protected $isReplace;
    /** @var string */
    protected $status;
    /** @var User */
    protected $user;
    /** @var int */
    protected $year;

    /** @var UploadedFile */
    public $uploadedFile;

    public function __construct()
    {
        $this->dataLines = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getDataLines(): ?Collection
    {
        return $this->dataLines;
    }

    /**
     * @param DataLine $dataLines
     */
    public function addDataLine(?DataLine $dataLines): void
    {
        $this->dataLines = $dataLines;
    }

    /**
     * @return Department
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * @param Department $department
     */
    public function setDepartment(?Department $department): void
    {
        $this->department = $department;
    }

    /**
     * @return ImportMetadata
     */
    public function getImportMetadata(): ?ImportMetadata
    {
        return $this->importMetadata;
    }

    /**
     * @param ImportMetadata $importMetadata
     */
    public function setImportMetadata(?ImportMetadata $importMetadata): void
    {
        $this->importMetadata = $importMetadata;
    }

    /**
     * @return ImportModel
     */
    public function getImportModel(): ?ImportModel
    {
        return $this->importModel;
    }

    /**
     * @param ImportModel $importModel
     */
    public function setImportModel(?ImportModel $importModel): void
    {
        $this->importModel = $importModel;
    }

    /**
     * @return bool
     */
    public function isReplace(): ?bool
    {
        return $this->isReplace;
    }

    /**
     * @param bool $isReplace
     */
    public function setIsReplace(?bool $isReplace): void
    {
        $this->isReplace = $isReplace;
    }

    /**
     * @return string
     */
    public function getYear(): ?string
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear(?string $year): void
    {
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    /**
     * @param bool $isDisable
     */
    public function setIsDisabled(?bool $isDisabled): void
    {
        $this->isDisabled = $isDisabled;
    }
}
