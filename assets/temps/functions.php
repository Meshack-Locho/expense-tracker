<?php

function clean_input($data) {
    return trim($data);
}

function generateRandomString($length) {
        return substr(bin2hex(random_bytes($length)), 0, $length);
}

function displayErrors(array $errors): string
{
    if (empty($errors)) {
        return '';
    }

    $html = '<ul class="form-errors">';

    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error) . '</li>';
    }

    $html .= '</ul>';

    return $html;
}


function format_pretty_date($dateString) {
    $date = DateTime::createFromFormat('Y-m-d', $dateString);

 
    if (!$date) {
        return $dateString; 
    }

    return $date->format('d M Y');
}



function generateInvoicePDF($pdf, $invoiceRef)
{
    global $invoices;

    if (!preg_match('/^INV-\d{4}-\d{3}$/', $invoiceRef)) {
        add_audit_log('customer', $_SESSION['customer_id'], 'Document Download Failed', [
                "reason" => "Invalid invoice reference format."
        ]);
        http_response_code(400);
        exit('Invalid invoice reference format.');
    }

    $invoice = null;

    foreach ($invoices as $inv) {
        if (
            $inv['invoice_no'] === $invoiceRef &&
            $inv['customer_id'] === $_SESSION['customer_id']
        ) {
            $invoice = $inv;
            break;
        }
    }

    if (!$invoice) {
        add_audit_log('customer', $_SESSION['customer_id'], 'Document Download Failed', [
                "reason" => "Invoice was not found."
        ]);
        http_response_code(404);
        exit('Invoice not found.');
    }

    /* ===== HEADER ===== */

    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'INVOICE',0,1,'R');

    $pdf->SetFont('Arial','',12);
    $pdf->Cell(100,6,'Invoice No: ' . $invoice['invoice_no'],0,1);
    $pdf->Cell(100,6,'Invoice Date: ' . $invoice['invoice_date'],0,1);
    $pdf->Cell(100,6,'Due Date: ' . $invoice['due_date'],0,1);
    $pdf->Cell(100,6,'Status: ' . $invoice['status'],0,1);

    $pdf->Ln(8);

    /* ===== TABLE ===== */

    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(25,8,'SKU',1);
    $pdf->Cell(55,8,'Item',1);
    $pdf->Cell(20,8,'Unit',1);
    $pdf->Cell(25,8,'Qty',1,0,'R');
    $pdf->Cell(30,8,'Unit Price',1,0,'R');
    $pdf->Cell(35,8,'Total',1,1,'R');

    $pdf->SetFont('Arial','',10);

    foreach ($invoice['items'] as $item) {
        $pdf->Cell(25,8,$item['sku'],1);
        $pdf->Cell(55,8,$item['name'],1);
        $pdf->Cell(20,8,$item['unit'],1);
        $pdf->Cell(25,8,$item['quantity'],1,0,'R');
        $pdf->Cell(30,8,number_format($item['unit_price'],2),1,0,'R');
        $pdf->Cell(35,8,number_format($item['line_total'],2),1,1,'R');
    }

    /* ===== TOTALS ===== */

    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(155,8,'Total:',0,0,'R');
    $pdf->Cell(35,8,number_format($invoice['total'],2),1,1,'R');

    $vatAmt = (16 * $invoice['total'])/100;

    $pdf->Cell(155,8,'VAT:',0,0,'R');
    $pdf->Cell(35,8,number_format($vatAmt,2),1,1,'R');

    $pdf->Cell(155,8,'Total/Balance:',0,0,'R');
    $pdf->Cell(35,8,number_format(($invoice['balance']+$vatAmt),2),1,1,'R');



    $pdf->Output('D', $invoice['invoice_no'] . '.pdf');
    add_audit_log('customer', $_SESSION['customer_id'], 'Invoice Download Successful', [
        "invoice_no" => $invoice['invoice_no']
    ]);
    exit;
}

function recordExists(
    mysqli $db,
    string $table,
    string $type,
    string $column,
    mixed $value
): bool
{
    $sql =
        "
        SELECT 1
        FROM {$table}
        WHERE {$column}=?
        LIMIT 1
        ";

    $stmt =
        $db->prepare(
            $sql
        );

    $stmt->bind_param(
        "$type",
        $value
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    return
        $result->num_rows > 0;
}



?>