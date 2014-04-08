<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\Step;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\StepType;

/**
 * Step controller.
 *
 * @Route("/")
 */
class StepController extends Controller
{

    /**
     * Lists all Step entities.
     *
     * @Route("/", name="admin_step")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('s')
            ->from('PROCERGSVPRCoreBundle:Step', 's')
            ->innerJoin('s.poll','p')
            ->orderBy('p.openingTime','DESC')
            ->addOrderBy('s.sorting','ASC')
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10,
            array('distinct' => true)
        );

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Step entity.
     *
     * @Route("/", name="admin_step_create")
     * @Method("POST")
     * @Template("PROCERGSVPRCoreBundle:Step:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Step();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_added_record'));

            return $this->redirect($this->generateUrl('admin_step_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Step entity.
    *
    * @param Step $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Step $entity)
    {
        $form = $this->createForm(new StepType(), $entity, array(
            'action' => $this->generateUrl('admin_step_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Step entity.
     *
     * @Route("/new", name="admin_step_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Step();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Step entity.
     *
     * @Route("/{id}", name="admin_step_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Step entity.
     *
     * @Route("/{id}/edit", name="admin_step_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
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
    * Creates a form to edit a Step entity.
    *
    * @param Step $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Step $entity)
    {
        $form = $this->createForm(new StepType(), $entity, array(
            'action' => $this->generateUrl('admin_step_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Step entity.
     *
     * @Route("/{id}", name="admin_step_update")
     * @Method("PUT")
     * @Template("PROCERGSVPRCoreBundle:Step:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_changed_record'));

            return $this->redirect($this->generateUrl('admin_step_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Step entity.
     *
     * @Route("/{id}", name="admin_step_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Step entity.');
            }

            $em->remove($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_removed_record'));
        }

        return $this->redirect($this->generateUrl('admin_step'));
    }

    /**
     * Creates a form to delete a Step entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_step_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Save sorting steps
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Route("/save_sorting", name="admin_step_save_sorting")
     */
    public function saveSortingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $steps = $request->get('steps');

        try{
            foreach($steps as $i => $step){
                $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($step);

                if (!$entity) {
                    throw new \Exception('error!');
                }

                $entity->setSorting($i+1);
                $em->flush();
            }

            $data = array('success' => true, 'message' => $translator->trans("admin.success_save_sorting"));

        } catch (\Exception $e) {
            $data = array('success' => false, 'message' => $translator->trans("admin.unable_to_save_sorting"));
        }

        $response = new JsonResponse();
        $response->setData($data);

        return $response;        
    }
}
