<?php

class ImportDati {

//	const PATH = '/import/';
//	const FILEPRODOTTIFITOSANITARI =  'C_17_dataset_6_download_itemDownload0_upFile.csv';
//	const FILECOMUNI =  'ComuniConISTAT-Galileo-IMPORT.xls';
//	const FILEINCASSI =  'Procedura_batch_su_mod_pagamento_2_trimestre.xls';
//        const FILEARTICOLI =  'ElencoArticoli.xls______';
//        const FILEADDEBITI =  'AddebitiBLU_7.xls';
//        const NUMEROLOTTO = 7;
//        const FILEFATTURE =  'Fatture_2014_BLU_LOTTO11.xls';
//        const FILEFATTURESM =  'FattureSANMARTINO_2014_BLU_LOTTO6.xls';
//	const FILEPIANODEICONTI =  'PDCBibico.xls______';
//	const FILEFORNITORI =  'ElencoFornitori.xls_________';
//	const FILEMEDICI =  'ElencoMedici.xls________';
//        const FILECLIENTI = 'ElencoClientiImage.xls_________';
//        const FILEPRESTAZIONI = 'ElencoPrestazioniImage.xls_________';
//        const FILECOMPARTECIPAZIONI = 'Compartecipazioni.xls_________';
//        const FILEPAZIENTI = 'ElencoPazientiAggiornato_TEST.xls_________';//'ElencoPazientiAggiornato.xls';
//        const FILEALLINEAMENTOMEDICI = 'batchMediciIncassi.xls';//'ElencoPazientiAggiornato.xls';


    public function __construct() {
        
    }

