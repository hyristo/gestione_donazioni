<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProdottiFertilizzanti
 *
 * @author Anselmo
 */
require_once ROOT . "lib/classes/IDataClass.php";

class ProdottiFertilizzanti extends DataClass {
    
    const TABLE_NAME = 'PRODOTTI_FERTILIZZANTI';

    public $ID = 0;
    public $NUMERO_REGISTRO = "";
    public $NOME_COMMERCIALE = "";
    public $TIPOLOGIA_CONCIME = "";
    public $FABBRICANTE = "";    
    public $CANCELLATO = 0;    
    
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
    
    public function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    /**
     * Load titoli di studio
     * 
     * * return
     * For $id => array of AnagraficaTitoliStudio, for other array of array of AnagraficaTitoliStudio
     * 
     * * params: 
     * $id (int): load single rows from id
     * $descrizione (string): filter from like descrizione
     * $tipo (int): filter from tipo
     */
    public static function load($id = 0, $descrizione = '') {
        global $con;
        $rows = array();
        $filter = " CANCELLATO = 0 ";
        $order = " ORDER NOME_COMMERCIALE ";
        if (!empty($tipo)) {
            $order = " ORDER BY NOME_COMMERCIALE";
        }
        if (!empty($id)) {
            $filter = " ID = :id";
        } else {
            if (!empty($descrizione)) {
                $filter = " NOME_COMMERCIALE LIKE %:descrizione% ";
            }            
            
        }
        $filter = ($filter != "" ? " WHERE " : "") . $filter;
        $sql = "SELECT * FROM " . self::TABLE_NAME . $filter. $order;
        //echo $sql;
        $query = $con->prepare($sql);
        if (!empty($id)) {
            $query->bindParam(":id", $id);
        } else {
            if (!empty($descrizione)) {
                $query->bindParam(":descrizione", $descrizione);
            }            
        }
        try {
            $query->execute();
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($id) && count($rows) > 0) {
                return $rows[0];
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
        return $rows;
    }

    /**
     * Autocomplete
     * * params: 
     * $string (string): filter from like descrizione
     * $tipo (int): filter from tipo
     */
    public static function autocomplete($string = '', $tipo = null) {
        global $con;
        $return = array();
        if (strlen($string) >= 3) {
            $filter = '';
            if (!empty($tipo)) {
                $filter = " TIPO = :tipo AND ";
            }
            $string = "%" . strtolower($string) . "%";
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE  " . $filter . ' LOWER( NOME_COMMERCIALE ) LIKE :term ORDER BY "NOME_COMMERCIALE"';
            $query = $con->prepare($sql);
            if (!empty($tipo)) {
                $query->bindParam(":TIPO", $tipo);
            }
            //echo $sql;
            $query->bindParam(":TERM", $string);
            try {
                $query->execute();
                while ($it = $query->fetch(PDO::FETCH_ASSOC)) {
                    $row['id'] = $it['ID'];
                    $row['text'] = $it['NOME_COMMERCIALE'];
                    $row['label'] = $it['NOME_COMMERCIALE'];                    
                    $row['articolo'] = $it;                    
                    $row['tipo_articolo'] = 'ProdottiFertilizzanti';                    
                    array_push($return, $row);
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $return;
    }
    
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        switch ($campo) {

            default:
                array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
                break;
        }
    }
    

}
