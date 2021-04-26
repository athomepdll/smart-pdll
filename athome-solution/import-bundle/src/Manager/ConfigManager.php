<?php


namespace AthomeSolution\ImportBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use AthomeSolution\ImportBundle\Entity\Config;

class ConfigManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * AbstractManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param Config $config
     * @param bool $flush
     * @return Config
     */
    public function saveEntity(Config $config, $flush = true)
    {
        $this->entityManager->persist($config);

        if ($flush === true) {
            $this->entityManager->flush();
        }

        return $config;
    }

    /**
     * @param Config $config
     * @param bool $flush
     */
    public function removeEntity(Config $config, $flush = true)
    {
        $this->entityManager->remove($config);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}