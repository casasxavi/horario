<?php

namespace App\Controller;

use App\Entity\Horario;
use App\Form\HorarioType;
use App\Repository\HorarioRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
/**
 * @Route("/horario")
 */
class HorarioController extends AbstractController
{

    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @Route("/", name="horario_index", methods={"GET"})
     */
    public function index(HorarioRepository $horarioRepository, PaginatorInterface $paginator, Request $request): Response
    {
//////////////////////////////////////////

        if ($request->query->get('mes')) {
            $mes = $request->query->get('mes');
            $anyo = $request->query->get('anyo');
        } else {
            $d = new \DateTime();
            $mes = $d->format('m');
            $anyo = $d->format('Y');
        }
///////////////////////////////////////////

        $saldoTotal = $this->getDoctrine()
            ->getRepository(Horario::class)
            ->dameSaldo($this->getUser()->getId(), $mes, $anyo);

        if ($saldoTotal[0]['saldo_Entrada'] == null) {

            $saldoMensual = "No hay saldo disponible para el mes seleccionado";
            $color_saldo = null;
        } else {

            $saldoEntrada = $saldoTotal[0]['saldo_Entrada'];
            $saldoSalida = $saldoTotal[0]['saldo_salida'];

            $trozo1 = explode(':', $saldoEntrada);
            $hora1 = $trozo1[0];
            $minuto1 = $trozo1[1];
            $segundos1 = $trozo1[2];
            $total1 = ($hora1 * 3600) + ($minuto1 * 60) + ($segundos1); //hora salida en segundos

            $trozo2 = explode(':', $saldoSalida);
            $hora2 = $trozo2[0];
            $minuto2 = $trozo2[1];
            $segundos2 = $trozo2[2];
            $total2 = ($hora2 * 3600) + ($minuto2 * 60) + ($segundos2); //hora entrada en segundos

            if ($total1 > $total2) {
                $segundos = ($total1) - ($total2);
            } else {
                $segundos = ($total2) - ($total1);
            }
            $minutos = $segundos / 60;
            $horas = floor($minutos / 60);
            $minutos2 = $minutos % 60;
            $segundos_2 = $segundos % 60 % 60 % 60;
            if ($minutos2 < 10) {
                $minutos2 = '0' . $minutos2;
            }
            if ($segundos_2 < 10) {
                $segundos_2 = '0' . $segundos_2;
            }
            if ($horas < 10) {
                $horas = '0' . $horas;
            }

            if ($segundos < 60) { /* segundos */
                $saldo = round($segundos) . ' Segundos';
            } elseif ($segundos > 60 && $segundos < 3600) { /* minutos */
                $saldo = $minutos2 . ':' . $segundos_2 . ' Minutos';
            } else { /* horas */
                $saldo = $horas . ':' . $minutos2 . ':' . $segundos_2 . ''; // el saldo formateado
            }

            if (($total1) >= ($total2)) {
                $color_saldo = "text-black";
                $saldoMensual = $saldo;
            } else {
                $color_saldo = "negativo";
                $saldoMensual = '-';
                $saldoMensual .= $saldo;
            }
        }
/////////////////////////////////////////////////////

        $query = $this->getDoctrine()
            ->getRepository(Horario::class)
            ->dameHorario($this->getUser()->getId(), $mes, $anyo);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10/*limit per page*/
        );

        return $this->render('horario/index.html.twig', [
            'horarios' => $pagination,
            'color_saldo' => $color_saldo,
            'saldoMensual' => $saldoMensual,
            'mes' => $mes,
            'anyo' => $anyo,
        ]);
    }

    /**
     * @Route("/new", name="horario_new", methods={"GET","POST"})
     */
    function new (Request $request): Response {

        $date = $this->fecha();

        $horario = new Horario();
        $form = $this->createForm(HorarioType::class, $horario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $horario->setUser($this->getUser());
            $horario->sethoraSaldo(new \DateTime($date));
            $entityManager->persist($horario);
            $entityManager->flush();

            return $this->redirectToRoute('horario_index');
        }

        return $this->render('horario/new.html.twig', [
            'horario' => $horario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="horario_show", methods={"GET"})
     */
    public function show(Horario $horario): Response
    {
        return $this->render('horario/show.html.twig', [
            'horario' => $horario,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="horario_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Horario $horario): Response
    {
        $form = $this->createForm(HorarioType::class, $horario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->session->getFlashBag()->add("edit", "El registro ha sido editado con exito");

            return $this->redirectToRoute('horario_index');
        }

        return $this->render('horario/edit.html.twig', [
            'horario' => $horario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="horario_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Horario $horario): Response
    {
        if ($this->isCsrfTokenValid('delete' . $horario->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($horario);
            $entityManager->flush();
        }
        $this->session->getFlashBag()->add("edit", "El registro ha sido editado con exito");
        return $this->redirectToRoute('horario_index');
    }

    public function fecha()
    {
        $fecha1 = date('Y-06-15');
        $fecha2 = date('Y-09-14');
        $date = date('Y-m-d');

        if (($date < $fecha1) || ($date > $fecha2)) {
            $date = '07:00:00';
        } else {
            $date = '06:30:00';
        }
        return $date;
    }

}
