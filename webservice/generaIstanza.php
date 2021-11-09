<?php

include '../lib/api.php'; // da commentare
set_time_limit(0);
ini_set('max_execution_time', 0);

$id_domanda = $_POST['domanda'];
$LoggedAccount->checkAuthShowDomandaPage($id_domanda);
//$domanda_completa = Domanda::loadDomandaFromAccount('MNDLCU98T68C933T');


$domanda = Domanda::loadDomandaCompleta($id_domanda);
$valutazione_individuale = $domanda['VALUTAZIONE_INDIVIDUALE'];
require_once ROOT . "lib/modules/tcpdf/lang/ita.php";
require_once ROOT . "lib/modules/tcpdf/tcpdf.php";
// inserire i controlli per il periodo del bando 
//$response = Utils::initDefaultResponse();
//if (Utils::isStatoSportelloPresentazione() && $domanda['STATO'] == DACOMPLETARE) {

/*
 * Assessorato Regionale Delle Autonomie Locali
  e Della Funzione Pubblica
  Dipartimento Regionale Della  Funzione
  Pubblica e Del Personale
 */

$htmlIntestazionePrimaPagina = "<h3>AUTOCERTIFICAZIONE</h3>";
$htmlHeaderPagina1 = <<<EOD
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
    <div style="text-align: right">
        <h2>Alla Regione Siciliana</h2><br>
        Assessorato Regionale Delle Autonomie Locali<br>
        e Della Funzione Pubblica<br>
        Dipartimento Regionale Della  Funzione<br>
        Pubblica e Del Personale<br>
    </div>
EOD;

// Tipo di documento : <b>$anagraficaUtente->SPID_TIPO_DOCUMENTO</b> <br>   
//   Cellulare :  <b> $anagraficaUtente->SPID_CELLULARE    </b> <br>
//   Documento :<b> $anagraficaUtente->SPID_DOCUMENTO     </b><br>
//   Ente di rilascio :<b> $anagraficaUtente->SPID_DOCUMENTO_ENTE </b><br>    
//   data scadenza : <b>$anagraficaUtente->SPID_DOCUMENTO_SCADENZA  </b><br>    
//   Email associata : <b>$anagraficaUtente->SPID_EMAIL  </b><br>

$domandaOggetto = new Domanda($id_domanda);
$ilsottoscritto = $domanda['COGNOME'] . " " . $domanda['NOME'];
$nato = $domanda['COMUNE_NASCITA'];
$ProvNascita = $domanda['PROV_NASCITA'];
$il = Date::FormatDate($domanda['DATA_NASCITA']);
//$il = Utils::($domanda['DATA_NASCITA']));
$CF = $domanda['CODICE_FISCALE'];
$residente = $domanda['COMUNE_RESIDENZA'];
$Prov = $domanda['PROV_RESIDENZA'];
$in_via = $domanda['INDIRIZZO_RESIDENZA'];
$sesso = intval((substr($CF, 9, 2)));
$descrizione_intervento = ''; //$domanda->DESCRIZIONE_INTERVENTO;
$txt_sottoscritto = ($sesso <= 31 ? 'Il sottoscritto ' : 'La sottoscritta ');
$txt_natoa = ($sesso <= 31 ? 'nato a ' : 'nata a ');

$anagraficaUtente = new AccountAnagrafica($domanda['CODICE_FISCALE']);
$personale = AnagraficaPersonale::load($LoggedAccount->Anagrafica->CODICE_FISCALE);
$DDG = TXT_DDG;
$capoverso = "";
$htmlPrimaPagina = <<<EOD
<div style="text-align: justify">
$capoverso $txt_sottoscritto <b>$ilsottoscritto</b>, $txt_natoa <b>$nato</b>, Prov. (<b>$ProvNascita</b>), il <b>$il </b> C.F. <b>$CF</b> residente in
    <b>$residente</b> in via <b>$in_via</b> recapito tel. Cell. <b>$anagraficaUtente->SPID_CELLULARE</b> recapito mail <b>$anagraficaUtente->SPID_EMAIL.</b>
    dipendente a tempo indeterminato da almeno 36 mesi alla data del 01/01/2019.
