<?php

$action = $_REQUEST["action"];

switch ($action) {
    case 'search':
        $term = Utils::getFromReq("term", "");
        search($term);
        break;
    case 'load':
        load();
        break;
    case "list_trattamenti":
        listTrattamenti();
        break;
    case "list_particelle":
        listParticelle();
        break;
    case "listOperazioniVoci":
        listOperazioniVoci();
        break;
    case 'save':
        save();
        break;

    case 'deletelogical':
        deleteLogical();
        break;
}
exit();

function search($term) {
    session_write_close();
    $particella = new Particella(intval($_REQUEST['id_particella']));
    $return = Giacenze::autocomplete($term, $particella->id_magazzino);
    exit(html_entity_decode(json_encode($return)));
}

function deleteLogical() {
    global $con, $LoggedAccount;
    $con->db_transactionStart();
    $quadernodiCampanga = new QuadernodiCampagna(RequestFilter::get_filter_int('id'));
    $result = $quadernodiCampanga->LogicalDelete();
    
    if ($result['esito'] == 1) {
        $particella = new Particella($quadernodiCampanga->id_particella);
        $magazzino = new Magazzino($particella->id_magazzino);
        /// INSERISCO IL MOVIMENTO DI MAGAZZINO
        $movimento = new Movimento();
        $movimento->id_magazzino = $magazzino->id;
        $movimento->data_movimento = $quadernodiCampanga->data_intervento;
        $movimento->tipo = TIPO_CARICO;
        $movimento->id_causale = RETTIFICA_CARICO;
        $movimento->id_utente_creazione = $LoggedAccount->id;
        $movimento->descrizione = "QDC - CANCELLAZIONE ";
        $resultMovimento = $movimento->Save();
        $id_movimento = $resultMovimento['lastId'];
        if ($resultMovimento['esito'] != 1) {
            $result['esito'] = -999;
            
            
        }else{
            foreach ($quadernodiCampanga->qdc_voci as $value){
                /// INSERISCO LA VOCE DEL MOVIMENTO DI MAGAZZINO
                $movimento_voce = new MovimentoVoce();
                $movimento_voce->id_movimento = $id_movimento;
                $movimento_voce->id_articolo = $value['id_prodotto_fitosanitario'];
                $movimento_voce->quantita = $value['dose_utilizzata'];
                $movimento_voce->unita_misura = $value['dose_ha'];
                $movimento_voce->id_utente_creazione = $LoggedAccount->id;
                $resultStrCampagnaVoci = $movimento_voce->Save();
                if ($resultStrCampagnaVoci['esito'] != 1) {
                    $result['esito'] = -999;
                    break; //
                }

            }
        }
        
    }
    
    
    if ($result['esito'] == 1) {
        $con->db_transactionCommit();
    } else {
        $con->db_transactionRollback();
    }
    exit(json_encode($result));
}

