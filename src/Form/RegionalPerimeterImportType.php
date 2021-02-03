<?php


namespace App\Form;

use App\Repository\CityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImportType
 * @package App\Form
 */
class RegionalPerimeterImportType extends AbstractType
{
    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * ImportLogType constructor.
     * @param CityRepository $cityRepository
     */
    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', ChoiceType::class, [
                'label' => 'Année',
                'choices' => $this->getYears(),

            ])
            ->add('regional_perimeter', FileType::class, [
                'label' => 'Périmètre régional',
            ])
            ->add('insee_siren', FileType::class, [
                'label' => 'Insee - Siren',
            ])
            ->add('cities', FileType::class, [
                'label' => 'Communes',
            ])
            ->add('new_cities', FileType::class, [
                'label' => 'Communes nouvelles',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Importer',
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'insee';
    }

    /**
     * @param $min
     * @param string $max
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getYears()
    {
        $min = $this->cityRepository->getMaxYear();
        $max = $min + 1;

        if ($min === null) {
            $min = 2000;
            $max = 'current';
        }

        $years = range($min, ($max === 'current' ? date('Y') : $max));

        return array_combine($years, $years);
    }
}
