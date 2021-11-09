<?php

require_once ROOT . "lib/classes/IDataClass.php";
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnagraficaComuni
 *
 * @author Anselmo
 */
class AnagraficaComuni extends DataClass {

    const TABLE_NAME = "public.AABRCOMU_TAB";

    public $codi_stat = "";
    public $codi_zona_geog = "";
    public $codi_regi = "";
    public $codi_prov = "";
    public $codi_comu = "";
    public $codi_fisc_luna = "";
    public $desc_luogo = "";
    public $codi_sigl_prov = "";
    public $desc_prov = "";
    public $flag_prov_auto = 0;
    public $flag_atti = "";
    public $data_iniz = null;
    public $data_fine = null;

    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            $this->_loadByRow($src, $stripSlashes);
        } else {
            // Carichiamo tramite ID
            $sql = "SELECT distinct * FROM " . self::TABLE_NAME . " WHERE codi_comu = :codi_comu";
            $query = $conSian->prepare($sql);
            $query->bindParam(":codi_comu", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                Utils::FillObjectFromRow($this, $it[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
    }

    public function LoadByIstat($CODICE_ISTAT) {
        global $conSian;
        $sql = "SELECT distinct * FROM " . self::TABLE_NAME . " WHERE codi_comu = :codi_comu";
        $query = $conSian->prepare($sql);
        $it = [];
        $query->bindParam(":codi_comu", $CODICE_ISTAT);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
            Utils::FillObjectFromRow($this, $it[0]);
        } catch (Exception $exc) {
            $exc->getMessage();
        }
        return $it;
    }

    public static function LoadByComune($comune = "") {
        global $conSian;
        $sql = "SELECT distinct * FROM " . self::TABLE_NAME . " WHERE LOWER(desc_luogo)=:comune"; // OR  LOWER(DESCRIZIONE) = :comune
        $query = $conSian->prepare($sql);
        $query->bindParam(":comune", strtolower(trim($comune)));

        try {
            $query->execute();
            $it = $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $exc->getMessage();
        }
        return $it;
    }

    public static function getProvinciaFromComune($DESCRIZIONE = '') {
        global $conSian;
        $response = '';
        if ($DESCRIZIONE != '') {
            $sql = "SELECT DISTINCT(codi_sigl_prov) as codice_provincia FROM " . self::TABLE_NAME . " WHERE desc_luogo = :desc_luogo";
            $query = $conSian->prepare($sql);
            $query->bindParam(":desc_luogo", strtoupper($DESCRIZIONE));
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                $response = $it[0]['codice_provincia'];
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
        return $response;
    }

    public static function autocomplete($string = '', $regione = null) {
        global $conSian;
        $return = array();
        if (strlen($string) >= 3) {
            $filter = '';
            if (!empty($regione)) {
                $filter = " codi_regi = :regione AND ";
            }
            $string = "%" . strtolower($string) . "%";
            $sql = "SELECT distinct desc_luogo, codi_comu, codi_sigl_prov FROM " . self::TABLE_NAME . " WHERE  " . $filter . " LOWER(desc_luogo) LIKE :term ORDER BY desc_luogo";
            $query = $conSian->prepare($sql);
            if (!empty($regione)) {
                $query->bindParam(":regione", $regione);
            }
            $query->bindParam(":term", $string);
            try {
                $query->execute();
                while ($it = $query->fetch(PDO::FETCH_ASSOC)) {
                    $row['id'] = $it['desc_luogo'];
                    $row['text'] = $it['desc_luogo'];
                    $row['label'] = $it['desc_luogo'];
                    $row['codice_istat'] = $it['codi_comu'];
                    $row['codice_provincia'] = $it['codi_sigl_prov'];
                    $row['cap'] = '';
                    array_push($return, $row);
                }
//                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
//                $return = "ERROR";
            }
        }
        return $return;
    }

    public static function autocompleteSigla($string = '') {
        global $conSian;
        $return = array();
        $string = "%" . strtoupper($string) . "%";
        $sql = "SELECT DISTINCT(codi_prov) as label FROM " . self::TABLE_NAME . " WHERE codi_prov LIKE :term ORDER BY label";
        $query = $conSian->prepare($sql);
        $query->bindValue(":term", $string);
        try {
            $query->execute();
            $return = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            echo $exc->getMessage();
//                $return = "ERROR";
        }
        return $return;
    }

}
