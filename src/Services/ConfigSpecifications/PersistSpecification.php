<?php


namespace App\Services\ConfigSpecifications;

use AthomeSolution\ImportBundle\Services\ConfigSpecification\AbstractConfigSpecification;
use AthomeSolution\ImportBundle\Services\ConfigSpecification\DatabaseSpecification;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PersistSpecifications
 * @package App\Services\ConfigSpecifications
 */
class PersistSpecification extends AbstractConfigSpecification
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PersistSpecifications constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $data
     * @param array $objects
     * @return bool
     * @throws \Exception
     */
    public function support(array $data, array &$objects)
    {
        if (!is_array($data) && !empty($objects['errors'])) {
            $this->successor->support($data, $objects);
            return false;
        }

        $this->execute($data, $objects);
        return true;
    }

    /**
     * @param array $data
     * @param array $objects
     * @return void
     * @throws \Exception
     */
    public function execute(array $data, array &$objects)
    {
        try {
            foreach ($objects as $key => $object) {
                if ($key === 'department' || $key === 'errors') {
                    continue;
                }

                $this->entityManager->persist($object);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
