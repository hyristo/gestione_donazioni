<?php

require_once ROOT . "lib/classes/IDataClass.php";

/**
 * Description of RappresentanteLegale
 *
 * @author Anselmo
 */
class Isola extends DataClass {

    const TABLE_NAME = "core.isole1";

    /* public $prg_scheda = 0;
      public $codi_isol = "";
      public $id_scheda = "";
      public $data_inizio = null;
      public $data_fine = null;
      public $data_rife = null;
      public $supe = 0;
      public $shape = "";
      public $id_orpa = 0;
      public $shape_text = "";
      public $esito_trasf = 0;
      public $id_shape = 0; */

    public $id_isol = 0;
    public $id_tavc = 0;
    public $codice_isola = "";
    public $superficie = "";
    public $tipo_isola = "";
    public $data_inizio = null;
    public $data_fine = null;
    public $id_pcg_vers = 0;
    public $codice_belfiore = "";
    public $sezione = "";
    public $foglio = "";
    public $geom = "";
    public $codi_tipo_isol = 0;
    public $appezzamenti = array();

    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['prg_scheda']) && $src['prg_scheda'] > 0) {
                $this->_loadAndFillObject($src['prg_scheda'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "id_pcg_vers");
        }
    }

    public static function load($prg_scheda = 0) {
        global $conSian;
        $rows = array();

        if (!empty($prg_scheda)) {
            $filter = " id_pcg_vers = :prg_scheda and (data_fine >= :data_fine or data_fine is null)";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            //
//            $sqlView = "SELECT *,	
//                            jsonb_build_object(
//                        'type',     'FeatureCollection',
//                        'features', jsonb_build_object(
//                                                    'type',       'Feature',
//                                                    'id',         id_isol,
//                                                    'geometry',   ST_AsGeoJSON(ST_Transform(geom, 4326),15,0)::json,
//                                                    'properties', to_jsonb(row) - 'gid' - 'geom'
//                      )) AS feature
//                      FROM (
//                              $sql
//                      ) row";
            //echo $sqlView;
            $query = $conSian->prepare($sql);
            $query->bindParam(":prg_scheda", $prg_scheda);
            $query->bindParam(":data_fine", date('Y-m-d'));

            try {
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $rows;
    }

    /**
     * 
     * @global type $conSian
     * @param type $id
     * @return type
     */
    public static function loadGeoJESONByID($id = '') {
        global $conSian;
        $rows = array();

        if (!empty($id)) {
            $filter = " id_isol = :id_isol and (data_fine >= :data_fine or data_fine is null)";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            //
            $sqlView = "SELECT jsonb_build_object(
                        'type',     'FeatureCollection',
                        'features', ARRAY[jsonb_build_object(
                                                    'type',       'Feature',
                                                    'id',         id_isol,
                                                    'geometry',   ST_AsGeoJSON(ST_Transform(geom, 4326),15,0)::json,
                                                    'properties', to_jsonb(row) - 'gid' - 'geom'
                      )]) AS feature
                      FROM (
                              $sql
                      ) row";

            $query = $conSian->prepare($sqlView);
            $query->bindParam(":id_isol", $id);
            $query->bindParam(":data_fine", date('Y-m-d'));

            try {
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $rows;
    }

    /**
     * 
     * @global type $conSian
     * @param type $id
     * @return type
     */
    public static function loadGeoJESONByScheda($prg_scheda = '') {
        global $conSian;
        $rows = array();

        if (!empty($prg_scheda)) {
            $join = " left join core.tavola1 T on T.codice_belfiore = A.codice_belfiore and T.foglio = A.foglio and T.id_pcg_vers = A.id_pcg_vers ";
            $filter = " A.id_pcg_vers = :prg_scheda and (A.data_fine >= :data_fine or A.data_fine is null)";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;            
            $sql = "SELECT * FROM " . self::TABLE_NAME ." A ".$join.$filter;
            
            //echo $sql;
            //
            $sqlView = "SELECT 
                            jsonb_build_object(
                                'type',     'FeatureCollection',
                                'features', jsonb_agg(feature)
                            ) as geomJson
                            FROM (
                              SELECT	
                                    jsonb_build_object(
                                'type',       'Feature',
                                'id',         id_isol,
                                'geometry',   ST_AsGeoJSON(ST_Transform(geom, 4326),15,0)::json,
                                'properties', to_jsonb(row) - 'gid' - 'geom'
                              ) AS feature
                              FROM (
                              $sql
                      ) row) features";

            //echo $sqlView;

            $query = $conSian->prepare($sqlView);
            $query->bindParam(":prg_scheda", $prg_scheda);
            $query->bindParam(":data_fine", date('Y-m-d'));

            try {
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $rows;
    }

}

class Appezzamento extends DataClass {

    const TABLE_NAME = "core.appezzamento1";

    public $id_appe = '';
    public $id_isol = '';
    public $id_pcg_vers = '';
    public $data_iniz_appe = '';
    public $data_fine_appe = '';
    public $codi_rile = '';
    public $codi_prod_rile = '';
    public $supe_appe = '';
    public $geom = '';
    public $flag_biol = '';
    public $flag_vinc_ammi = '';
    public $flag_prat_sens = '';
    public $flag_psr = '';
    public $pendenza = '';
    public $codi_uso = '';
    public $codi_dest_uso = '';
    public $codi_usoo = '';
    public $codi_qual = '';
    public $num_dett = '';
    public $desc_prod = '';
    public $codice_belfiore = '';
    public $sezione = '';
    public $foglio = '';
    public $id_tavc = '';
    public $cod_appe = '';

    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id_appe']) && $src['id_appe'] > 0) {
                $this->_loadAndFillObject($src['id_appe'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "id_appe");
        }
    }

    /**
     * 
     * @global type $conSian
     * @param type $id_isol
     * @return type
     */
    public static function load($id_isol = 0) {
        global $conSian;
        $rows = array();

        if (!empty($id_isol)) {
            $filter = " id_isol = :id_isol ";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            $query = $conSian->prepare($sql);
            $query->bindParam(":id_isol", $id_isol);

            try {
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $rows;
    }

    /**
     * 
     * @global type $conSian
     * @param type $id_isol
     * @return type
     */
    public static function loadGeoJESONByIsola($id_isol = '',$codi_uso = '', $prg_scheda='') {
        global $conSian;
        $rows = array();
        
        if (!empty($prg_scheda)) {
            
            $filter = " A.id_pcg_vers = :id_pcg_vers ";
            if (!empty($id_isol)) {
                $filter .= " AND A.id_isol = :id_isol ";
            }
            if (!empty($codi_uso)) {
                $filter .= " AND A.codi_uso = :codi_uso ";
            }
            
            $join = " left join core.tavola1 T on T.codice_belfiore = A.codice_belfiore and T.foglio = A.foglio and T.id_pcg_vers = A.id_pcg_vers ";
            //$filter = " A.codi_uso = :codi_uso  AND A.id_pcg_vers = :id_pcg_vers  ";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME ." A ".$join.$filter;
            
            //$filter = ($filter != "" ? " WHERE " : "") . $filter;
            //$sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            //
            $sqlView = "SELECT 
                            jsonb_build_object(
                                'type',     'FeatureCollection',
                                'features', jsonb_agg(feature)
                            ) as geomJson
                            FROM (
                              SELECT	
                                    jsonb_build_object(
                                'type',       'Feature',
                                'id',         id_isol,
                                'geometry',   ST_AsGeoJSON(ST_Transform(geom, 4326),15,0)::json,
                                'properties', to_jsonb(row) - 'gid' - 'geom'
                              ) AS feature
                              FROM (
                              $sql
                      ) row) features";

            //echo $sqlView;exit();

            $query = $conSian->prepare($sqlView);
            $query->bindParam(":id_pcg_vers", $prg_scheda);            
            if (!empty($id_isol)) {
                $query->bindParam(":id_isol", $id_isol);
            }
            if (!empty($codi_uso)) {
                $query->bindParam(":codi_uso", $codi_uso);
            }
            //Utils::print_array($query);exit();
            try {
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                //Utils::print_array($rows);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $rows;
    }
    
    public static function loadGeoJESONByTrattamenti($cod_appe = array(), $prg_scheda='') {
        global $conSian;
        $rows = array();
        
        if (!empty($prg_scheda)) {
            
            $filter = " A.id_pcg_vers = :id_pcg_vers ";
            if (!empty($cod_appe) && count($cod_appe)>0) {                
                foreach ($cod_appe as $value) {
                    $in_appe.="'".$value."', ";
                }
                $in_appe.= "''";
                
                $filter .= " AND A.cod_appe in ( ".$in_appe." ) ";
            }
            
            $join = " left join core.tavola1 T on T.codice_belfiore = A.codice_belfiore and T.foglio = A.foglio and T.id_pcg_vers = A.id_pcg_vers ";
            //$filter = " A.codi_uso = :codi_uso  AND A.id_pcg_vers = :id_pcg_vers  ";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME ." A ".$join.$filter;
            
            //$filter = ($filter != "" ? " WHERE " : "") . $filter;
            //$sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            //
            $sqlView = "SELECT 
                            jsonb_build_object(
                                'type',     'FeatureCollection',
                                'features', jsonb_agg(feature)
                            ) as geomJson
                            FROM (
                              SELECT	
                                    jsonb_build_object(
                                'type',       'Feature',
                                'id',         id_isol,
                                'geometry',   ST_AsGeoJSON(ST_Transform(geom, 4326),15,0)::json,
                                'properties', to_jsonb(row) - 'gid' - 'geom'
                              ) AS feature
                              FROM (
                              $sql
                      ) row) features";

            //echo $sqlView;//exit();

            $query = $conSian->prepare($sqlView);
            $query->bindParam(":id_pcg_vers", $prg_scheda);            
            try {
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                //Utils::print_array($rows);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $rows;
    }
    

    /**
     * Restituisce l'elenco della tipologia di appezzamento di una azienda
     * @global type $conSian
     * @param type $prg_scheda
     * @return type
     */
    public function getTipoAppezzamento($prg_scheda = 0) {
        global $conSian;
        $rows = array();
        if (!empty($prg_scheda)) {
            $filter = " id_pcg_vers = :id_pcg_vers ";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT distinct codi_uso, desc_prod  FROM " . self::TABLE_NAME . $filter . ' order by desc_prod ';
            //echo $sql;
            $query = $conSian->prepare($sql);
            $query->bindParam(":id_pcg_vers", $prg_scheda);

            try {
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $rows;
    }

    /* Restituisce l'elenco degli appezzamenti tramite la descrizione 
     * @global type $conSian
     * @param type $descrizione
     * @return type
     */

    public function getAppezzamentoPerTipo($codi_uso = 0, $prg_scheda = 0) {
        global $conSian;
        $rows = array();
        if (!empty($codi_uso)) {
            $join = " left join core.tavola1 T on T.codice_belfiore = A.codice_belfiore and T.foglio = A.foglio and T.id_pcg_vers = A.id_pcg_vers ";
            $filter = " A.codi_uso = :codi_uso  AND A.id_pcg_vers = :id_pcg_vers  ";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME ." A ".$join.$filter;
            $query = $conSian->prepare($sql);
            $query->bindParam(":codi_uso", $codi_uso);
            $query->bindParam(":id_pcg_vers", $prg_scheda);

            try {
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $rows;

//        echo $descrizione;
    }

}
