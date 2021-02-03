<?php


namespace App\Form;

use App\Entity\ImportMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImportMetadataType
 * @package App\Form
 */
class ImportMetadataType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dataDate', DateType::class, [
                'data' => new \DateTime('first day of january'),
                'label' => 'import_log.form.data_date',
                'required' => false
            ])
            ->add('dataProvider', TextType::class, [
                'label' => 'import_log.form.data_provider',
                'required' => false
            ])
            ->add('serviceEmitter', TextType::class, [
                'label' => 'import_log.form.service_emitter',
                'required' => false
            ])
            ->add('emitterLastName', TextType::class, [
                'label' => 'import_log.form.emitter_last_name',
                'required' => false
            ])
            ->add('emitterFirstName', TextType::class, [
                'label' => 'import_log.form.emitter_first_name',
                'required' => false
            ])
            ->add('emitterMail', TextType::class, [
                'label' => 'import_log.form.emitter_mail',
                'required' => false
            ])
            ->add('emitterPhone', TextType::class, [
                'label' => 'import_log.form.emitter_phone',
                'required' => false,
                'attr' => [
                    'maxlength' => 10
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => ImportMetadata::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'import_metadata';
    }
}
