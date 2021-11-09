<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProdottiFitosanitari
 *
 * @author Anselmo
 */
require_once ROOT . "lib/classes/IDataClass.php";

class ProdottiFitosanitari extends DataClass {
    //put your code here
    
    const TABLE_NAME = 'PRODOTTI_FITOSANITARI';

    public $ID = 0;
    public $NUMERO_REGISTRAZIONE = "";
    public $PRODOTTO = "";
    public $IMPRESA = "";
    public $SEDE_LEGALE_IMPRESA = "";
    public $CAP_SEDE_LEGALE_IMPRESA = "";
    public $CITTA_SEDE_LEGALE_IMPRESA = "";
    public $PROVINCIA_SEDE_LEGALE_IMPRESA = "";
    public $SEDE_AMMINISTRATIVA_IMPRESA = "";
    public $CAP_SEDE_AMMINISTRATIVA_IMPRESA = "";
    public $CITTA_SEDE_AMMINISTRATIVA_IMPRESA = "";
    public $PROVINCIA_SEDE_AMMINISTRATIVA_IMPRESA = "";
    public $DATA_REGISTRAZIONE = NULL;
    public $SCADENZA_AUTORIZZAZIONE = "";
    public $INDICAZIONI_DI_PERICOLO = "";
    public $ATTIVITA = "";
    public $CODICE_FORMULAZIONE = "";
    public $DESCRIZIONE_FORMULAZIONE = "";
    public $SOSTANZE_ATTIVE = "";
    public $CONTENUTO_PER_CENTOG_DI_PRODOTTO = "";
    public $IP = "";
    public $PPO = "";
    public $STATO_AMMINISTRATIVO = "";
    public $MOTIVO_DELLA_REVOCA = "";
    public $DATA_DECRETO_REVOCA = NULL;
    public $DATA_DECORRENZA_REVOCA = NULL;
    
