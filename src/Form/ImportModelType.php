<?php


namespace App\Form;

use App\DataTransformer\ConfigToNumberTransformer;
use App\Entity\Columns\DataColumn;
use App\Entity\Domain;
use App\Entity\ImportModel;
use App\Entity\ImportType;
use App\Repository\ImportLogRepository;
use AthomeSolution\ImportBundle\Entity\Config;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class ImportModelType
 * @package App\Form
 */
class ImportModelType extends AbstractType
{
    /**
     * @var ConfigToNumberTransformer
     */
    private $configToNumberTransformer;
    /**
     * @var ImportLogRepository
     */
    private $importLogRepository;

    /**
     * ImportModelType constructor.
     * @param ConfigToNumberTransformer $configToNumberTransformer
     * @param ImportLogRepository $importLogRepository
     */
    public function __construct(
        ConfigToNumberTransformer $configToNumberTransformer,
        ImportLogRepository $importLogRepository
    ) {
        $this->configToNumberTransformer = $configToNumberTransformer;
        $this->importLogRepository = $importLogRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $importModel = $builder->getData();
        $isDisabled = false;
        $colorDisabled = false;
        if ($importModel instanceof ImportModel && $importModel->getId() !== null) {
            $isDisabled = !empty($this->importLogRepository->getByImportModel($builder->getData()));
            $colorDisabled = $importModel->getImportType()->getName() === ImportType::INDICATOR;
        }
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 30
                    ]),
                ]
            ])
            ->add('importType', EntityType::class, [
                'class' => ImportType::class,
                'disabled' => $isDisabled,
            ])
            ->add('domains', EntityType::class, [
                'label' => 'import_model.form.domains',
                'required' => false,
                'disabled' => $isDisabled,
                'class' => Domain::class,
                'multiple' => true,
                'attr' => [
                    'size' => '7',
                    'class' => 'selectpicker',
                    "data-with" => "50%",
                    'data-live-search' => "true",
                    "data-actions-box" => "true",

                ]
            ])
            ->add('color', ColorType::class, [
                'label' => 'import_model.form.color',
                'disabled' => $colorDisabled
            ])
            ->add('config', HiddenType::class, [
            ])
            ->add('isMapView', CheckboxType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-success'
                ]
            ])
        ;
        $builder->get('config')
            ->addModelTransformer($this->configToNumberTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => ImportModel::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'import_model';
    }
}
