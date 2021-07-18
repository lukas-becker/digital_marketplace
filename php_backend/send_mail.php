<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php_backend/dompdf/autoload.inc.php');

// reference the Dompdf namespace
use Dompdf\Dompdf;

function send_email($address, $subject, $title_primary, $title_secondary, $headline, $body, $link, $link_target)
{

    //Send  E-Mail
    include($_SERVER['DOCUMENT_ROOT'] . '/php_backend/mail_template.php');
    $from = 'noreply@marketplace.de';
    $fromName = 'Marketplace';

    $mail_subject = "=?utf-8?b?" . base64_encode($subject) . "?=";
    $mail_html = str_replace("%r_title_primary_%r", $title_primary, $mailContent);
    $mail_html = str_replace("%r_title_secondary_%r", $title_secondary, $mail_html);
    $mail_html = str_replace("%r_headline_%r", $headline, $mail_html);
    $mail_html = str_replace("%r_content_%r", $body, $mail_html);
    $mail_html = str_replace("%r_link_%r", $link, $mail_html);
    $mail_html = str_replace("%r_link_target_%r", $link_target, $mail_html);
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . $fromName . '<' . $from . '>' . "\r\n";

    //The demo server is not configured to send Mails
    //Thus all mails will be stopped and saved to pdf and html
    //return mail($address, $mail_subject, $mail_html, $headers);


    // instantiate and use the dompdf class
    $dompdf = new Dompdf();
    $dompdf->loadHtml($mail_html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to local Storage
    $output = $dompdf->output();
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/saved_mails/' . $address . '_' . $subject . '_' . date("d.m.Y") . '.pdf', $output);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/saved_mails/' . $address . '_' . $subject . '_' . date("d.m.Y") . '.html', $mail_html);
}

?>