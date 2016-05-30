<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PersonType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * @Route("/", name="admin_user_list")
     * @Route("/search", name="admin_user_search")
     * @Method("GET")
     * @Template("PROCERGSVPRCoreBundle:Admin/User:index.html.twig")
     */
    public function searchAction(Request $request)
    {
        return [];
    }

    /**
     * Finds and displays an User entity.
     *
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->get('form.factory')->create(
            $this->get('vpr.form.admin.user'),
            $entity,
            array('available_roles' => $this->getRoles())
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $securityHelper = $this->get('vpr.security.helper');
            $loggedUserLevel = $securityHelper->getLoggedInUserLevel();
            $targetPersonLevel = $securityHelper->getTargetPersonLevel($entity);

            if ($loggedUserLevel >= $targetPersonLevel) {
                // TODO: persist changes
            }

            return $this->redirectToRoute('admin_user_edit', compact('id'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
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
