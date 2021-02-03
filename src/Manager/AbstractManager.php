<?php


namespace App\Manager;

use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AbstractManager
 * @package App\Manager
 */
abstract class AbstractManager implements ManagerInterface
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
     * @param Entity $entity
     * @param bool $flush
     * @return Entity
     */
    public function saveEntity(Entity $entity, $flush = true)
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $entity;
    }

    /**
     * @param Entity $entity
     * @param bool $flush
     * @return mixed|void
     */
    public function removeEntity(Entity $entity, $flush = true)
    {
        $this->entityManager->remove($entity);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
