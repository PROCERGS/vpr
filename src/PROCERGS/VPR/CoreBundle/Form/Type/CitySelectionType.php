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
                ->add('city', new CityType(), array('required' => false))
                ->add('voterRegistration', 'voter_registration', array(
                    'required' => false
                ))
                ->add('submit', 'submit');
    }

    public function getName()
    {
        return 'citySelection';
    }

}
