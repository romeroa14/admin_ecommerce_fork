<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function pdf(Invoice $invoice)
    {
        // Cargar las relaciones necesarias
        $invoice->load([
            'order.user',
            'order.items.product',
            'order.items.productVariant'
        ]);

        // Generar el PDF
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        // Configurar el PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        // Retornar el PDF para descarga
        return $pdf->download("factura-{$invoice->invoice_number}.pdf");
    }

    public function view(Invoice $invoice)
    {
        // Cargar las relaciones necesarias
        $invoice->load([
            'order.user',
            'order.items.product',
            'order.items.productVariant'
        ]);

        // Retornar la vista HTML para previsualización
        return view('invoices.pdf', compact('invoice'));
    }
}