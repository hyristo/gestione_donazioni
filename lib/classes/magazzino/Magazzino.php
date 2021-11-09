<?php

/**
 * Description of Magazzino
 *
 * @author Gigi
 */
require_once ROOT . "/lib/classes/IDataClass.php";

class Magazzino extends DataClass{
    const TABLE_NAME = "MAGAZZINO";

    public $ID = 0;
    public $CUAA = null; // UK
    public $NOME = null;
    public $DESCRIZIONE = null;
    public $COMUNE = null;
    public $CIVICO = null;
    public $CANCELLATO = 0;
    public $INDIRIZZO = null;
    public $CF_ACCOUNT_INSERIMENTO = null;
    public $DATA_CREAZIONE = null;
    public $DATA_MODIFICA = null;
    public $CF_ACCOUNT_MODIFICA = null;    

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
    
    public static function CountFromAzienda($cuaa = '') {
        $ret = 0;
        if (!empty($cuaa)){
            $ret = parent::_count(self::TABLE_NAME, "CUAA = " . $cuaa);
        }
        return $ret;
    }
    
    public function Load($cuaa = '', $cancellato = 0) {
        global $con;
        $where = "";
        $order = " ORDER BY NOME";
        if($id_ditta<=0)
            return array();
        
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :cancellato";
        if (!empty($cuaa))
            $where .= ($where == "" ? "" : " AND ") . "CUAA = :cuaa";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        //echo $id_ditta.$sql; exit();

        $query = $con->prepare($sql);
        if (intval($cancellato) >= 0)
            $query->bindParam(":CANCELLATO", intval($cancellato));
        if (!empty($cuaa))
            $query->bindParam(":CUAA", $cuaa);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }
    
    
    public static function autocomplete($string = '', $id_ditta , $id_magazzino = 0){
        global $con;
        $return = array();        
        if (strlen ($string) >= 3){
            $string = "%". strtoupper($string)."%"; 
//            $sql = "SELECT id, CONCAT(prodotto, ' - ', impresa, ' (',descrizione_formulazione,'-', codice_formulazione,')') as label, prodotto, impresa, descrizione_formulazione, codice_formulazione, contenuto_per_100g_di_prodotto, sostanze_attive  FROM " . self::TABLE_NAME . " WHERE (prodotto LIKE :term OR descrizione_formulazione LIKE :term) AND stato_amministrativo = 'Autorizzato' ";
            $sql = "SELECT ID, NOME AS label FROM " . self::TABLE_NAME . " WHERE CUAA = :cuaa AND UPPER(NOME) LIKE :term  AND CANCELLATO = 0 ";
            if ($id_magazzino > 0){
                $sql .= " AND ID <> :ID_MAGAZZINO" ;
            }
            $sql .= " ORDER BY nome ASC";
            $query = $con->prepare($sql);
            $query->bindParam(":TERM", $string);
            $query->bindParam(":CUAA", $id_ditta);
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
    
    public static function autocomplete_articoli($string = '', $id_magazzino = 0){
        global $con;
        $return = array();        
        if (strlen ($string) >= 3){
            $string = "%". strtoupper($string)."%"; 
//            $sql = "SELECT P.id, CONCAT(prodotto, ' - Fornito da: ', impresa, ' - Formulazione: ',descrizione_formulazione,' - Contenuto per 100g: ' , contenuto_per_100g_di_prodotto) as label, prezzo, costo, quantita, unita_misura FROM " . Movimento::TABLE_NAME . " M INNER JOIN " . MovimentoVoce::TABLE_NAME . " V ON M.id = V.id_movimento INNER JOIN " . ProdottiFitosanitari::TABLE_NAME . " P ON P.id = V.id_articolo WHERE M.id_magazzino = :id_magazzino AND (prodotto LIKE :term OR descrizione_formulazione LIKE :term) AND tipo = 1 AND stato_amministrativo = 'Autorizzato' ";
            $sql = "SELECT P.ID, CONCAT(PRODOTTO, ' - Fornito da: ', IMPRESA, ' - Formulazione: ',DESCRIZIONE_FORMULAZIONE,' - Contenuto per 100g: ' , CONTENUTO_PER_100G_DI_PRODOTTO) as label, 0 as prezzo, 0 as costo, QUANTITA, UNITA_MISURA FROM V_GIACENZE_MAGAZZINO M INNER JOIN " . ProdottiFitosanitari::TABLE_NAME . " P ON P.ID = M.ID_ARTICOLO WHERE M.ID_MAGAZZINO = :id_magazzino AND (PRODOTTO LIKE :term OR DESCRIZIONE_FORMULAZIONE LIKE :term) AND QUANTITA > 0 AND STATO_AMMINISTRATIVO = 'Autorizzato' ";
            $sql .= " ORDER BY PRODOTTO ASC";
            $query = $con->prepare($sql);
            $query->bindParam(":TERM", $string);
            $query->bindParam(":ID_MAGAZZINO", $id_magazzino);
            try {
                $query->execute();
                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $return;
    }
    
    public function aggiornaGiacenze($incrementa = 0, $idVoce = 0, $annullamovimento = 1){
        global $con;
        $it = array(
            'esito' => 0
        );
        $sql = '';
        if ($idVoce > 0){
            $voce = new MovimentoVoce($idVoce);
            $giacenza = new Giacenze($this->ID, $voce->ID_ARTICOLO);
            $sql = "";
            if ($giacenza->id > 0){
                $sql = "UPDATE " . Giacenze::TABLE_NAME . " SET QUANTITA=( QUANTITA + :quantita) WHERE ID_ARTICOLO=:id_articolo AND ID_MAGAZZINO=:id_magazzino";
            } else {                                
                $sql = "INSERT INTO " . Giacenze::TABLE_NAME . " (ID_ARTICOLO, ID_MAGAZZINO, QUANTITA, UNITA_MISURA) VALUES (:ID_ARTICOLO, :ID_MAGAZZINO, :QUANTITA, :UNITA_MISURA)";
            }
            $query = $con->prepare($sql);
            $qta = ($voce->QUANTITA*$incrementa*$annullamovimento);
            $query->bindParam(":QUANTITA", $qta);
            $query->bindParam(":ID_ARTICOLO", $voce->ID_ARTICOLO);
            $query->bindParam(":ID_MAGAZZINO", $this->ID);
            if ($giacenza->id <= 0){
                $query->bindParam(":UNITA_MISURA", $voce->UNITA_MISURA);
            }
            try {
                if ($query->execute()){
                    $it['esito'] = 1;
                }
                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                $it['esito'] = -999;
                $it['descrizioneErrore'] = $exc->getMessage();
            }
        }
        return $it;
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
        global $con, $STATO_EXPORT;
        $this->setLog();
        $vars = get_object_vars($this);
        $sendNotify = false;
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && ($v == 0 || $v == ''))
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
    
    
    /**
     * 
     * @global type $con
     * @param type $cuaa
     * @return type
     */
    public function loadFromAzienda($cuaa = ''){
        global $con;
        
        $filter = "";
        $magazzino = new Magazzino();
        if (!empty($cuaa)) {            
            $filter = " CUAA = :cuaa";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            $query = $con->prepare($sql);            
            $query->bindParam(":CUAA", $cuaa);            
            try {
                $query->execute();
                $row = $query->fetch(PDO::FETCH_ASSOC);    
                if (intval($row['ID']) > 0) {
                    Utils::FillObjectFromRow($magazzino, $row);                    
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        } 
        return $magazzino;
    }
    
    
    
    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "ID = " . $this->ID);
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {

        return parent::_LogicalDelete(self::TABLE_NAME, '"ID" = ' . $this->ID);
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