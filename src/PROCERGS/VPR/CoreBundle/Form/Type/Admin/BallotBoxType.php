<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BallotBoxType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('secret', 'hidden')
            // ->add('address', 'text',
            //     array(
            //     'required' => false
            // ))
            // ->add('latitude')
            // ->add('longitude')
            ->add('openingTime', 'datetime',
                array(
                'required' => false,
                'date_format' => 'dd MMMM yyyy',
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') - 70)
            ))
            ->add('closingTime', 'datetime',
                array(
                'required' => false,
                'date_format' => 'dd MMMM yyyy',
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y') - 70)
            ))
            ->add('isOnline', null,
                array(
                'required' => false
            ))
            ->add('isSms', null,
                array(
                'required' => false
            ))
            ->add('poll', 'entity',
                array(
                'class' => 'PROCERGSVPRCoreBundle:Poll',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.openingTime', 'DESC');
                },
                'property' => 'name',
                'empty_value' => '',
                'required' => true
            ))
            ->add('city', 'entity',
                array(
                'class' => 'PROCERGSVPRCoreBundle:City',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'property' => 'name',
                'required' => false
            ))
            ->add('email', 'email', array('max_length' => 255, 'required' => false))
            ->add('fone', 'text', array('max_length' => 9, 'pattern'=>"\d*{8,}", 'required' => false))
            ->add('ddd', 'text', array('max_length' => 2, 'pattern'=>"\d*{8,}", 'required' => false))
            ->add('lote', 'file', array('mapped' => false, 'label' => 'Arquivo em lote', 'required' => false, 'multiple' =>'multiple'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PROCERGS\VPR\CoreBundle\Entity\BallotBox'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'procergs_vpr_corebundle_ballotbox';
    }
}
