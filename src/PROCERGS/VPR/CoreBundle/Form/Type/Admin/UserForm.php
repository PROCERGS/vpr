<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'city',
                'entity',
                array(
                    'class' => 'PROCERGSVPRCoreBundle:City',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'property' => 'name',
                    'required' => false,
                )
            )
            ->add('email')
            ->add(
                'roles',
                'static_role',
                [
                    "multiple" => true,
                    "expanded" => true,
                    'label_attr' => array('class' => 'col-sm-3 control-label'),
                    "roles_with_tags" => []
                ]
            );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'vpr_user';
    }
}
