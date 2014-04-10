<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\PollOption;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollOptionType;

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
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        
        if($request->get('clear_filter')){
            $session->clear();
            return $this->redirect($this->generateUrl('admin_poll_option'));
        }
        
        $filterOptions = $session->get('filterOptions');

        $polls = $em->createQueryBuilder()
            ->select('p')
            ->from('PROCERGSVPRCoreBundle:Poll', 'p')
            ->orderBy('p.openingTime','DESC')
            ->getQuery()
            ->getResult();
        
        $coredes = $em->createQueryBuilder()
            ->select('c')
            ->from('PROCERGSVPRCoreBundle:Corede', 'c')
            ->orderBy('c.name','ASC')
            ->getQuery()
            ->getResult();

        $form = $this->createFormBuilder()
            ->add('poll', 'entity', array(
                'class' => 'PROCERGSVPRCoreBundle:Poll',
                'choices' => $polls,
                'empty_value' => 'Selecione',
                'property' => 'name',
                'required' => true
            ))
            ->add('corede', 'entity', array(
                'class' => 'PROCERGSVPRCoreBundle:Corede',
                'choices' => $coredes,
                'empty_value' => 'Selecione',
                'property' => 'name',
                'required' => true
            ))
            ->getForm();

        $entities = array();
        if ($request->isMethod('POST') || $filterOptions) {
            if(!$request->isMethod('POST') && $filterOptions){
                $form->bind($filterOptions);
                
            }else{
                $form->bind($request);
                $session->set('filterOptions', $request);
            }
            $selected = $form->getData();

            $corede = $selected['corede'];
            $poll = $selected['poll'];
            $steps = $poll->getSteps();

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
        $entity = new PollOption();
        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();
        $polls = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findAll();
        
        return array(
            'entity' => $entity,
            'polls' => $polls,
            'form'   => $form->createView(),
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
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:PollOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PollOption entity.');
        }

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
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:PollOption')->find($id);
        $polls = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findAll();
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PollOption entity.');
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
}