</div>
<h3 style="text-align: center">CHIEDE</h3>
<div style="text-align: justify">
l’attribuzione della Progressione Economica Orizzontale (PEO), prevista dall’art. 22 del CCRL del comparto non dirigenziale 2016/2018 pubblicato nella Supplemento ordinario della Gazzetta Ufficiale della Regione Siciliana (p. I) n. 24 del 24 maggio 2019 (n. 23), per quanto disciplinato dall’Accordo
tra l’ARAN Sicilia e le Organizzazioni sindacali del 27/12/2019 e per quanto disposto nel Bando DDG $DDG.
</div>
<div style="text-align:justify">
$capoverso A tal fine, preso atto dei punteggi attribuiti dall’Amministrazione secondo i criteri selettivi generali, ossia:
</div>
<br>
EOD;


//<br pagebreak="true"/> Per andare direttamente all'altra pagina




$ptposizione = $domandaOggetto->GetPuntiEsperienzaMaturata(ID_ANZIANITA_POSIZIONE);
$ptruolo = $domandaOggetto->GetPuntiEsperienzaMaturata(ID_ANZIANITA_REGIONE);


//$data_finePosizione = new DateTime($personale->ANZIANITA_POSIZIONE);
//$data_fineRuolo = new DateTime($personale->ANZIANITA_RUOLO);
////DATA_FINE_CALCOLO_ESPERIENZA
//$fine_calcolo = new DateTime(DATA_FINE_CALCOLO_ESPERIENZA);
//$fine_calcolo->add(new DateInterval('P1D'));
//
//$differenzaPosizone = $data_finePosizione->diff($fine_calcolo);
//$differenzaRuolo = $data_fineRuolo->diff($fine_calcolo);



$anni_ruolo = $personale->ANZIANITA_RUOLO_ANNI;
$mesi_ruolo = $personale->ANZIANITA_RUOLO_MESI;
$giorni_ruolo = $personale->ANZIANITA_RUOLO_GIORNI;
$anni_posizione = $personale->ANZIANITA_POSIZIONE_ANNI;
$mesi_posizione = $personale->ANZIANITA_POSIZIONE_MESI;
$giorni_posizione = $personale->ANZIANITA_POSIZIONE_GIORNI;

/* FIZ SULLA VISUALIZZAIONE NEL CASO DI num gg = 30 */
if ($giorni_ruolo == 30) {
    $giorni_ruolo = 0;
    $mesi_ruolo = ($mesi_ruolo + 1);
    if ($mesi_ruolo == 12) {
        $mesi_ruolo = 0;
        $anni_ruolo = ($anni_ruolo + 1);
    }
}

if ($giorni_posizione == 30) {
    $giorni_posizione = 0;
    $mesi_posizione = ($mesi_posizione + 1);
    if ($mesi_posizione == 12) {
        $mesi_posizione = 0;
        $giorni_posizione = ($giorni_posizione + 1);
    }
}

$htmlPuntoC = " <b>c.</b>";

$htmlTabellaInterruzioni = "";

