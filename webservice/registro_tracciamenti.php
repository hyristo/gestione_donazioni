<?php

$action = $_REQUEST["action"];

switch ($action) {
    case 'load':
        load();
        break;
    case "list":
        listSemine();
        break;
    case "listgiacenze":
        listGiacenze();
        break;
    case "save":
        save();
        break;
//    case "searchbotanic":
//        $term = Utils::getFromReq("term", "");
//        searchBotanic($term);
//        break;
    case "searchFitosanitari":
        searchFitosanitari();
        break;
    case "searchFertilizzanti":
        searchFertilizzanti();
        break;
    case "listSelect":
        listSelect();
        break;
    case "listAppezzamenti":
        listAppezzamenti();
        break;
    case "deleteLogical":
        deleteLogical();
        break;
}

function deleteLogical() {
    $qdc = new QuadernodiCampagna($_REQUEST['id']);
    $res = $qdc->LogicalDelete();
    exit(json_encode($res));
}

function listAppezzamenti() {
    global $LoggedAccount;
    $prg_scheda = $_POST['prg_scheda'];
    $id_appe = array();
    $geo = array();
    if($LoggedAccount->checkAuthorized()){
        $qdc_voci = new QuadernodiCampagnaVoci();
        $res = $qdc_voci->Load($_REQUEST['id']);
        foreach ($res as $key => $value) {
            $id_appe[$key] = $value['ID_APPEZZAMENTO'];
        }
        $geo = Appezzamento::loadGeoJESONByTrattamenti($id_appe,$prg_scheda);
    }
    
    //echo Utils::print_array($geo);
    exit(json_encode($geo));
}

