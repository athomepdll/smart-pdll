<?php


namespace App\DataFixtures;

use App\Entity\DataLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DataLevelFixtures
 * @package App\DataFixtures
 */
class DataLevelFixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $synth = new DataLevel();
        $synth->setName('summary');
        $synth->setValue('Synthèse');

        $manager->persist($synth);

        $detail = new DataLevel();
        $detail->setName('detail');
        $detail->setValue('Détail');

        $manager->persist($detail);

        $stock = new DataLevel();
        $stock->setName('stock');
        $stock->setValue('Stock');

        $manager->persist($stock);

        $ignore = new DataLevel();
        $ignore->setName('ignore');
        $ignore->setValue('Ignore');

        $manager->persist($ignore);

        $manager->flush();
    }
}
