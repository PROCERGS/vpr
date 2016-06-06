<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use PROCERGS\VPR\CoreBundle\Entity\User;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\UserForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
     * @Method({"GET","POST"})
     * @Template("PROCERGSVPRCoreBundle:Admin/User:index.html.twig")
     */
    public function searchAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER_READ');
        $repo = $this->getDoctrine()->getRepository('PROCERGSVPRCoreBundle:User');
        $query = $request->get('query', null);
        if ($request->isMethod(Request::METHOD_POST) || $query !== null) {
            $users = $repo->findBy(['email' => $request->get('query')]);
        } else {
            $users = $repo->findAll();
        }

        return compact('users');
    }

    /**
     * Finds and displays an User entity.
     *
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Method({"GET","POST"})
     * @Template("PROCERGSVPRCoreBundle:Admin/User:form.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER_UPDATE');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User.');
        }

        $form = $this->createForm(new UserForm(), $entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush($entity);

            return $this->redirectToRoute('admin_user_edit', compact('id'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays an User entity.
     *
     * @Route("/new", name="admin_user_new")
     * @Method({"GET","POST"})
     * @Template("PROCERGSVPRCoreBundle:Admin/User:form.html.twig")
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER_CREATE');
        $entity = new User();

        $form = $this->createForm(new UserForm(), $entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush($entity);

            return $this->redirectToRoute('admin_user_edit', ['id' => $entity->getId()]);
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }
}