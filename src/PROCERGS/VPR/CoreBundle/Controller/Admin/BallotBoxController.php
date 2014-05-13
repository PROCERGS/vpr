<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\BallotBoxType;

/**
 * BallotBox controller.
 *
 * @Route("/")
 */
class BallotBoxController extends Controller
{

    /**
     * Lists all BallotBox entities.
     *
     * @Route("/", name="admin_ballotbox")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createQueryBuilder()
            ->select('b')
            ->from('PROCERGSVPRCoreBundle:BallotBox', 'b')
            ->innerJoin('b.city','c')
            ->innerJoin('b.poll','p')
            ->orderBy('p.openingTime','DESC')
            ->addOrderBy('c.name','ASC')
            ->addOrderBy('b.address','ASC')
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        
        $entities = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            20
        );

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new BallotBox entity.
     *
     * @Route("/", name="admin_ballotbox_create")
     * @Method("POST")
     * @Template("PROCERGSVPRCoreBundle:Admin\BallotBox:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new BallotBox();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_added_record'));

            return $this->redirect($this->generateUrl('admin_ballotbox_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a BallotBox entity.
    *
    * @param BallotBox $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(BallotBox $entity)
    {
        $form = $this->createForm(new BallotBoxType(), $entity, array(
            'action' => $this->generateUrl('admin_ballotbox_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BallotBox entity.
     *
     * @Route("/new", name="admin_ballotbox_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new BallotBox();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a BallotBox entity.
     *
     * @Route("/{id}", name="admin_ballotbox_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BallotBox entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing BallotBox entity.
     *
     * @Route("/{id}/edit", name="admin_ballotbox_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BallotBox entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a BallotBox entity.
    *
    * @param BallotBox $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BallotBox $entity)
    {
        $form = $this->createForm(new BallotBoxType(), $entity, array(
            'action' => $this->generateUrl('admin_ballotbox_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BallotBox entity.
     *
     * @Route("/{id}", name="admin_ballotbox_update")
     * @Method("PUT")
     * @Template("PROCERGSVPRCoreBundle:Admin\BallotBox:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BallotBox entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_changed_record'));

            return $this->redirect($this->generateUrl('admin_ballotbox_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a BallotBox entity.
     *
     * @Route("/{id}", name="admin_ballotbox_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BallotBox entity.');
            }

            $em->remove($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_removed_record'));
        }

        return $this->redirect($this->generateUrl('admin_ballotbox'));
    }

    /**
     * Creates a form to delete a BallotBox entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_ballotbox_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
