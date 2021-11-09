<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of QuadernodiCampagna
 *
 * @author Anselmo
 */
require_once ROOT . "lib/classes/IDataClass.php";

class QuadernodiCampagna extends DataClass {

    const TABLE_NAME = 'QDC';
    const TABLE_NAME_APPEZZAMENTO = 'QDC_APPEZZAMENTO';

    public $ID = 0;
    public $CUAA = "";
    public $ID_OPERATORE = "";
    public $ID_APPEZZAMENTO = "";
    public $TIPO_INTERVENTO = "";
    public $DATA_INTERVENTO = null;
    public $DATA_FINE_INTERVENTO = null;
    public $VOLUME_L_HA = "";
    public $VOLUME_ACQUA_UTILIZZATA = "";
    public $AUTORIZZAZIONE_TECNICA = "";
    public $ADDETTO_AL_TRATTAMENTO = "";
    public $METODO_MACCHINA = "";
    public $ACQUA_RISCIACQUO_ECCESSO = "";
    public $NOTE = "";
    public $STADIO_FENOLOGICO = "";
    public $PORTATA_IRRIGAZIONE = "";
    public $QUANTITA_IRRIGAZIONE = "";
    public $DURATA_IRRIGAZIONE = "";
    public $DATA_FINE_IRRIGAZIONE = null;
    public $CANCELLATO = 0;
    public $DATA_INTERVENTO_START = null;
    public $DATA_INTERVENTO_END = null;
    public $AVVERSITA = "";
    public $ID_PRODOTTO_FITOSANITARIO = 0;
    public $DOSE_HA = "";
    public $DOSE_UTILIZZATA = "";
    public $TEMPO_RIENTRO = "";
    public $INTERVALLO_SICUREZZA = "";
    public $COMPOSIZIONE_AZOTO = "";
    public $COMPOSIZIONE_FOSFORO = "";
    public $COMPOSIZIONE_POTASSIO = "";
    public $ID_OPERAZIONE = 0;
    public $TIPO_ARTICOLO = "";
    public $SPECIE = "";
    public $QUANTITA_UTILIZZATA = "";
    public $PRINCIPIO_ATTIVO = "";
    public $UNITA_MISURA = 0;
    public $QUANTITA_RACCOLTA = "";
    public $TIPO_PRELIEVO_ACQUA = "";

//    public $QDC_VOCI = array();

    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id']) && $src['id'] > 0) {
                $this->_loadAndFillObject($src['id'], self::TABLE_NAME, $src);
                $this->QDC_VOCI = QuadernodiCampagnaVoci::Load($src['ID']);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true);
