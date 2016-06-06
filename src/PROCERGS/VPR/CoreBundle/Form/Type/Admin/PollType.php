<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PollType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'textarea', array('max_length' => 255))
            ->add('openingTime', 'datetime',array(
                'required' => true,
                'date_format' => 'dd MMMM yyyy',
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') - 70)
            ))
            ->add('closingTime', 'datetime',array(
                'required' => true,
                'date_format' => 'dd MMMM yyyy',
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') - 70)
            ))
            ->add('apurationTime', 'datetime',array(
            		'required' => true,
            		'date_format' => 'dd MMMM yyyy',
            		'widget' => 'choice',
            		'years' => range(date('Y'), date('Y') - 70)
            ))
            ->add('description', 'textarea')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PROCERGS\VPR\CoreBundle\Entity\Poll'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'procergs_vpr_corebundle_poll';
    }
}
