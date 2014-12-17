<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CoredeType extends TextType
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
        return 'corede';
    }

}
