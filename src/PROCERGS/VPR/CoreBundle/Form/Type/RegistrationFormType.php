<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'voter_registration',array('required' => true))
                ->add('firstname', 'text', array('required' => true))
                ->add('email', 'email', array('required' => false))
                ->add('mobile', null, array(
                    'required' => false,
                    'attr' => array(
                        'type' => 'tel',
                        'pattern' => '[0-9- ()+]*'
                )))
                ->add('loginCidadaoAcceptRegistration', 'checkbox', array(
                    'required'  => false,
                    'attr' => array('checked' => 'checked')
                ));
    }

    public function getName()
    {
        return 'vpr_person_registration';
    }

}
