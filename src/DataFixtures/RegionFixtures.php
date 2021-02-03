<?php


namespace App\DataFixtures;

use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RegionFixtures
 * @package App\DataFixtures
 */
class RegionFixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $paysDeLaLoire = new Region();
        $paysDeLaLoire->setName('Pays de la Loire');
        $paysDeLaLoire->setCode('52');

        $manager->persist($paysDeLaLoire);

        $manager->flush();
    }
}