function searchFitosanitari() {
    $return = array();
    $term = Utils::getFromReq("searchTerm", "");
    $idMagazzino = Utils::getFromReq("id_magazzino", 0);
    if ($idMagazzino > 0) {
        $return = array();
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
    $return = array();
    $term = Utils::getFromReq("searchTerm", "");
    $idMagazzino = Utils::getFromReq("id_magazzino", 0);
    if ($idMagazzino > 0) {
        $return = array();
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

function listSelect() {
    $appezzamento = new Appezzamento();
    $valore = $appezzamento->getAppezzamentoPerTipo($_REQUEST['specie'], $_REQUEST['prg_scheda']);
    $valore['point'] = Appezzamento::loadGeoJESONByIsola(0, $_REQUEST['specie'], $_REQUEST['prg_scheda']);
    
    exit(json_encode($valore));
}

function load() {
    $id = Utils::getFromReq("id", 0);
    $record = new QuadernodiCampagna($id);
    $tipo_i = CodiciVari::Load($record->TIPO_INTERVENTO,'TIPO_INTERVENTO');
    
    if ($record->TIPO_INTERVENTO == NUTRIZIONE) {
        $record->TIPO_INTERVENTO = $tipo_i['DESCRIZIONE'];
        $prodotto = new ProdottiFertilizzanti($record->ID_PRODOTTO_FITOSANITARIO);
        $record->ID_PRODOTTO_FITOSANITARIO = $prodotto->NOME_COMMERCIALE;
    } else if ($record->TIPO_INTERVENTO == DIFESA) {
        $record->TIPO_INTERVENTO = $tipo_i['DESCRIZIONE'];
        $prodotto = new ProdottiFitosanitari($record->ID_PRODOTTO_FITOSANITARIO);
        $record->ID_PRODOTTO_FITOSANITARIO = $prodotto->PRODOTTO;
    } else if ($record->TIPO_INTERVENTO == IRRIGAZIONE) {
        $record->TIPO_INTERVENTO = $tipo_i['DESCRIZIONE'];
    } else if ($record->TIPO_INTERVENTO == OPERAZIONE) {
        $record->TIPO_INTERVENTO = $tipo_i['DESCRIZIONE'];
    } else if ($record->TIPO_INTERVENTO == RACCOLTA) {
        $record->TIPO_INTERVENTO = $tipo_i['DESCRIZIONE'];
    }
    if($record->UNITA_MISURA > 0){
        $qta = CodiciVari::Load($record->UNITA_MISURA, 'UNITA_MISURA');
        $record->UNITA_MISURA = $qta['DESCRIZIONE'];
    }
    $record->DATA_INTERVENTO = Date::FormatDate($record->DATA_INTERVENTO, DATE_FORMAT_ITA);
    $record->DATA_INTERVENTO_START = Date::FormatDate($record->DATA_INTERVENTO_START, DATE_FORMAT_ITA);
    $record->DATA_INTERVENTO_END = Date::FormatDate($record->DATA_INTERVENTO_END, DATE_FORMAT_ITA);
    //$stadioFenologico = CodiciVari::Load($record->STADIO_FENOLOGICO, 'STADIO_FENOLOGICO');
    //$record->STADIO_FENOLOGICO = $stadioFenologico['DESCRIZIONE'];
    if (intval($record->ID_OPERAZIONE) > 0) {
        $operazione = CodiciVari::Load($record->ID_OPERAZIONE, 'OPERAZIONE');
        $record->ID_OPERAZIONE = $operazione['DESCRIZIONE'];
    }
    if (intval($record->AVVERSITA) > 0) {
        $operazione = CodiciVari::Load($record->AVVERSITA, 'TIPO_AVVERSITA');
        $record->AVVERSITA = $operazione['DESCRIZIONE'];
    }
    
    if (intval($record->TIPO_PRELIEVO_ACQUA) > 0) {
        $tipo_prelievo = CodiciVari::Load($record->TIPO_PRELIEVO_ACQUA, 'TIPO_PRELIEVO_ACQUA');
        $record->TIPO_PRELIEVO_ACQUA = $tipo_prelievo['DESCRIZIONE'];
    }
    
    exit(json_encode($record));
}

function listSemine() {
    global $LoggedAccount;
    $cuaa = $_POST['cuaa']; //CCHMNL57T03M088S
//    $cuaa = "CCHMNL57T03M088S";//CCHMNL57T03M088S
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $tipo = $_POST['tipo'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
//     DA SCOMMENTARE PER LA RICERCA
    $searchQuery .= " AND ( CUAA = :cuaa) AND TIPO_INTERVENTO = :tipo_intervento AND CANCELLATO =0 ";
    $searchArray['CUAA'] = $cuaa;
    $searchArray['TIPO_INTERVENTO'] = $tipo;
    ## Search
//    if ($searchValue != '') {
//        $searchQuery .= " AND ( lower(SPECIE)  LIKE :specie) ";
//        $searchArray['SPECIE'] = "%$searchValue%";
//    }
    $res = QuadernodiCampagna::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);

    foreach ($res['empRecords'] as $row) {
        $fnView = ' <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalCenter" onclick="viewOperation(' . $row['ID'] . ')" > <i class="fa fa-edit"></i> </button>';
        $fnViewAppe = ' <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mapsModal" onclick="viewAppezzamenti(' . $row['ID'] . ')" > <i class="fas fa-map-marker"></i> </button>';

        $tipo_intervento = CodiciVari::Load($row['TIPO_INTERVENTO'], 'TIPO_INTERVENTO');
        $data[] = array(
            "specie" => $row['SPECIE'],
            "data" => Date::FormatDate($row['DATA_INTERVENTO']),
            "tipo_intervento" => $tipo_intervento['DESCRIZIONE'],
            "qta" => $row['QUANTITA_RACCOLTA'],
            "visualizza" => $fnViewAppe . $fnView
//            "visualizza" => $fnView
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
    $con->db_transactionStart();
    $response = Utils::initDefaultResponse();
    $transaction = false;
    /*
     * manca da inserire la funzione per il decremento delle quantita sul magazzino
     */
    //Utils::print_array($_REQUEST);exit();
    $array_isole = $_REQUEST['id_isol'];
    if(count($array_isole)<=0){
        $response['erroreDescrizione'] = "Selezionare almeno un appezzamento di terreno per registrare il trattamento";
        exit(json_encode($response));
    }
    $con->db_transactionStart();
    $rec = new QuadernodiCampagna($_REQUEST);
    $rec->DATA_INTERVENTO = ($rec->DATA_INTERVENTO!="" ? Date::FormatDate($rec->DATA_INTERVENTO, DATE_FORMAT_ISO) : null);
    $rec->DATA_FINE_INTERVENTO =($rec->DATA_FINE_INTERVENTO!="" ? Date::FormatDate($rec->DATA_FINE_INTERVENTO, DATE_FORMAT_ISO) : null);
    $rec->DATA_FINE_IRRIGAZIONE = ($rec->DATA_FINE_IRRIGAZIONE!="" ? Date::FormatDate($rec->DATA_FINE_IRRIGAZIONE, DATE_FORMAT_ISO) : null);
    $rec->DATA_INTERVENTO_START = ($rec->DATA_INTERVENTO_START!="" ? Date::FormatDate($rec->DATA_INTERVENTO_START, DATE_FORMAT_ISO) : null);
    $rec->DATA_INTERVENTO_END = ($rec->DATA_INTERVENTO_END!="" ? Date::FormatDate($rec->DATA_INTERVENTO_END, DATE_FORMAT_ISO) : null);
    $rec->ID_OPERAZIONE = (intval($rec->ID_OPERAZIONE));
    $rec->ID_PRODOTTO_FITOSANITARIO = (intval($rec->ID_PRODOTTO_FITOSANITARIO));
    $rec->QUANTITA_UTILIZZATA = (intval($rec->QUANTITA_UTILIZZATA));
    $rec->QUANTITA_RACCOLTA = (intval($rec->QUANTITA_RACCOLTA));
    $rec->UNITA_MISURA = (intval($rec->UNITA_MISURA));
    $rec->ID_OPERATORE = $LoggedAccount->CODICE_FISCALE;
    //Utils::print_array($rec);exit();
    $response = $rec->Save();
    if ($response['esito'] == 1) {
        $id_qdc = $response['lastId'];
        $rec->ID = $id_qdc;
        ######################################
        # REGISTRAZIONE MOVIMENTO DI SCARICO #
        ######################################                
        if($rec->TIPO_INTERVENTO == DIFESA || $rec->TIPO_INTERVENTO == NUTRIZIONE ){
            $response = $rec->Scarico();
        }
        ######################################        
        if($response['esito'] == 1 ){        
            if (count($array_isole) > 0) {
                foreach ($array_isole as $key => $value) {
                    $qdc_voci = new QuadernodiCampagnaVoci();
                    $qdc_voci->ID_QDC = $id_qdc;
                    $qdc_voci->ID_APPEZZAMENTO = $value;
                    $returnSaveVoci = $qdc_voci->Save();
                    if ($returnSaveVoci['esito'] == 1) {
                        $transaction = true;
                    }
                }
            }
        }
    }
    if ($transaction) {
        $con->db_transactionCommit();
    } else {
        $con->db_transactionRollback();
    }
    $response['cuaa'] = $rec->CUAA;
    exit(json_encode($response));
}