function save() {
    global $LoggedAccount, $con;
    $result = array();
    $note = RequestFilter::get_filter_int('note');
    $tipo_intervento = RequestFilter::get_filter_int('tipo_intervento');
    $quadernoCampagna = new QuadernodiCampagna($_POST);
    $con->db_transactionStart();
    $quadernoCampagna->id_operatore = $LoggedAccount->id;
    $result = $quadernoCampagna->Save();
    if (intval($quadernoCampagna->id) > 0) {
        $ultimoId = $quadernoCampagna->id;
    } else {
        $ultimoId = $result['lastId'];
    }
    $particella = new Particella($quadernoCampagna->id_particella);
    $magazzino = new Magazzino($particella->id_magazzino);
    
    $codiceIntervento = CodiciVari::Load($tipo_intervento, 'TIPO_INTERVENTO');
    
    if (intval($result['esito']) == 1) {
        
        /// INSERISCO IL MOVIMENTO DI MAGAZZINO
        $movimento = new Movimento();
        $movimento->id_magazzino = $magazzino->id;
        $movimento->data_movimento = $quadernoCampagna->data_intervento;
        $movimento->tipo = TIPO_SCARICO;
        $movimento->id_causale = SCARICO_CONSUMO;
        $movimento->id_utente_creazione = $LoggedAccount->id;
        
        if ($tipo_intervento == 1) {
            $composizione_azoto = $_POST['composizione_azoto'];
            $id_nome_prodotto_nutrizione = $_POST['id_nome_prodotto_nutrizione'];
            $composizione_fosforo = $_POST['composizione_fosforo'];
            $composizione_potassio = $_POST['composizione_potassio'];
            $dose_ha = $_POST['dose_ha'];
            $dose_utilizzata = $_POST['dose_utilizzata'];
            $tempo_rientro = $_POST['tempo_rientro'];
            $intervallo_sicurezza = $_POST['intervallo_sicurezza'];
            $id_operazione = $_POST['id_operazione'];
            $countValue = count($composizione_azoto);
            
            $movimento->descrizione = "QDC - ".$codiceIntervento['descrizione'];
            $resultMovimento = $movimento->Save();
            $id_movimento = $resultMovimento['lastId'];
            if (intval($resultMovimento['esito']) == 1) {
                
                for ($i = 0; $i < $countValue; $i++) {
                    if ($composizione_azoto[$i] != "" || $id_nome_prodotto_nutrizione[$i] != "" || $composizione_fosforo[$i] != "" || $composizione_potassio[$i] != "" || $dose_ha[$i] != "" || $dose_utilizzata[$i] != "" || $tempo_rientro[$i] != "" || $intervallo_sicurezza[$i] != "" || $id_operazione[$i] != "") {
                        $quadernovoci = new QuadernodiCampagnaVoci();
                        $id_prodotto = $id_nome_prodotto_nutrizione[$i];
                        $coAzoto = $composizione_azoto[$i];
                        $coFosforo = $composizione_fosforo[$i];
                        $coPotassio = $composizione_potassio[$i];
                        $dHa = $dose_ha[$i];
                        $dUtilizzata = $dose_utilizzata[$i];
                        $trientro = $tempo_rientro[$i];
                        $intervallSicure = $intervallo_sicurezza[$i];
                        $id_op = $id_operazione[$i];
                        $quadernovoci->id_quaderno = $ultimoId;
                        $quadernovoci->id_prodotto_fitosanitario = $id_prodotto;
                        $quadernovoci->dose_ha = $dHa;
                        $quadernovoci->dose_utilizzata = $dUtilizzata;
                        $quadernovoci->tempo_rientro = $trientro;
                        $quadernovoci->intervallo_sicurezza = $intervallSicure;
                        $quadernovoci->composizione_azoto = $coAzoto;
                        $quadernovoci->composizione_fosforo = $coFosforo;
                        $quadernovoci->composizione_potassio = $coPotassio;
                        $quadernovoci->id_operazione = $id_op;
                        $resultStrCampagna = $quadernovoci->Save();
                        if ($resultStrCampagna['esito'] != 1) {
                            $result['esito'] = -9912;
                            break; //
                        } else {
                            /// INSERISCO LA VOCE DEL MOVIMENTO DI MAGAZZINO
                            $movimento_voce = new MovimentoVoce();
                            $movimento_voce->id_movimento = $id_movimento;
                            $movimento_voce->id_articolo = $id_prodotto;
                            $movimento_voce->quantita = $dUtilizzata;
                            $movimento_voce->unita_misura = $dHa;
                            $movimento_voce->id_utente_creazione = $LoggedAccount->id;
                            $movimento_voce->data_creazione = $quadernoCampagna->data_intervento;
                            $resultStrCampagnaVoci = $movimento_voce->Save();
                            if ($resultStrCampagnaVoci['esito'] != 1) {
                                $result['esito'] = -9913;
                                break; //
                            }

                        }

                    }
                }
                
            }else{
                $result['esito'] = -9911;
            }
        } else if ($tipo_intervento == 2) {
            $id_nome_prodotto = $_POST['id_prodotto_fitosanitario'];
            $avversita = $_POST['avversita'];
            $sostanze_attive = $_POST['sostanze_attive'];
            $dose_ha = $_POST['dose_ha'];
            $dose_utilizzata = $_POST['dose_utilizzata'];
            $tempo_rientro = $_POST['tempo_rientro'];
            $countValue = count($avversita);
            
            $movimento->descrizione = "QDC - ".$codiceIntervento['descrizione'];
            ###### SALVO IL MOVIMENTO BEGIN #######
            $resultMovimento = $movimento->Save();
            $id_movimento = $resultMovimento['lastId'];
            ###### SALVO IL MOVIMENTO END #######
            if (intval($resultMovimento['esito']) == 1) {
            
                for ($i = 0; $i < $countValue; $i++) {
                    if ($id_nome_prodotto[$i] != "" || $avversita[$i] != "" || $sostanze_attive[$i] != "" || $dose_ha[$i] != "" || $dose_utilizzata[$i] != "" || $tempo_rientro[$i] != "") {
                        $quadernovoci = new QuadernodiCampagnaVoci();
                        $id_prodotto = $id_nome_prodotto[$i];
                        $coavversita = $avversita[$i];
                        $cosostanze_attive = $sostanze_attive[$i];
                        $codose_ha = $dose_ha[$i];
                        $dUtilizzata = $dose_utilizzata[$i];
                        $trientro = $tempo_rientro[$i];
                        $quadernovoci->id_quaderno = $ultimoId;
                        $quadernovoci->avversita = $coavversita;
                        $quadernovoci->id_prodotto_fitosanitario = $id_prodotto;
                        $quadernovoci->dose_ha = $codose_ha;
                        $quadernovoci->dose_utilizzata = $dUtilizzata;
                        $quadernovoci->tempo_rientro = $trientro;
                        $resultStrCampagna = $quadernovoci->Save();
                        if (intval($resultStrCampagna['esito']) < 1) {
                            $result['esito'] = -9922;
                            break; // 
                        } else {
                            /// INSERISCO LA VOCE DEL MOVIMENTO DI MAGAZZINO BEGIN
                            $movimento_voce = new MovimentoVoce();
                            $movimento_voce->id_movimento = $id_movimento;
                            $movimento_voce->id_articolo = $id_prodotto;
                            $movimento_voce->quantita = $dUtilizzata;                        
                            $movimento_voce->unita_misura = $codose_ha;
                            $movimento_voce->id_utente_creazione = $LoggedAccount->id;
                            $movimento_voce->data_creazione = $quadernoCampagna->data_intervento;
                            $resultStrCampagnaVoci = $movimento_voce->Save();
                            if (intval($resultStrCampagnaVoci['esito']) < 1) {
                                $result['esito'] = -9923;
                                break; //
                            }

                        }
                    }
                }
            }else{
                $result['esito'] = -9921;
            }
        }
        if (intval($result['esito']) == 1) {
            $con->db_transactionCommit();
        } else {
            $con->db_transactionRollback();
        }
    } else {
        $con->db_transactionRollback();
    }
    exit(json_encode($result));
}

