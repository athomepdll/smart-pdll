<?php


namespace App\Entity;

use App\Entity\Behavior\TimestampableBehavior;
use AthomeSolution\ImportBundle\Entity\Config;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class ImportModel
 * @package App\Entity
 */
class ImportModel extends Entity
{
    use TimestampableBehavior;
    /** @var Collection */
    protected $domains;
    /** @var string */
    protected $name;
    /** @var ImportType */
    protected $importType;
    /** @var Config */
    protected $config;
    /** @var bool */
    protected $isMapView;
    /** @var Collection */
    protected $importLogs;
    /** @var string */
    protected $color;
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? '';
    }

    /**
     * ImportModel constructor.
     */
    public function __construct()
    {
        $this->domains = new ArrayCollection();
        $this->importLogs = new ArrayCollection();
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
     * @return ImportType
     */
    public function getImportType(): ?ImportType
    {
        return $this->importType;
    }

    /**
     * @param ImportType $importType
     */
    public function setImportType(?ImportType $importType): void
    {
        $this->importType = $importType;
    }

    /**
     * @return Config
     */
    public function getConfig(): ?Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(?Config $config): void
    {
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isMapView(): ?bool
    {
        return $this->isMapView;
    }

    /**
     * @param bool $isMapView
     */
    public function setIsMapView(?bool $isMapView): void
    {
        $this->isMapView = $isMapView;
    }

    /**
     * @return Collection
     */
    public function getDomains(): ?Collection
    {
        return $this->domains;
    }

    /**
     * @param Collection $domains
     */
    public function setDomains(?Collection $domains): void
    {
        $this->domains = $domains;
    }

    /**
     * @param Domain $domain
     */
    public function addDomain(Domain $domain)
    {
        $this->domains->add($domain);
    }

    /**
     * @return Collection
     */
    public function getImportLogs(): ?Collection
    {
        return $this->importLogs;
    }

    /**
     * @param Collection $importLogs
     */
    public function setImportLogs(?Collection $importLogs): void
    {
        $this->importLogs = $importLogs;
    }

    /**
     * @param ImportLog $importLog
     */
    public function addImportLog(?ImportLog $importLog)
    {
        $this->importLogs->add($importLog);
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}
