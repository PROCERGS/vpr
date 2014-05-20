<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VoterRegistrationType extends TextType
{

    public function getParent()
    {
        return 'form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'voter_registration';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'max_length' => 12,
            'attr' => array(
                'pattern' => '[0-9]*'
            )
        ));
    }

}
