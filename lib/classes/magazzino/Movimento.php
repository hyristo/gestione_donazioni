<?php

/**
 * Description of Movimento
 *
 * @author Gigi
 */
require_once ROOT . "/lib/classes/IDataClass.php";

class Movimento extends DataClass {

    const TABLE_NAME = 'MOVIMENTO';

    public $ID = 0;
    public $ID_MAGAZZINO = 0;
    public $FORNITORE = '';
    public $DOCUMENTO_ACQUISTO = '';
    public $DATA_ACQUISTO = null;
    public $DATA_MOVIMENTO = null;
    public $TIPO = 0;
    public $ID_CAUSALE = 0;
    public $ID_ARTICOLO = 0;
    public $TIPO_ARTICOLO = 0;
    public $PREZZO = 0;
    public $COSTO = 0;
    public $QUANTITA = 0;
    public $UNITA_MISURA = 0;
    public $NOTE = '';
    public $CANCELLATO = 0;
    public $CF_ACCOUNT_INSERIMENTO = 0;
    public $DATA_CREAZIONE = null;
    public $CF_ACCOUNT_MODIFICA = 0;
    public $DATA_MODIFICA = null;
    public $DESTINAZIONE_USO = '';
    public $ID_QDC = 0;
    public $PATH_FILE = "";

    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['ID']) && $src['ID'] > 0) {
                $this->_loadAndFillObject($src['ID'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true);
        }
    }

    public function setLog() {
        global $LoggedAccount;
        if ($this->ID <= 0) {
            $this->CF_ACCOUNT_INSERIMENTO = $LoggedAccount->CODICE_FISCALE;
            $this->DATA_CREAZIONE = date('Y-m-d H:i:s');
        } else {
            $this->CF_ACCOUNT_MODIFICA = $LoggedAccount->CODICE_FISCALE;
            $this->DATA_MODIFICA = date('Y-m-d H:i:s');
        }
    }

    public function Save() {
        global $con, $STATO_EXPORT;
        $this->setLog();
        $vars = get_object_vars($this);
        $sendNotify = false;
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        //echo $sql;
        //Utils::print_array($vars);        
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

    public static function LoadDataTableEx($field = "*", $DBtable = self::TABLE_NAME, $searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTableEx($field, $DBtable, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

//    
//    public function Scarico($qdc = null){
//        $response = Utils::initDefaultResponse();
//        $magazzino = Magazzino::loadFromAzienda($qdc->CUAA);
//        if(!empty($qdc) && $magazzino->ID > 0){
//           $giacenza = Giacenze::Load($magazzino->ID, $qdc->ID_PRODOTTO_FITOSANITARIO, $qdc->TIPO_ARTICOLO);
//           if($giacenza[0]['QUANTITA']<= $qdc->QUANTITA_UTILIZZATA){
//               $this->ID_MAGAZZINO = $magazzino->ID;
//               $this->TIPO = TIPO_SCARICO;
//               $this->ID_CAUSALE = TIPO_SCARICO;
//               $this->ID_ARTICOLO = $qdc->ID_PRODOTTO_FITOSANITARIO;
//               $this->TIPO_ARTICOLO = $qdc->TIPO_ARTICOLO;
//               $this->ID_QDC = $qdc->ID;
//               $this->QUANTITA = $qdc->QUANTITA_UTILIZZATA;
//               $this->UNITA_MISURA = $qdc->UNITA_MISURA;
//               $this->NOTE = 'SCARICO DA REGISTRO TRATTAMENTI DAL QDC';
//               $this->DATA_MOVIMENTO = date('Y-m-d H:i:s');               
//               $response = $this->Save();
//               
//           }
//           
//        }
//        return $response;
//    }

    public function RettificaMovimento($id_qdc) {
        global $con;
        $this->_loadById($id_qdc, self::TABLE_NAME, true, $con, 'ID_QDC');
        $this->ID = 0;
        $this->TIPO = ($this->TIPO == TIPO_CARICO ? TIPO_SCARICO : TIPO_CARICO);
        $this->ID_CAUSALE = ($this->ID_CAUSALE == TIPO_CARICO ? TIPO_SCARICO : TIPO_CARICO);
        $this->NOTE = 'RETTIFICA MOVIMENTO DA REGISTRO TRATTAMENTI DAL QDC';
        $this->DATA_MOVIMENTO = date('Y-m-d H:i:s');
        $response = $this->Save();
        return $response;
    }

    public function delete() {
        global $con;
        $filter = "ID = " . $this->ID;
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
