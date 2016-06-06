<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Entity\City;
use PROCERGS\VPR\CoreBundle\Entity\BallotBox;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\BallotBoxType;
use PROCERGS\VPR\CoreBundle\Form\Type\Admin\BallotBoxFilterType;
use PROCERGS\VPR\CoreBundle\Entity\SentMessage;

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
     * @Route("/index", name="admin_ballotbox")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em        = $this->getDoctrine()->getManager();
        $session   = $this->getRequest()->getSession();
        $paginator = $this->get('knp_paginator');
        $form      = $this->createForm(new BallotBoxFilterType());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $session->set('ballotBox_filters', $request);
        } else {
            $request = $session->get('ballotBox_filters');
            if ($request) $form->handleRequest($request);
        }

        $filters = $form->getData();

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select('b, sm1, sm2, c')
            ->from('PROCERGSVPRCoreBundle:BallotBox', 'b')
            ->where('1=1')
            ->leftJoin('b.city', 'c')
            ->innerJoin('b.poll', 'p')
            ->leftJoin('b.sentMessage1', 'sm1')
            ->leftJoin('b.sentMessage2', 'sm2')
            ->orderBy('p.openingTime', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->addOrderBy('b.address', 'ASC');

        if (isset($filters['poll'])) {
            $queryBuilder->andWhere('b.poll = :poll');
            $queryBuilder->setParameter('poll', $filters['poll']);
        } else {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
            $queryBuilder->andWhere('b.poll = :poll');
            $queryBuilder->setParameter('poll', $poll->getId());
            $form->get('poll')->setData($poll);
        }

        if (isset($filters['city'])) {
            $queryBuilder->andWhere('b.city = :city');
            $queryBuilder->setParameter('city', $filters['city']);
        }

        if (!is_null($filters['is_online'])) {
            $queryBuilder->andWhere('b.isOnline = :online');
            $queryBuilder->setParameter('online', $filters['is_online']);
        } else {
            $queryBuilder->andWhere('b.isOnline = :online');
            $queryBuilder->setParameter('online', false);
        }
        switch ($filters['status1']) {
            case 1:
                $queryBuilder->andWhere('b.setupAt is null');
            break;
            case 2:
                $queryBuilder->andWhere('b.setupAt is not null and b.closedAt is null');
            break;
            case 3:
                $queryBuilder->andWhere('b.setupAt is not null and b.closedAt is not null');
            break;
        }
        if ($filters['pin']) {
            $queryBuilder->andWhere('b.pin = :pin');
            $queryBuilder->setParameter('pin', $filters['pin']);
        }

        $query = $queryBuilder->getQuery();

        $page     = $this->get('request')->query->get('page', 1);

        ;

        $entities = $paginator->paginate($query, $page, 20);

        return array(
            'entities' => $entities,
            'form' => $form->createView()
        );
    }

    /**
     * BallotBox send.
     *
     * @Route("/send", name="admin_ballotbox_send")
     * @Template()
     */
    public function sendAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        try {
            $params = $request->request->all();
    
            if ($params['selection_type'] == 1) {
                foreach ($params['ballotboxes'] as $ballot) {
                    if (!is_numeric($ballot)) {
                        throw new \Exception("Urna deve ser numerico");
                    }
                }
                $ballotboxes = implode(", ", $params['ballotboxes']);
                $extra = "where b.id in (" . $ballotboxes . ")";
            } else {
                $session = $session->get('ballotBox_filters');
                $filters = $session->request->get('procergs_vpr_corebundle_ballotbox_filter');
    
                $extra = " left join city c on c.id = b.city_id ";
                $extra .= " where email is not null ";
    
    
                if (isset($filters['poll']) && $filters['poll']) {
                    $extra .= " and b.poll_id = " . $filters['poll'];
                }
                if (isset($filters['city']) && $filters['city']) {
                    $extra .= " and b.city_id = " . $filters['city'];
                }
                if (isset($filters['is_online'])) {
                    if ($filters['is_online']) {
                        $extra .= " and b.is_online = true";
                    } else {
                        $extra .= " and b.is_online = false";
                    }
                }
                if (isset($filters['status1'])) {
                    switch ($filters['status1']) {
                        case 1:
                            $extra .= " and b.setup_at is null";
                            break;
                        case 2:
                            $extra .= " and (b.setup_at is not null and b.closed_at is null)";
                            break;
                        case 3:
                            $extra .= " and (b.setup_at is not null and b.closed_at is not null)";
                            break;
                    }
                }
                if (isset($filters['pin']) && $filters['pin']) {
                    $extra .= " and b.pin = " .$filters['pin'];
                }
            }
    
            $connection = $em->getConnection();
            $connection->beginTransaction();
    
    		$sql = " select to_char(p.apuration_time, 'DD/MM/YYYY HH24:MI:SS') apuration_time, b.email, string_agg('PIN:' || b.pin::text || ' - senha:' || b.secret || ' - Dt. Fechamento:' ||  to_char(coalesce(b.closing_time, p.closing_time), 'DD/MM/YYYY HH24:MI:SS') , E'\\n') as content, string_agg(b.id::text, ','::text) as ids from ballot_box b ";
    		$sql .= " inner join poll p on p.id = b.poll_id ";
    		$sql .= $extra;
    		$sql .= " and b.email is not null ";
    		$sql .= " group by p.apuration_time, email ";
            $statement = $connection->prepare($sql);
            $statement->execute();
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
            $msg2 = "
<p>Você foi autorizado a utilizar um urna offline do sistema <a href='https://vota.rs.gov.br'>Consulta Popular<a></p>
<p>A seguir está a lista de PINs, senhas e horários para as urnas offline autorizadas que devem ser devolvidas até <strong>%s</strong></p>
<p>PIN;SENHA</p>
%s
<p>Certifique-se que seu aplicativo está atualizado e que seu smartphone atenda aos requisitos, descritos na loja do respectivo aparelho.</p>
<p>Caso tenha dificuldades em consultar a versão atual do seu aplicativo, entre em contato com a SEPLAN ou com a PROCERGS.</p>
<p>Evite imprevistos, envie com antecedência.</p>
";

            $msg1 = "
<p>Você foi requisitado a transmitr um urna offline do sistema <a href='https://vota.rs.gov.br'>Consulta Popular<a></p>
<p>A seguir está a lista de PINs que você deve transmitir até <strong>%s</strong></p>
%s
<p>Certifique-se que seu aplicativo está atualizado e que seu smartphone atenda aos requisitos, descritos na loja do respectivo aparelho.</p>
<p>Caso tenha dificuldades em consultar a versão atual do seu aplicativo, entre em contato com a SEPLAN ou com a PROCERGS.</p>
";
            $repoSentMessage = $em->getRepository('PROCERGSVPRCoreBundle:SentMessage');
    		while ($result = $statement->fetch()) {    		    
    		    try {
    		        $success = true;
    		        $message = \Swift_Message::newInstance();
    		        $message->setFrom($this->getParameter('mailer_sender_mail'), $this->getParameter('mailer_sender_name'));
    		        $message->setTo($result['email']);
    		        if ($params['message_type'] == SentMessage::TYPE_REQUISICAO) {
    		            $message->setSubject('Autorizacao para urna offline');
    		            $message->setBody(sprintf($msg2, $result['apuration_time'], $result['content']), 'text/html');
    		        } else {
    		            $message->setSubject('Urgente! Retorno de urna offline');
    		            $message->setBody(sprintf($msg1, $result['apuration_time'], $result['content']), 'text/html');
    		        }
    		        $this->get('mailer')->send($message);
    		    } catch (\Exception $e) {
    		        $success = false;
    		    }
    		    $ids = explode(',', $result['ids']);
    		    foreach ($ids as $id) {
    		        $log = new SentMessage();
    		        $log->setBallotBoxId($id);
    		        $log->setSentMessageTypeId($params['message_type']);
    		        $log->setSentMessageModeId(SentMessage::MODE_EMAIL);
    		        $log->setDestination($result['email']);
    		        $log->setSuccess($success);
    		        $repoSentMessage->save($log);
    		    }
    		}
    		$sql = " select to_char(p.apuration_time, 'DD/MM/YYYY HH24:MI:SS') apuration_time, b.ddd, b.fone, b.pin, b.secret, to_char(coalesce(b.closing_time, p.closing_time), 'DD/MM/YYYY HH24:MI:SS') as closing_time, b.id from ballot_box b ";
    		$sql .= " inner join poll p on p.id = b.poll_id ";
    		$sql .= $extra;
    		$sql .= " and b.fone is not null and b.ddd is not null ";
    		$statement = $connection->prepare($sql);
    		$statement->execute();
    		$statement->setFetchMode(\PDO::FETCH_ASSOC);        
    		$msg2 = "Autorizacao urna offline da consulta popular. PIN: %s SENHA: %s ENCERRAMENTO: %s";
            $msg1 = "Urgente! Transmita urna offline consulta popular de PIN %s ate %s";
            
            $smsHelper = $this->get('vpr.smshelper');        
            $smsHelper->setAplicacao('votação de prioridades');
            $smsHelper->setRemetente('PROCERGS');
            while ($result = $statement->fetch()) {
                try {
                    $success = true;
                    $protocolo = null;
                    $smsHelper->setDdd($result['ddd']);
                    $smsHelper->setTelefone($result['fone']);
                    if ($params['message_type'] == SentMessage::TYPE_REQUISICAO) {
                        $smsHelper->setMensagem(sprintf($msg2, $result['pin'], $result['secret'], $result['closing_time']));
                    } else {
                        $smsHelper->setMensagem(sprintf($msg1, $result['pin'], $result['apuration_time']));
                    }
                    $protocolo = $smsHelper->send();
                } catch (\Exception $e) {
                    $success = false;
                }
                $log = new SentMessage();
                $log->setBallotBoxId($result['id']);
                $log->setSentMessageTypeId($params['message_type']);
                $log->setSentMessageModeId(SentMessage::MODE_SMS);
                $log->setDestination($result['ddd'].$result['fone']);
                $log->setSmsCode($protocolo);
                $log->setSuccess($success);
                $repoSentMessage->save($log);
            }
            $this->get('session')->getFlashBag()->add('success', 'Mensagens enviadas com sucesso');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }
        return $this->redirectToRoute('admin_ballotbox');
    }
    
    

    /**
     * Creates a new BallotBox entity.
     *
     * @Route("/", name="admin_ballotbox_create")
     * @Method("POST")
     * @Template("PROCERGSVPRCoreBundle:Admin\BallotBox:edit.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new BallotBox();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);

        try {
        	if ($form->isValid()) {
        		$em = $this->getDoctrine()->getManager();

        		$repo = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox');
        		self::isValid1($entity);
        		$pin = $repo->generateUniquePin($entity->getPoll(), 4);
        		$entity->setPin($pin);
        		$entity->setKeys();
        		$em->persist($entity);
        		$em->flush();

        		$translator = $this->get('translator');
        		$this->get('session')->getFlashBag()->add('success',
        				$translator->trans('admin.successfully_added_record'));

        		return $this->redirect($this->generateUrl('admin_ballotbox_show',
        				array('id' => $entity->getId())));
        	}
        } catch (\Exception $e) {
        	$this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }
        return array(
            'entity' => $entity,
            'edit_form' => $form->createView(),
			'delete_form' => null
        );
    }

    private static function isValid1(&$entity)
    {
    	if ($entity->getIsOnline()) {
    		$ballotBox = $this->getDoctrine()->getManager()->getRepository('PROCERGSVPRCoreBundle:BallotBox')->hasOnline($entity->getPoll());
    		if ($ballotBox) {
    			if ($entity->getId()) {
    				if ($entity->getId() != $ballotBox->getId()) {
    					throw new \Exception("Ja tem um urna online para essa votacao");
    				}
    			} else {
    				throw new \Exception("Ja tem um urna online para essa votacao");
    			}
    		}
    	} else {
    		if (!$entity->getCity()) {
    			throw new \Exception("Precisa selecionar uma cidade");
    		}
    		if ($entity->getOpeningTime() || $entity->getClosingTime()) {
    			if (!($entity->getOpeningTime() && $entity->getClosingTime())) {
    				throw new \Exception("Necessario colocar tanto a data de abertura quanto a data de fechamento");
    			}
    		}
    	}
    	if ($entity->getFone() || $entity->getDdd()) {
    	    if (!$entity->getFone() || !$entity->getDdd()) {
    	        throw new \Exception("Colocar ddd e fone");
    	    }
    	}
    	if ($entity->getDdd()) {
    	    if (!is_numeric($entity->getDdd()) || strlen($entity->getDdd()) !=2) {
    	        throw new \Exception("Colocar um ddd com 2 numeros");
    	    }
    	}
    	if ($entity->getFone()) {
    		if (!is_numeric($entity->getFone()) || strlen($entity->getFone()) < 8) {
    			throw new \Exception("Colocar um numero de telefone com no minimo 8 numeros");
    		}
    	}
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
        $form = $this->createForm(new BallotBoxType(), $entity,
            array(
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
     * @Template("PROCERGSVPRCoreBundle:Admin\BallotBox:edit.html.twig")
     */
    public function newAction()
    {
        $entity = new BallotBox();
        $entity->setSecret($entity->generatePassphrase());
        $form   = $this->createCreateForm($entity);

        return array(
			'entity' => $entity,
			'edit_form' => $form->createView(),
			'delete_form' => null
        );
    }

    /**
     * Finds and displays a BallotBox entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_ballotbox_show")
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
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing BallotBox entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="admin_ballotbox_edit")
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

        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
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
        $form = $this->createForm(new BallotBoxType(), $entity,
            array(
            'action' => $this->generateUrl('admin_ballotbox_update',
                array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing BallotBox entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_ballotbox_update")
     * @Method("PUT")
     * @Template("PROCERGSVPRCoreBundle:Admin\BallotBox:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->find($id);
        $entityOld = clone $entity; 
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BallotBox entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        try {
        	if ($editForm->isValid()) {
        		self::isValid1($entity);
        		if ($entityOld->getEmail() != $entity->getEmail() || $entityOld->getDdd() != $entity->getDdd() || $entityOld->getFone() != $entity->getFone()) {
        		    $entity->setSentMessage1Id(null);
        		    $entity->setSentMessage2Id(null);
        		}
        		$em->flush();

        		$translator = $this->get('translator');
        		$this->get('session')->getFlashBag()->add('success',
        				$translator->trans('admin.successfully_changed_record'));

        		return $this->redirect($this->generateUrl('admin_ballotbox_show',
        				array('id' => $id)));
        	}
        } catch (\Exception $e) {
        	$this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }
        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a BallotBox entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_ballotbox_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em     = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BallotBox entity.');
            }

            $em->remove($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success',
                $translator->trans('admin.successfully_removed_record'));
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
                ->setAction($this->generateUrl('admin_ballotbox_delete',
                        array('id' => $id)))
                ->setMethod('DELETE')
                ->add('submit', 'submit', array('label' => 'Delete'))
                ->getForm()
        ;
    }

    /**
     * Clear Filters
     * @Method("GET")
     * @Route("/filters/clear", name="admin_ballotbox_clear_filters")
     */
    public function clearFiltersAction()
    {
        $session = $this->getRequest()->getSession();
        $session->remove('ballotBox_filters');
        return $this->redirect($this->generateUrl('admin_ballotbox'));
    }

    /**
     * @Route("/batch", name="vpr_ballotbox_batch_create")
     * @Template
     */
    public function batchCreateAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $pollId = $request->get('poll_id');

        if ($pollId !== null) {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->find($pollId);
        } else {
            $poll = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findLastPoll();
        }


        $default = array(
            'openingTime' => $poll->getOpeningTime(),
            'closingTime' => $poll->getClosingTime()
        );
        $builder = $this->createFormBuilder($default)
            ->add('openingTime', 'datetime',
                array('date_widget' => 'single_text'))
            ->add('closingTime', 'datetime',
                array('date_widget' => 'single_text'))
            ->add('config', 'file');

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data   = $form->getData();
            $config = $this->parseData($data['config']);

            $openingTime = $data['openingTime'];
            $closingTime = $data['closingTime'];

            if (!($openingTime instanceof \DateTime)) {
                $openingTime = $poll->getOpeningTime();
            }

            if (!($closingTime instanceof \DateTime)) {
                $closingTime = $poll->getClosingTime();
            }

            $cities = $em->getRepository('PROCERGSVPRCoreBundle:City')
                ->findByCityName($config['cities']);

            $result = '';
            foreach ($config['requests'] as $req) {
                $index = strtolower($req['city']);
                if (array_key_exists($index, $cities)) {
                    $city = $cities[$index];

                    $ballotBox = $this->createOfflineBallotBox($em, $poll,
                        $city, $openingTime, $closingTime);

                    $req['pin']        = $ballotBox->getPin();
                    $req['passphrase'] = $ballotBox->getSecret();
                }

                $result .= sprintf('%s;%s;%s;%s;%s;%s', $req['city'],
                        $req['person'], $req['cpf'], $req['email'],
                        $req['passphrase'], $req['pin']).PHP_EOL;
            }

            $em->flush();

            $response = new \Symfony\Component\HttpFoundation\Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', 'text/csv');
            $response->headers->set('Content-Disposition',
                'attachment; filename="offline_ballot_boxes.csv";');
            $response->headers->set('Content-length', strlen($result));

            $response->sendHeaders();

            $response->setContent($result);
            return $response;
        }

        return array('form' => $form->createView());
    }

    private function createOfflineBallotBox(EntityManager $em, Poll $poll,
                                            City $city, \DateTime $openingTime,
                                            \DateTime $closingTime)
    {
        $name = "Urna offline de {$city->getName()}";

        $entity = new BallotBox();
        $entity->setSecret($entity->generatePassphrase())
            ->setCity($city)
            ->setIsOnline(false)
            ->setName($name)
            ->setOpeningTime($openingTime)
            ->setClosingTime($closingTime)
            ->setPoll($poll);

        $repo = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox');
        $pin  = $repo->generateUniquePin($entity->getPoll(), 4);
        $entity->setPin($pin);

        $em->persist($entity);
        return $entity;
    }

    private function parseData(UploadedFile $file)
    {
        $handle = $file->openFile();

        $first  = true;
        $config = array();
        while (($data   = $handle->fgetcsv(';')) !== FALSE) {
            if (count($data) < 4) {
                break;
            }
            if ($first) {
                $first = false;
                continue;
            }
            $cleanData = array_map('trim', $data);
            $city      = strtolower($cleanData[0]);

            $config['cities'][] = $city;

            $config['requests'][] = array(
                'city' => $city,
                'person' => $cleanData[1],
                'cpf' => $cleanData[2],
                'email' => $cleanData[3],
                'passphrase' => @$cleanData[4],
                'pin' => @$cleanData[5]
            );
        }

        return $config;
    }

    /**
     * @Route("/email", name="vpr_ballotbox_email")
     * @Template
     */
    public function emailBallotBoxesAction(Request $request)
    {
        $builder = $this->createFormBuilder()
            ->add('config', 'file');

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data   = $form->getData();
            $config = $this->parseData($data['config']);

            $emails = $this->prepareEmails($config);
            foreach ($emails as $message) {
                $this->get('mailer')->send($message);
            }

            return $this->redirectToRoute('vpr_ballotbox_email');
        }

        return array('form' => $form->createView());
    }

    private function groupRequestsByEmail($config)
    {
        $requests = array();
        foreach ($config['requests'] as $req) {
            $requests[$req['email']][] = $req;
        }
        return $requests;
    }

    private function prepareEmails($config)
    {
        $emails   = array();
        $requests = $this->groupRequestsByEmail($config);
        foreach ($requests as $email => $reqs) {
            $emailRegex  = "/^[^a-zA-Z0-9@.\-!#$%&'*+\/\=?^_`{|}~]*/";
            $cleanEmail  = preg_replace($emailRegex, '', $email);
            $destination = explode(' ', $cleanEmail)[0];

            $message = \Swift_Message::newInstance()
                ->setSubject('Informações de Urna Offline')
                ->setFrom($this->getParameter('mailer_sender_mail'),
                    $this->getParameter('mailer_sender_name'))
                ->setTo($destination)
                ->setBody(
                $this->renderView(
                    'Emails/ballotboxes.txt.twig', array('requests' => $reqs)
                ), 'text/plain'
            );

            $emails[] = $message;
        }

        return $emails;
    }
}