$interruzioniLavorative = DomandaInterruzioni::load(0, $id_domanda);
if (count($interruzioniLavorative) > 0) {
    $htmlPuntoC .= " che il servizio di ruolo ha subito uno o più periodi di interruzione per:<br/>";
    $htmlTabellaInterruzioni = '<br><table nobr="true" cellpadding="2" border="1" cellspacing="0" style="width:100%">
    <tr>
        <td style="width:40%"><b>Tipo interruzione</b></td>
        <td style="width:15%"><b>Dal</b></td>
        <td  style="width:15%"><b>Al</b></td>
        <td  style="width:10%"><b>Anni</b></td>
        <td  style="width:10%"><b>Mesi</b></td>
        <td  style="width:10%"><b>Giorni</b></td>
    </tr>
    ';
    foreach ($interruzioniLavorative as $value) {
        $anagraficaInterruzione = new AnagraficaInterruzioni($value['ID_ANAG_INTERRUZIONI']);
        $htmlTabellaInterruzioni .= '
        <tr>
        <td>' . $anagraficaInterruzione->DESCRIZIONE . '</td>
        <td>' . Utils::normalizeDefaultValueTypeDate($value['DATA_INIZIO'], "d-m-Y") . '</td>
        <td>' . Utils::normalizeDefaultValueTypeDate($value['DATA_FINE'], "d-m-Y") . '</td>
        <td><b>' . $value['ANNI'] . '</b></td>
        <td><b>' . $value['MESI'] . '</b></td>
        <td><b>' . $value['GIORNI'] . '</b></td>
    </tr> ';
    }
    $htmlTabellaInterruzioni .= '</table>  <br><br>';
} else {
    $htmlPuntoC .= " che il periodo di servizio di ruolo non è stato interrotto da astensioni non retribuite o sospensioni dal servizio;";
}
$anaPosizione = new AnagraficaEsperienzeMaturate(ID_ANZIANITA_POSIZIONE);
$anaRuolo = new AnagraficaEsperienzeMaturate(ID_ANZIANITA_REGIONE);
$descPosizione = $anaPosizione->DESCRIZIONE;
$descRuolo = $anaRuolo->DESCRIZIONE;
$htmlEsperienzaProfessionaleMaturata = <<<EOD
<table nobr="true" cellpadding="2" cellspacing="0" style="width:100%">
    <tr>
        <td colspan="3">ESPERIENZA PROFESSIONALE MATURATA</td>
    </tr>
    <tr>
        <td border="1" style="width:60%">&nbsp;</td>
        <td border="1" style="width:10%"><b>Anni</b></td>
        <td border="1" style="width:10%"><b>Mesi</b></td>
        <td border="1" style="width:10%"><b>Giorni</b></td>
        <td border="1" style="width:10%"><b>Punti</b></td>
    </tr>      
    <tr>
        <td  border="1">$descPosizione</td>
        <td  border="1" ><b>$anni_posizione</b></td>
        <td  border="1" ><b>$mesi_posizione</b></td>
        <td  border="1" ><b>$giorni_posizione</b></td>
        <td  border="1" ><b>$ptposizione</b></td>
    </tr>
    <tr>
        <td  border="1" >$descRuolo</td>
        <td  border="1" ><b>$anni_ruolo</b></td>
        <td  border="1" ><b>$mesi_ruolo</b></td>
        <td  border="1" ><b>$giorni_ruolo</b></td>
        <td border="1" ><b>$ptruolo</b></td>
    </tr>
</table>
        <small>(*) La suddivisione dei periodi di anzianità di ruolo, di anzianità di posizione, in anni, mesi e giorni con i relativi punteggi, sono al netto di eventuali periodi di interruzione di servizio inseriti nel punto (c)</small>
<br/><br/>
EOD;

$htmlValutazioneIndividuale = '<table nobr="true" cellpadding="2" cellspacing="0" style="width:100%">
    <tr>
        <td colspan="3" >RISULTANZE DELLA VALUTAZIONE NEL TRIENNIO 2016/2018</td>
    </tr>
    <tr>
        <td border="1"><b>Anno</b></td>
        <td border="1"><b>valutazione</b></td>
        <td border="1"><b>Punti</b></td>
    </tr>';
$valutazioneTriennio = ValutazioneIndividuale::loadPunteggioPeriodo($domanda['CODICE_FISCALE']);
foreach ($valutazioneTriennio as $value) {
//    $htmlValutazioneIndividuale .= "<tr>"
//            . "<td border='1' colspan='4'><b>" . $value['ANNO'] . "</b></td>"
//            . "<td  border='1' colspan='4'><b>" . floatval($value['PUNTEGGIO']) . "</b></td>"
//            . "<td border='1' colspan='4' ><b>" . floatval($domandaOggetto->GetPuntiTriennio($value['ANNO'])) . "</b></td>"
//            . "</tr>";
    $htmlValutazioneIndividuale .= '
    <tr>
        <td border="1"><b>' . $value['ANNO'] . '</b></td>
        <td  border="1"><b>' . floatval($value['PUNTEGGIO']) . '</b></td>
        <td border="1"><b>' . floatval($domandaOggetto->GetPuntiTriennio($value['ANNO'])) . '</b></td>
    </tr> ';
}
$htmlValutazioneIndividuale .= '</table><br>
<div>ad integrazione degli stessi per il completamento del punteggio totale</div>';


$primaPagine = $htmlHeaderPagina1 . $htmlPrimaPagina . $htmlEsperienzaProfessionaleMaturata . $htmlValutazioneIndividuale;

