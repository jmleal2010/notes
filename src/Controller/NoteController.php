<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Tag;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/note")
 */
class NoteController extends AbstractController
{
    /**
     * @Route("/", name="app_note_index", methods={"GET"})
     */
    public function index(NoteRepository $noteRepository): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $noteRepository->getNotes($this->getUser()),
        ]);
    }

    /**
     * @Route("/new", name="app_note_new", methods={"GET", "POST"})
     */
    public function new(Request $request, NoteRepository $noteRepository): Response
    {
        $note = new Note();
        $note->setUser($this->getUser());
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $tags = $request->get('note')['tags'];
            foreach ($tags as $tag) {
                $newTag = new Tag();
                $newTag->setTitle($tag);
                $note->addTag($newTag);
            }
            $noteRepository->add($note);

            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/new.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
            'tags' => [],
        ]);
    }

    /**
     * @Route("/{id}", name="app_note_show", methods={"GET"})
     */
    /*  public function show(Request $request, Note $note): Response
      {
          return $this->render('note/show.html.twig', [
              'note' => $note,
          ]);
      }*/

    /**
     * @Route("/{id}/edit", name="app_note_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Note $note, NoteRepository $noteRepository): Response
    {
        if(!is_null($note->getDeletedAt()) || $note->getUser()->getId() !== $this->getUser()->getId())
            return new RedirectResponse($this->generateUrl('app_note_index'));

        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        $tags = [];

        foreach ($note->getTags() as $tag) {
            $tags[] = ['id' => $tag->getId(), 'title' => $tag->getTitle()];
        }
        if ($form->isSubmitted()) {
            $tags = $request->request->get('note')['tags'];

            foreach ($note->getTags() as $tag) {
                $note->removeTag($tag);
            }

            foreach ($tags as $tag) {
                $newTag = new Tag();
                $newTag->setTitle($tag);
                $note->addTag($newTag);
            }

                $noteRepository->add($note);


            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/{id}", name="app_note_delete",options={"expose"=true}, methods={"POST"})
     */
    public function delete(Request $request, Note $note, NoteRepository $noteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $noteRepository->remove($note);
        }

        return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/get_tags",name="get_tags",options={"expose"=true}, methods={"GET"})
     */
    public function getAllTags(): JsonResponse
    {
        $response = [];
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        foreach ($tags as $tag) {
            $response[] = ['id' => $tag->getId(), 'title' => $tag->getTitle()];
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/restore_note/{id}",name="app_restore_note",options={"expose"=true}, methods={"POST"})
     */
    public function restore(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('restore'.$note->getId(), $request->request->get('_token'))) {
            $note->setDeletedAt(null);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
    }
}
