<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Form\AgendaType;
use App\Repository\AgendaRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/agenda")
 */
class AgendaController extends AbstractController
{

    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @Route("/", name="agenda_index", methods={"GET"})
     */
    public function index(AgendaRepository $agendaRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Agenda::class);

        $query = $repository->findBy(['user' => $this->getUser()]);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            15/*limit per page*/
        );
        return $this->render('agenda/index.html.twig', [
            'agendas' => $pagination,
        ]);
        //dd($agendas);

    }
    /**
     * @Route("/new", name="agenda_new", methods={"GET","POST"})
     */
    function new (Request $request): Response {

        $agenda = new Agenda();
        $form = $this->createForm(AgendaType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $agenda->setUser($this->getUser());
            $entityManager->persist($agenda);
            $entityManager->flush();

            $this->session->getFlashBag()->add("guardar", "El registro ha sido creado con exito");

            return $this->redirectToRoute('agenda_index');
        }

        return $this->render('agenda/new.html.twig', [
            'agenda' => $agenda,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="agenda_show", methods={"GET"})
     */
    public function show(Agenda $agenda): Response
    {
        return $this->render('agenda/show.html.twig', [
            'agenda' => $agenda,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="agenda_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Agenda $agenda): Response
    {
        $form = $this->createForm(AgendaType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->session->getFlashBag()->add("edit", "El registro ha sido editado con exito");

            return $this->redirectToRoute('agenda_index');
        }

        return $this->render('agenda/edit.html.twig', [
            'agenda' => $agenda,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="agenda_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Agenda $agenda): Response
    {
        if ($this->isCsrfTokenValid('delete' . $agenda->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($agenda);
            $entityManager->flush();
        }
        $this->session->getFlashBag()->add("delete", "El registro ha sido eliminado con exito");
        return $this->redirectToRoute('agenda_index');
    }
}