//            $this->QDC_VOCI = QuadernodiCampagnaVoci::Load($this->id);
        }
    }

    public function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public function Load($id_azienda = 0, $id_particella = 0) {
        global $con;
        $where = "cancellato = 0";
        $order = " ORDER BY data_intervento ";
        if (intval($id_azienda) > 0)
            $where .= ($where == "" ? "" : " AND ") . "id_azienda = :id_azienda";
        if (intval($id_particella) > 0)
            $where .= ($where == "" ? "" : " AND ") . "id_particella = :id_particella";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        if (intval($id_azienda) > 0)
            $query->bindParam(":id_azienda", intval($id_azienda));
        if (intval($id_particella) > 0)
            $query->bindParam(":id_particella", intval($id_particella));

        //echo $sql; exit();
        $query = $con->prepare($sql, true);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }

    /**
     * Funzione di salvataggio generica (INSERT/UPDATE)
     * @global type $con
     * @return type
     */
    public function Save() {
        global $con;
        $vars = get_object_vars($this);
//        unset($vars['QDC_VOCI']);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && $v == 0)
                continue;
            $queryabi->bindValue(":" . $k, $v);
        }

        try {
            $it = parent::_Save($queryabi);
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    /*
     * Funzione che permette il controllo delle Operazioni che sono state effettuate 
     * su una determinata particella 
     * @return elenco completo delle operazioni
     */

    public function getOperazioni($id_particella = 0, $id_azienda = 0) {
        global $con;
        if (intval($id_particella > 0) && intval($id_azienda > 0)) {

            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id_azienda=:id_azienda AND id_particella=:id_particella ";
            $query = $con->prepare($sql);
            $query->bindParam(":id_azienda", $id_azienda);
            $query->bindParam(":id_particella", $id_particella);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $it;
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "id = " . $this->id);
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {
        $result = Utils::initDefaultResponse();
        if($this->TIPO_ARTICOLO == CLASSE_FERTILIZZANTI || $this->TIPO_ARTICOLO == CLASSE_FITOSANITARI){
            $movimento = new Movimento();
            $result = $movimento->RettificaMovimento($this->ID);
            if($result['esito'] == 1 ){
                $result = parent::_LogicalDelete(self::TABLE_NAME, '"ID" = ' . $this->ID);
            }
            
        }else{
            $result = parent::_LogicalDelete(self::TABLE_NAME, '"ID" = ' . $this->ID);
        }
//        if ($result['esito'] == 1) {
//            $resultQv = parent::_LogicalDelete(self::TABLE_NAME_APPEZZAMENTO, "id_quaderno = " . $this->id);
//            if ($resultQv['esito'] != 1) {
//                $result['esito'] = -999;
//            }
//        }
        return $result;
    }
    
    
    public function Scarico(){
        $response = Utils::initDefaultResponse();
        $magazzino = Magazzino::loadFromAzienda($this->CUAA);
        if($this->ID > 0 && $magazzino->ID > 0){
           $giacenza = Giacenze::Load($magazzino->ID, $this->ID_PRODOTTO_FITOSANITARIO, $this->TIPO_ARTICOLO);
           //Utils::print_array($giacenza);
           if($giacenza[0]['QUANTITA'] >= $this->QUANTITA_UTILIZZATA){
               $movimento = new Movimento();
               $movimento->ID_MAGAZZINO = $magazzino->ID;
               $movimento->TIPO = TIPO_SCARICO;
               $movimento->ID_CAUSALE = TIPO_SCARICO;
               $movimento->ID_ARTICOLO = $this->ID_PRODOTTO_FITOSANITARIO;
               $movimento->TIPO_ARTICOLO = $this->TIPO_ARTICOLO;
               $movimento->ID_QDC = $this->ID;
               $movimento->QUANTITA = $this->QUANTITA_UTILIZZATA;
               $movimento->UNITA_MISURA = $this->UNITA_MISURA;
               $movimento->NOTE = 'SCARICO DA REGISTRO TRATTAMENTI DAL QDC';
               $movimento->DATA_MOVIMENTO = date('Y-m-d H:i:s');               
               $response = $movimento->Save();
           }else{
               $response['erroreDescrizione'] = 'Il prodotto utilizzato non ha giacenze sufficienti';
           }
           
        }
        return $response;
    }
    
    public function RettificaScarico() {
        
        
    }

    /**
     * Funzione per la visualizzazione dei logs
     * @param type $record
     * @param type $campo
     */
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        switch ($campo) {

            default:
                array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
                break;
        }
    }

}

class QuadernodiCampagnaVoci extends DataClass {

    const TABLE_NAME = 'QDC_APPEZZAMENTO';

    public $ID_QDC = 0;
    public $ID_APPEZZAMENTO = 0;

//    public $CANCELLATO = 0;

    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['ID_QDC']) && $src['ID_QDC'] > 0) {
                $this->_loadAndFillObject($src['ID_QDC'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true);
        }
    }

    public function Load($id_quaderno = 0) {
        global $con;
//        $where = "CANCELLATO = 0";
        if (intval($id_quaderno) > 0) {
            $where .= ($where == "" ? "" : " AND ") . ' "ID_QDC"=:id_qdc';
        }
        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "") {
            $sql .= " WHERE $where ";
        }
//        echo $sql; exit();
        $query = $con->prepare($sql);
        if (intval($id_quaderno) > 0) {
            $query->bindValue(":ID_QDC", $id_quaderno);
        }
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }

    /**
     * Funzione di salvataggio generica (INSERT/UPDATE)
     * @global type $con
     * @return type
     */
    public function Save() {
        global $con;
        $vars = get_object_vars($this);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "id" && $v == 0)
                continue;
            $queryabi->bindValue(":" . $k, $v);
        }
        try {
            $it = parent::_Save($queryabi);
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "id_quaderno = " . $this->id_quaderno);
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {
        return parent::_LogicalDelete(self::TABLE_NAME, "id_quaderno = " . $this->id_quaderno);
    }

    /**
     * Funzione per la visualizzazione dei logs
     * @param type $record
     * @param type $campo
     */
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        switch ($campo) {

            default:
                array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
                break;
        }
    }

}
