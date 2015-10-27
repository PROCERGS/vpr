<?php

namespace PROCERGS\VPR\CoreBundle\Form\Type\Admin;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use PROCERGS\VPR\CoreBundle\Helper\SecurityHelper;
use PROCERGS\VPR\CoreBundle\Entity\Person;
use Doctrine\ORM\EntityManager;

class PersonType extends AbstractType
{
    /** @var SecurityHelper */
    private $securityHelper;

    /** @var AuthorizationCheckerInterface */
    protected $security;

    /** @var EntityManager */
    protected $em;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(SecurityHelper $securityHelper)
    {
        $this->securityHelper = $securityHelper;
    }

    public function setSecurity(AuthorizationCheckerInterface $security)
    {
        $this->security = $security;
        return $this;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles')
        ;

        $allRoles       = $this->translateRoles($builder->getOption('available_roles'));
        $securityHelper = $this->securityHelper;
        $security       = $this->security;
        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use (&$allRoles, &$securityHelper, &$security) {
            $person = $event->getData();
            $form   = $event->getForm();
            $roles  = PersonType::filterRoles($person, $form, $allRoles,
                    $securityHelper, $security);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PROCERGS\VPR\CoreBundle\Entity\Person',
            'available_roles' => array(),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vpr_admin_person';
    }

    public static function filterRoles(Person $person, FormInterface $form,
                                       array $roles, $securityHelper, $security)
    {
        $loggedUserLevel        = $securityHelper->getLoggedInUserLevel();
        $targetPersonLevel      = $securityHelper->getTargetPersonLevel($person);
        $isLoggedUserSuperAdmin = $security->isGranted('ROLE_SUPER_ADMIN');

        $filteredRoles = array();
        foreach ($roles as $role => $name) {
            $isFeature = preg_match('/^FEATURE_/', $role) === 1;
            if (!$isLoggedUserSuperAdmin && $isFeature) {
                continue;
            }

            if ($loggedUserLevel < $securityHelper->getRoleLevel($role)) {
                continue;
            }

            $filteredRoles[$role] = $name;
        }

        $form->add('roles', 'choice',
            array(
            'choices' => $filteredRoles,
            'multiple' => true,
            'read_only' => ($targetPersonLevel > $loggedUserLevel),
            'disabled' => ($targetPersonLevel > $loggedUserLevel),
            'label_attr' => array('class' => 'col-sm-3 control-label'),
            'label' => 'admin.person.roles.label',
        ));
        return $filteredRoles;
    }

    private function translateRoles($roles)
    {
        $translated = array();
        foreach ($roles as $role) {
            if ($role == 'ROLE_ALLOWED_TO_SWITCH') {
                continue;
            }
            $translated[$role] = $this->translator->trans($role);
        }
        return $translated;
    }
}
