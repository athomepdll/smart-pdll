<?php

namespace App\Entity;

use App\Entity\Behavior\StateableBehavior;
use App\Entity\Behavior\TimestampableBehavior;

/**
 * Class ImportExport
 * @package App\Entity
 */
class ImportExport extends Entity
{
    use StateableBehavior, TimestampableBehavior;

    const INSEE_IMPORT_TYPE = 'insee';
    const DATA_IMPORT_TYPE = 'data';

    const STATE_NEW = 'new';
    const STATE_RUNNING = 'running';
    const STATE_SUCCESS = 'success';
    const STATE_ERROR = 'error';

    /** @var string */
    protected $type;

    /** @var string */
    protected $name;

    /** @var string */
    protected $filePath;

    /** @var string */
    protected $fileName;

    /** @var int */
    protected $currentPage;

    /** @var int */
    protected $increment;

    /** @var int */
    protected $year;

    /** @var bool */
    protected $enabled;

    /** @var string */
    protected $errorStack;

    /**
     * ImportExport constructor.
     */
    public function __construct()
    {
        $this->state = self::STATE_NEW;
        $this->currentPage = 1;
    }

    // doctrine lifecycle-callbacks
    public function postRemove()
    {
        // Save the name of the file we would want to remove
        $oldFile = $this->filePath . $this->fileName;

        // PostRemove => We no longer have the entity's ID => Use the name we saved
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->filePath . $this->fileName;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return mixed
     */
    public function getIncrement(): ?int
    {
        return $this->increment;
    }

    /**
     * @param int $increment
     * @return void
     */
    public function setIncrement(int $increment): void
    {
        $this->increment = $increment;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(?bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getErrorStack(): ?string
    {
        return $this->errorStack;
    }

    /**
     * @param string $errorStack
     */
    public function setErrorStack(?string $errorStack): void
    {
        $this->errorStack = $errorStack;
    }
}