$htmlPuntoA = "<b>a</b>. di essere consapevole che, in caso di mendaci dichiarazioni, il D.P.R. n. 445 del 28/12/2000 prevede sanzioni penali e decadenza dai benefici (artt. 76 e 75);<br/>";
$htmlPuntoB = "<b>b</b>. di essere informato/a, ai sensi dell'art. 13 del Regolamento UE 2016/679, che i dati forniti saranno trattati, anche con strumenti informatici, per l'emanazione dei consequenziali provvedimenti;<br/>";
$htmlPuntoD = '<b>d</b>. di avere la seguente anzianità non di ruolo avendo prestato servizio presso l’Amministrazione regionale nei seguenti periodi:';

$htmlparteDichiara = <<<EOD
<h3 style="text-align: center">DICHIARA</h3>
<div style="text-align: justify">
$htmlPuntoA $htmlPuntoB $htmlPuntoC  $htmlTabellaInterruzioni  $htmlPuntoD
</div><br />   
EOD;

$domanda_esperienza = DomandaEsperienze::load(0, $domanda['ID'], '', ID_ANZIANITA_NON_RUOLO);
$resTot_non_ruolo = DomandaEsperienze::GetTotalePeriodiTemporali($domanda['ID'], ID_ANZIANITA_NON_RUOLO);
$j = 0;
foreach ($domanda_esperienza as $value) {
    $j++;
    $data_inizio = Utils::normalizeDefaultValueTypeDate($value['DATA_INIZIO'], "d-m-Y");
    $data_fine = Utils::normalizeDefaultValueTypeDate($value['DATA_FINE'], "d-m-Y");
    //$first_date = new DateTime($data_inizio);
    //$second_date = new DateTime($data_fine);
    //$difference = $first_date->diff($second_date);
    $corpo .= "<tr>";
    $corpo .= "<td align='center'>" . $j . "</td>";
    $corpo .= "<td align='center'>" . $value['ENTE'] . "</td>";
    $corpo .= "<td align='center'>" . $data_inizio . "</td>";
    $corpo .= "<td align='center'>" . $data_fine . "</td>";
    $corpo .= "<td align='center'>" . $value['ANNI'] . "</td>";
    $corpo .= "<td align='center'>" . $value['MESI'] . "</td>";
    $corpo .= "<td align='center'>" . $value['GIORNI'] . "</td>";
    $corpo .= "</tr>";
}
if ($j == 0) {
    $nessunCorpo = "<b>Nessuna anzianità non di ruolo inserita</b>";
}

$htmlTabellaEsperienza = '
<div>
<table nobr="true" cellpadding="5" cellspacing="0" border="1" style="width:100%;">
<tr>
    <th style="width:5%;"><b>N.</b></th>
    <th style="width:45%;"><b>Assessorato/Dipartimento/Servizio/Ufficio</b></th>
    <th style="width:13%;"><b>Dal</b></th>
    <th style="width:13%;"><b>Al</b></th>
    <th style="width:8%;"><b>Anni</b></th>
    <th style="width:8%;"><b>Mesi</b></th>
    <th style="width:8%;"><b>Giorni</b></th>
  </tr>
        ' . $corpo . '
  <tr>
        <td colspan="4"></td>
        <td>' . $resTot_non_ruolo['ANNI'] . '</td>
        <td>' . $resTot_non_ruolo['MESI'] . '</td>
        <td>' . $resTot_non_ruolo['GIORNI'] . '</td>
  </tr>
</table> ' . $nessunCorpo . '
</div>';

