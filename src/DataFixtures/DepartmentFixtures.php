<?php


namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DepartmentFixtures
 * @package App\DataFixtures
 */
class DepartmentFixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $loireAtlantique = new Department();
        $loireAtlantique->setName('Loire-Atlantique');
        $loireAtlantique->setCode('44');

        $manager->persist($loireAtlantique);

        $maineEtLoire = new Department();
        $maineEtLoire->setName('Maine-et-Loire');
        $maineEtLoire->setCode('49');

        $manager->persist($maineEtLoire);

        $mayenne = new Department();
        $mayenne->setName('Mayenne');
        $mayenne->setCode('53');

        $manager->persist($mayenne);

        $sarthe = new Department();
        $sarthe->setName('Sarthe');
        $sarthe->setCode('72');

        $manager->persist($sarthe);

        $vendee = new Department();
        $vendee->setName('Vendée');
        $vendee->setCode('85');

        $manager->persist($vendee);

        $manager->flush();
    }
}
