<?php

$action = $_REQUEST["action"];

switch ($action) {
    case "searchComune":
        $term = Utils::getFromReq("term", "");
        searchComune($term);
    case 'searchFitosanitari':
        searchFitosanitari();
        break;
    case 'searchFertilizzanti':
        searchFertilizzanti();
        break;
    case 'load':
        load();
        break;
    case "lista_magazzini":
        lista_magazzini();
        break;
    case 'save':
        save();
        break;
    case 'deletelogical':
        deleteLogical();
        break;
    case "listGiacenze":
        listGiacenze();
        break;
    case "listMovimenti":
        listMovimenti();
        break;
    case "listFatture":
        listFatture();
        break;
    case "saveMovimento":
        saveMovimento();
        break;
    case "loadMovimento":
        loadMovimento();
        break;
    case "loadGiacenze":
        loadGiacenze();
        break;
    case "loadArticolo":
        loadArticolo();
        break;
    case "deleteFile":
        deleteFileMovimento();
        break;
}
exit();

function deleteFileMovimento() {
    global $LoggedAccount;
    $file = $_POST['file'];
    $movimento = new Movimento($_POST['id']);
    $movimento->PATH_FILE = "";
    $response = $movimento->Save();
    if ($response['esito'] == 1) {
        $folder = File::generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, $LoggedAccount->AziendaQiam['codi_fisc']);
        $filePath = $folder . "/" . $file;
        unlink($filePath);
    }
    exit(json_encode($response));
}

function searchComune($term) {
//    session_write_close();
    $return = AnagraficaComuni::autocomplete($term, CODICE_REGIONE_SICILIA);
    exit(html_entity_decode(json_encode($return)));
}

function searchFitosanitari() {
    session_write_close();
    $return = array();
    $term = Utils::getFromReq("searchTerm", "");
    $idMagazzino = Utils::getFromReq("id_magazzino", 0);
    if ($idMagazzino > 0) {
        $return = array();
        //$results = Magazzino::autocomplete_articoli($term, $idMagazzino);
        $results = Giacenze::autocomplete_articolo($term, $idMagazzino, 'ProdottiFitosanitari');
        foreach ($results as $res) {
            $res['id'] = $res['ID'];
            $res['text'] = $res['label'];
            $umobj = CodiciVari::Load($res['UNITA_MISURA'], "UNITA_MISURA");
            $res["unita_misura_a"] = $umobj['SIGLA'];
            $return[] = $res;
        }
    } else {
        $return = ProdottiFitosanitari::autocomplete($term);
    }
    exit(html_entity_decode(json_encode($return)));
}

function searchFertilizzanti() {
    session_write_close();
    $return = array();
    $term = Utils::getFromReq("searchTerm", "");
    $idMagazzino = Utils::getFromReq("id_magazzino", 0);
    if ($idMagazzino > 0) {
        $return = array();
        //$results = Magazzino::autocomplete_articoli($term, $idMagazzino);
        $results = Giacenze::autocomplete_articolo($term, $idMagazzino, 'ProdottiFertilizzanti');
        foreach ($results as $res) {
            $res['id'] = $res['ID'];
            $res['text'] = $res['label'];
            $umobj = CodiciVari::Load($res['UNITA_MISURA'], "UNITA_MISURA");
            $res["unita_misura_a"] = $umobj['SIGLA'];
            $return[] = $res;
        }
    } else {
        $return = ProdottiFertilizzanti::autocomplete($term);
    }
    exit(html_entity_decode(json_encode($return)));
}

function save() {
    global $LoggedAccount, $con;
    $result = Utils::initDefaultResponse();
    $filteredInput = Utils::requestDati($_POST);
    $magazzino = new Magazzino($filteredInput);
    if ($magazzino->DATA_CREAZIONE != "") {
        $magazzino->DATA_MODIFICA = date("Y-m-d");
        $magazzino->CF_ACCOUNT_MODIFICA = $LoggedAccount->CODICE_FISCALE;
    } else {
        $magazzino->DATA_CREAZIONE = date("Y-m-d");
        $magazzino->CF_ACCOUNT_INSERIMENTO = $LoggedAccount->CODICE_FISCALE;
    }
    $result = $magazzino->Save();
    exit(json_encode($result));
}

function load() {
    $magazzino = new Magazzino($_REQUEST['id']);
    exit(json_encode($magazzino));
}