    /**
     * 
     * Converte i dati presenti nelle colonne di un file xls in un array di array 
     * @param percorso del file xls da parsare $file
     */
    private static function ParseExcel($file) {
        $objReader = new PHPExcel_Reader_Excel5();
        $objReader->setReadDataOnly();
        $objPHPExcel = $objReader->load($file);
        $c = array();
        if (file_exists($file)) {
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getFormattedValue();
                        $c[$row][$col] = $val;
                    }
                }
            }
        }

        return $c;
    }

    /**
     * 
     * Converte i dati presenti nelle colonne di un file xls in un array di array 
     * @param percorso del file csv da parsare $file
     */
    private static function ParseCsv($file) {
        
        $content = file_get_contents($file);
        $rows = explode("\n", $content);
        
        $c = array();
        if (file_exists($file)) {
            //echo "<pre>".print_r($file, true)."</pre>";exit();
            foreach ($rows as $key => $value) {
                if($key==0)
                  continue; 
                $value = $value;
                $cols = explode(";", $value);
                //Utils::print_array($cols);exit();
                foreach ($cols as $k => $v) {
                    //echo "KKKK-->".$k."<---";
                    //echo "VVVV-->".utf8_decode($v)."<---<br>";
                    $c[$key][$k] = utf8_decode($v);
                }
            }
        }
        //Utils::print_array($c);exit();
        return $c;
    }

    /**
     * Aggiorna i tutti i prodotti fitosanitari
     */
    public function AggiornaProdottiFitosanitari() {
        global $con, $LoginOperatore, $App;
        $res = array();
        // http://www.dati.salute.gov.it/dati/dettaglioDataset.jsp?menu=dati&idPag=6
        
        
        //Utils::print_array($_FILES);exit();
        
        //$file = ROOT_IMPORT."C_17_dataset_6_download_itemDownload0_upFile.csv";
        $file = $_FILES['listino']['tmp_name'];
        
        //$c = self::ParseExcel($file);	
        $c = self::ParseCsv($file);
        
        /*
          [0] => NUMERO_REGISTRAZIONE
          [1] => PRODOTTO
          [2] => IMPRESA
          [3] => SEDE_LEGALE_IMPRESA
          [4] => CAP_SEDE_LEGALE_IMPRESA
          [5] => CITTA_SEDE_LEGALE_IMPRESA
          [6] => PROVINCIA_SEDE_LEGALE_IMPRESA
          [7] => SEDE_AMMINISTRATIVA_IMPRESA
          [8] => CAP_SEDE_AMMINISTRATIVA_IMPRESA
          [9] => CITTA_SEDE_AMMINISTRATIVA_IMPRESA
          [10] => PROVINCIA_SEDE_AMMINISTRATIVA_IMPRESA
          [11] => DATA_REGISTRAZIONE
          [12] => SCADENZA_AUTORIZZAZIONE
          [13] => INDICAZIONI_DI_PERICOLO
          [14] => ATTIVITA
          [15] => CODICE_FORMULAZIONE
          [16] => DESCRIZIONE_FORMULAZIONE
          [17] => SOSTANZE_ATTIVE
          [18] => CONTENUTO_PER_100G_DI_PRODOTTO
          [19] => IP
          [20] => PFnPO
          [21] => PFnPE
          [22] => STATO_AMMINISTRATIVO
          [23] => MOTIVO_DELLA_REVOCA
          [24] => DATA_DECRETO_REVOCA
          [25] => DATA_DECORRENZA_REVOCA
         */

        $prodProcessati = 0;
        $prodProcessatiOK = 0;
        $prodProcessatiKO = 0;
        $prodNuovi = 0;

        foreach ($c as $key => $value) {
            if ($key == 0 || $key >= count($c) || intval($value[0]) <= 0)
                continue;
            //echo "<pre>".print_r($value, true)."</pre>";
            $prodotto = new ProdottiFitosanitari($value[0], TRUE);
            
            //[numero_registrazione]
            if (intval($prodotto->ID) == 0) {
                $prodotto->NUMERO_REGISTRAZIONE = $value[0];
                $prodNuovi++;
            }

            $prodotto->PRODOTTO = utf8_encode($value[1]);
            $prodotto->IMPRESA = utf8_encode($value[2]);
            $prodotto->SEDE_LEGALE_IMPRESA = utf8_encode($value[3]);
            $prodotto->CAP_SEDE_LEGALE_IMPRESA = utf8_encode($value[4]);
            $prodotto->CITTA_SEDE_LEGALE_IMPRESA = utf8_encode($value[5]);
            $prodotto->PROVINCIA_SEDE_LEGALE_IMPRESA = utf8_encode($value[6]);
            $prodotto->SEDE_AMMINISTRATIVA_IMPRESA = utf8_encode($value[7]);
            $prodotto->CAP_SEDE_AMMINISTRATIVA_IMPRESA = utf8_encode($value[8]);
            $prodotto->CITTA_SEDE_AMMINISTRATIVA_IMPRESA = utf8_encode($value[9]);
            $prodotto->PROVINCIA_SEDE_AMMINISTRATIVA_IMPRESA = utf8_encode($value[10]);
            $prodotto->DATA_REGISTRAZIONE = (Date::is_date($value[11]) ? Date::FormatDate($value[11], DATE_FORMAT_ISO) : NULL);
            $prodotto->SCADENZA_AUTORIZZAZIONE = (Date::is_date($value[12]) ? Date::FormatDate($value[12], DATE_FORMAT_ISO) : NULL);
            $prodotto->INDICAZIONI_DI_PERICOLO = utf8_encode($value[13]);
            $prodotto->ATTIVITA = utf8_encode($value[14]);
            $prodotto->CODICE_FORMULAZIONE = utf8_encode($value[15]);
            $prodotto->DESCRIZIONE_FORMULAZIONE = utf8_encode($value[16]);
            $prodotto->SOSTANZE_ATTIVE = utf8_encode($value[17]);
            $prodotto->CONTENUTO_PER_CENTOG_DI_PRODOTTO = utf8_encode($value[18]);
            $prodotto->IP = utf8_encode($value[19]);
            $prodotto->PPO = utf8_encode($value[20]);
            $prodotto->STATO_AMMINISTRATIVO = utf8_encode($value[22]);
            $prodotto->MOTIVO_DELLA_REVOCA = utf8_encode($value[23]);
            $prodotto->DATA_DECRETO_REVOCA = (Date::is_date($value[24]) ? Date::FormatDate($value[24], DATE_FORMAT_ISO) : NULL);
            $prodotto->DATA_DECORRENZA_REVOCA = (Date::is_date($value[25]) ? Date::FormatDate($value[25], DATE_FORMAT_ISO) : NULL);
            $rec = $prodotto->Save();
            ($rec['esito'] == 1 ? $prodProcessatiOK++ : $prodProcessatiKO++);
            //echo ($rec['esito']==1 ? "OK ===>>>".$rec['lastId']."</br>" : "ERRORE ===>".$rec['descrizioneErrore']." - ".$prodotto->id."</br>");
            //echo "Salvataggio=<pre>".print_r($rec, true)."</pre>";
            //echo "Dopo=<pre>".print_r($prodotto, true)."</pre>";

            $prodProcessati++;
        }
        
        $oggi = new DateTime();
        $res['processati'] = $prodProcessati;
        $res['prodottiOK'] = $prodProcessatiOK;
        $res['prodottiKO'] = $prodProcessatiKO;
        $res['prodNuovi'] = $prodNuovi;
        $res['ultimoAggiornamento'] = $oggi->format('d/m/Y');
        $res['esito'] = 1;

        return $res;
    }

    public function ImportIspettoriFitosanitari() {
        global $con, $LoginOperatore, $App;
        $res = array();

        // http://www.dati.salute.gov.it/dati/dettaglioDataset.jsp?menu=dati&idPag=6


        $file = ROOT_IMPORT . 'ispettori_fitosanitari.xls';

        $c = self::ParseExcel($file);
        //$c = self::ParseCsv($file);	
        //Utils::print_array($c);
        //exit();
        /*
          [0] => N.Ord.
          [1] => Num. Tessera
          [2] => Cognome
          [3] => Nome
          [4] => sesso
          [5] => Data di nascita
          [6] => Provincia di nascita
          [7] => Luogo di nascita
          [8] => Ufficio
          [9] => Provincia
          [10] => CODICE FISCALE
          [11] => email
         */
        $passwordGenerica = 'fitosan';
        $prodProcessati = 0;
        $prodProcessatiOK = 0;
        $prodProcessatiKO = 0;
        $con->db_transactionStart();
        $err = false;
        foreach ($c as $key => $value) {
            if ($key == 0 || $key >= count($c) || intval($value[0]) <= 0)
                continue;

            $record = new Operatore();

            $record->cognome = utf8_encode($value[2]);
            $record->nome = utf8_encode($value[3]);
            $record->sesso = utf8_encode($value[4]);
            $record->data_nascita = (Utils::is_date($value[5]) ? Utils::FormatDate($value[5], DATE_FORMAT_ISO) : NULL);
            $record->prov_nascita = utf8_encode($value[6]);
            $record->luogo_nascita = utf8_encode($value[7]);
            $record->codice_fiscale = utf8_encode($value[10]);
            $record->email = utf8_encode($value[11]);
            $return = $record->Save();
            ($return['esito'] == 1 ? $prodProcessatiOK++ : $prodProcessatiKO++);
            if ($return['esito'] != 1) {
                $return['punto_rottura'] = 'Operatore ==>' . $record->id;
                $err = true;
                break;
            } else {

                $login = new LoginOperatore();
                $login->id_anagrafica_operatore = intval($return['lastId']);
                $login->id_gruppo = GRUPPO_ISPETTORE_SANITARIO;
                $login->username = $record->email;
                $login->password = md5($passwordGenerica);
                $login->provincia_appartenenza = utf8_encode($value[9]);
                $login->data_aggiornamento_pass = '2010-01-01 20:00:00';
                $return = $login->Save();
                if ($return['esito'] != 1) {
                    $return['punto_rottura'] = 'Login ==>' . $login->id;
                    $err = true;
                    break;
                } else {
                    /*
                      $emailInvio = $login->username;
                      $resEmail = Utils::invioMailRegOperatore($emailInvio, $login->username, $passwordGenerica);
                      if ($resEmail['esito'] != 1){
                      $return['punto_rottura'] = 'Email ==>'.$resEmail['descrizioneErrore'];
                      $err = true;
                      break;
                      }
                     */
                }
            }


            $prodProcessati++;
        }

        $oggi = new DateTime();
        $res['processati'] = $prodProcessati;
        $res['prodottiOK'] = $prodProcessatiOK;
        $res['prodottiKO'] = $prodProcessatiKO;
        $res['ultimoAggiornamento'] = $oggi->format('d/m/Y');
        $res['esito'] = 1;

        if ($err) {
            $con->db_transactionRollback();
            $return = array('esito' => -999, 'descrizioneErrore' => 'Erore nel salvataggio', 'rottura' => $return);
        } else {
            $con->db_transactionCommit();
            $return = array('esito' => 1, 'result' => $res);
        }

        Utils::print_array($return);

        return $return;
    }

    public function ImportPatentini() {
        global $con, $LoginOperatore, $App;
        $res = array();

        $file = ROOT_IMPORT . 'ispettori_fitosanitari.xls';

        $c = self::ParseExcel($file);
        
        //Utils::print_array($c);
        //exit();
        
        //$c = self::ParseCsv($file);	
        
        /*$numeroPatentinoArr = explode('/', $c[62][1]);
        $numeroPatentino = (isset($numeroPatentinoArr[1]) ? intval($numeroPatentinoArr[1]) : 0 );
        
        Utils::print_array($numeroPatentinoArr); echo "<br>--->".$numeroPatentino;
        exit(); */
        /*
          [0] => N.Ord.
          [1] => Num. Tessera
          [2] => Cognome
          [3] => Nome
          [4] => sesso
          [5] => Data di nascita
          [6] => Provincia di nascita
          [7] => Luogo di nascita
          [8] => Ufficio
          [9] => Provincia
          [10] => CODICE FISCALE
          [11] => email
         */

        $prodProcessati = 0;
        $prodProcessatiOK = 0;
        $prodProcessatiKO = 0;
        $con->db_transactionStart();
        $err = false;
        $oggi = new DateTime();

        $evento = new FormazioneEvento();
        $evento->descrizione = "EVENTO CREATO PER PRIMO IMPORT";
        $evento->data_inizio = $oggi->format('Y-m-d');
        $evento->data_fine = $oggi->format('Y-m-d');
        $evento->numero_ore = 0;
        $evento->tipologia = 1; // PATENTINI
        $evento->stato = STATO_EVENTO_CHIUSO;
        $evento->sede_evento = "PRIMO IMPORT";
        $returnEvento = $evento->Save();
        $newIdEvento = intval($returnEvento['lastId']);

        if ($returnEvento['esito'] != 1) {
            $return['punto_rottura'] = 'Evento ==>' . $newIdEvento;
            $err = true;
        } else {

            $esame = new FormazioneEsame();
            $esame->id_evento = $newIdEvento;
            $esame->descrizione = "ESAME CREATO PER PRIMO IMPORT";
            $esame->stato = STATO_ESAME_CHIUSO;
            $returnEsame = $esame->Save();
            $newIdEsame = intval($returnEsame['lastId']);

            if ($returnEsame['esito'] != 1) {
                $return['punto_rottura'] = 'Esame ==>' . $newIdEsame;
                $err = true;
            } else {
                foreach ($c as $key => $value) {
                    if ($key == 0 || $key >= count($c) || intval($value[0]) <= 0)
                        continue;

                    $record = new FormazioneAlbo();
                    $record->tipologia_albo = ALBO_PARTECIPANTI;
                    $record->cognome = utf8_encode($value[2]);
                    $record->nome = utf8_encode($value[3]);
                    $record->codice_fiscale = utf8_encode($value[10]);
                    $record->email = utf8_encode($value[11]);
                    $record->provincia = utf8_encode($value[9]);
                    $record->data_nascita = (Utils::is_date($value[5]) ? Utils::FormatDate($value[5], DATE_FORMAT_ISO) : NULL);
                    $returnAlbo = $record->Save();
                    $newIdAlbo = intval($returnAlbo['lastId']);
                    ($returnAlbo['esito'] == 1 ? $prodProcessatiOK++ : $prodProcessatiKO++);
                    if ($returnAlbo['esito'] != 1) {
                        $return['punto_rottura'] = 'Albo ==>' . $newIdAlbo;
                        $err = true;
                        break;
                    } else {

                        $evento_docente = new FormazioneEventoDocente();
                        $evento_docente->id_evento = $newIdEvento;
                        $evento_docente->id_utente_albo = $newIdAlbo;
                        $evento_docente->esente = 1; // ESENTE
                        $evento_docente->certificato = 1;
                        $evento_docente->data_idoneo = $oggi->format('Y-m-d H:i:s');
                        $returnEventoDocente = $evento_docente->Save();
                        $newIdEventoDocente = intval($returnEventoDocente['lastId']);
                        if ($returnEventoDocente['esito'] != 1) {
                            $return['punto_rottura'] = 'EventoDocente ==>' . $newIdEventoDocente;
                            $err = true;
                            break;
                        } else {

                            $esame_albo = new FormazioneEsameAlbo();
                            $esame_albo->id_esame = $newIdEsame; // ESAME FITTIZIO
                            $esame_albo->id_utente_albo = $newIdAlbo;
                            $esame_albo->esente = 1; //
                            $esame_albo->esito = ESITO_ESAME_POSITIVO;
                            $returnEsameAlbo = $esame_albo->Save();
                            $newIdEventoDocente = intval($returnEsameAlbo['lastId']);
                            if ($returnEsameAlbo['esito'] != 1) {
                                $return['punto_rottura'] = 'EsameAlbo ==>' . $newIdEventoDocente;
                                $err = true;
                                break;
                            } else {
                                
                                $numeroPatentinoArr = explode('/', $value[1]);
                                $numeroPatentino = (isset($numeroPatentinoArr[1]) ? $numeroPatentinoArr[1] : 0 );
                                
                                $certificato = new FormazioneCertificato();
                                $certificato->id_esame = $newIdEsame;
                                $certificato->id_utente_albo = $newIdAlbo;
                                $certificato->numero_certificazione = $numeroPatentino;
                                $certificato->cancellato = 0;
                                $returnCertificato = $certificato->Save();
                                $newIdCertificato = intval($returnCertificato['lastId']);
                                if ($returnCertificato['esito'] != 1) {
                                    $return['punto_rottura'] = 'Certificato ==>' . $newIdCertificato;
                                    $err = true;
                                    break;
                                }
                            }
                        }
                    }

                    $prodProcessati++;
                }
            }
        }



        $res['processati'] = $prodProcessati;
        $res['prodottiOK'] = $prodProcessatiOK;
        $res['prodottiKO'] = $prodProcessatiKO;

        $res['ultimoAggiornamento'] = $oggi->format('d/m/Y');
        $res['esito'] = 1;

        if ($err) {
            $con->db_transactionRollback();
            $return = array('esito' => -999, 'descrizioneErrore' => 'Erore nel salvataggio', 'rottura' => $return);
        } else {
            $con->db_transactionCommit();

            $return = array('esito' => 1, 'result' => $res);
        }

        Utils::print_array($return);

        return $return;
    }
    
    
    
    public function ImportParticelle() {
        global $con, $LoginOperatore, $App;
        $res = array();

        $file = ROOT_IMPORT . 'particelleConGPS.xls';
        
        //echo $file;
        /*
            [0] => codice
            [1] => ragione_sociale
            [2] => comune
            [3] => foglio
            [4] => particelle
            [5] => sup catast mq
            [6] => superficie mq
            [7] => comparto
            [8] => LAT (WGS84)
            [9] => LON (WGS84)
         */
        
        $c = self::ParseExcel($file);
        
        //Utils::print_array($c);
        
        
         /// $codice = str_pad($codice, 4, "0", STR_PAD_LEFT);
        $codRuopTmp = 0;
        foreach ($c as $key => $value) {
            if ($key == 0 || $key >= count($c) || intval($value[0]) <= 0)
                        continue;
            $codRuop = intval(ltrim($value[0], '0'));
            $array_coordinate_gps = array();
            echo "<br/>VALUE=".$codRuop;
            
            if($codRuopTmp != $codRuop){
                $ditta = Ditta::getDittaFromRuop($codRuop);
                //Utils::print_array($ditta);
                $codRuopTmp = $codRuop;
                echo "<br/>TMP=".$codRuopTmp;
                echo "<br/>ragsoc=".$value[1];
                echo "<br/>Cognome=".$ditta['cognome'];
                
            }
            if($ditta['id']>0){
                $particella = new Particella();
                $particella->id_ditta = $ditta['id'];
                $particella->id_magazzino = 0;
                $particella->foglio = $value[3];
                $particella->particella = $value[4];
                $particella->comparto = $value[7];
                $particella->comune = $value[2];
                $particella->data = date('Y-m-d');
                $particella->centro_codice = $value[0];
                $particella->superfice_mq = $value[5];
                $particella->superfice_catastale = $value[6];
                $particella->stato = 'VALIDATO';
                $particella->titolo_disp = 'PROPRIETARIO';      
                $array_coordinate_gps['coordinate_gps'][] = $value[8].', '.$value[9];
                $particella->coordinate_gps = json_encode($array_coordinate_gps);      
                $res = $particella->Save();
                Utils::print_array($res);
            }
            //
            
            
            
            
            
            
            //$ditta = Ditta::getDittaFromRuop($codRuop);
            
            //Utils::print_array($ditta);
            
            //exit();
            //echo $value[0];
            
        }
        
        
        
        exit();
        
        //$c = self::ParseCsv($file);	
        
        /*$numeroPatentinoArr = explode('/', $c[62][1]);
        $numeroPatentino = (isset($numeroPatentinoArr[1]) ? intval($numeroPatentinoArr[1]) : 0 );
        
        Utils::print_array($numeroPatentinoArr); echo "<br>--->".$numeroPatentino;
        exit(); */
        

        $prodProcessati = 0;
        $prodProcessatiOK = 0;
        $prodProcessatiKO = 0;
        $con->db_transactionStart();
        $err = false;
        $oggi = new DateTime();

        $evento = new FormazioneEvento();
        $evento->descrizione = "EVENTO CREATO PER PRIMO IMPORT";
        $evento->data_inizio = $oggi->format('Y-m-d');
        $evento->data_fine = $oggi->format('Y-m-d');
        $evento->numero_ore = 0;
        $evento->tipologia = 1; // PATENTINI
        $evento->stato = STATO_EVENTO_CHIUSO;
        $evento->sede_evento = "PRIMO IMPORT";
        $returnEvento = $evento->Save();
        $newIdEvento = intval($returnEvento['lastId']);

        if ($returnEvento['esito'] != 1) {
            $return['punto_rottura'] = 'Evento ==>' . $newIdEvento;
            $err = true;
        } else {

            $esame = new FormazioneEsame();
            $esame->id_evento = $newIdEvento;
            $esame->descrizione = "ESAME CREATO PER PRIMO IMPORT";
            $esame->stato = STATO_ESAME_CHIUSO;
            $returnEsame = $esame->Save();
            $newIdEsame = intval($returnEsame['lastId']);

            if ($returnEsame['esito'] != 1) {
                $return['punto_rottura'] = 'Esame ==>' . $newIdEsame;
                $err = true;
            } else {
                foreach ($c as $key => $value) {
                    if ($key == 0 || $key >= count($c) || intval($value[0]) <= 0)
                        continue;

                    $record = new FormazioneAlbo();
                    $record->tipologia_albo = ALBO_PARTECIPANTI;
                    $record->cognome = utf8_encode($value[2]);
                    $record->nome = utf8_encode($value[3]);
                    $record->codice_fiscale = utf8_encode($value[10]);
                    $record->email = utf8_encode($value[11]);
                    $record->provincia = utf8_encode($value[9]);
                    $record->data_nascita = (Utils::is_date($value[5]) ? Utils::FormatDate($value[5], DATE_FORMAT_ISO) : NULL);
                    $returnAlbo = $record->Save();
                    $newIdAlbo = intval($returnAlbo['lastId']);
                    ($returnAlbo['esito'] == 1 ? $prodProcessatiOK++ : $prodProcessatiKO++);
                    if ($returnAlbo['esito'] != 1) {
                        $return['punto_rottura'] = 'Albo ==>' . $newIdAlbo;
                        $err = true;
                        break;
                    } else {

                        $evento_docente = new FormazioneEventoDocente();
                        $evento_docente->id_evento = $newIdEvento;
                        $evento_docente->id_utente_albo = $newIdAlbo;
                        $evento_docente->esente = 1; // ESENTE
                        $evento_docente->certificato = 1;
                        $evento_docente->data_idoneo = $oggi->format('Y-m-d H:i:s');
                        $returnEventoDocente = $evento_docente->Save();
                        $newIdEventoDocente = intval($returnEventoDocente['lastId']);
                        if ($returnEventoDocente['esito'] != 1) {
                            $return['punto_rottura'] = 'EventoDocente ==>' . $newIdEventoDocente;
                            $err = true;
                            break;
                        } else {

                            $esame_albo = new FormazioneEsameAlbo();
                            $esame_albo->id_esame = $newIdEsame; // ESAME FITTIZIO
                            $esame_albo->id_utente_albo = $newIdAlbo;
                            $esame_albo->esente = 1; //
                            $esame_albo->esito = ESITO_ESAME_POSITIVO;
                            $returnEsameAlbo = $esame_albo->Save();
                            $newIdEventoDocente = intval($returnEsameAlbo['lastId']);
                            if ($returnEsameAlbo['esito'] != 1) {
                                $return['punto_rottura'] = 'EsameAlbo ==>' . $newIdEventoDocente;
                                $err = true;
                                break;
                            } else {
                                
                                $numeroPatentinoArr = explode('/', $value[1]);
                                $numeroPatentino = (isset($numeroPatentinoArr[1]) ? $numeroPatentinoArr[1] : 0 );
                                
                                $certificato = new FormazioneCertificato();
                                $certificato->id_esame = $newIdEsame;
                                $certificato->id_utente_albo = $newIdAlbo;
                                $certificato->numero_certificazione = $numeroPatentino;
                                $certificato->cancellato = 0;
                                $returnCertificato = $certificato->Save();
                                $newIdCertificato = intval($returnCertificato['lastId']);
                                if ($returnCertificato['esito'] != 1) {
                                    $return['punto_rottura'] = 'Certificato ==>' . $newIdCertificato;
                                    $err = true;
                                    break;
                                }
                            }
                        }
                    }

                    $prodProcessati++;
                }
            }
        }



        $res['processati'] = $prodProcessati;
        $res['prodottiOK'] = $prodProcessatiOK;
        $res['prodottiKO'] = $prodProcessatiKO;

        $res['ultimoAggiornamento'] = $oggi->format('d/m/Y');
        $res['esito'] = 1;

        if ($err) {
            $con->db_transactionRollback();
            $return = array('esito' => -999, 'descrizioneErrore' => 'Erore nel salvataggio', 'rottura' => $return);
        } else {
            $con->db_transactionCommit();

            $return = array('esito' => 1, 'result' => $res);
        }

        Utils::print_array($return);

        return $return;
    }
    
    
    
    public function ImportAccreditamento() {
        global $con, $LoginOperatore, $App;
        $res = array();

        $file = ROOT_IMPORT . 'Accreditamenti.xls';
        
        //echo $file;
        /*
            [0] => COD
            [1] => fruttiferi
            [2] => p finite
            [3] => parti di p
            [4] => portinn
            [5] => sementi
            [6] => ortive
            [7] => piantine
            [8] => bulbi
            [9] => altro
            [10] => ornamentali1
            [11] => p finiteor
            [12] => parti di p or
            [13] => portinnor
            [14] => sementi or
         */
        
        $c = self::ParseExcel($file);
        
        //Utils::print_array($c);
        
        //exit();
         /// $codice = str_pad($codice, 4, "0", STR_PAD_LEFT);
        $codRuopTmp = 0;
        foreach ($c as $key => $value) {
            if ($key == 0 || $key >= count($c) || intval($value[0]) <= 0)
                        continue;
            $codRuop = intval(ltrim($value[0], '0'));
            //$array_coordinate_gps = array();
            echo "<br/>VALUE=".$codRuop;
            
            if($codRuopTmp != $codRuop){
                $ditta = Ditta::getDittaFromRuop($codRuop);
                //Utils::print_array($ditta);
                $codRuopTmp = $codRuop;
                echo "<br/>TMP=".$codRuopTmp;
                //echo "<br/>ragsoc=".$value[1];
                echo "<br/>Cognome=".$ditta['cognome'];
                
            }
            if($ditta['id']>0){
                echo "<br/>Inserisco:";
                $id_fruttifero = 0;
                $piante_finite = 0;
                $partidipiante = 0;
                $portinnesti = 0;
                $sementi = false;                
                if($value[1]!=""){
                    echo "<br/>Fruttiferi ->".$value[1];
                    $moltiplicazioneFruttiferi = new MoltiplicazioneFruttiferi();
                    $moltiplicazioneFruttiferi->id_ditta = $ditta['id'];
                    
                    $fruttiferi = Fruttiferi::Load($value[1]);
                    $moltiplicazioneFruttiferi->id_fruttifero = $fruttiferi[0]['id'];
                    $moltiplicazioneFruttiferi->id_art = 5;
                    $moltiplicazioneFruttiferi->codice = 'D';
                    if($value[2]){
                        echo "<br/>// PIANTE FINITE";
                        $moltiplicazioneFruttiferi->id_tipologia_produzione = 1;
                        $moltiplicazioneFruttiferi->Save();
                    }
                    if($value[3]){
                        echo "<br/>// PARTI DI PIANTE";
                        $moltiplicazioneFruttiferi->id_tipologia_produzione = 2;
                        $moltiplicazioneFruttiferi->Save();
                    }
                    if($value[4]){
                        echo "<br/>// PORTINNESTI";
                        $moltiplicazioneFruttiferi->id_tipologia_produzione = 3;
                        $moltiplicazioneFruttiferi->Save();
                    }
                    if($value[5]){
                        echo "<br/>// SEMENTI";
                        $moltiplicazioneFruttiferi->id_tipologia_produzione = 4;
                        $moltiplicazioneFruttiferi->Save();
                    }                    
                }
                if($value[6]!=""){
                    echo "<br/>Ortive ->".$value[6];
                    $moltiplicazioneOrtive = new MoltiplicazioneOrtive();
                    $moltiplicazioneOrtive->id_ditta = $ditta['id'];
                    $ortive = Ortive::Load($value[6]);
                    $moltiplicazioneOrtive->id_ortive = $ortive[0]['id'];
                    $moltiplicazioneOrtive->id_art = 7;
                    $moltiplicazioneOrtive->codice = 'E';
                    if($value[7]){
                        echo "<br/>// PIANTINE";
                        $moltiplicazioneOrtive->id_tipologia_specie = 1;
                        $moltiplicazioneOrtive->Save();
                    }
                    if($value[8]){
                        echo "<br/>// BULBI";
                        $moltiplicazioneOrtive->id_tipologia_specie = 2;
                        $moltiplicazioneOrtive->Save();
                    }
                    if($value[9]){
                        echo "<br/>// ALTRO";
                        $moltiplicazioneOrtive->id_tipologia_specie = 3;
                        $moltiplicazioneOrtive->Save();
                    }
                }
                if($value[10]!=""){
                    echo "<br/>ORNAMENTALI ->".$value[10];
                    $moltiplicazionePiante = new MoltiplicazionePiante();
                    $moltiplicazionePiante->id_ditta = $ditta['id'];
                    
                    $moltiplicazionePiante->nome = trim($value[10]);
                    $moltiplicazionePiante->id_art = 9;
                    $moltiplicazionePiante->codice = 'F';
                    if($value[11]){
                        echo "<br/>// PIANTE FINITE";
                        $moltiplicazionePiante->id_tipologia_produzione = 1;
                        $moltiplicazionePiante->Save();
                    }
                    if($value[12]){
                        echo "<br/>// PARTI DI PIANTE";
                        $moltiplicazionePiante->id_tipologia_produzione = 2;
                        $moltiplicazionePiante->Save();
                    }
                    if($value[13]){
                        echo "<br/>// PORTINNESTI";
                        $moltiplicazionePiante->id_tipologia_produzione = 3;
                        $moltiplicazionePiante->Save();
                    }
                    if($value[14]){
                        echo "<br/>// SEMENTI";
                        $moltiplicazionePiante->id_tipologia_produzione = 4;
                        $moltiplicazionePiante->Save();
                    }                    
                }
            }
        }

        return $return;
    }
    

}
