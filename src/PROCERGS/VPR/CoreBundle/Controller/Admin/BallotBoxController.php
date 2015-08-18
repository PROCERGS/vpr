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
            ->select('b')
            ->from('PROCERGSVPRCoreBundle:BallotBox', 'b')
            ->where('1=1')
            ->leftJoin('b.city', 'c')
            ->innerJoin('b.poll', 'p')
            ->orderBy('p.openingTime', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->addOrderBy('b.address', 'ASC');

        if (isset($filters['poll'])) {
            $queryBuilder->andWhere('b.poll = :poll');
            $queryBuilder->setParameter('poll', $filters['poll']);
        }

        if (isset($filters['city'])) {
            $queryBuilder->andWhere('b.city = :city');
            $queryBuilder->setParameter('city', $filters['city']);
        }

        if (!is_null($filters['is_online'])) {
            $queryBuilder->andWhere('b.isOnline = :online');
            $queryBuilder->setParameter('online', $filters['is_online']);
        }

        $query = $queryBuilder->getQuery();

        $page     = $this->get('request')->query->get('page', 1);
        $entities = $paginator->paginate($query, $page, 20);

        return array(
            'entities' => $entities,
            'form' => $form->createView()
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
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $repo = $em->getRepository('PROCERGSVPRCoreBundle:BallotBox');
            $pin  = $repo->generateUniquePin($entity->getPoll(), 4);
            $entity->setPin($pin);

            $em->persist($entity);
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success',
                $translator->trans('admin.successfully_added_record'));

            return $this->redirect($this->generateUrl('admin_ballotbox_show',
                        array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
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
     * @Template()
     */
    public function newAction()
    {
        $entity = new BallotBox();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
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

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BallotBox entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $translator = $this->get('translator');
            $this->get('session')->getFlashBag()->add('success',
                $translator->trans('admin.successfully_changed_record'));

            return $this->redirect($this->generateUrl('admin_ballotbox_show',
                        array('id' => $id)));
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
                //$this->get('mailer')->send($message);
            }
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
