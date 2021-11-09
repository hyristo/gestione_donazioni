<?php

$action = $_REQUEST["action"];

switch ($action) {
    case 'load':
        load();
        break;
    case "list":
        listRegistro();
        break;
    case "listgiacenze":
        listGiacenze();
        break;
    case "save":
        save();
        break;
    case "searchbotanic":
        $term = Utils::getFromReq("term", "");
        searchBotanic($term);
        break;
    case "deleteFile":
        deleteFileMovimento();
        break;
}

function deleteFileMovimento() {
    global $LoggedAccount;
    $file = $_POST['file'];
    $registro = new RegistroTracciabilita($_POST['id']);
    $registro->PATH_FILE = "";
    $response = $registro->Save();
    if ($response['esito'] == 1) {
        $folder = File::generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, $LoggedAccount->AziendaQiam['codi_fisc'] . "/registro_tracciabilita/");
        $filePath = $folder . "/" . $file;
        unlink($filePath);
    }
    exit(json_encode($response));
}

function load() {
    $id = Utils::getFromReq("id", 0);
    $record = new RegistroTracciabilita($id);
    exit(json_encode($record));
}

function listRegistro() {
    global $LoggedAccount;
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $CUAA = $_POST['cuaa'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
    $searchQuery .= " AND ( CUAA = :cuaa) ";
    $searchArray['CUAA'] = $CUAA;
    ## Search     
    if ($searchValue != '') {
        $searchQuery .= " AND ( lower(NOME_BOTANICO) LIKE :nome_botanico) ";
        $searchArray['NOME_BOTANICO'] = "%$searchValue%";
    }

    $res = RegistroTracciabilita::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    foreach ($res['empRecords'] as $row) {

        $fnAddMod = ' <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalCenter" onclick="editMovimento(\'' . $row['ID'] . '\', \'' . $row['CUAA'] . '\')" > <i class="fa fa-edit"></i> </button>';

//        if (trim($row['TIPO_OPERAZIONE']) == 'CARICO') {
//            $btnOperazione = '<a class="btn-floating mb-1 btn-flat waves-effect waves-light green darken-4 white-text" href="#"><i class="material-icons">arrow_upward</i></a>';
//        } elseif (trim($row['tipo_operazione']) == 'SCARICO') {
//            $btnOperazione = '<a class="btn-floating mb-1 btn-flat waves-effect waves-light red darken-4 white-text" href="#"><i class="material-icons">arrow_downward</i></a>';
//        }
//        if ($row['cancellato'] == 0) {
//            $onclickDisable = 'onclick="fitosanFramework.takeCharge(\'' . $row['id'] . '\',\'campioni\', \'delete\', \'#ListCampioni\' )"';
//            $colorBtnDisable = 'green';
//            $iconBtnDisable = 'cloud_done';
//        } else {
//            $onclickDisable = 'onclick="fitosanFramework.takeCharge(\'' . $row['id'] . '\',\'campioni\', \'riattiva\', \'#ListCampioni\' )"';
//            $colorBtnDisable = 'red';
//            $iconBtnDisable = "cloud_off";
//        }
//        $fnDisable = '<a href="#" ' . $onclickDisable . ' rel="' . RELUPDATE . '"  class="btn-floating mb-1 ' . $colorBtnDisable . ' waves-effect waves-light "><i class="material-icons dp48">' . $iconBtnDisable . '</i></a>';

        $data[] = array(
//            "id"=>$row['id'],            
            "id" => $row['ID'],
            "data_operazione" => Date::FormatDate($row['DATA_OPERAZIONE']), //Utils::FormatDate($row['DATA_OPERAZIONE'])
            "tipo_operazione" => ($row['TIPO_OPERAZIONE'] == CARICO ? "CARICO" : "SCARICO"),
            "prodotto" => "(" . $row['NOME_BOTANICO'] . ") " . $row['NOME_COMMERCIALE'],
            "modifica" => $fnAddMod
        );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $res['iTotalRecords'],
        "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
        "aaData" => $data
    );

    exit(json_encode($response));
}

function listGiacenze() {
    global $LoggedAccount;
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $partita_iva = $_POST['cuaa'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
    $searchQuery .= " AND ( CUAA = :cuaa) ";
    $searchArray['CUAA'] = $partita_iva;
    ## Search     
    if ($searchValue != '') {
        $searchQuery .= " AND ( lower(nome_botanico) LIKE :nome_botanico) ";
        $searchArray['nome_botanico'] = "%$searchValue%";
    }

    $res = vGiacenzeMaterialeForestale::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    foreach ($res['empRecords'] as $row) {
//        $btnOperazione = '';
//        if (trim($row['tipo_operazione']) == 'CARICO') {
//            $btnOperazione = '<a class="btn-floating mb-1 btn-flat waves-effect waves-light green darken-4 white-text" href="#"><i class="fas fa-arrow-up"></i></a>';
//        } elseif (trim($row['tipo_operazione']) == 'SCARICO') {
//            $btnOperazione = '<a class="btn-floating mb-1 btn-flat waves-effect waves-light red darken-4 white-text" href="#"><i class="fas fa-arrow-down"></i></a>';
//        }
        if ($row['GIACENZA'] < 0) {
            $colorGiacenza = 'danger';
            $iconGiacenza = '<i class="fas fa-arrow-down"></i>';
        } elseif ($row['GIACENZA'] == 0) {
            $colorGiacenza = 'warning';
            $iconGiacenza = '  <i class="fas fa-times"></i>';
        } elseif ($row['GIACENZA'] > 0) {
            $colorGiacenza = 'success';
            $iconGiacenza = ' <i class="fas fa-check"></i>';
        } else {
            $colorGiacenza = '';
            $iconGiacenza = '';
        }
        $btnGiacenza = '<a class="btn btn-outline-' . $colorGiacenza . ' white-text rounded-circle " href="#">' . $iconGiacenza . '</i></a>';
        $data[] = array(
            "specie_vegetale" => $row['SPECIE_VEGETALE'],
            "prodotto" => "(" . $row['NOME_BOTANICO'] . ") " . $row['NOME_COMMERCIALE'],
            "giacenza" => $btnGiacenza . " <b>" . $row['GIACENZA'] . "</b>",
            "unita_misura" => $row['UNITA_MISURA']
        );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $res['iTotalRecords'],
        "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
        "aaData" => $data
    );

    exit(json_encode($response));
}

function searchBotanic($term) {
    $tipologia = Utils::getFromReq('tipologia', false);
    session_write_close();
    $return = MaterialiForestali::autocomplete($term, $tipologia);
    exit(html_entity_decode(json_encode($return)));
}

function save() {
    global $con, $LoggedAccount;
    $response = array();
    $rec = new RegistroTracciabilita($_POST);
    $con->db_transactionStart();
    if (UPLOAD_ERR_NO_FILE != $_FILES['PATH_FILE']['error']) {
        $ext = pathinfo($_FILES['PATH_FILE']['name'], PATHINFO_EXTENSION);
        if ($ext != 'pdf') {
            $response['esito'] = "-999";
            $response['erroreDescrizione'] = "Al momento non è possibile procedere con l'operazione scelta.";
            exit(json_encode($response));
        }
        $transaction = false;
        $folder = File::generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, $LoggedAccount->AziendaQiam['codi_fisc'] . "/registro_tracciabilita/");
        if ($folder) {
            $fileName = round(microtime(true) * 1000) . "_" . File::GetValidFilename($_FILES['PATH_FILE']['name']);
            if (is_uploaded_file($_FILES['PATH_FILE']['tmp_name'])) {
                $fileFullPath = $folder . "/" . $fileName;
                if (copy($_FILES['PATH_FILE']['tmp_name'], $fileFullPath)) { //move_uploaded_file
                    $transaction = true;
                    $returnEsiti['erroreDescrizione'] = "";
                } else {
                    $returnEsiti['erroreDescrizione'] = "Al momento non è possibile allegare il file, contattare un amministratore! codice(1001.0)";
                }
            } else {
                $returnEsiti['erroreDescrizione'] = "Al momento non è possibile allegare il file, contattare un amministratore! codice errore (1002)";
            }
            $rec->PATH_FILE = $fileName;
        }
    } else {
        $transaction = true;
    }
    if ($rec->DATA_INSERIMENTO != "") {
        $rec->DATA_MODIFICA = date("Y-m-d");
        $rec->CF_ACCOUNT_MODIFICA = $LoggedAccount->CODICE_FISCALE;
    } else {
        $rec->CF_ACCOUNT_INSERIMENTO = $LoggedAccount->CODICE_FISCALE;
        $rec->DATA_INSERIMENTO = date("Y-m-d");
    }
    if ($transaction) {
        $con->db_transactionCommit();
        $response = $rec->Save();
        $response['cuaa'] = $rec->CUAA;
    } else {
        $con->db_transactionRollback();
        unlink($fileFullPath);
        $response['esito'] = "-999";
        $response['erroreDescrizione'] = "Al momento non è possibile procedere con l'operazione scelta.";
        $response['file'] = $returnEsiti['erroreDescrizione'];
    }
    exit(json_encode($response));
}
