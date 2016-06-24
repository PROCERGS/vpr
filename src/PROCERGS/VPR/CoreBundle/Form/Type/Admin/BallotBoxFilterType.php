<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;

class BallotBoxFilterType extends AbstractType
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
                'empty_value' => 'Todos',
                'empty_data' => 0,
                'required' => false
            ))
            ->add('city', 'entity', array(
                'class' => 'PROCERGSVPRCoreBundle:City',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'empty_value' => 'Todos',
                'property' => 'name',
                'required' => false
            ))
            ->add('is_online', 'choice', array(
                'choices' => array(true => 'Online', false => 'Offline'),
                'empty_value' => false,
                'required' => false,
                'data' => false
            ))
            ->add('is_sms', 'choice',
                array(
                'choices' => array(true => 'Urna SMS'),
                'multiple' => true,
                'expanded' => true,
                'label'    => false
            ))
            ->add('pin', 'text', array(
                'required' => false,
                'data' => false
            ))
            ->add('status1', 'choice', array(
                'choices' => BallotBox::getAllowedStatus1(),
                'empty_value' => 'Selecione',
                'empty_data' => null,
                'required' => false,
                'data' => false
            ))
            ->add('email', 'text', array(
                'required' => false,
                'data' => false
            ))
            ->add('name', 'text', array(
                'required' => false,
                'data' => false
            ))
            ;
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'procergs_vpr_corebundle_ballotbox_filter';
    }
}