function listParticelle() {

    global $LoggedAccount;
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
    $searchQuery .= " AND ( id_ditta = :id_ditta) AND id_magazzino > 0  ";
    $searchArray['id_ditta'] = $LoggedAccount->id_azienda;
    ## Search     
    if ($searchValue != '') {
        $searchQuery .= " AND ( lower(comparto) LIKE :comparto) ";
        $searchArray['comparto'] = "%$searchValue%";
    }
    $res = Particella::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    foreach ($res['empRecords'] as $row) {
        $fnAddMod = '<a rel="' . RELUPDATE . '"  href="' . BASE_HTTP . 'azienda/quadernodicampagna.php?mode=add&id_p=' . base64_encode($row['id']) . '&id_d=' . base64_encode($LoggedAccount->id_azienda) . '" class="btn-floating mb-1 btn-flat waves-effect waves-light indigo darken-4 white-text modal-trigger "><i class="material-icons dp48">add</i></a>';
        $fnView = '<a rel="' . RELUPDATE . '"  href="' . BASE_HTTP . 'azienda/quadernodicampagna.php?mode=listOp&id_p=' . base64_encode($row['id']) . '&id_d=' . base64_encode($LoggedAccount->id_azienda) . '" class="btn-floating mb-1 btn-flat waves-effect waves-light green darken-4 white-text modal-trigger "><i class="material-icons dp48">remove_red_eye</i></a>';
        $data[] = array(
            "centro_codice" => $row['centro_codice'],
            "comune" => $row['comune'],
            "foglio_particella" => $row['foglio_particella'],
            "comparto" => $row['comparto'],
            "modifica" => $fnView . " " . $fnAddMod
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

function listOperazioniVoci() {

    global $LoggedAccount;

//    $id_azienda = $_POST['id_azienda'];
    $id_particella = $_POST['id_particella'];
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
    $searchQuery .= " AND id_azienda=" . $LoggedAccount->id_azienda . " AND id_particella=" . $id_particella . " AND cancellato=0";
    ## Search     
    if ($searchValue != '') {
        $searchQuery .= " AND ( lower(comparto) LIKE :comparto) ";
        $searchArray['comparto'] = "%$searchValue%";
    }
    $res = QuadernodiCampagna::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    foreach ($res['empRecords'] as $row) {
        $tipo_intervento = CodiciVari::Load($row['tipo_intervento'], 'TIPO_INTERVENTO');
        $fnView = '<a rel="' . RELUPDATE . '"  href="' . BASE_HTTP . 'azienda/quadernodicampagna.php?mode=viewE&id_q=' . base64_encode($row['id']) . '&id_d=' . base64_encode($LoggedAccount->id_azienda) . '" class="btn-floating mb-1 btn-flat waves-effect waves-light green darken-4 white-text modal-trigger "><i class="material-icons dp48">remove_red_eye</i></a>';
        $data[] = array(
            "tipo_intervento" => $tipo_intervento['descrizione'],
            "data" => Utils::FormatDate($row['data_intervento']),
            "visualizza" => $fnView
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