function loadArticolo() {
    $id_articolo = Utils::getFromReq("id_articolo", 0);
    $tipo_articolo = Utils::getFromReq("tipo_articolo", "");
    $articolo = array();

    if ($tipo_articolo == "ProdottiFertilizzanti" && $id_articolo > 0) {
        $articolo = new ProdottiFertilizzanti($id_articolo);
    } else if ($tipo_articolo == "ProdottiFitosanitari" && $id_articolo > 0) {
        $articolo = new ProdottiFitosanitari($id_articolo);
    }

    //Utils::print_array($articolo);

    exit(json_encode($articolo));
}

function deleteLogical() {
    global $con, $LoggedAccount;
    $magazzino = new Magazzino($_REQUEST['id']);
    $result = $magazzino->LogicalDelete();
    exit(json_encode($result));
}

function lista_magazzini() {
    global $LoggedAccount;
    $CUA = $_POST['cuaa'];
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
    $searchQuery .= '  AND CUAA = :cuaa  AND "CANCELLATO"=0 ';
    $searchArray['CUAA'] = $CUA;
    if ($searchValue != '') {
        $searchQuery .= ' AND ( lower("NOME") LIKE :nome OR upper("NOME") LIKE :nome ) ';
        $searchArray = array(
            'NOME' => "%$searchValue%"
        );
    }
    $res = Magazzino::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    foreach ($res['empRecords'] as $row) {
        $buttonGestione = ' <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalCenter" onclick="editDati(' . $row['ID'] . ')" > <i class="fa fa-edit"></i> </button>'
                . ' <button type="button" class="btn btn-danger"  onclick="deleteDati(' . $row['ID'] . ')"  ><i class="fa fa-times"></i></button>';
        $data[] = array(
//            "nome" => $row['NOME'] . " Descrizione : " . $row['DESCRIZIONE'],
            "nome" => $row['NOME'],
            "particella" => $row['ID_ISOLA'],
            "sede" => $row['INDIRIZZO'] . " n° " . $row['CIVICO'] . ", nel comune di " . $row['COMUNE'],
            "gestione" => $buttonGestione
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
    $id_magazzino = Utils::getFromReq('id_magazzino', 0);
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnIndex = $columnIndex < 2 ? 2 : $columnIndex;
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    switch ($columnName) {
        case 'edit':
            $columnName = 'id';
            break;
        case 'um':
            $columnName = 'unita_misura';
            break;
    }
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    $searchArray = array();

    ## Search     
//    if($searchValue != ''){
//       $searchQuery = " AND ( lower(nome) LIKE :nome ) ";
//       $searchArray = array( 
//            'nome'=>"%$searchValue%"
//       );
//    }

    $searchQuery .= " AND ( ID_MAGAZZINO = :id_magazzino) ";
    $searchArray['ID_MAGAZZINO'] = $id_magazzino;

    $res = Giacenze::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    //exit();
    $data = array();
    foreach ($res['empRecords'] as $row) {
        $edit = '';
        $delete = '';
        $prodotto = new $row['TIPO_ARTICOLO']($row['ID_ARTICOLO']);
        if ($row['TIPO_ARTICOLO'] == 'ProdottiFitosanitari') {
            $articolo = $prodotto->PRODOTTO;
        } elseif ($row['TIPO_ARTICOLO'] == 'ProdottiFertilizzanti') {
            $articolo = $prodotto->NOME_COMMERCIALE;
        }
        $prodotto_str = $articolo;
        /*
          if ($LoggedAccount->id_gruppo == GRUPPO_UTENTE){
          $edit = '<a title="Genera Movimento Ingresso" style="float:left;margin-right:5px;" class="mb-6 btn-floating waves-effect waves-light btn-small amber darken-4" href="#" onclick="javascript:generaVoceMovimento(0, 1, \''.$prodotto_str.'\','.$row['id_prodotto'].')"><i title="INGRESSO" class="material-icons dp48 green">arrow_downward</i></a>';
          $delete = '<a title="Genera Movimento Uscita" style="float:left;margin-right:5px;" class="mb-6 btn-floating waves-effect waves-light btn-small amber darken-4" href="#" onclick="javascript:generaVoceMovimento(0, 2, \''.$prodotto_str.'\','.$row['id_prodotto'].')"><i title="USCITA" class="material-icons dp48 red">arrow_upward</i></a>';
          } */
        $um = CodiciVari::Load($row['UNITA_MISURA'], "UNITA_MISURA"); //, $GRUPPI_CODICIVARI['CAUSALI_MOVIMENTO']);
        if (intval($row['QUANTITA']) <= 0) {
            $qta = "<span class='red-text darken-4'>" . round($row['QUANTITA'], 2) . "</span>";
        } else {
            $qta = "<span>" . round($row['QUANTITA'], 2) . "</span>";
        }


        $data[] = array(
            "edit" => $edit . " " . $delete,
            "id" => $row['id'],
            "tipo" => $row['TIPO_ARTICOLO'],
            "articolo" => $prodotto_str,
            "quantita" => $qta,
            "um" => $um['DESCRIZIONE']
        );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $res['iTotalRecords'],
        "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
        "aaData" => $data
    );

    exit(html_entity_decode((json_encode($response))));
}

function listMovimenti() {
    global $LoggedAccount, $CARICO_DA_FATTURA, $RETTIFICA_CARICO, $RETTIFICA_SCARICO;
    $id_magazzino = Utils::getFromReq('id_magazzino', 0);
    $azienda = Utils::getFromReq('azienda', 0);
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    switch ($columnName) {
        case 'edit':
            $columnName = 'id';
            break;
        case 'causale':
            $columnName = 'id_causale';
            break;
        case 'articolo':
            $columnName = 'id_articolo';
            break;
        case 'data':
            $columnName = 'data_movimento';
            break;
        case 'um':
            $columnName = 'unita_misura';
            break;
        case 'numero_articoli':
            $columnName = 'id';
            break;
//        numero_articoli
    }
    $searchQuery = '';
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    $searchArray = array();
    ## Search     
//    if($searchValue != ''){
//       $searchQuery = " AND ( lower(articolo) LIKE :articolo OR lower(fattura) LIKE :fattura ) ";
//       $searchArray = array( 
//            'articolo'=>"%$searchValue%",
//            'fattura'=>"%$searchValue"
//       );
//    }
//    $field = "M.id, M.id_fattura, M.tipo, M.data_movimento, M.id_causale, M.descrizione, V.id_articolo, V.quantita, V.unita_misura, V.prezzo, V.costo";
//    $DBtable = Movimento::TABLE_NAME . " M LEFT JOIN " . MovimentoVoce::TABLE_NAME . " V ON V.id_movimento = M.id";
    //$searchQuery = " AND cancellato = 0 AND id_magazzino = '" . $id_magazzino . "'";

    $searchQuery .= " AND ID_MAGAZZINO = :id_magazzino AND CANCELLATO = :cancellato ";
    $searchArray['ID_MAGAZZINO'] = $id_magazzino;
    $searchArray['CANCELLATO'] = 0;

//    $res = Movimento::LoadDataTableEx($field, $DBtable, $searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    $res = Movimento::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    $data = array();
    foreach ($res['empRecords'] as $row) {

        $edit = '<button class="btn btn-sm btn-warning" onclick="goToMovimento(\'movimento_magazzino\', \'' . $azienda . '\', \'' . $row['TIPO'] . '\', \'' . $row['ID'] . '\');" title="Modifica movimento"><i class="far fa-edit"></i></button>'; //goToMovimento('movimento_magazzino', '', 'TIPO_CARICO', 'id_mov');
        $delete = '';
        $download = '';
        $um = '';
        $causale = '';


        if ($row['UNITA_MISURA'] > 0) {
            $umobj = CodiciVari::Load($row['UNITA_MISURA'], 'UNITA_MISURA');
            $um = $umobj['DESCRIZIONE'];
        }
        //echo "<pre>".print_r($causaleObj, true)."</pre>";
        $prodotto = new $row['TIPO_ARTICOLO']($row['ID_ARTICOLO']);
        if ($row['TIPO_ARTICOLO'] == 'ProdottiFitosanitari') {
            $articolo = $prodotto->PRODOTTO;
        } elseif ($row['TIPO_ARTICOLO'] == 'ProdottiFertilizzanti') {
            $articolo = $prodotto->NOME_COMMERCIALE;
        }
        
        if($row['DESTINAZIONE_USO']>0){
            $dusobj = CodiciVari::Load($row['DESTINAZIONE_USO'], 'DESTINAZIONE_USO');
            $duso = $dusobj['DESCRIZIONE'];
        }

        //Utils::print_array($prodotto);

        $data[] = array(
            "id" => $row['ID'],
            "tipo_articolo" => $row['TIPO_ARTICOLO'],
            "causale" => $row['TIPO'] == TIPO_CARICO ? '<i style="color:green" class="fas fa-upload"></i>&nbsp;CARICO' : '<i style="color:red" class="fas fa-download"></i>&nbsp;SCARICO',
            "qta" => $row['QUANTITA'] . " " . $um,
            "data_movimento" => date('d-m-Y', strtotime($row['DATA_MOVIMENTO'])),
            "articolo" => $articolo,
            "uso" => $duso,
            "edit" => $edit . " " . $download . " " . $delete
        );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $res['iTotalRecords'],
        "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
        "aaData" => $data
    );
    exit(html_entity_decode((json_encode($response))));
}

function listFatture() {
    global $LoggedAccount;
    $id_magazzino = Utils::getFromReq('id_magazzino', 0);
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnIndex = ($columnIndex < 1 ? 1 : $columnIndex);
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    switch ($columnName) {
        case 'data':
            $columnName = 'data_fattura';
            break;
    }
    $searchArray = array();
    ## Search     
    if ($searchValue != '') {
        $searchQuery = " AND ( LOWER(NUMERO) LIKE :numero ) ";
        $searchArray = array(
            'NUMERO' => "%$searchValue%"
        );
    }


    $searchQuery .= " AND ID_MAGAZZINO = :id_magazzino AND CANCELLATO = :cancellato ";
    $searchArray['ID_MAGAZZINO'] = $id_magazzino;
    $searchArray['CANCELLATO'] = 0;

    $res = Fattura::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    $data = array();
    foreach ($res['empRecords'] as $row) {
        $newMovimento = '';
        $edit = '';
        $download = '';
        $storico = '';
        $delete = '';
//        if ($LoggedAccount->id_gruppo == GRUPPO_UTENTE){
////            $newMovimento = '<a title="Genera movimento" style="float:left;margin-right:5px;" class="mb-6 btn-floating waves-effect waves-light btn-small lime darken-1" href="#" onclick="javascript:generaMovimento('.$row['id'].')"><i class = "material-icons dp48">add</i></a>';
//            $edit = '<a title="Edit Fattura" style="float:left;margin-right:5px;" class="mb-6 btn-floating waves-effect waves-light btn-small amber darken-4" href="#" onclick="javascript:editFattura('.$row['id'].')"><i class = "material-icons dp48">mode_edit</i></a>';
//            $download = '<a title="Download Fattura" style="float:left;margin-right:5px;" class="mb-6 btn-floating waves-effect waves-light btn-small green darken-4" href="'.BASE_HTTP.'/upload/'.$row['filepath'].'" target="_blank"><i class = "material-icons dp48">cloud_download</i></a>';
////            $delete = '<a title="Elimina Fattura" style="float:left;margin-right:5px;" class="mb-6 btn-floating waves-effect waves-light btn-small red darken-4" href="#" onclick="javascript:deleteFattura('.$row['id'].')"><i class = "material-icons dp48">delete_forever</i></a>';
//        }
        $delete = '<a title="Visualizza articoli fattura" style="float:left;margin-right:5px;" class="mb-6 btn-floating waves-effect waves-light btn-small red darken-4" href="#" onclick="javascript:movimentiFattura(\'' . $row['ID'] . '\',\'' . $row['NUMERO'] . '\')"><i class = "material-icons dp48">format_list_bulleted</i></a>';

        $data[] = array(
            "edit" => $newMovimento . " " . $edit . " " . $download . " " . $delete,
            "id" => $row['ID'],
            "numero" => $row['NUMERO'],
            "data" => date('d-m-Y H:i:s', strtotime($row['DATA_FATTURA'])),
            "note" => $row['NOTE']
        );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $res['iTotalRecords'],
        "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
        "aaData" => $data
    );
    exit(html_entity_decode((json_encode($response))));
}

function loadMovimento() {
    $response = array();
    $movimento = new Movimento($_REQUEST['id']);
    $response = $movimento;
    if ($response->UNITA_MISURA > 0) {
        $umobj = CodiciVari::Load($response->UNITA_MISURA, 'UNITA_MISURA');
        //Utils::print_array($umobj);
        $response->UNITA_MISURA_A = $umobj['DESCRIZIONE'];
    }
    if ($response->TIPO_ARTICOLO != "") {
        $prodotto = new $response->TIPO_ARTICOLO($response->ID_ARTICOLO);
        $response->ARTICOLO_OBJ = $prodotto;
    }

    if ($response->TIPO_ARTICOLO == 'ProdottiFitosanitari') {
        $response->ARTICOLO_TXT = $prodotto->PRODOTTO;
    } elseif ($response->TIPO_ARTICOLO == 'ProdottiFertilizzanti') {
        $response->ARTICOLO_TXT = $prodotto->NOME_COMMERCIALE;
    }

    exit(json_encode($response));
}

function saveMovimento() {
    global $con, $LoggedAccount;
    $response = Utils::initDefaultResponse();
    $returnEsiti = Utils::initDefaultResponse();
    $risposta = Utils::initDefaultResponse();
    $con->db_transactionStart();
    $movimento = new Movimento($_POST);
    if (UPLOAD_ERR_NO_FILE != $_FILES['PATH_FILE']['error']) {
        $ext = pathinfo($_FILES['PATH_FILE']['name'], PATHINFO_EXTENSION);
        if ($ext != 'pdf') {
            $response['esito'] = "-999";
            $response['erroreDescrizione'] = "Al momento non è possibile procedere con l'operazione scelta.";
            exit(json_encode($response));
        }
        $transaction = false;
        $folder = File::generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, $LoggedAccount->AziendaQiam['codi_fisc']);
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
            $movimento->PATH_FILE = $fileName;
        }
    } else {
        $transaction = true;
    }
    $saveMovimento = false;
    if ($movimento->TIPO == TIPO_SCARICO) {
        $giacenza = Giacenze::Load($movimento->ID_MAGAZZINO, $movimento->ID_ARTICOLO, $movimento->TIPO_ARTICOLO);
        if ($giacenza[0]['QUANTITA'] <= 0) {
            $response['erroreDescrizione'] = 'In magazzino non risultano giacenze per questo articolo.';
        } elseif ($giacenza[0]['QUANTITA'] < $movimento->QUANTITA) {
            $response['erroreDescrizione'] = 'La quantità di gacienza in magazzino non è sufficiente per consentire la quantità del movimento di scarico.';
        } else {
            $saveMovimento = true;
        }
    } else {
        $saveMovimento = true;
    }
    if ($saveMovimento) {
        $response = $movimento->Save();
    }
    if ($transaction && $saveMovimento) {
        $con->db_transactionCommit();
        $risposta = $response;
    } else {
        $con->db_transactionRollback();
        unlink($fileFullPath);
        $risposta['esito'] = "-999";
        $risposta['erroreDescrizione'] = "Al momento non è possibile procedere con l'operazione scelta.";
        $risposta['file'] = $returnEsiti['erroreDescrizione'];
    }
    exit(html_entity_decode((json_encode($risposta))));
}

