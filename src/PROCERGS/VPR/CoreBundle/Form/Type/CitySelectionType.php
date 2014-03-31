<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class CitySelectionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('city', new CityType())
                ->add('voterRegistration', 'number', array(
                    'constraints' => new Length(array('max' => 12)),
                ))
                ->add('select', 'submit');
    }

    public function getName()
    {
        return 'citySelection';
    }

}
