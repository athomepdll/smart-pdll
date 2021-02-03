<?php


namespace App\Form;

use App\DataTransformer\SirenToCityTransformer;
use App\DataTransformer\SirenToEpciTransformer;
use App\Entity\CarryingStructure;
use App\Entity\City;
use App\Entity\Department;
use App\Entity\District;
use App\Entity\Epci;
use App\Entity\ImportModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DataType
 * @package App\Form
 */
class TerritoryType extends AbstractType
{
    /**
     * @var SirenToCityTransformer
     */
    private $sirenToCityTransformer;
    /**
     * @var SirenToEpciTransformer
     */
    private $sirenToEpciTransformer;

    /**
     * DataType constructor.
     * @param SirenToCityTransformer $sirenToCityTransformer
     * @param SirenToEpciTransformer $sirenToEpciTransformer
     */
    public function __construct(
        SirenToCityTransformer $sirenToCityTransformer,
        SirenToEpciTransformer $sirenToEpciTransformer
    ) {
        $this->sirenToCityTransformer = $sirenToCityTransformer;
        $this->sirenToEpciTransformer = $sirenToEpciTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dataLevel', TextType::class)
            ->add('yearStart', NumberType::class)
            ->add('yearEnd', NumberType::class)
            ->add('department', EntityType::class, [
                'class' => Department::class
            ])
            ->add('district', EntityType::class, [
                'class' => District::class
            ])
            ->add('epci', TextType::class, [
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'required' => false,
            ])
            ->add('financialImportModels', EntityType::class, [
                'class' => ImportModel::class,
                'multiple' => true
            ])
            ->add('indicatorImportModels', EntityType::class, [
                'class' => ImportModel::class,
                'multiple' => true
            ])
            ->add('carryingStructure', EntityType::class, [
                'class' => CarryingStructure::class
            ])
        ;

        $builder->get('epci')
            ->addModelTransformer($this->sirenToEpciTransformer);
        $builder->get('city')
            ->addModelTransformer($this->sirenToCityTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'method' => 'GET',
            'allow_extra_fields' => true,
            'csrf_protection' => false,
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
