<?php


namespace App\DataFixtures;

use App\Entity\ImportType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ImportTypeFixtures
 * @package App\DataFixtures
 */
class ImportTypeFixtures extends Fixture
{
    const IMPORT_TYPE = [
        'financial' => 'Financier',
        'indicator' => 'Indicateur'
    ];


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::IMPORT_TYPE as $key => $field) {
            $importType = new ImportType();
            $importType->setName($key);
            $importType->setValue($field);

            $manager->persist($importType);
        }

        $manager->flush();
    }
}
