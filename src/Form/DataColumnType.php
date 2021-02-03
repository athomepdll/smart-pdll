<?php


namespace App\Form;

use App\Entity\Columns\DataColumn;
use App\Entity\DataLevel;
use App\Entity\DataType;
use AthomeSolution\ImportBundle\Entity\Config;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DataColumnType
 * @package App\Form
 */
class DataColumnType extends AbstractType
{
    const FINANCIAL_FIELDS = [
        'Nom du projet' => 'Nom du projet',
        'Coût du projet HT' => 'Coût du projet HT',
        'Subvention' => 'Subvention',
        'Thème' => 'Thème'
    ];

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('columnName', TextType::class, [])
            ->add('dataType', EntityType::class, [
                'class' => DataType::class
            ])
            ->add('dataLevel', EntityType::class, [
                'class' => DataLevel::class
            ])
            ->add('config', EntityType::class, [
                'class' => Config::class
            ])
            ->add('identifier', ChoiceType::class, [
                'choices' => self::FINANCIAL_FIELDS,
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DataColumn::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
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
