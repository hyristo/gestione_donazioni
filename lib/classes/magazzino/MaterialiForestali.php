<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MaterialiForestali
 *
 * @author Anselmo
 * 
 */
require_once ROOT . "lib/classes/IDataClass.php";

class MaterialiForestali extends DataClass {

    const TABLE_NAME = 'MATERIALI_FORESTALI';

    public $ID = 0;
    public $TIPOLOGIA = "";
    public $DESCRIZIONE = "";
    public $CANCELLATO = 0;

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

    public static function autocomplete($string = '', $tipologia = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 1) {
            $where = '(UPPER("DESCRIZIONE") LIKE :term ) AND "CANCELLATO" = 0';
            if (trim($tipologia) != "")
                $where .= ($where == "" ? "" : " AND ") . '"TIPOLOGIA" = :tipologia';

            $string = "%" . strtoupper($string) . "%";

            $sql = 'SELECT "ID", "DESCRIZIONE" as label FROM "' . self::TABLE_NAME . '" WHERE ' . $where;
            $sql .= ' ORDER BY "DESCRIZIONE" ASC';
            $query = $con->prepare($sql,false);
            $query->bindParam(":term", $string);
            if (trim($tipologia) != "")
                $query->bindParam(":tipologia", $tipologia);
            try {
                $query->execute();
                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                $return['esito'] = -999;
                $return['descrizioneErrore'] = $exc->getMessage();
            }
        }
        return $return;
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
    public function Load($tipologia = "", $cancellato = 0) {
        global $con;

        $where = "";

        $order = " ORDER BY descrizione";

        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "cancellato = :cancellato";
        if (trim($tipologia) != "")
            $where .= ($where == "" ? "" : " AND ") . "tipologia = :tipologia";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        if (trim($tipologia) != "")
            $query->bindParam(":tipologia", trim($tipologia));
        if (intval($cancellato) >= 0)
            $query->bindParam(":cancellato", trim($cancellato));

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

    public static function getTiplogia() {
        global $con;
        $sql = 'SELECT DISTINCT ("TIPOLOGIA") FROM  "' . self::TABLE_NAME . '"  ORDER BY "TIPOLOGIA"';
        $query = $con->prepare($sql, false);
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
