<?php


namespace App\Form;

use App\Entity\Department;
use App\Entity\ImportLog;
use App\Entity\ImportMetadata;
use App\Entity\ImportModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImportLogType
 * @package App\Form
 */
class ImportLogType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('importModel', EntityType::class, [
                'class' => ImportModel::class,
                'label' => 'import_log.form.import_model',
                'attr' => [
                    'class'=>'form-control'
                ],
            ])
            ->add('uploadedFile', FileType::class, [
                'label' => 'import_log.form.file_name',
                'attr' => [
                    'class'=>'form-control'
                ],
            ])
            ->add('year', ChoiceType::class, [
                'label' => 'import_log.form.year',
                'data' => date('Y'),
                'choices' => $this->getYears(2000),
                'attr' => [
                    'class'=>'form-control'
                ],
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'label' => 'general.department',
                'attr' => [
                    'class'=>'form-control'
                ],
            ])
            ->add('importMetadata', ImportMetadataType::class, [])
            ->add('isReplace', CheckboxType::class, [
                'label' => 'import_log.form.is_replace',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Importer',
                'attr'=> [
                    'class'=>'btn btn-primary btn-lg'

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
            'data_class' => ImportLog::class,
            'user' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'import_log';
    }

    /**
     * @param $min
     * @param string $max
     * @return array
     */
    private function getYears($min, $max = 'current')
    {
        $years = range($min, ($max === 'current' ? date('Y') : $max));

        return array_combine($years, $years);
    }
}
