<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Collection;
use AppBundle\Entity\Note;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lists = $em->getRepository('AppBundle:Collection')->findAll();
/*
        $collection = new Collection();
        $form = $this->createForm('AppBundle\Form\CollectionType', $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collection->setCreatedAt(new \DateTime("now"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();

            //return $this->redirectToRoute('admin_round_index');
        }
*/
        return $this->render('default/index.html.twig', [
            'lists' => $lists,
            //'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajax/listAdd", name="list_add")
     * @Method("POST")
     */
    public function listAddAction(Request $request)
    {
        $title = $request->request->get('title');

        if (!empty($title))
        {
            $collection = new Collection();
            $collection->setTitle($title);
            $collection->setCreatedAt(new \DateTime("now"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();

            $data = $this->render('default/listitem.html.twig', ['list' => $collection])->getContent();
        }
        else
            $data = '';

        return new JsonResponse($data);
    }

    /**
     * @Route("/ajax/listDelete", name="list_delete")
     * @Method("POST")
     */
    public function listDeleteAction(Request $request)
    {
        $listId = $request->request->get('listid');

        if (!empty($listId))
        {
            $em = $this->getDoctrine()->getManager();
            $list = $em->getRepository('AppBundle:Collection')->find($listId);
            $em->remove($list);
            $em->flush();

            $data = array(
                'status' => true
            );
        }
        else
            $data = array(
                'status' => false
            );

        return new JsonResponse($data);
    }

    /**
    * @Route("/ajax/noteAdd", name="note_add")
    * @Method("POST")
    */
    public function noteAddAction(Request $request)
    {
        $collectionId = $request->request->get('collid');
        $text = $request->request->get('text');

        if (!empty($text) && !empty($collectionId))
        {
            $em = $this->getDoctrine()->getManager();
            $collection = $em->getRepository('AppBundle:Collection')->find($collectionId);
            $note = new Note();

            $note->setText($text);
            $note->setCollection($collection);
            $em->persist($note);
            $em->flush();

            $data = array(
                'status' => true,
                'html' => $this->render('default/noteitem.html.twig', ['note' => $note])->getContent()
            );
        }
        else
            $data = array(
                'status' => false
            );

        return new JsonResponse($data);
    }

    /**
     * @Route("/ajax/noteUpdate", name="note_update")
     * @Method("POST")
     */
    public function noteUpdateAction(Request $request)
    {
        $noteId = $request->request->get('noteid');
        $text = $request->request->get('text');

        if (!empty($text) && !empty($noteId))
        {
            $em = $this->getDoctrine()->getManager();
            $note = $em->getRepository('AppBundle:Note')->find($noteId);

            $note->setText($text);
            $em->persist($note);
            $em->flush();

            $data = array(
                'status' => true,
            );
        }
        else
            $data = array(
                'status' => false
            );

        return new JsonResponse($data);
    }

    /**
     * @Route("/ajax/noteDelete", name="note_delete")
     * @Method("POST")
     */
    public function noteDeleteAction(Request $request)
    {
        $noteId = $request->request->get('noteid');

        if (!empty($noteId))
        {
            $em = $this->getDoctrine()->getManager();
            $note = $em->getRepository('AppBundle:Note')->find($noteId);
            $em->remove($note);
            $em->flush();

            $data = array(
                'status' => true
            );
        }
        else
            $data = array(
                'status' => false
            );

        return new JsonResponse($data);
    }

}
