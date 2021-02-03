<?php


namespace App\Entity;

/**
 * Class Data
 * @package App\Entity
 */
class Data extends Entity
{
    /** @var DataLine */
    protected $dataLine;

    /** @var DataType */
    protected $dataType;

    /** @var DataLevel */
    protected $dataLevel;

    /** @var string */
    protected $name;

    /** @var string */
    protected $value;

    /**
     * @return DataLine
     */
    public function getDataLine(): ?DataLine
    {
        return $this->dataLine;
    }

    /**
     * @param DataLine $dataLine
     */
    public function setDataLine(?DataLine $dataLine): void
    {
        $this->dataLine = $dataLine;
    }

    /**
     * @return DataType
     */
    public function getDataType(): ?DataType
    {
        return $this->dataType;
    }

    /**
     * @param DataType $dataType
     */
    public function setDataType(?DataType $dataType): void
    {
        $this->dataType = $dataType;
    }

    /**
     * @return DataLevel
     */
    public function getDataLevel(): ?DataLevel
    {
        return $this->dataLevel;
    }

    /**
     * @param DataLevel $dataLevel
     */
    public function setDataLevel(?DataLevel $dataLevel): void
    {
        $this->dataLevel = $dataLevel;
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
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }
}
