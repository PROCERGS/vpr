<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\PollOption;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollOptionType;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollOptionFilterType;

/**
 * PollOption controller.
 *
 * @Route("/")
 */
class PollOptionController extends Controller
{

    /**
     * Lists all PollOption entities.
     *
     * @Route("/index", name="admin_poll_option")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_READ');
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();

        $poll_filters = $session->get('poll_filters');
        $form = $this->createForm(new PollOptionFilterType());

        $entities = array();
        $poll = null;
        if ($request->isMethod('POST') || $poll_filters) {
            if(!$request->isMethod('POST') && $poll_filters){
                $form->bind($poll_filters);

            }else{
                $form->bind($request);
                $session->set('poll_filters', $request);
            }
            $selected = $form->getData();

            $corede = $selected['corede'];
            $poll = $selected['poll'];
            $steps = isset($poll) ? $poll->getSteps() : array();

            $checkPoll = $this->get('vpr.checkpoll.helper');
            $status = $checkPoll->checkBlocked($poll->getId());
            if ($status) {
                $poll->setBlocked(true);
            }

            $pollOptionRepos = $em->getRepository('PROCERGSVPRCoreBundle:PollOption');
            foreach($steps as $step){
                $pollOptions = $pollOptionRepos->findByPollCoredeStep($poll, $corede, $step);

                foreach ($pollOptions as $option) {
                    $entities[$option->getStep()->getName()][$option->getCategory()->getName()][] = $option;
                }
            }
        }



        return array(
            'entities' => $entities,
            'poll' => $poll,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new PollOption entity.
     *
     * @Route("/", name="admin_poll_option_create")
     * @Method("POST")
     * @Template("PROCERGSVPRCoreBundle:Admin\PollOption:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_CREATE');
        $poll_selected_id = $request->get('poll_select');

        $entity = new PollOption();
        $form = $this->createCreateForm($entity , $poll_selected_id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $polls = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findAll();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_added_record'));

            return $this->redirect($this->generateUrl('admin_poll_option_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'polls' => $polls,
            'poll_selected_id' => $poll_selected_id,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a PollOption entity.
    *
    * @param PollOption $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(PollOption $entity, $poll_selected = null)
    {
        $form = $this->createForm(new PollOptionType(), $entity, array(
            'action' => $this->generateUrl('admin_poll_option_create'),
            'method' => 'POST',
        ));

        $stepOptions = $this->generateStepOptions($poll_selected);

        $form->add('step', 'entity', $stepOptions);
        $form->add('rlAgency', 'entity', $this->generateRlAgencyOptions($poll_selected));
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PollOption entity.
     *
     * @Route("/new", name="admin_poll_option_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_CREATE');
        $entity = new PollOption();

        $em = $this->getDoctrine()->getManager();
        $polls = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findAll();

        $lastPoll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        $poll_selected_id = $lastPoll->getId();

        $lastPollOption = $em->getRepository('PROCERGSVPRCoreBundle:PollOption')->findLastPollOption();

        $entity->setCorede($lastPollOption->getCorede());
        $entity->setCategory($lastPollOption->getCategory());
        $entity->setStep($lastPollOption->getStep());

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'polls' => $polls,
            'form'   => $form->createView(),
            'poll_selected_id' => $poll_selected_id
        );
    }

    /**
     * Finds and displays a PollOption entity.
     *
     * @Route("/{id}", name="admin_poll_option_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_READ');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:PollOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PollOption entity.');
        }

        $checkPoll = $this->get('vpr.checkpoll.helper');
        $poll = $entity->getStep()->getPoll();
        $status = $checkPoll->checkBlocked($poll->getId());
        $entity->getStep()->getPoll()->setBlocked($status);

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PollOption entity.
     *
     * @Route("/{id}/edit", name="admin_poll_option_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_UPDATE');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:PollOption')->find($id);
        $polls = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PollOption entity.');
        }

        $checkPoll = $this->get('vpr.checkpoll.helper');
        $poll = $entity->getStep()->getPoll();
        $status = $checkPoll->checkBlocked($poll->getId());

        if ($status) {
            throw $this->createNotFoundException('Closed Poll');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'polls'       => $polls,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a PollOption entity.
    *
    * @param PollOption $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PollOption $entity)
    {
        $form = $this->createForm(new PollOptionType(), $entity, array(
            'action' => $this->generateUrl('admin_poll_option_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $poll_selected = $entity->getStep()->getPoll()->getId();
        $stepOptions = $this->generateStepOptions($poll_selected);

        $form->add('step', 'entity', $stepOptions);
        $form->add('rlAgency', 'entity', $this->generateRlAgencyOptions($poll_selected));
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PollOption entity.
     *
     * @Route("/{id}", name="admin_poll_option_update")
     * @Method("PUT")
     * @Template("PROCERGSVPRCoreBundle:Admin\PollOption:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_UPDATE');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:PollOption')->find($id);
        $polls = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PollOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_changed_record'));

            return $this->redirect($this->generateUrl('admin_poll_option'));
        }

        return array(
            'entity'      => $entity,
            'polls'       => $polls,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a PollOption entity.
     *
     * @Route("/{id}", name="admin_poll_option_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_DELETE');
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PROCERGSVPRCoreBundle:PollOption')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PollOption entity.');
            }

            $em->remove($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_removed_record'));
        }

        return $this->redirect($this->generateUrl('admin_poll_option'));
    }

    /**
     * Creates a form to delete a PollOption entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_poll_option_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    private function generateStepOptions($poll_selected)
    {
        $steps = array();
        if($poll_selected){
            $em = $this->getDoctrine()->getManager();
            $pollSelected = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($poll_selected);

            if($pollSelected){
                $steps = $pollSelected->getSteps();
            }
        }

        $stepOptions = array(
            'class' => 'PROCERGSVPRCoreBundle:Step',
            'choices' => $steps,
            'property' => 'name',
            'empty_value' => '',
            'required' => true
        );
        $stepOptions['disabled'] = $steps ? false : true;

        return $stepOptions;
    }
    private function generateRlAgencyOptions($poll_selected)
    {
        $steps = array();
        if($poll_selected){
            $em = $this->getDoctrine()->getManager();
            $pollSelected = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($poll_selected);
            $steps = $em->getRepository('PROCERGSVPRCoreBundle:RlAgency')->findBy(array('poll' => $pollSelected), array('name' => 'ASC'));
        }
    
        $stepOptions = array(
            'class' => 'PROCERGSVPRCoreBundle:RlAgency',
            'choices' => $steps,
            'property' => 'name',
            'empty_value' => '',
            'required' => true
        );
        $stepOptions['disabled'] = $steps ? false : true;
    
        return $stepOptions;
    }

    /**
     * Save sorting pollOption
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Route("/save_sorting", name="admin_poll_option_save_sorting")
     */
    public function saveSortingAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_UPDATE');
        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $ids = $request->get('ids');

        try{
            foreach($ids as $i => $id){
                $entity = $em->getRepository('PROCERGSVPRCoreBundle:PollOption')->find($id);

                if (!$entity) {
                    throw new \Exception('error!');
                }

                $entity->setCategorySorting($i+1);
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
     * Clear Filters
     * @Method("GET")
     * @Route("/filters/clear", name="admin_poll_option_clear_filters")
     */
    public function clearFiltersAction()
    {
        $this->denyAccessUnlessGranted('ROLE_BALLOT_READ');
        $session = $this->getRequest()->getSession();
        $session->remove('poll_filters');
        return $this->redirect($this->generateUrl('admin_poll_option'));
    }
}
