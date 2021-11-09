<?php

$mode = $_REQUEST['action'];
switch ($mode) {
    case 'list':
        listCuaa();
        break;
}

function listCuaa() {
    global $LoggedAccount;    
    $response = array();
    
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();    
    ## Search     
    $totCount = false;
    $searchQuery = " and (a.data_mort = '9999-12-31' or a.data_mort is null) ";    
    if ($searchValue != '') {
        $totCount = true;
        $searchQuery .= " AND ( lower(a.desc_ragi_soci) like :ente or lower(a.desc_cogn) like :ente or lower(a.desc_nome) like :ente or lower(a.codi_fisc) like :ente) ";
        $searchArray['ente'] = "%".strtolower($searchValue)."%";
    }
    $res = AnagraficaUffici::LoadDataTableCustom($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage, $totCount);


    foreach ($res['empRecords'] as $row) {
        $btn = '';
        
        $btn .= '<button class="btn btn-sm btn-primary" onclick="goTo(\'azienda\',\'' . $row['prg_scheda'] . '\');" alt="Vai nella gestione aziendale" title="Vai nella gestione aziendale"><i class="fas fa-tractor"></i>&nbsp; Apri</button>';

//        if ($domanda->STATO < PRESENTATA && Utils::isStatoSportelloPresentazione()) {
//            //$btn = '<button class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#allegatiModal" onclick="assegnaValue(\'' . $row['DOCUMENTO_ID'] . '\', \'' . $row['OBBLIGO'] . '\');" alt="Allega documento" title="Allega documento"><i class="fas fa-paper-plane"></i>&nbsp; Allega</button>';
//            $btn .= '<button class="btn btn-sm btn-danger" onclick="deleteEsperienza(\'' . $row['ID'] . '\', \'' . $row['ID_ANAG_ESPERIENZE_MATURATE'] . '\');" alt="Elimina esperienza" title="Elimina"><i class="fas fa-trash"></i>&nbsp; Elimina</button>';
//        }
//
//        $note = '';
//        if ($row['NOTE'] != "") {
//            $note = '<br><small><em>' . $row['NOTE'] . '</em></small>';
//        }
//
//        if ($row['ID_ANAG_ESPERIENZE_MATURATE'] > 0) {
//            $desc_esperienza = new AnagraficaEsperienzeMaturate($row['ID_ANAG_ESPERIENZE_MATURATE']);
//            //Utils::print_array($desc_esperienza);
//            $esperienza_txt = $desc_esperienza->DESCRIZIONE;
//        }
        /*
          $first_date = new DateTime($row['DATA_INIZIO']);
          $second_date = new DateTime($row['DATA_FINE']);
          $difference = $first_date->diff($second_date);
         */

        //Utils::print_array($difference);

        /* $row['ANZIANITA_POSIZIONE_1'] = Date::dateOracleDate($row['ANZIANITA_POSIZIONE']);
          $row['ANZIANITA_POSIZIONE_2'] = Date::dateOracleDateNascita($row['ANZIANITA_POSIZIONE']);
          $row['ANZIANITA_POSIZIONE_3'] = Date::FormatDate($row['ANZIANITA_POSIZIONE']); */


        $data[] = array(
            "id_sogg" => $row['id_sogg'],
            "prg_scheda" => $row['prg_scheda'],            
            "nominativo" => (!empty($row['desc_ragi_soci']) ? $row['desc_ragi_soci'] : $row['desc_cogn']. ' ' .$row['desc_nome']),
            "codi_fisc" => $row['codi_fisc'],
            "comune" => $row['desc_comu_nasc'],
            "OP" => $btn
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
