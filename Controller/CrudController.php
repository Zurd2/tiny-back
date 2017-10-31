<?php

namespace Zurd2\SmallCrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CrudController extends Controller
{
    /**
     * @Route("/crud", name="crud_homepage")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction()
    {
        $securityContext = $this->get('security.authorization_checker');

        if ($securityContext->isGranted('ROLE_ADMIN')) {
            return $this->render('@SmallCrud/templates/index.html.twig');
        }

        return $this->redirectToRoute('fos_user_security_login');
    }

    /**
     * @Route("/crud/list/{entityName}", name="crud_list")
     *
     * @param $entityName
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($entityName)
    {
        $em = $this->getDoctrine()->getManager();
        $entityService = $this->get('scb_entity_service');

        $entities = $em->getRepository('BackendBundle:'.$entityName)->findAll();

        return $this->render('@SmallCrud/templates/list.html.twig', array(
            'entityName' => $entityName,
            'entityAttr' => $entityService->getProperties($entityName),
            'entities' => $entities,
        ));
    }

    /**
     * @Route("/crud/new/{entityName}/{id}", name="crud_new", requirements={"id": "\d+"})
     * @Route("/crud/edit/{entityName}/{id}", name="crud_edit", requirements={"id": "\d+"})
     *
     * @param Request $request
     * @param $entityName
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newEditAction(Request $request, $entityName, $id=null)
    {
        $em = $this->getDoctrine()->getManager();
        $entityService = $this->get('scb_entity_service');

        $entityPath = $entityService->getPath($entityName);
        $entityFormPath = $entityService->getPath($entityName, 'Form');

        if ($id == null) {
            $entity = new $entityPath();
        } else {
            $entity = $em->getRepository('BackendBundle:'.$entityName)->findOneBy(array('id' => $id));
        }

        $form = $this->createForm($entityFormPath.'Type', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('crud_list', array('entityName' => $entityName));
        }

        return $this->render('@SmallCrud/templates/new_edit.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'entityName' => $entityName,
        ));
    }

    /**
     * @Route("/crud/show/{entityName}/{id}", name="crud_show")
     *
     * @param $entityName
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($entityName, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entityService = $this->get('scb_entity_service');

        $entity = $em->getRepository('BackendBundle:'.$entityName)->findOneBy(array('id' => $id));

        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('@SmallCrud/templates/show.html.twig', array(
            'filmScore' => $entity,
            'delete_form' => $deleteForm->createView(),
            'entityName' => $entityName,
            'entity' => $entity,
            'entityAttr' => $entityService->getProperties($entityName),
        ));
    }

    /**
     * @Route("/crud/delete/{entityName}/{id}", name="crud_delete")
     *
     * @param Request $request
     * @param $entityName
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $entityName, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:'.$entityName)->findOneBy(array('id' => $id));

        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirectToRoute('crud_list', array('entityName' => $entityName));
    }

    /**
     * @param $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('crud_delete', array(
                'entityName' => 'FilmScore',
                'id' => $entity->getId()
            )))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}