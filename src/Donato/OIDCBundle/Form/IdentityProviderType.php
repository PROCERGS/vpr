<?php

namespace Donato\OIDCBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdentityProviderType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $submitType = 'Symfony\Component\Form\Extension\Core\Type\SubmitType';
        $submitType = new SubmitType();
        $urlType = 'Symfony\Component\Form\Extension\Core\Type\UrlType';
        $urlType = new UrlType();
        $builder
            ->add('providerUrl', $urlType)
            ->add('submit', $submitType);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Donato\OIDCBundle\Entity\IdentityProvider',
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'idp';
    }
}
