<?php


namespace App\DataTransformer;

use App\Entity\Epci;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class SirenToEpciTransformer
 * @package App\DataTransformer
 */
class SirenToEpciTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  $epci|null $config
     * @return string
     */
    public function transform($epci)
    {
        if (null === $epci) {
            return '';
        }

        return $epci->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $siren
     * @return Epci|null
     * @throws TransformationFailedException if object (issue) is not found.
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function reverseTransform($siren)
    {
        // no issue number? It's optional, so that's ok
        if (!$siren) {
            return;
        }
        $epciRepository = $this->entityManager->getRepository(Epci::class);
        $maxYear = $epciRepository->getMaxYear();

        $epci = $epciRepository
            // query for the issue with this id
            ->findOneBy(['siren' => $siren, 'year' => $maxYear])
        ;

        if (null === $epci) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $siren
            ));
        }

        return $epci;
    }
}
