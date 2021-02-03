<?php


namespace App\DataTransformer;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class SirenToCityTransformer
 * @package App\DataTransformer
 */
class SirenToCityTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  City|null $city
     * @return string
     */
    public function transform($city)
    {
        if (null === $city) {
            return '';
        }

        return $city->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $siren
     * @return $city|null
     * @throws TransformationFailedException if object (issue) is not found.
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function reverseTransform($siren)
    {
        // no issue number? It's optional, so that's ok
        if (!$siren) {
            return;
        }

        $cityRepository = $this->entityManager->getRepository(City::class);
        $maxYear = $cityRepository->getMaxYear();
        $city = $cityRepository
            // query for the issue with this id
            ->findOneBy(['siren' => $siren, 'year' => $maxYear])
        ;

        if (null === $city) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $siren
            ));
        }

        return $city;
    }
}
