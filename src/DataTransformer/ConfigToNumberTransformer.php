<?php


namespace App\DataTransformer;

use AthomeSolution\ImportBundle\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ConfigToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Config|null $config
     * @return string
     */
    public function transform($config)
    {
        if (null === $config) {
            return '';
        }

        return $config->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $configNumber
     * @return $config|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($configNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$configNumber) {
            return;
        }

        $config = $this->entityManager
            ->getRepository(Config::class)
            // query for the issue with this id
            ->find($configNumber)
        ;

        if (null === $config) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $configNumber
            ));
        }

        return $config;
    }
}
