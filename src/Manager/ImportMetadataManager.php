<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Class ImportMetadataManager
 * @package App\Manager
 */
class ImportMetadataManager extends AbstractManager
{
    /**
     * @param Entity $importMetadata
     * @param bool $flush
     * @return void
     */
    public function saveEntity(Entity $importMetadata, $flush = true)
    {
        $this->entityManager->persist($importMetadata);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
