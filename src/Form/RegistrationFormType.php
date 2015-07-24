<?php
/**
 * This form exists to overwrite the FOSUserBundle Registration form and remove
 * the 'username' field.
 */
namespace Sc\CoreBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder
            ->add('first_name')
            ->add('last_name')
            ->remove('username');
    }

    public function getName()
    {
        return 'sc_core_user_registration';
    }
}