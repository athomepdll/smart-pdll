<?php


namespace App\Manager;

use App\Entity\Entity;

/**
 * Class ImportExportManager
 * @package App\Manager
 */
class ImportExportManager extends AbstractManager
{

    /**
     * @param Entity $importExport
     * @param bool $flush
     */
    public function saveEntity(Entity $importExport, $flush = true)
    {
        $this->entityManager->persist($importExport);

        if ($flush === true) {
            $this->entityManager->flush();
        }
    }
}
