<?php


namespace App\Form;

use App\DataTransformer\SirenToCityTransformer;
use App\DataTransformer\SirenToEpciTransformer;
use App\Entity\City;
use App\Entity\Department;
use App\Entity\District;
use App\Entity\Epci;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CarryingStructureType
 * @package App\Form
 */
class CarryingStructureType extends AbstractType
{
    /** @var SirenToCityTransformer  */
    private $sirenToCityTransformer;
    /** @var SirenToEpciTransformer  */
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
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'required' => false
            ])
            ->add('district', EntityType::class, [
                'class' => District::class,
                'required' => false,
            ])
            ->add('epci', TextType::class, [
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'required' => false,
            ]);

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
