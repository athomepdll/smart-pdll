<?php


namespace App\Factory;

use App\Entity\ImportModel;
use AthomeSolution\ImportBundle\Entity\Config;
use AthomeSolution\ImportBundle\Manager\ConfigManager;

/**
 * Class ImportModelFactory
 * @package App\Factory
 */
class ImportModelFactory
{
    /**
     *
     */
    public function create()
    {
        $importModel = new ImportModel();
        $importModel->setIsMapView(false);

        return $importModel;
    }
}
