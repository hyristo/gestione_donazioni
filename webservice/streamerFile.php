<?php

Utils::checkLogin();
switch ($_POST['action']) {
    case 'download':
        download();
        break;
}

function download() {
    global $LoggedAccount;
    $file = $_POST['value'];
    if ($_POST['tipo_download'] == 'reg') {
        $folder = File::generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, $LoggedAccount->AziendaQiam['codi_fisc'] . "/registro_tracciabilita/");
    } else {
        $folder = File::generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, $LoggedAccount->AziendaQiam['codi_fisc']);
    }
    $filePath = $folder . "/" . $file;
    if (file_exists($filePath) && filesize($filePath) > 0) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . File::GetValidFilename($file));
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
        $msg = "Non è possibile visualizzare il file richiesto, contattare il supporto tecnico (1316).";
    }
    echo $msg;
}

function printAllegato($allegato, $domanda) {
    global $LoggedAccount;
    if (!empty($allegato) && !empty($domanda)) {
        if ($allegato->FILEPATH != "") {
            $file = $allegato->FILEPATH;
            if ($LoggedAccount->checkIstitutoFromLoggedAccount($domanda->CODICE_ISTITUTO)) { // controllo proprietario domanda
                switch ($allegato->TIPO_DOCUMENTO) {
                    case TIPO_UPLOAD_OBBLIGATORIO:
                        $basePath = File::getFolderFromDomanda($domanda, ROOT_UPLOAD_PRESENTAZIONE);
                        $basePath .= '/obbligatori/';
                        break;
                    case TIPO_UPLOAD_OPZIONALE:
                        $basePath = File::getFolderFromDomanda($domanda, ROOT_UPLOAD_PRESENTAZIONE);
                        $basePath .= '/opzionali/';
                        break;
                    case TIPO_UPLOAD_ISTANZA:
                        $basePath = File::getFolderFromDomanda($domanda, ROOT_UPLOAD_PRESENTAZIONE);
                        $basePath .= '/istanza/';
                        break;
                    case TIPO_UPLOAD_CONTRIBUTO:
                        $basePath = File::getFolderFromDomanda($domanda, ROOT_UPLOAD_EROGAZIONE);
                        $basePath .= '/contributo/';
                        break;
                }
                $filePath = $basePath . $file;
                if (file_exists($filePath) && filesize($filePath) > 0) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . File::GetValidFilename($file));
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
                    $msg = "Non è possibile visualizzare il file richiesto, contattare il supporto tecnico (1316).";
                }
            } else {
                $msg = "Accesso non autorizzato";
            }
        } else {
            $msg = "Non è possibile visualizzare il file richiesto, contattare il supporto tecnico (1321).";
        }
    } else {
        $msg = "Non è possibile visualizzare il file richiesto, contattare il supporto tecnico (1325).";
    }

    echo $msg;
}
