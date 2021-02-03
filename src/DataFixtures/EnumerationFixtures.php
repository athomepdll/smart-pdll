<?php


namespace App\DataFixtures;

use App\Entity\Enumeration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class EnumerationFixtures
 * @package App\DataFixtures
 */
class EnumerationFixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $enumeration = new Enumeration();
        $enumeration->setName('help_page');
        $enumeration->setValue('');

        $manager->persist($enumeration);

        $manager->flush();
    }
}
