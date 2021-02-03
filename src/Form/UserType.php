<?php


namespace App\Form;

use App\Entity\Department;
use App\Entity\District;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 * @package App\Form
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'user.form.firstname',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'user.form.lastname',
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'user.form.password',
                'required' => false,
            ])
            ->add('department', EntityType::class, [
                'label' => 'general.department',
                'class' => Department::class,
                'required' => false,
            ])
            ->add('district', EntityType::class, [
                'label' => 'general.department',
                'class' => District::class,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'user.actions.submit',
                'attr' => [
                    'class' => 'button'
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'edit_user';
    }
}
