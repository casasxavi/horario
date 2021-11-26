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

class PdfController extends AbstractController
{
    /**
     * @Route("/pdf", name="pdf")
     */
    public function index(HorarioRepository $horarioRepository, PaginatorInterface $paginator, Request $request): Response
    {


        // Configure Dompdf según sus necesidades
        $pdfOptions = new Options();
        $pdfOptions->set('isRemoteEnabled', true);
        //$pdfOptions->set('defaultFont', 'Arial');
        
        // Crea una instancia de Dompdf con nuestras opciones
        $dompdf = new Dompdf($pdfOptions); 
        
/////////////////////////////////////////////////////////////////////////////////////      


        if ($request->query->get('mes')) {
            $mes = $request->query->get('mes');
            $anyo = $request->query->get('anyo');
        } else {
            $d = new \DateTime();
            $mes = $d->format('m');
            $anyo = $d->format('Y');
        }

        $saldoTotal = $this->getDoctrine()
            ->getRepository(Horario::class)
            ->dameSaldo($this->getUser()->getId(), $mes, $anyo);

        if ($saldoTotal[0]['saldo_Entrada'] == null) {

            $saldoMensual = "No hay marcajes para el mes seleccionado";
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

        $query = $this->getDoctrine()
            ->getRepository(Horario::class)
            ->dameHorario($this->getUser()->getId(), $mes, $anyo);
//dd($query);
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            15/*limit per page*/
        );
///////////////////////////////////////////////////////////////////

        
        // Recupere el HTML generado en nuestro archivo twig
        $html = $this->renderView('pdf/index.html.twig', [
            'horarios' => $pagination,
            'color_saldo' => $color_saldo,
            'saldoMensual' => $saldoMensual,
        ]);
        
        // Cargar HTML en Dompdf
        $dompdf->loadHtml(utf8_decode($html));
        
        // (Opcional) Configure el tamaño del papel y la orientación 'vertical' o 'vertical'
        $dompdf->setPaper('A4', 'portrait');

        // Renderiza el HTML como PDF
        $dompdf->render();

        // Envíe el PDF generado al navegador (vista en línea)
        $dompdf->stream("index.pdf", [
            "Attachment" => false
        ]);

    }
}
