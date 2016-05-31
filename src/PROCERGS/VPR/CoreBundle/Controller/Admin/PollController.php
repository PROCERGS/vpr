<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Poll controller.
 *
 * @Route("/")
 */
class PollController extends Controller
{

    /**
     * Lists all Poll entities.
     *
     * @Route("/", name="admin_poll")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('p')
            ->from('PROCERGSVPRCoreBundle:Poll', 'p')
            ->orderBy('p.openingTime','DESC')
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10
        );

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Poll entity.
     *
     * @Route("/", name="admin_poll_create")
     * @Method("POST")
     * @Template("PROCERGSVPRCoreBundle:Admin\Poll:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Poll();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->generatePrivateAndPublicKeys();
            $em->persist($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_added_record'));

            return $this->redirect($this->generateUrl('admin_poll_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Poll entity.
    *
    * @param Poll $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Poll $entity)
    {
        $form = $this->createForm(new PollType(), $entity, array(
            'action' => $this->generateUrl('admin_poll_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Poll entity.
     *
     * @Route("/new", name="admin_poll_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Poll();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Poll entity.
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="admin_poll_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        $steps = $entity->getSteps();

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'steps'       => $steps,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Poll entity.
     *
     * @Route("/{id}/edit", name="admin_poll_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        if ($entity->getApurationDone()) {
            throw $this->createNotFoundException('Closed Poll');
        }

        $steps = $entity->getSteps();

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'steps'       => $steps,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Poll entity.
    *
    * @param Poll $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Poll $entity)
    {
        $form = $this->createForm(new PollType(), $entity, array(
            'action' => $this->generateUrl('admin_poll_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Poll entity.
     *
     * @Route("/{id}", name="admin_poll_update")
     * @Method("PUT")
     * @Template("PROCERGSVPRCoreBundle:Admin\Poll:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_changed_record'));

            return $this->redirect($this->generateUrl('admin_poll_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Poll entity.
     *
     * @Route("/{id}", name="admin_poll_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Poll entity.');
            }
            $translator = $this->get('translator');
			try {
				$em->remove($entity);
				$em->flush();
				$this->get('session')->getFlashBag()->add('success', $translator->trans('admin.successfully_removed_record'));
			} catch (\Exception $e) {
				if (strstr($e->getMessage(), 'SQLSTATE[23503]') !== false) {
					$this->get('session')->getFlashBag()->add('danger', 'Não é possivel deletar, pois existem itens vinculados a essa votação');
				} else {
					$this->get('session')->getFlashBag()->add('danger', $e->getMessage());
				}

			}
        }

        return $this->redirect($this->generateUrl('admin_poll'));
    }

    /**
     * Creates a form to delete a Poll entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_poll_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }


    /**
     * Lists poll stats.
     *
     * @Route("/stats", name="admin_stats")
     * @Method("GET")
     * @Template()
     */
    public function statsListAction()
    {
        $em = $this->getDoctrine()->getManager();

        // $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();

        $statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');
        $coredeRepo    = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
        $votes    = $statsRepo->findTotalVotesByPoll(4);

        foreach ($votes as $vote) {
            $corede = $coredeRepo->find($vote['corede_id']);
            $coredeId = $corede->getId();

            $coredes[$coredeId]['corede'] = $corede->getName();
            $coredes[$coredeId]['votes_online'] = $vote['votes_online'];
            $coredes[$coredeId]['votes_offline'] = $vote['votes_offline'];
        }

        $voters    = $statsRepo->findTotalVotersByPoll(4);
        foreach ($voters as $vote) {
            $coredeId = $vote['corede_id'];
            $coredes[$coredeId]['voters_online'] = $vote['voters_online'];
            $coredes[$coredeId]['voters_offline'] = $vote['voters_offline'];
        }

        return array(
            'coredes' => $coredes,
        );
    }


    /**
     * Load steps by poll
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Route("/select_poll", name="admin_select_poll")
     * @Method("POST")
     */
    public function selectPollAction(Request $request)
    {
    	$data = array();
    	$poll_id = $request->get('poll_id');
    	$response = new JsonResponse();
    	try{
    		$em = $this->getDoctrine()->getManager();
    		if(!$poll_id){
    			throw new \Exception('Sem id!');
    		}
    		$poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findOneById($poll_id);
    		if(!$poll){
    			throw new \Exception('Nao encontrei nada!');
    		}
    		$data['poll']['openingTime'] = $poll->getOpeningTime()->format('Y-m-d H:i:s');
    		$data['poll']['closingTime'] = $poll->getClosingTime()->format('Y-m-d H:i:s');
    		$response->setData($data);
    	} catch (\Exception $e) {
    		$response->setStatusCode(500);
    		$response->setData(array('message' => $e->getMessage()));
    	}
    	return $response;
    }
}
