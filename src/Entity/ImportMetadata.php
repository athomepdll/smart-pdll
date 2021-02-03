<?php


namespace App\Entity;

/**
 * Class ImportLogMetadata
 * @package App\Entity
 */
class ImportMetadata extends Entity
{
    /** @var \DateTime */
    protected $dataDate;
    /** @var string */
    protected $dataProvider;
    /** @var string */
    protected $emitterFirstName;
    /** @var string */
    protected $emitterLastName;
    /** @var string */
    protected $emitterMail;
    /** @var string */
    protected $emitterPhone;
    /** @var ImportLog */
    protected $importLog;
    /** @var string */
    protected $serviceEmitter;

    /**
     * @return \DateTime
     */
    public function getDataDate(): ?\DateTime
    {
        return $this->dataDate;
    }

    /**
     * @param \DateTime $dataDate
     */
    public function setDataDate(?\DateTime $dataDate): void
    {
        $this->dataDate = $dataDate;
    }

    /**
     * @return string
     */
    public function getEmitterFirstName(): ?string
    {
        return $this->emitterFirstName;
    }

    /**
     * @param string $emitterFirstName
     */
    public function setEmitterFirstName(?string $emitterFirstName): void
    {
        $this->emitterFirstName = $emitterFirstName;
    }

    /**
     * @return string
     */
    public function getEmitterLastName(): ?string
    {
        return $this->emitterLastName;
    }

    /**
     * @param string $emitterLastName
     */
    public function setEmitterLastName(?string $emitterLastName): void
    {
        $this->emitterLastName = $emitterLastName;
    }

    /**
     * @return string
     */
    public function getEmitterMail(): ?string
    {
        return $this->emitterMail;
    }

    /**
     * @param string $emitterMail
     */
    public function setEmitterMail(?string $emitterMail): void
    {
        $this->emitterMail = $emitterMail;
    }

    /**
     * @return string
     */
    public function getEmitterPhone(): ?string
    {
        return $this->emitterPhone;
    }

    /**
     * @param string $emitterPhone
     */
    public function setEmitterPhone(?string $emitterPhone): void
    {
        $this->emitterPhone = $emitterPhone;
    }

    /**
     * @return ImportLog
     */
    public function getImportLog(): ?ImportLog
    {
        return $this->importLog;
    }

    /**
     * @param ImportLog $importLog
     */
    public function setImportLog(?ImportLog $importLog): void
    {
        $this->importLog = $importLog;
    }

    /**
     * @return string
     */
    public function getDataProvider(): ?string
    {
        return $this->dataProvider;
    }

    /**
     * @param null|string $dataProvider
     */
    public function setDataProvider(?string $dataProvider): void
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @return string
     */
    public function getServiceEmitter(): ?string
    {
        return $this->serviceEmitter;
    }

    /**
     * @param string $serviceEmitter
     */
    public function setServiceEmitter(?string $serviceEmitter): void
    {
        $this->serviceEmitter = $serviceEmitter;
    }
}
