<?

set_time_limit(0);
include '../lib/api.php';
require_once ROOT . "lib/modules/tcpdf/lang/ita.php";
require_once ROOT . "lib/modules/tcpdf/tcpdf.php";
$id_domanda = $_POST['id'];
//$id_domanda = 81;
$LoggedAccount->checkAuthShowDomandaPage($id_domanda);
$domanda = Domanda::loadDomandaCompleta($id_domanda);
//exit();
//if ($domanda['ID'] > 0 && $LoggedAccount->checkIstitutoFromLoggedAccount($domanda['CODICE_MECCANOGRAFICO']) && $domanda['STATO'] >= PRESENTATA) { // controllo proprietario domanda
$response = Utils::initDefaultResponse();
$domandaObj = new Domanda($domanda['ID']);
if ($domandaObj->FILEPATH_PROTOCOLLO != "") {
    $response['esito'] = 1;
} else {
    $domanda_id = $domanda['ID'];
    $protocollo = $domanda['NUMERO_PROTOCOLLO'];
    $data_protoollo = $domanda['DATA_PROTOCOLLO'];
    $data_presentazione = $domanda['DATA_PRESENTAZIONE'];
    $nome = $domanda['NOME'];
    $cognome = $domanda['COGNOME'];
    $htmlDomanda = <<<EOD
            <style>
    .header-tb td{        
        border-top:1px solid #000000;
        border-bottom:1px solid #000000;
    }
    .all-tb td{
        padding: 5px;
        border:1px solid #000000;
    }
    ul li{
        margin-bottom: 1px;
    }
    </style>
    <div style="text-align: center">
        $img
    </div>
    <h2 style="text-align: center; color:#2E64FE">Ricevuta di avvenuta protocollazione</h2>
<p style="text-align: justify;"></p>    
        
          <table class="all-tb" width="100%" cellpadding="3">
        <tr><td width="40%" style="text-align: left">Nominativo</td><td width="60%"><b>$cognome $nome</b></td></tr>
        <tr><td width="40%" style="text-align: left">Identificativo Domanda</td><td width="60%"><b>$domanda_id</b></td></tr>
        <tr><td width="40%" style="text-align: left">Data presentazione </td><td width="60%"><b>$data_presentazione</b></td></tr>
        <tr><td colspan="2"><br><hr></td></tr>
        <tr><td width="40%" style="text-align: left">Numero di protocollo:</td><td width="60%"><b>$protocollo</b></td></tr>
        <tr><td width="40%" style="text-align: left">Data protocollo:</td><td width="60%"><b>$data_protoollo</b></td></tr>
        
        
EOD;
    $fileName = $domandaObj->ID . "_" . $domandaObj->CODICE_FISCALE . ".pdf";
    ob_clean();
    $pdf = new PDF_DOMANDA(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pageBreack = "";
    $pdf->SetCreator(PDF_CREATOR);
// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
// set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT + 3, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT + 3);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//set some language-dependent strings
    $pdf->setLanguageArray($l);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage();
    $pdf->writeHTML($htmlDomanda);
    if ($_POST['action'] == 'saveFile') {
        $domandaObj = new Domanda(intval($domanda['ID']));
        if (Utils::isStatoSportelloPresentazione() && $domandaObj->NUMERO_PROTOCOLLO != "") {
            $folder = File::getFolderFromDomanda($domandaObj, ROOT_UPLOAD_PRESENTAZIONE);
            $folder .= "/domanda_protocollo";
            $folder = File::createPath($folder);
            if (!empty($folder)) {
                $fileName = time() . "_PROTOCOLLO_" . $domandaObj->ID . "_" . $domandaObj->CODICE_FISCALE . ".pdf";
                $filePath = $folder . "/" . $fileName;
                $nomefilePrefix = $pdf->Output($filePath, 'F');
                $hashfile = File::md5File($filePath);
                $domandaObj->FILEPATH_PROTOCOLLO = $fileName;
                if (file_exists($filePath) && !empty($hashfile)) {
                    $response = $domandaObj->Save();
                }
            }
        } else {
            $response['erroreDescrizione'] = 'Operazione non consentita in questa fase.';
        }
    } else {
        $pdf->Output($fileName, 'D');
    }
}
exit(json_encode($response));


//$pdf->writeHTML($htmlDomanda, false, 0, true, 0);
//$nomefilePrefix = $pdf->Output($nomeFilePdf, 'D');
//} else {
//    echo "<script type='text/javascript'>window.close();</script>";
//}