<?php

namespace PROCERGS\VPR\CoreBundle\Form;

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
            ->add('name')
            ->add('openingTime')
            ->add('closingTimime')
            ->add('description')
            ->add('publicKey')
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