$htmlC = <<<EOD
<div  style="text-align: justify">
<b>e</b>. di avere la seguente anzianità di ruolo, avendo prestato servizio presso la/le seguente/i  Pubblica Amministrazione nel relativo arco temporale:</div>    
EOD;
$domanda_esperienzaRuolo = DomandaEsperienze::load(0, $domanda['ID'], '', ID_ANZIANITA_DI_RUOLO);
$resTot_alre_pa = DomandaEsperienze::GetTotalePeriodiTemporali($domanda['ID'], ID_ANZIANITA_DI_RUOLO);
$i = 0;
foreach ($domanda_esperienzaRuolo as $value) {
    $i++;
    $data_inizio = Utils::normalizeDefaultValueTypeDate($value['DATA_INIZIO'], "d-m-Y");
    $data_fine = Utils::normalizeDefaultValueTypeDate($value['DATA_FINE'], "d-m-Y");
    //$first_date = new DateTime($data_inizio);
    //$second_date = new DateTime($data_fine);
    //$difference = $first_date->diff($second_date);
    $corpoRuolo .= "<tr>";
    $corpoRuolo .= "<td align='center'>" . $i . "</td>";
    $corpoRuolo .= "<td align='center'>" . $value['ENTE'] . "</td>";
    $corpoRuolo .= "<td align='center'>" . $value['SEDE'] . "</td>";
    $corpoRuolo .= "<td align='center'>" . $data_inizio . "</td>";
    $corpoRuolo .= "<td align='center'>" . $data_fine . "</td>";
    $corpoRuolo .= "<td align='center'>" . $value['ANNI'] . "</td>";
    $corpoRuolo .= "<td align='center'>" . $value['MESI'] . "</td>";
    $corpoRuolo .= "<td align='center'>" . $value['GIORNI'] . "</td>";
    $corpoRuolo .= "</tr>";
}
if ($i == 0) {
    $nessunCorpoRuolo = "<b>Nessuna anzianità di ruolo inserita</b>";
}
$htmlTabellaEsperienzaRuolo = '<div>
<table nobr="true" cellpadding="5" cellspacing="0" border="1" style="width:100%;">
<tr>   
    <th style="width:4%;"><b>N.</b></th>
    <th style="width:26%;"><b>Pubblica Amministrazione</b></th>
    <th style="width:20%;"><b>Sede di servizio</b></th>
    <th style="width:13%;"><b>Dal</b></th>
    <th style="width:13%;"><b>Al</b></th>
    <th style="width:8%;"><b>Anni</b></th>
    <th style="width:8%;"><b>Mesi</b></th>
    <th style="width:8%;"><b>Giorni</b></th>
  </tr>
        ' . $corpoRuolo . '
  <tr>
        <td colspan="5"></td>
        <td>' . $resTot_alre_pa['ANNI'] . '</td>
        <td>' . $resTot_alre_pa['MESI'] . '</td>
        <td>' . $resTot_alre_pa['GIORNI'] . '</td>
  </tr>
</table> ' . $nessunCorpoRuolo . '

</div>
';

$totTitoli = 0;
$domandaN = Domanda::loadDomandaFromAccount($domanda['CODICE_FISCALE']);
$titoli_studio = DomandaTitoliStudio::AllTitoliFromDomanda($id_domanda, ID_TIPO_TITOLISTUDIO, $personale->CATEGORIA, true);

$titoloStudiohtml = "<ul>";
foreach ($titoli_studio as $value) {


    $titoloStudiohtml .= "<li><b>" . $value['DESCRIZIONE'];
    ($value['DESCRIZIONE_TITOLO'] != "" ? $titoloStudiohtml .= "</b>, descrizione <b>" . $value['DESCRIZIONE_TITOLO'] : "");
    ($value['CONSEGUITO_PRESSO'] != "" ? $titoloStudiohtml .= "</b>, conseguito presso <b>" . $value['CONSEGUITO_PRESSO'] : "");
    ($value['CONSEGUITO_IN_DATA'] != "" ? $titoloStudiohtml .= "</b>,in data <b>" . Date::convertData($value['CONSEGUITO_IN_DATA']) : "");
    $titoloStudiohtml .= "</b></li>";
}
$titoloStudiohtml .= "</ul>";

$titoli_studioUlteriore = DomandaTitoliStudio::AllTitoliFromDomanda($id_domanda, ID_TIPO_ULTERIORITITOLI, $personale->CATEGORIA, true);
$titoli_studioUlteriorehtml = "<ul>";
foreach ($titoli_studioUlteriore as $value) {
    $titoli_studioUlteriorehtml .= "<li><b>" . $value['DESCRIZIONE'] . "</b>, descrizione <b>" . $value['DESCRIZIONE_TITOLO'] . "</b> conseguito presso <b>" . $value['CONSEGUITO_PRESSO'] . "</b> in data <b>" . Date::convertData($value['CONSEGUITO_IN_DATA']) . "</b></li>";
}
if (count($titoli_studioUlteriore) == 0) {
    $titoli_studioUlteriorehtml .= "<li><b>Nessun ulteriore titolo posseduto</b></li>";
}
$titoli_studioUlteriorehtml .= "</ul>";




