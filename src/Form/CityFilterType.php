<?php


namespace App\Form;

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
 * Class CityFilterType
 * @package App\Form
 */
class CityFilterType extends AbstractType
{
    /**
     * @var SirenToEpciTransformer
     */
    private $sirenToEpciTransformer;

    /**
     * CityFilterType constructor.
     * @param SirenToEpciTransformer $sirenToEpciTransformer
     */
    public function __construct(SirenToEpciTransformer $sirenToEpciTransformer)
    {
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
            ->add('epci', TextType::class)
            ->add('district', EntityType::class, [
                'class' => District::class,
                'required' => false,
            ])
            ->add('actualCity', EntityType::class, [
                'class' => City::class,
                'required' => false,
            ]);

        $builder->get('epci')
            ->addModelTransformer($this->sirenToEpciTransformer);
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
