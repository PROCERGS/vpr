<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class PollOptionFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('poll', 'entity', array(
                'class' => 'PROCERGSVPRCoreBundle:Poll',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.openingTime', 'DESC');
                },
                'property' => 'name',
                'required' => true
            ))
            ->add('corede', 'entity', array(
                'class' => 'PROCERGSVPRCoreBundle:Corede',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'empty_value' => 'Selecione',
                'property' => 'name',
                'required' => true
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'procergs_vpr_corebundle_poll_filter';
    }
}
