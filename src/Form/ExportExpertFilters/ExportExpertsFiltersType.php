<?php


namespace App\Form\ExportExpertFilters;

use App\DataTransformer\SirenToEpciTransformer;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Epci;
use App\Entity\ImportLog;
use App\Entity\ImportModel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Entity\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CityFilterType
 * @package App\Form
 */
class ExportExpertsFiltersType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ExportExpertsFiltersType constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('importModel', EntityType::class, [
                'class' => ImportModel::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->orderBy('i.name', 'ASC');
                },
                'placeholder' => 'Sélectionnez un modèle d\'import'
            ])
            ->add('year', ChoiceType::class, [
                'choices'  => $this->fillYears(),
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Sélectionnez une année'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => ['class' => 'btn btn-outline-info col-lg-12']
            ])
            ->add('export', SubmitType::class, [
                'label' => 'Exporter',
                'attr' => ['class' => 'btn btn-outline-info col-lg-12']
            ])
        ;
    }

    /**
     * @return mixed
     */
    private function fillYears()
    {
        /** @var QueryBuilder $queryBuilder */
        $results = $this->em->getRepository(ImportLog::class)->createQueryBuilder('i')
            ->orderBy('i.year', 'DESC')
            ->getQuery()
            ->getScalarResult()
        ;
        $years = [];
        foreach ($results as $year) {
            array_push($years, $year['i_year']);
        }
        foreach ($years as $key => $year) {
            $years[$year] = $years[$key];
            unset($years[$key]);
        }
        return $years;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => ImportLog::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
