<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PersonType;

/**
 * Person controller.
 *
 * @Route("/")
 */
class PersonController extends Controller
{

    /**
     * Finds and displays a Person entity.
     *
     * @Route("/{id}/edit", name="admin_person_edit")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Person')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        $form = $this->get('form.factory')->create(
            $this->get('vpr.form.admin.person'), $entity,
            array('available_roles' => $this->getRoles())
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $securityHelper    = $this->get('vpr.security.helper');
            $loggedUserLevel   = $securityHelper->getLoggedInUserLevel();
            $targetPersonLevel = $securityHelper->getTargetPersonLevel($entity);

            if ($loggedUserLevel >= $targetPersonLevel) {
                $userManager = $this->get('fos_user.user_manager');
                $userManager->updateUser($entity);
            }

            return $this->redirectToRoute('admin_person_edit', compact('id'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\PersonRepository
     */
    private function getPersonRepository()
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('PROCERGSVPRCoreBundle:Person');
    }

    private function getRoles()
    {
        $rolesHierarchy = $this->getParameter('security.role_hierarchy.roles');

        $roles = array();
        foreach ($rolesHierarchy as $role => $children) {
            $roles[$role] = $children;
            foreach ($children as $child) {
                if (!array_key_exists($child, $roles)) {
                    $roles[$child] = 0;
                }
            }
        }

        return array_keys($roles);
    }
}
