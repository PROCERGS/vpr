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
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollOptionFilterType;

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
     * @Route("/index", name="admin_step")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_STEP_READ');
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();

        $poll_filters = $session->get('poll_filters');
        $form = $this->createForm(new PollOptionFilterType());
        $form->remove("corede");

        $entities = array();
        if ($request->isMethod('POST') || $poll_filters) {
            if(!$request->isMethod('POST') && $poll_filters){
                $form->bind($poll_filters);
            } else{
                $form->bind($request);
                $session->set('poll_filters', $request);
            }
            $selected = $form->getData();

            $poll = $selected['poll'];
            if (!$poll) {
                $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
            }
        } else {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        }

        $query = $em->createQueryBuilder()
            ->select('s')
            ->from('PROCERGSVPRCoreBundle:Step', 's')
            ->innerJoin('s.poll','p')
            ->where('p.id = :id')
            ->orderBy('p.openingTime','DESC')
            ->addOrderBy('s.sorting','ASC')
            ->setParameter('id', $poll->getId())
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10,
            array('distinct' => true)
        );

        $checkPoll = $this->get('vpr.checkpoll.helper');
        foreach ($entities as $e) {
            $status = $checkPoll->checkBlocked($e->getPoll()->getId());
            if ($status) {
                $e->getPoll()->setBlocked(true);
            }
        }

        return array(
            'entities' => $entities,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Step entity.
     *
     * @Route("/", name="admin_step_create")
     * @Method("POST")
     * @Template("PROCERGSVPRCoreBundle:Admin\Step:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_STEP_CREATE');
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
        $this->denyAccessUnlessGranted('ROLE_STEP_CREATE');
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
        $this->denyAccessUnlessGranted('ROLE_STEP_READ');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $checkPoll = $this->get('vpr.checkpoll.helper');
        $status = $checkPoll->checkBlocked($entity->getPoll()->getId());
        if ($status) {
            $entity->getPoll()->setBlocked(true);
        }

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
        $this->denyAccessUnlessGranted('ROLE_STEP_UPDATE');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
        }

        $checkPoll = $this->get('vpr.checkpoll.helper');
        $status = $checkPoll->checkBlocked($entity->getPoll()->getId());
        if ($status) {
            $entity->getPoll()->setBlocked(true);
        }

        if ($entity->getPoll()->getBlocked() || $entity->getPoll()->getApurationDone()) {
            throw new \Exception('Unable to edit!');
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
     * @Template("PROCERGSVPRCoreBundle:Admin\Step:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_STEP_UPDATE');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Step entity.');
        }

        $checkPoll = $this->get('vpr.checkpoll.helper');
        $status = $checkPoll->checkBlocked($entity->getPoll()->getId());
        if ($status) {
            $entity->getPoll()->setBlocked(true);
        }

        if ($entity->getPoll()->getBlocked() || $entity->getPoll()->getApurationDone()) {
            throw new \Exception('Unable to edit!');
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
        $this->denyAccessUnlessGranted('ROLE_STEP_DELETE');
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Step entity.');
            }

            if ($entity->getPoll()->getApurationDone()) {
                throw new \Exception('Unable to delete!');
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
        $this->denyAccessUnlessGranted('ROLE_STEP_UPDATE');
        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $ids = $request->get('ids');

        try{
            foreach($ids as $i => $id){
                $entity = $em->getRepository('PROCERGSVPRCoreBundle:Step')->find($id);

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

    /**
     * Load steps by poll
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Route("/select_steps", name="admin_select_steps")
     * @Method("POST")
     */
    public function selectStepsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_STEP_READ');
        $data = array();
        $poll_id = $request->get('poll_id');
        try{
            $em = $this->getDoctrine()->getManager();

            if(!$poll_id){
                throw new \Exception('error!');
            }

            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findOneById($poll_id);
            $steps = $poll->getSteps();

            $data['steps'][] = array('id'=>'','value'=>'');
            foreach($steps as $step){
                $data['steps'][] = array('id'=>$step->getId(),'value'=>$step->getName());
            }            
            $rlAgencys = $em->getRepository('PROCERGSVPRCoreBundle:RlAgency')->findBy(array('poll' => $poll), array('name' => 'ASC'));
            $data['rlAgencys'][] = array('id'=>'','value'=>'');
            foreach($rlAgencys as $rlAgency){
                $data['rlAgencys'][] = array('id'=>$rlAgency->getId(),'value'=>$rlAgency->getName());
            }
            
            $data['success'] = true;

        } catch (\Exception $e) {
            $data = array('success' => false);
        }

        $response = new JsonResponse();
        $response->setData($data);

        return $response;
    }

    /**
     * Clear Filters
     * @Method("GET")
     * @Route("/filters/clear", name="admin_step_clear_filters")
     */
    public function clearFiltersAction()
    {
        $session = $this->getRequest()->getSession();
        $session->remove('poll_filters');
        return $this->redirect($this->generateUrl('admin_step'));
    }


}
