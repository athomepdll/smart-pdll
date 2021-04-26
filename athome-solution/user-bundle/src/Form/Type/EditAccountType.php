<?php

namespace Athome\UserBundle\Form\Type;

use Athome\UserBundle\Form\Model\ChangePassword;
use Athome\UserBundle\Form\Model\EditAccount;
use Athome\UserBundle\Form\Model\EditUser;
use Athome\UserBundle\Model\UserManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAccountType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email', EmailType::class)
            ->add('oldPassword', PasswordType::class)
            ->add('newPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'user.password.mismatch',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EditAccount::class,
            'csrf_token_id' => 'edit_user',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'edit_user';
    }
}
