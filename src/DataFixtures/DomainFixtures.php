<?php


namespace App\DataFixtures;

use App\Entity\Domain;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DomainFixtures
 * @package App\DataFixtures
 */
class DomainFixtures extends Fixture
{
    const DOMAINS = [
        "Affaires juridiques - contrôles" => "Affaires juridiques - contrôles",
        "Agriculture" => "Agriculture",
        "Aménagement urbanisme" => "Aménagement urbanisme",
        "Culture" => "Culture",
        "Démographie" => "Démographie",
        "Eau environnement littoral" => "Eau environnement littoral",
        "Économie travail emploi" => "Économie travail emploi",
        "Enseignement recherche" => "Enseignement recherche",
        "Energie" => "Energie",
        "Finances publiques" => "Finances publiques",
        "Hébergement - cohésion sociale - santé" => "Hébergement - cohésion sociale - santé",
        "Logement – habitat - bâtiment" => "Logement – habitat - bâtiment",
        "Numérique - téléphonie mobile" => "Numérique - téléphonie mobile",
        "Risques naturels et technologiques" => "Risques naturels et technologiques",
        "Sécurité" => "Sécurité",
        "Sport" => "Sport",
        "Transports - infrastructures" => "Transports - infrastructures"
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::DOMAINS as $key => $value) {
            $domainObject = new Domain();
            $domainObject->setName($key);
            $domainObject->setValue($value);

            $manager->persist($domainObject);
        }

        $manager->flush();
    }
}
