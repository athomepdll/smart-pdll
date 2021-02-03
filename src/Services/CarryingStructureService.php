<?php


namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CarryingStructureService
 * @package App\Services
 */
class CarryingStructureService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * CarryingStructureService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }
}
