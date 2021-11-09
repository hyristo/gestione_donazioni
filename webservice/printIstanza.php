<?php

//include_once "../lib/api.php";
Utils::checkLogin();
require_once ROOT . 'lib/modules/pdfMerge/tcpdf/PDFMerger.php';
//echo ROOT . 'lib/modules/pdfMerge/tcpdf/PDFMerger.php';
$msg = "Accesso non autorizzato";
if (Utils::isStatoSportelloPresentazione()) {
    $id_domanda = $_POST["id"];
    $LoggedAccount->checkAuthShowDomandaPage($id_domanda);
    if ($id_domanda > 0) {

        $domanda = new Domanda($id_domanda);
        $filePath = File::getFolderFromDomanda($domanda, ROOT_UPLOAD_PRESENTAZIONE);
        $filePath = $filePath . "/domanda_generata/" . $domanda->FILEPATH;
        if (!DOWNLOAD_PDFMERGE && !isset($_POST['assign'])) {
            if (file_exists($filePath) && filesize($filePath) > 0) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . File::GetValidFilename($domanda->FILEPATH));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                ob_clean();
                flush();
                readfile($filePath);
                exit;
            } else {
                echo "Si prega di procedere alla generazione del file.";
            }
        } else if (!DOWNLOAD_PDFMERGE && isset($_POST['assign'])) {
            $filePathProtocollo = File::getFolderFromDomanda($domanda, ROOT_UPLOAD_PRESENTAZIONE);
            $filePathProtocollo = $filePathProtocollo . "/domanda_protocollo/" . $domanda->FILEPATH_PROTOCOLLO;
            if (file_exists($filePathProtocollo) && filesize($filePathProtocollo) > 0) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . File::GetValidFilename($domanda->FILEPATH_PROTOCOLLO));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePathProtocollo));
                ob_clean();
                flush();
                readfile($filePathProtocollo);
                exit;
            } else {
                echo "Si prega di procedere alla generazione del file.";
            }
        } else if (DOWNLOAD_PDFMERGE && !isset($_POST['assign']) && intval($domanda->NUMERO_PROTOCOLLO) <= 0) {
            if (file_exists($filePath) && filesize($filePath) > 0) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . File::GetValidFilename($domanda->FILEPATH));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                ob_clean();
                flush();
                readfile($filePath);
                exit;
            } else {
                echo "Si prega di procedere alla generazione del file.";
            }
        } else {
            $filePathProtocollo = File::getFolderFromDomanda($domanda, ROOT_UPLOAD_PRESENTAZIONE);
            $filePathProtocollo = $filePathProtocollo . "/domanda_protocollo/" . $domanda->FILEPATH_PROTOCOLLO;
            if (file_exists($filePath) && filesize($filePath) > 0 && file_exists($filePathProtocollo) && filesize($filePathProtocollo) > 0) {
                ob_end_clean();
                $pdf = new PDFMerger; // or use $pdf = new \PDFMerger; for Laravel
                $pdf->addPDF($filePath, 'all');
                $pdf->addPDF($filePathProtocollo, 'all');
                $pdf->merge('download', 'Istanza_'.$domanda->CODICE_FISCALE.'_'.$domanda->ID.'.pdf'); // force download
                exit;
            } else {

                echo "Si prega di procedere alla generazione del file.";
            }
        }
    }
}
