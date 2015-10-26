<?php

namespace PROCERGS\VPR\CoreBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Person controller.
 *
 * @Route("/")
 */
class PersonController extends Controller
{

    /**
     * Lists all Person entities.
     *
     * @Route("/", name="admin_person")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('PROCERGSVPRCoreBundle:Person')
            ->getfindLoginCidadaoQuery()
            ->orderBy('p.firstName')
            ->getQuery();

        $paginator = $this->get('knp_paginator');
        $entities  = $paginator->paginate(
            $query, $this->get('request')->query->get('page', 1), 10
        );

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Route("/search/{query}", name="admin_person_search")
     * @Method("GET")
     * @Template("PROCERGSVPRCoreBundle:Admin/Person:index.html.twig")
     */
    public function searchAction(Request $request, $query)
    {
        $search = $this->getPersonRepository()
            ->getfindLoginCidadaoQuery()
            ->join('p.treVoter', 't')
            ->orderBy('p.firstName');

        $search->andWhere(
                $search->expr()->orX('p.firstName LIKE :name', 't.id = :query')
            )
            ->setParameter('name', "%$query%")
            ->setParameter('query', $query);

        $paginator = $this->get('knp_paginator');
        $entities  = $paginator->paginate(
            $search->getQuery(), $this->get('request')->query->get('page', 1),
            10
        );

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Person entity.
     *
     * @Route("/{id}", name="admin_person_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PROCERGSVPRCoreBundle:Person')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * @return \PROCERGS\VPR\CoreBundle\Entity\PersonRepository
     */
    private function getPersonRepository()
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('PROCERGSVPRCoreBundle:Person');
    }
}
