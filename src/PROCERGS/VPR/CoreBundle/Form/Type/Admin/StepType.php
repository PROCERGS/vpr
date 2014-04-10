<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StepType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('sorting','number',array(
                'attr'=>array('pattern'=>'[0-9]*')
            ))
            ->add('maxSelection','number',array(
                'attr'=>array('pattern'=>'[0-9]*')
            ))
            ->add('poll', 'entity', array(
                'class' => 'PROCERGSVPRCoreBundle:Poll',
                'property' => 'name',
                'empty_value' => '',
                'required' => true
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PROCERGS\VPR\CoreBundle\Entity\Step'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'procergs_vpr_corebundle_step';
    }
}
