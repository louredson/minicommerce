<?php

namespace App\Core;

class PdfSimple
{
    public static function ordersReport(array $orders): string
    {
        $lines = [];
        $lines[] = 'Relatorio de Compras - Buyprime';
        $lines[] = 'Gerado em: ' . date('Y-m-d H:i:s');
        $lines[] = str_repeat('-', 120);
        $lines[] = sprintf('%-6s %-26s %-20s %-14s %12s', 'ID', 'CLIENTE', 'DATA', 'ESTADO', 'TOTAL (Kz)');
        $lines[] = str_repeat('-', 120);

        if (!$orders) {
            $lines[] = 'Sem compras no periodo selecionado.';
        } else {
            foreach ($orders as $o) {
                $date = isset($o['created_at']) ? date('Y-m-d H:i', strtotime((string) $o['created_at'])) : '-';
                $customer = mb_substr((string)($o['customer_name'] ?? '-'), 0, 26);
                $status = mb_substr((string)($o['status'] ?? '-'), 0, 14);
                $lines[] = sprintf(
                    '%-6s %-26s %-20s %-14s %12.2f',
                    '#' . (int)$o['id'],
                    $customer,
                    $date,
                    $status,
                    (float)$o['total_amount']
                );
            }
        }

        $content = "BT /F1 9 Tf 40 800 Td 12 TL ";
        $first = true;
        foreach ($lines as $line) {
            $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
            if (!$first) {
                $content .= 'T* ';
            }
            $content .= '(' . $safe . ') Tj ';
            $first = false;
        }
        $content .= 'ET';

        $objs = [];
        $objs[] = '1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj';
        $objs[] = '2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj';
        $objs[] = '3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>endobj';
        $objs[] = '4 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj';
        $objs[] = '5 0 obj<< /Length ' . strlen($content) . ' >>stream' . "\n" . $content . "\nendstream\nendobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj . "\n";
        }

        $xref = strlen($pdf);
        $pdf .= 'xref' . "\n" . '0 ' . (count($objs) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf('%010d 00000 n ', $off) . "\n";
        }
        $pdf .= 'trailer<< /Size ' . (count($objs) + 1) . ' /Root 1 0 R >>' . "\n";
        $pdf .= 'startxref' . "\n" . $xref . "\n%%EOF";

        return $pdf;
    }
}
