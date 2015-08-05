<?php
namespace PROCERGS\VPR\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('sorting', 'integer')
            ->add('titleBg')
            ->add('iconBg')
            ->add('optionBg')
            ->add('iconNum', 'choice', array(
            'choices' => array(
                1 => 'Violão'
                ,'Dinheiro'
                ,'Trator'
                ,'Boneco em Yoga'
                ,'Boneco em festa'
                ,'Fogo'
                ,'Cartilha'
                ,'Folhas de planta'
                ,'Bola futebol'
                ,'Predios'
                ,'Casa'
                ,'Diploma'
                ,'Avião'
                ,'Caminhão'
                ,'Equipe'
                ,'Calendario'
            )
        ));
        ;
    }

    /**
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PROCERGS\VPR\CoreBundle\Entity\Category'
        ));
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return 'procergs_vpr_corebundle_category';
    }
}
