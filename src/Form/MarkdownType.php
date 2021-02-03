<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MarkdownType
 * @package App\Form
 */
class MarkdownType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('markdown', TextareaType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'help_markdown';
    }
}