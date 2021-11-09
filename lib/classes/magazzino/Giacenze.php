<?php

/**
 * Description of Giacenze
 *
 * @author Gigi
 */
require_once ROOT . "/lib/classes/IDataClass.php";

class Giacenze extends DataClass {

    const TABLE_NAME = 'V_GIACENZE_MAGAZZINO';
    const TABLE_NAME_PRODOTTI_FITOSANITARI = 'PRODOTTI_FITOSANITARI';
    const TABLE_NAME_CODICI_VARI = 'CODICI_VARI';

    public $ID_ARTICOLO = 0;
    public $TIPO_ARTICOLO = 0;
    public $ID_MAGAZZINO = 0;
    public $QUANTITA = 0;
    public $UNITA_MISURA = 0;

    public function __construct($src = null, $idMagazzino = 0, $idArticolo = 0) {
        global $con;
        if ($idMagazzino <= 0 || $idArticolo <= 0) {
            if ($src == null) {
                return;
            }
            if (is_array($src)) {
                if (isset($src['ID']) && $src['ID'] > 0) {
                    $this->_loadAndFillObject($src['ID'], self::TABLE_NAME, $src);
                } else {
                    $this->_loadByRow($src, $stripSlashes);
                }
            } elseif (intval($src)) {
                $this->_loadById($src, self::TABLE_NAME, true);
            }
        } else {
            $where = "";
            if (intval($idMagazzino) > 0)
                $where .= ($where == "" ? "" : " AND ") . "id_magazzino = :id_magazzino";
            if (intval($idArticolo) > 0)
                $where .= ($where == "" ? "" : " AND ") . "id_articolo = :id_articolo";

            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE $where";
            $query = $con->prepare($sql);
            if (intval($idMagazzino) > 0)
                $query->bindParam(":ID_MAGAZZINO", $idMagazzino);
            if (intval($idArticolo) > 0)
                $query->bindParam(":ID_ARTICOLO", $idArticolo);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                Utils::FillObjectFromRow($this, $it[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
    }

    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public function Load($idMagazzino = 0, $idArticolo = 0, $tipoArticolo = '') {
        global $con;
        $where = "";
        if ($idMagazzino <= 0 || $idArticolo <= 0 || $tipoArticolo == '') {
            return array();
        }

        if (intval($idMagazzino) > 0)
            $where .= ($where == "" ? "" : " AND ") . "id_magazzino = :id_magazzino";
        if (intval($idArticolo) > 0)
            $where .= ($where == "" ? "" : " AND ") . "id_articolo = :id_articolo";
        if (trim($tipoArticolo) != '')
            $where .= ($where == "" ? "" : " AND ") . "tipo_articolo = :tipo_articolo";

        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE $where";
        $query = $con->prepare($sql);
        if (intval($idMagazzino) > 0)
            $query->bindParam(":ID_MAGAZZINO", $idMagazzino);
        if (intval($idArticolo) > 0)
            $query->bindParam(":ID_ARTICOLO", $idArticolo);
        if (trim($tipoArticolo) != '')
            $query->bindParam(":TIPO_ARTICOLO", $tipoArticolo);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    public static function autocomplete_articolo($string = '', $id_magazzino = 0, $tipo_articolo = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 3) {
            if (intval($id_magazzino > 0)) {
                $string = "%" . strtoupper($string) . "%";

                if ($tipo_articolo == 'ProdottiFitosanitari') {
                    $select = ' select a."ID"  , a."PRODOTTO" AS label, g."UNITA_MISURA", g."QUANTITA" AS GIACENZE ';
                    $join = ' left join "' . ProdottiFitosanitari::TABLE_NAME . '" a on g."ID_ARTICOLO" = a."ID"  ';
                    $where = ' where g."ID_MAGAZZINO" = :id_magazzino and g."QUANTITA" > 0 AND a."PRODOTTO" LIKE :term and g."TIPO_ARTICOLO" = :tipo_articolo  ';

                    $order = ' ORDER BY a."PRODOTTO" ASC ';
                } elseif ($tipo_articolo == 'ProdottiFertilizzanti') {
                    $select = ' select a."ID" , a."NOME_COMMERCIALE" AS label,  g."UNITA_MISURA", g."QUANTITA" AS GIACENZE ';
                    $join = ' left join "' . ProdottiFertilizzanti::TABLE_NAME . '" a on g."ID_ARTICOLO" = a."ID"  ';
                    $where = ' where g."ID_MAGAZZINO" = :id_magazzino and g."QUANTITA" > 0 AND a."NOME_COMMERCIALE" LIKE :term and g."TIPO_ARTICOLO" = :tipo_articolo ';

                    $order = ' ORDER BY a."NOME_COMMERCIALE" ASC ';
                }

                $from = ' FROM "' . self::TABLE_NAME . '" g ';

                $sql = $select . $from . $join . $where . $order;
//echo $sql;
//            $sql = "SELECT id, CONCAT(prodotto, ' - ', impresa, ' (',descrizione_formulazione,'-', codice_formulazione,')') as label, prodotto, impresa, descrizione_formulazione, codice_formulazione, contenuto_per_100g_di_prodotto, sostanze_attive  FROM " . self::TABLE_NAME . " WHERE (prodotto LIKE :term OR descrizione_formulazione LIKE :term) AND stato_amministrativo = 'Autorizzato' ";
//                $sql = "select b.id, c.descrizione AS descrizione_unita_di_misura, a.unita_misura, CONCAT(prodotto, ' - Fornito da: ', impresa, ' - Formulazione: ',descrizione_formulazione,' - Contenuto per 100g: ' ,
//                    contenuto_per_100g_di_prodotto) as label, prodotto, impresa, descrizione_formulazione, codice_formulazione, contenuto_per_100g_di_prodotto,
//                    sostanze_attive from " . self::TABLE_NAME . " a
//                    left join " . self::TABLE_NAME_PRODOTTI_FITOSANITARI . " b on a.id_articolo = b.id 
//                    LEFT JOIN " . self::TABLE_NAME_CODICI_VARI . " c on  c.id_codice = a.unita_misura AND c.gruppo='UNITA_MISURA'
//                    where a.id_magazzino = :id_magazzino and a.quantita > 0 AND prodotto LIKE :term ";
//                $sql .= " ORDER BY prodotto ASC";
                $query = $con->prepare($sql, false);
                $query->bindParam(":term", $string);
                $query->bindParam(":id_magazzino", $id_magazzino);
                $query->bindParam(":tipo_articolo", $tipo_articolo);
                try {
                    $query->execute();
                    $return = $query->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $exc) {
                    echo $exc->getMessage();
//                $return = "ERROR";
                }
            }
        }
        return $return;
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
