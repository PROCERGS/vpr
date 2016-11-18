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
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\PollOptionFilterType;
use PROCERGS\VPR\CoreBundle\Helper\PPPHelper;
use Symfony\Component\HttpKernel\Exception\HttpException;
use PROCERGS\VPR\CoreBundle\Entity\StatsTotalCoredeVoteRepository;

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
     * @Route("/index", name="admin_poll")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_POLL_READ');
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

        $checkPoll = $this->get('vpr.checkpoll.helper');
        foreach ($entities as $e) {
            $status = $checkPoll->checkBlocked($e->getId());

            if ($status) {
                $e->setBlocked(true);
            }
        }

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Poll entity.
     *
     * @Route("/", name="admin_poll_create")
     * @Method("POST")
     * @Template("PROCERGSVPRCoreBundle:Admin\Poll:edit.html.twig")
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_POLL_CREATE');
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
            'edit_form'   => $form->createView(),
            'delete_form'  => null,
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
     * @Template("PROCERGSVPRCoreBundle:Admin\Poll:edit.html.twig")
     */
    public function newAction()
    {
        $this->denyAccessUnlessGranted('ROLE_POLL_CREATE');
        $entity = new Poll();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'edit_form'   => $form->createView(),
            'delete_form' => null
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
        $this->denyAccessUnlessGranted('ROLE_POLL_READ');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        $steps = $entity->getSteps();

        $deleteForm = $this->createDeleteForm($id);

        $checkPoll = $this->get('vpr.checkpoll.helper');
        $status = $checkPoll->checkBlocked($entity->getId());
        if ($status) {
            $entity->setBlocked(true);
        }

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
        $this->denyAccessUnlessGranted('ROLE_POLL_UPDATE');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poll entity.');
        }

        $checkPoll = $this->get('vpr.checkpoll.helper');
        $status = $checkPoll->checkBlocked($entity->getId());
        if ($status) {
            $entity->setBlocked(true);
        }

        if ($entity->getBlocked() || $entity->getApurationDone()) {
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
        $this->denyAccessUnlessGranted('ROLE_POLL_UPDATE');
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
        $this->denyAccessUnlessGranted('ROLE_POLL_DELETE');
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
     * @Template()
     */
    public function statsListAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_RESULTS');

        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        
        /* @var $statsRepo StatsTotalCoredeVoteRepository */
        $statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');
        $coredeRepo    = $em->getRepository('PROCERGSVPRCoreBundle:Corede');

        $poll_filters = $session->get('poll_filters');

        $form = $this->createForm(new PollOptionFilterType());
        $form->remove("corede");


        if ($request->isMethod('POST') || $poll_filters) {
            if(!$request->isMethod('POST') && $poll_filters){
                $form->bind($poll_filters);
            } else{
                $form->bind($request);
                $session->set('poll_filters', $request);
            }
            $selected = $form->getData();

            $poll = $selected['poll'];
        } else {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        }

        $votes = $statsRepo->findTotalVotesByPoll($poll->getId());

        $coredes = null;
        foreach ($votes as $vote) {
            $coredeId = $vote['corede_id'];
            $coredes[$coredeId]['corede_id'] = $vote['corede_id'];
            $coredes[$coredeId]['corede'] = $vote['name'];
            $coredes[$coredeId]['votes_online'] = $vote['votes_online'];
            $coredes[$coredeId]['votes_offline'] = $vote['votes_offline'];
            $coredes[$coredeId]['votes_sms'] = $vote['votes_sms'];
            $coredes[$coredeId]['votes_total'] = $vote['votes_total'];
        }

        $voters    = $statsRepo->findTotalVotersByPoll($poll->getId());
        foreach ($voters as $vote) {
            $coredeId = $vote['corede_id'];
            $coredes[$coredeId]['voters_online'] = $vote['voters_online'];
            $coredes[$coredeId]['voters_offline'] = $vote['voters_offline'];
            $coredes[$coredeId]['voters_sms'] = $vote['voters_sms'];
            $coredes[$coredeId]['voters_total'] = $vote['voters_total'];
        }
        $voters = $statsRepo->findTotalVotersByPollFake($poll->getId());
        foreach ($voters as $vote) {
            $coredeId = $vote['corede_id'];
            $coredes[$coredeId]['fake_voters_online'] = $vote['voters_online'];
            $coredes[$coredeId]['fake_voters_offline'] = $vote['voters_offline'];
            $coredes[$coredeId]['fake_voters_sms'] = $vote['voters_sms'];
            $coredes[$coredeId]['fake_tot_pop'] = $vote['tot_pop'];
            $coredes[$coredeId]['fake_tot'] = $vote['tot'];
            $coredes[$coredeId]['fake_perc'] = $vote['perc'];
        }
        return array(
            'poll' => $poll,
            'coredes' => $coredes,
            'form' => $form->createView()
        );
    }
    
    /**
     * Lists poll stats.
     *
     * @Route("/stats2", name="admin_stats2")
     * @Template()
     */
    public function statsList2Action(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_RESULTS');
    
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
    
        $poll_filters = $session->get('poll_filters');
    
        $form = $this->createForm(new PollOptionFilterType());
        $form->remove("corede");
    
    
        if ($request->isMethod('POST') || $poll_filters) {
            if(!$request->isMethod('POST') && $poll_filters){
                $form->bind($poll_filters);
            } else{
                $form->bind($request);
                $session->set('poll_filters', $request);
            }
            $selected = $form->getData();
    
            $poll = $selected['poll'];
        } else {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        }
        $statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');
        $coredes = $statsRepo->findTotalVotersByPollFake($poll->getId());
        return array(
            'poll' => $poll,
            'coredes' => $coredes,
            'form' => $form->createView()
        );
    }

    /**
     * Lists poll stats by corede.
     *
     * @Route("/stats2/{poll}/corede/{corede}", name="admin_stats_corede2")
     * @Template()
     */
    public function statsListCorede2Action(Request $request, $poll, $corede)
    {
        $this->denyAccessUnlessGranted('ROLE_RESULTS');
    
        $em = $this->getDoctrine()->getManager();
    
        $coredeRepo    = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
        $corede = $coredeRepo->find($corede);
        $statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');
        $cities = $statsRepo->findTotalVotersByPollAndCoredeFake($poll, $corede->getId());
        return array(
            'corede' => $corede,
            'cities' => $cities,
        	'poll'   => $poll
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

    /**
     * Lists poll stats by corede.
     *
     * @Route("/stats/{poll}/corede/{corede}", name="admin_stats_corede")
     * @Template()
     */
    public function statsListCoredeAction($poll, $corede)
    {
        $this->denyAccessUnlessGranted('ROLE_RESULTS');

        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        /* @var $statsRepo StatsTotalCoredeVoteRepository */
        $statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');
        $coredeRepo    = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
        $corede = $coredeRepo->find($corede);

        $cityRepo    = $em->getRepository('PROCERGSVPRCoreBundle:City');

        $votes = $statsRepo->findTotalVotesByPollAndCorede($poll, $corede->getId());

        $cities = null;
        foreach ($votes as $vote) {            
            $cityId = $vote['city_id'];
            $cities[$cityId]['city_id'] = $vote['city_id'];
            $cities[$cityId]['city'] = $vote['name'];
            $cities[$cityId]['votes_online'] = $vote['votes_online'];
            $cities[$cityId]['votes_offline'] = $vote['votes_offline'];
            $cities[$cityId]['votes_sms'] = $vote['votes_sms'];
            $cities[$cityId]['votes_total'] = $vote['votes_total'];
        }

        $voters    = $statsRepo->findTotalVotersByPollAndCorede($poll, $corede->getId());
        foreach ($voters as $vote) {
            $cityId = $vote['city_id'];
            $cities[$cityId]['voters_online'] = $vote['voters_online'];
            $cities[$cityId]['voters_offline'] = $vote['voters_offline'];
            $cities[$cityId]['voters_sms'] = $vote['voters_sms'];
            $cities[$cityId]['voters_total'] = $vote['voters_total'];
        }
        $voters = $statsRepo->findTotalVotersByPollAndCoredeFake($poll, $corede->getId());
        foreach ($voters as $vote) {
            $cityId = $vote['city_id'];
            $cities[$cityId]['fake_voters_online'] = $vote['voters_online'];
            $cities[$cityId]['fake_voters_offline'] = $vote['voters_offline'];
            $cities[$cityId]['fake_voters_sms'] = $vote['voters_sms'];
            $cities[$cityId]['fake_tot_pop'] = $vote['tot_pop'];
            $cities[$cityId]['fake_tot'] = $vote['tot'];
            $cities[$cityId]['fake_perc'] = $vote['perc'];
        }
        return array(
            'corede' => $corede,
            'cities' => $cities
        );
    }
    
    /**
     * Lists all Poll entities.
     *
     * @Route("/admin_transfer_poll_option/{id}", name="admin_transfer_poll_option")
     * @Method("POST")
     */
    public function transferPollOptionAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_POLL_UPDATE');
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Poll $entity
         */
        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($id);
        if ($entity && $entity->getTransferYear() && $entity->getTransferPoolOptionStatus() == 0) {
            $entity->setTransferPoolOptionStatus(2);
            $em->persist($entity);
            $em->flush();
            $connection = $em->getConnection();
            /**
             * @var PPPHelper $pppHelper
             */
            $pppHelper = $this->get('vpr.ppphelper');
            $ret = $pppHelper->sync($entity, $connection);
            if (!$ret) {                
                $entity->setTransferPoolOptionStatus(0);
                $em->persist($entity);
                $em->flush();
                throw new HttpException(500, "Nao deu");
            }
            $entity->setTransferPoolOptionStatus(3);
            $em->persist($entity);
            $em->flush();            
        } else {
            $ret = false;
            if (!$entity) {
                throw new HttpException(500, 'Nao encontrei a urna.');
            }
            if (!$entity->getTransferYear()) {
                throw new HttpException(500, 'Essa urna nao e sincronizavel.');
            }
            if ($entity->getTransferPoolOptionStatus() != 0) {
                throw new HttpException(500, 'Essa urna nao pode ser sincornizada.');
            }            
        }
        return new JsonResponse(array(
            'hash' => $ret
        ));
    }
    
    /**
     * Lists all Poll entities.
     *
     * @Route("/admin_transfer_open_vote/{id}", name="admin_transfer_open_vote")
     * @Method("POST")
     */
    public function transferOpenVoteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_POLL_UPDATE');
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Poll $entity
         */
        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($id);
        if ($entity && $entity->getTransferYear() && $entity->getApurationStatus() == 3 && $entity->getTransferOpenVoteStatus() == 0 && $entity->getTransferPoolOptionStatus() == 3) {
            $entity->setTransferOpenVoteStatus(1);
            $em->persist($entity);
            $em->flush();            
            $ret = true;
        } else {
            $ret = false;
            if (!$entity) {
                throw $this->createNotFoundException("Nao encontrei a urna.");            
            }
            if (!$entity->getTransferYear()) {
                throw $this->createNotFoundException('Essa urna nao e sincronizavel.');
            }
            if ($entity->getTransferPoolOptionStatus() != 3) {
                throw $this->createNotFoundException('Essa urna não teve a cedula sincronizada.');
            }
            if ($entity->getApurationStatus() != 3) {
                throw $this->createNotFoundException('Essa urna não foi apurada.');
            }
            if ($entity->getTransferOpenVoteStatus() != 0) {
                throw $this->createNotFoundException('Essa urna nao pode ser sincornizada.');
            }
        }
        return new JsonResponse(array(
            'hash' => $ret
        ));        
    }
    
    /**
     * Lists poll stats.
     *
     * @Route("/stats2csv", name="admin_stats2_csv")
     */
    public function statsList2Actioncsv(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_RESULTS');
    
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
    
        $poll_filters = $session->get('poll_filters');
    
        $form = $this->createForm(new PollOptionFilterType());
        $form->remove("corede");
    
    
        if ($request->isMethod('POST') || $poll_filters) {
            if(!$request->isMethod('POST') && $poll_filters){
                $form->bind($poll_filters);
            } else{
                $form->bind($request);
                $session->set('poll_filters', $request);
            }
            $selected = $form->getData();
    
            $poll = $selected['poll'];
        } else {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        }
        $statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');
        $coredes = $statsRepo->findTotalVotersByPollFake($poll->getId());
        $result = '';
        $translator = $this->get('translator');
        $result .= sprintf(
        		'%s;%s;%s;%s;%s;%s;%s;%s',
        		'Corede ID',
        		$translator->trans('admin.corede'),
        		$translator->trans('admin.voters_online'),
        		$translator->trans('admin.voters_offline'),
        		$translator->trans('admin.voters_sms'),
        		$translator->trans('admin.tot'),
        		$translator->trans('admin.tot_pop'),
        		$translator->trans('admin.perc')
        ).PHP_EOL;
        $total_online = 0; 
        $total_offline = 0; 
        $total_sms = 0; 
        $total_pop = 0; 
        $total_tot = 0; 
        
        foreach ($coredes as $req) {
        	
        	$total_online = $total_online + $req['voters_online'];
        	$total_offline = $total_offline + $req['voters_offline'];
        	$total_sms = $total_sms + $req['voters_sms'];
        	$total_pop = $total_pop + $req['tot_pop'];
        	$total_tot = $total_tot + $req['tot'];
        	
        	$result .= sprintf(
        			'%s;%s;%s;%s;%s;%s;%s;%s',
        			$req['corede_id'],
        			$req['corede'],
        			$req['voters_online'],
        			$req['voters_offline'],
        			$req['voters_sms'],
        			$req['tot'],
        			$req['tot_pop'],
        			$req['perc']
        	).PHP_EOL;
        }
        
        $result .= sprintf(
        		'%s;%s;%s;%s;%s;%s;%s;%s',
        		'',
        		'',
        		$total_online,
        		$total_offline,
        		$total_sms,
        		$total_tot,
        		$total_pop,
        		''
        ).PHP_EOL;
        
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set(
        		'Content-Disposition',
        		'attachment; filename="apuracao_final.csv";'
        );
        $response->headers->set('Content-length', strlen($result));
        
        $response->sendHeaders();
        
        $response->setContent(utf8_decode($result));
        
        return $response;
    }
    
    /**
     * Export list poll stats by corede in csv.
     *
     * @Route("/stats2csv/{poll}/corede/{corede}", name="admin_stats_corede2_csv")
     */
    public function statsListCorede2Actioncsv(Request $request, $poll, $corede)
    {
    	$this->denyAccessUnlessGranted('ROLE_RESULTS');
    
    	$em = $this->getDoctrine()->getManager();
    
    	$coredeRepo    = $em->getRepository('PROCERGSVPRCoreBundle:Corede');
    	$corede = $coredeRepo->find($corede);
    	$statsRepo = $em->getRepository('PROCERGSVPRCoreBundle:StatsTotalCoredeVote');
    	$cities = $statsRepo->findTotalVotersByPollAndCoredeFake($poll, $corede->getId());
    	
    	$result = '';
    	$translator = $this->get('translator');
    	$result .= sprintf(
    			'%s;%s;%s;%s;%s;%s;%s;%s',
    			'Município ID',
    			$translator->trans('admin.city'),
    			$translator->trans('admin.voters_online'),
    			$translator->trans('admin.voters_offline'),
    			$translator->trans('admin.voters_sms'),
    			$translator->trans('admin.tot'),
    			$translator->trans('admin.tot_pop'),
    			$translator->trans('admin.perc')
    	).PHP_EOL;
    	
    	$total_online = 0;
    	$total_offline = 0;
    	$total_sms = 0;
    	$total_pop = 0;
    	$total_tot = 0;
    	
    	foreach ($cities as $req) {
    		 
    		$total_online = $total_online + $req['voters_online'];
    		$total_offline = $total_offline + $req['voters_offline'];
    		$total_sms = $total_sms + $req['voters_sms'];
    		$total_pop = $total_pop + $req['tot_pop'];
    		$total_tot = $total_tot + $req['tot'];
    		 
    		$result .= sprintf(
    				'%s;%s;%s;%s;%s;%s;%s;%s',
    				$req['city_id'],
    				$req['city'],
    				$req['voters_online'],
    				$req['voters_offline'],
    				$req['voters_sms'],
    				$req['tot'],
    				$req['tot_pop'],
    				$req['perc']
    		).PHP_EOL;
    	}
    	
    	$result .= sprintf(
    			'%s;%s;%s;%s;%s;%s;%s;%s',
    			'',
    			'',
    			$total_online,
    			$total_offline,
    			$total_sms,
    			$total_tot,
    			$total_pop,
    			''
    	).PHP_EOL;
    	
    	
    	$response = new \Symfony\Component\HttpFoundation\Response();
    	$response->headers->set('Cache-Control', 'private');
    	$response->headers->set('Content-type', 'text/csv');
    	$response->headers->set(
    			'Content-Disposition',
    			'attachment; filename="'.$corede->getName().'";'
    	);
    	$response->headers->set('Content-length', strlen($result));
    	
    	$response->sendHeaders();
    	
    	$response->setContent(utf8_decode($result));
    	
    	return $response;
    	
    }
    
}
