<?php

/**
 * Description of Fattura
 *
 * @author Gigi
 */
require_once ROOT . "/lib/classes/IDataClass.php";

class Fattura extends DataClass{
    const TABLE_NAME = 'FATTURA';
    public $ID = 0;
    public $ID_MAGAZZINO = 0;
    public $NUMERO = '';
    public $DATA_FATTURA = '';
    public $NOTE = '';
    public $FILENAME = '';
    public $FILEPATH = '';
    public $CANCELLATO = 0;
    public $CF_ACCOUNT_INSERIMENTO = 0;
    public $DATA_CREAZIONE = '';
    public $CF_ACCOUNT_MODIFICA = 0;
    public $DATA_MODIFICA = '';
    
    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['ID']) && $src['ID'] > 0){
                 $this->_loadAndFillObject($src['ID'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true);            
        }
    }
    
    public static function autocomplete($string = '', $id_magazzino = 0){
        global $con;
        $return = array();
        if (strlen ($string) >= 1){
            $string = "%". $string."%"; 
            $sql = "SELECT ID, NUMERO AS label FROM " . self::TABLE_NAME . " WHERE NUMERO LIKE :term AND CANCELLATO = 0 ";
            $sql .= ($id_magazzino > 0 ? " AND ID_MAGAZZINO=:id_magazzino" : "");
            $sql .= " ORDER BY numero ASC";
            $query = $con->prepare($sql);
            $query->bindParam(":TERM", $string);
            if ($id_magazzino > 0){
                $query->bindParam(":ID_MAGAZZINO", $id_magazzino);
            }
            try {
                $query->execute();
                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $return;
    }
    
    public function setLog() {
        global $LoggedAccount;
        if ($this->ID <= 0) {
            $this->CF_ACCOUNT_INSERIMENTO = $LoggedAccount->CODICE_FISCALE;
            $this->DATA_CREAZIONE = date('Y-m-d H:i:s');
        }
        $this->CF_ACCOUNT_MODIFICA = $LoggedAccount->CODICE_FISCALE;
        $this->DATA_MODIFICA = date('Y-m-d H:i:s');
    }
    
    public function Save() {
        global $con;
        $this->setLog();
        $vars = get_object_vars($this);
        $sendNotify = false;
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && $v == 0)
                continue;
//            echo ":" . $k.",".$v."<br>";
            $queryabi->bindValue(":" . $k, $v);
        }
        try {
            $it = parent::_Save($queryabi);            
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }
    
    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    public function delete(){
        global $con;
        $filter = "ID = " . $this->id;
        $ret = parent::_LogicalDelete(self::TABLE_NAME, $filter, $con);
        return $ret;
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