<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegionalPerimeterExportType
 * @package App\Form
 */
class RegionalPerimeterExportType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', ChoiceType::class, [
                'label' => 'Année',
                'choices' => $options['years']

            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Exporter',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => null,
            'years' => [],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'export_regional_perimeter';
    }
}