$htmlD = "<b>f</b>. di essere in possesso dei seguenti titoli di studio: " . $titoloStudiohtml . ""
        . "<b>g</b>. di essere in possesso degli ulteriori titoli: " . $titoli_studioUlteriorehtml;




// inserire qui i successivi titolo di studio
$anag_sanzioni = AnagraficaSanzioni::getSanzioniGruopByCodice();
$domanda_sanzione = DomandaSanzioni::load($domanda['ID']);
$htmlSanzioni = "";
foreach ($domanda_sanzione as $value) {
    $anagrafica_sazione = AnagraficaSanzioni::load($value['ID_ANAG_SANZIONI']);
    $htmlSanzioni .= "<b>" . strtolower($anagrafica_sazione['CODICE']) . "</b>" . ". ";
    if ($value['NUMERO'] != 0 && $anagrafica_sazione['CODICE'] == "L") {
//        $htmlSanzioni .= $anagrafica_sazione['DESCRIZIONE'];
        $htmlSanzioni .= str_replace("di avere avuto irrogate ", " di avere avuto irrogate <b>N. " . $value['NUMERO'] . "</b> ", $anagrafica_sazione['DESCRIZIONE']);
    } else {
        $htmlSanzioni .= $anagrafica_sazione['DESCRIZIONE'];
    }
    $htmlSanzioni .= "<br><br>";
}
$urlHtml = BASE_HTTP . "privacy.php";
$htmlPresaVisione = <<<EOD
dichiara di aver preso visione dell’informativa sul trattamento dei dati ai sensi dell'art. 13 del Regolamento UE 2016/679
<a href="$urlHtml">Informativa Privacy</a>
EOD;
//exit();
//$htmlProtocollo = "
//<div>
//<b><h3>Protocollo n. " . $domandaOggetto->NUMERO_PROTOCOLLO . ", del " . $domandaOggetto->DATA_PROTOCOLLO . "</b></h3></div>
//        ";
//echo HTTP_PRIVATE_SECTION;
//exit();
$htmlDomanda = $primaPagine . $htmlHeader . $htmlSottoScritto . $htmlparteDichiara . $htmlTabellaEsperienza . $htmlC . $htmlTabellaEsperienzaRuolo . $htmlD . $htmlSanzioni . $htmlFirma . $htmlPresaVisione . $htmllinkPrivacy;
//echo ($htmlDomanda);
//exit();
//$fileUnoEstratto = $cartella . "/" . $nomeFile . ".pdf";

$nomeFilePdf = md5($domanda['ID']) . ".pdf";
ob_clean();
$pdf = new PDF_DOMANDA(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pageBreack = "";
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT + 3, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT + 3);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->SetFont('helvetica', '', 10);
$pdf->setCustomFooterText("N. istanza " . $domanda['ID'] . ", Data presentazione: " . $domanda['DATA_PRESENTAZIONE']);
$pdf->Footer();
$pdf->AddPage();
//$pdf->writeHTML($htmlDomanda, false, 0, true, 0);
$pdf->writeHTML($htmlDomanda);
if ($_POST['action'] == 'saveFile') {
    $domandaObj = new Domanda(intval($domanda['ID']));
    $response = Utils::initDefaultResponse();
    if (Utils::isStatoSportelloPresentazione() && $domandaObj->STATO == PRESENTATA) {

        $folder = File::getFolderFromDomanda($domandaObj, ROOT_UPLOAD_PRESENTAZIONE);
        $folder .= "/domanda_generata";
        $folder = File::createPath($folder);
        if (!empty($folder)) {
            $fileName = time() . "_ISTANZA_" . $domandaObj->ID . "_" . $domandaObj->CODICE_FISCALE . ".pdf";
            $filePath = $folder . "/" . $fileName;
            $nomefilePrefix = $pdf->Output($filePath, 'F');
            $hashfile = File::md5File($filePath);
            $domandaObj->HASHFILE = $hashfile;
            $domandaObj->FILEPATH = $fileName;
            if (file_exists($filePath) && !empty($hashfile)) {
                $response = $domandaObj->Save();
            }
        }
    } else {
        $response['erroreDescrizione'] = 'Operazione non consentita in questa fase.';
    }
} else {
    $pdf->Output($nomeFilePdf, 'I');
}
//}
exit(json_encode($response));