function loadGiacenze() {
    $response = array();
    $id_magazzino = intval($_REQUEST['id_magazzino']);
    $id_articolo = intval($_REQUEST['id_articolo']);
    $tipo_articolo = trim($_REQUEST['tipo_articolo']);

    $response = Giacenze::Load($id_magazzino, $id_articolo, $tipo_articolo);

    //Utils::print_array($response);
//    $movimento = new Movimento($_REQUEST['id']);
//    $response = $movimento;
    if ($response[0]['UNITA_MISURA'] > 0) {
        $umobj = CodiciVari::Load($response[0]['UNITA_MISURA'], 'UNITA_MISURA');
        //Utils::print_array($umobj);
        $response[0]['UNITA_MISURA_A'] = $umobj['DESCRIZIONE'];
    }
//    if($response->TIPO_ARTICOLO!=""){
//        $prodotto = new $response->TIPO_ARTICOLO($response->ID_ARTICOLO);
//        $response->ARTICOLO_OBJ = $prodotto;
//    }
//    
//    if($response->TIPO_ARTICOLO == 'ProdottiFitosanitari'){
//        $response->ARTICOLO_TXT = $prodotto->PRODOTTO;
//    }elseif($response->TIPO_ARTICOLO == 'ProdottiFertilizzanti'){
//        $response->ARTICOLO_TXT = $prodotto->NOME_COMMERCIALE;
//    }

    exit(json_encode($response));
}
