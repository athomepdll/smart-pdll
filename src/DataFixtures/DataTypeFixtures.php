<?php


namespace App\DataFixtures;

use App\Entity\DataType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DataType
 * @package App\DataFixtures
 */
class DataTypeFixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $text = new DataType();
        $text->setName('text');
        $text->setValue('Texte');

        $manager->persist($text);

        $number = new DataType();
        $number->setName('number');
        $number->setValue('Numérique');

        $manager->persist($number);

        $integer = new DataType();
        $integer->setName('integer');
        $integer->setValue('Entier');

        $manager->persist($integer);

        $percent = new DataType();
        $percent->setName('percent');
        $percent->setValue('Pourcentage');

        $manager->persist($percent);

        $date = new DataType();
        $date->setName('date');
        $date->setValue('Date');

        $manager->persist($date);

        $yesNo = new DataType();
        $yesNo->setName('yes_no');
        $yesNo->setValue('Oui/Non');

        $manager->persist($yesNo);

        $manager->flush();
    }
}
