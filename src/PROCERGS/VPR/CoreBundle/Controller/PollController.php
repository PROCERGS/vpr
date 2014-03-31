<?php

namespace PROCERGS\VPR\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PROCERGS\VPR\CoreBundle\Entity\Poll;
use PROCERGS\VPR\CoreBundle\Form\PollType;

/**
 * Poll controller.
 *
 * @Route("/poll")
 */
class PollController extends Controller
{

    /**
     * Lists all Poll entities.
     *
     * @Route("/", name="poll")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PROCERGSVPRCoreBundle:Poll')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Poll entity.
     *
     * @Route("/", name="poll_create")
     * @Method("POST")
     * @Template("PROCERGSVPRCoreBundle:Poll:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Poll();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro adicionado com sucesso!');
            return $this->redirect($this->generateUrl('poll'));
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
            'action' => $this->generateUrl('poll_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Poll entity.
     *
     * @Route("/new", name="poll_new")
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
     * @Route("/{id}", name="poll_show")
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

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Poll entity.
     *
     * @Route("/{id}/edit", name="poll_edit")
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

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
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
            'action' => $this->generateUrl('poll_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Poll entity.
     *
     * @Route("/{id}", name="poll_update")
     * @Method("PUT")
     * @Template("PROCERGSVPRCoreBundle:Poll:edit.html.twig")
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

            return $this->redirect($this->generateUrl('poll_edit', array('id' => $id)));
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
     * @Route("/{id}", name="poll_delete")
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

            $em->remove($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Registro removido com sucesso!');
        }

        return $this->redirect($this->generateUrl('poll'));
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
            ->setAction($this->generateUrl('poll_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