    public function __construct($src = null, $proimport = false) {
        global $con;
        if ($src == null)
            return;
        if($proimport){
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE NUMERO_REGISTRAZIONE = :numero_registrazione";
            $query = $con->prepare($sql);
            $query->bindParam(":NUMERO_REGISTRAZIONE", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                Utils::FillObjectFromRow($this, $it[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }else{
            if (is_array($src)) {
                $this->_loadByRow($src, $stripSlashes);
            } elseif (intval($src)) {
                $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ID = :id";
                $query = $con->prepare($sql);
                $query->bindParam(":ID", $src);
                try {
                    $query->execute();
                    $it = $query->fetchAll(PDO::FETCH_ASSOC);
                    Utils::FillObjectFromRow($this, $it[0]);
                } catch (Exception $exc) {
                    $exc->getMessage();
                }
            }
        }
    }
    
    public static function autocomplete($string = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 3) {
            $filter = 'Autorizzato';            
            $string = "%" . strtolower($string) . "%";
            $sql = 'SELECT *  FROM ' . self::TABLE_NAME . " WHERE ( LOWER( PRODOTTO ) LIKE :term OR LOWER( DESCRIZIONE_FORMULAZIONE ) LIKE :term ) AND STATO_AMMINISTRATIVO = :stato_amministrativo ";
            $sql .= ' ORDER BY "PRODOTTO" ASC';
            
            $query = $con->prepare($sql);
            $query->bindParam(":TERM", $string);            
            $query->bindParam(":STATO_AMMINISTRATIVO", $filter);
            try {
                $query->execute();
                while ($it = $query->fetch(PDO::FETCH_ASSOC)) {
                    $row['id'] = $it['ID'];
                    $row['text'] = $it['PRODOTTO']. " - ".$it['IMPRESA']. "(".$it['DESCRIZIONE_FORMULAZIONE']."-".$it['CODICE_FORMULAZIONE'].")";
                    $row['label'] = $it['PRODOTTO']. " - ".$it['IMPRESA']. "(".$it['DESCRIZIONE_FORMULAZIONE']."-".$it['CODICE_FORMULAZIONE'].")";
                    $row['articolo'] = $it;                    
                    $row['tipo_articolo'] = 'ProdottiFitosanitari';
                    array_push($return, $row);
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $return;
    }
    
//    public static function autocomplete($string = ''){
//        global $con;
//        $return = array();        
//        if (strlen ($string) >= 3){
//            $string = "%" . strtolower($string) . "%";
////            $sql = "SELECT id, CONCAT(prodotto, ' - ', impresa, ' (',descrizione_formulazione,'-', codice_formulazione,')') as label, prodotto, impresa, descrizione_formulazione, codice_formulazione, contenuto_per_100g_di_prodotto, sostanze_attive  FROM " . self::TABLE_NAME . " WHERE (prodotto LIKE :term OR descrizione_formulazione LIKE :term) AND stato_amministrativo = 'Autorizzato' ";
//            //$sql = "SELECT ID , CONCAT( PRODOTTO , ' - Fornito da: ', IMPRESA, ' - Formulazione: ',DESCRIZIONE_FORMULAZIONE,' - Contenuto per 100g: ' , CONTENUTO_PER_100G_DI_PRODOTTO ) AS LABEL , PRODOTTO , IMPRESA , DESCRIZIONE_FORMULAZIONE , CODICE_FORMULAZIONE , CONTENUTO_PER_100G_DI_PRODOTTO , SOSTANZE_ATTIVE  FROM " . self::TABLE_NAME . " WHERE ( PRODOTTO LIKE :term OR DESCRIZIONE_FORMULAZIONE LIKE :term ) AND STATO_AMMINISTRATIVO = :stato_amministrativo ";
//            $sql = 'SELECT ID , PRODOTTO , IMPRESA , DESCRIZIONE_FORMULAZIONE , CODICE_FORMULAZIONE , "CONTENUTO_PER_100G_DI_PRODOTTO" , SOSTANZE_ATTIVE  FROM ' . self::TABLE_NAME . " WHERE ( LOWER( PRODOTTO ) LIKE :term OR LOWER( DESCRIZIONE_FORMULAZIONE ) LIKE :term ) AND STATO_AMMINISTRATIVO = :stato_amministrativo ";
//            $sql .= ' ORDER BY "PRODOTTO" ASC';
//            
//            $query = $con->prepare($sql);
//            $query->bindParam(":TERM", $string);            
//            $query->bindParam(":STATO_AMMINISTRATIVO", 'Autorizzato');            
//            try {
//                $query->execute();
//                $return = $query->fetchAll(PDO::FETCH_ASSOC);
//            } catch (Exception $exc) {
//                echo $exc->getMessage();
////                $return = "ERROR";
//            }
//        }
//        return $return;
//    }
    
    public function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    /**
     * Elenco dei prodotti fitorfarmaci 
     * @global type $con
     * @param type $all true restituisce tutti i prodotti anche quelli per i quali la data autorizzazione è scaduta
     * @param type $attivita 
     * @param type $formulazione
     * @param type $sostanze_attive
     * @return type
     */
    public function Load($all = false, $attivita = "", $formulazione = "", $sostanze_attive = "") {
        global $con;
        $where = "STATO_AMMINISTRATIVO != 'Revocato'";
        $order = " ORDER BY PRODOTTO";
        if(!$all){
            $where .= ($where == "" ? "" : " AND ") . "SCADENZA_AUTORIZZAZIONE >= NOW()";
        }
        if (trim($attivita) !="")
            $where .= ($where == "" ? "" : " AND ") . "ATTIVITA = :attivita";
        if (trim($formulazione) !="")
            $where .= ($where == "" ? "" : " AND ") . "DESCRIZIONE_FORMULAZIONE = :descrizione_formulazione";
        if (trim($sostanze_attive) !="")
            $where .= ($where == "" ? "" : " AND ") . "SOSTANZE_ATTIVE = :sostanze_attive";
        
        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        if (trim($attivita) !="")
            $query->bindParam(":attivita", trim($attivita));
        if (trim($formulazione) !="")
            $query->bindParam(":descrizione_formulazione", trim($formulazione));
        if (trim($sostanze_attive) !="")
            $query->bindParam(":sostanze_attive", trim($sostanze_attive));
            
        //echo $sql; exit();
        $query = $con->prepare($sql);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }
    
    
    public function getAttivita(){
        global $con;
        $where = "STATO_AMMINISTRATIVO != 'Revocato' ";
        $order = " ORDER BY ATTIVITA";
        
        $sql = "SELECT DISTINCT (ATTIVITA) FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        //echo $sql; exit();
        $query = $con->prepare($sql);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
        
    }
    
     public function getFormulazione(){
        global $con;
        $where = "STATO_AMMINISTRATIVO != 'Revocato' ";
        $order = " ORDER BY DESCRIZIONE_FORMULAZIONE";
        
        $sql = "SELECT DISTINCT (DESCRIZIONE_FORMULAZIONE) FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        //echo $sql; exit();
        $query = $con->prepare($sql);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
        
    }
    
    public function getSostanzeAttive(){
        global $con;
        $where = "stato_amministrativo != 'Revocato' ";
        $order = " ORDER BY sostanze_attive";
        
        $sql = "SELECT distinct (sostanze_attive) FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        //echo $sql; exit();
        $query = $con->prepare($sql);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
        
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
