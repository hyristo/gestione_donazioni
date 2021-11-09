<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegistroTracciabilita
 *
 * @author Anselmo
 */
require_once ROOT . "lib/classes/IDataClass.php";

class RegistroTracciabilita extends DataClass {

    const TABLE_NAME = 'QDC_REGISTRO_TRACCIABILITA';

    public $ID = 0;
    public $CUAA = "";
//    public $ID_OPERATORE = 0;
    public $PROGRESSIVO = 0;
    public $TIPO_OPERAZIONE = "";
    public $SPECIE_VEGETALE = "";
    public $NOME_BOTANICO = "";
    public $NOME_COMMERCIALE = "";
    public $PAESE_PROVENIENZA = "";
    public $PAESE_DESTINAZIONE = "";
    public $NUMERO_PASSAPORTO = "";
    public $PROGRESSIVO_RIFERIMENTO = 0;
    public $QUANTITA = 0;
    public $UNITA_MISURA = "";
    public $NOTE = "";
    public $DATA_OPERAZIONE = null;
    public $DATA_INSERIMENTO = null;
    public $CF_ACCOUNT_INSERIMENTO = null;
    public $DATA_MODIFICA = null;
    public $CF_ACCOUNT_MODIFICA = null;
    public $PATH_FILE = null;

    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            $this->_loadByRow($src, $stripSlashes);
        } elseif (intval($src)) {
            $sql = 'SELECT * FROM "' . self::TABLE_NAME . '" WHERE "ID" = :id';
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

    public function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public function Load($id_azienda = 0) {
        global $con;
        $where = "";
        $order = " ORDER BY progressivo";
        if (intval($id_azienda) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "id_azienda = :id_azienda";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        //echo $sql; exit();

        $query = $con->prepare($sql);
        if (intval($id_azienda) >= 0)
            $query->bindParam(":id_azienda", intval($id_azienda));
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
     * Restituisce il totale dei ASL registrati
     *
     * @param bool $filter Se specificato, filtra i comune in funzione dei parametri specificati in esso specificato
     * @return int
     */
    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }

    /**
     * Funzione di salvataggio generica (INSERT/UPDATE)
     * @global type $con
     * @return type
     */
    public function Save() {
        global $con, $LoggedAccount;
        $vars = get_object_vars($this);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql, true);
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
        return parent::_LogicalDelete(self::TABLE_NAME, "id = " . $this->id);
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

class vGiacenzeMaterialeForestale extends DataClass {

    const TABLE_NAME = 'V_GIACENZE_MATERIALE_FORESTALE';

    public $CUAA = 0;
    public $SPECIE_VEGETALE = "";
    public $NOME_BOTANICO = "";
    public $GIACENZA = 0;
    public $UNITA_MISURA = "";
    public $ANNO = 0;

    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id']) && $src['id'] > 0) {
                $this->_loadAndFillObject($src['id'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true);
        }
    }

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
    public function Load($id_azienda = 0, $anno = 0) {
        global $con;

        $where = "";

        $order = " ORDER BY nome_botanico, anno";

        if (intval($id_azienda) > 0)
            $where .= ($where == "" ? "" : " AND ") . "id_azienda = :id_azienda";
        if (intval($anno) > 0)
            $where .= ($where == "" ? "" : " AND ") . "anno = :anno";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        if (intval($id_azienda) > 0)
            $query->bindParam(":id_azienda", intval($id_azienda));
        if (intval($anno) > 0)
            $query->bindParam(":anno", intval($anno));

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

}
