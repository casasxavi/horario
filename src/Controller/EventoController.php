<?php

namespace App\Controller;

use App\Entity\Evento;
use App\Form\EventoType;
use App\Repository\EventoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evento")
 */
class EventoController extends AbstractController
{

    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @Route("/", name="evento_index", methods={"GET"})
     */
    public function index(EventoRepository $eventoRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $date = new \DateTime();
        $fecha_actual = $date->format('d-m-Y');

        if ($request->query->get('mes')) {
            $mes = $request->query->get('mes');
            $anyo = $request->query->get('anyo');
        } else {
            $mes = null;
            $anyo = null;
        }

        $query = $this->getDoctrine()
            ->getRepository(Evento::class)
            ->listarEventos($this->getUser()->getId(), $mes, $anyo);

        $eventos = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            15/*limit per page*/
        );

        return $this->render('evento/index.html.twig', [
            'eventos' => $eventos,
            'fecha_actual' => $fecha_actual
        ]);
    }

    /**
     * @Route("/new", name="evento_new", methods={"GET","POST"})
     */
    function new (Request $request): Response {
        $evento = new Evento();
        $form = $this->createForm(EventoType::class, $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $evento->setUser($this->getUser());
            $entityManager->persist($evento);
            $entityManager->flush();

            $this->session->getFlashBag()->add("guardar", "El evento ha sido creado con exito");

            return $this->redirectToRoute('evento_index');
        }

        return $this->render('evento/new.html.twig', [
            'evento' => $evento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evento_show", methods={"GET"})
     */
    public function show(Evento $evento): Response
    {
        return $this->render('evento/show.html.twig', [
            'evento' => $evento,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evento_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Evento $evento): Response
    {
        $form = $this->createForm(EventoType::class, $evento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->session->getFlashBag()->add("edit", "El evento ha sido editado con exito");

            return $this->redirectToRoute('evento_index');
        }

        return $this->render('evento/edit.html.twig', [
            'evento' => $evento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evento_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Evento $evento): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evento->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($evento);
            $entityManager->flush();
        }
        $this->session->getFlashBag()->add("delete", "El evento ha sido eliminado con exito");
        return $this->redirectToRoute('evento_index');
    }
}
