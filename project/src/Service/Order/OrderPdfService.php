<?php
namespace App\Service\Order;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use App\Entity\Order;

class OrderPdfService
{
    public function __construct(private Environment $twig) {}

    public function generate(Order $order): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->twig->render('Back/order/pdf.html.twig', ['order' => $order]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice_order_' . $order->getId() . '.pdf"',
        ]);
    }
}
