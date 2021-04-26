<?php


namespace AthomeSolution\ImportBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Class MapDiscriminatoreListener
 * @package AthomeSolution\ImportBundle\EventListener
 */
class MapDiscriminatorListener
{
    protected $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $metadata = $event->getClassMetadata();
        if ($metadata->getName() == 'AthomeSolution\ImportBundle\Entity\Column') {
            foreach ($this->map as $key => $mapDatum) {
                $metadata->addDiscriminatorMapClass($key, $mapDatum);
            }
        }
    }
}