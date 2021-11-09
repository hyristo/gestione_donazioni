<?php

require_once ROOT."lib/classes/IDataClass.php";
/**
 * Description of RappresentanteLegale
 *
 * @author Anselmo
 */
class AnagraficaUffici extends DataClass {
    const TABLE_NAME = "public.AABRENUF_TAB";
    
    public $id_ufficio = 0;
    public $descruff = "";
    public $descrestuff = "";
    public $stato = "";
    public $codi_nazi_caa = "";
    public $codi_prov_caa = "";
    public $codi_prog_caa = "";
    public $codi_regi_caa = "";
    
    
    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id_ufficio']) && $src['id_ufficio'] > 0) {
                $this->_loadAndFillObject($src['id_ufficio'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {                             
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "id_ufficio");
        }
    }
     /**
     * Restituisce tutte le aziande per le quale l'ufficio caaf possiede un mandato per poter operare
     * @global type $conSian
     * @global type $LoggedAccount
     * @param type $soggetto se impostato a false restituisce solo l'array di prg_scheda
     * @return type array()
     */
    public function loadMandatiFromUfficio($soggetto = true) {
        global $conSian, $LoggedAccount;
        $response = array();
        $filter = " id_ufficio = :id_ufficio and (data_fine_vali = '9999-12-31' or data_fine_vali is null) and flag_revo = 0 and flag_annu = 0 group by cuaa ";
        $filter = ($filter != "" ? " WHERE " : "") . $filter;
        $sql = "SELECT max(prg_scheda) as prg_scheda FROM " . AnagraficaMandato::TABLE_NAME . $filter;
        //echo $sql;exit();
        $query = $conSian->prepare($sql);                
        $query->bindParam(":id_ufficio", $LoggedAccount->ID_ENTE);
        $query->execute();                
        $row = $query->fetchAll(PDO::FETCH_ASSOC);
        //$aziende = array();
        
        foreach ($row as $value) {
            if($soggetto){
                $response[] = new AnagraficaSoggetto($value['prg_scheda']);
            }else{
                $response[] = $value['prg_scheda'];
            }
        }  
        
        
        return $response;
    }
    
    
    public function LoadDataTableCustom($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        global $conSian, $LoggedAccount;
        
        $tableJoin = AnagraficaSoggetto::TABLE_NAME." a inner join (SELECT max(b.prg_scheda) as prg_scheda, b.cuaa FROM public.SINC_FA_AABRMAND_TAB b where b.id_ufficio = ".$LoggedAccount->ID_ENTE." and (b.data_fine_vali = '9999-12-31' or b.data_fine_vali is null) and b.flag_revo = 0 and b.flag_annu = 0 group by b.cuaa)t on t.prg_scheda = a.prg_scheda and t.cuaa = a.codi_fisc ";
        
        //echo $tableJoin.' '.$searchQuery;
        
        return parent::_loadDataTableEx('a.*', $tableJoin , $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset, false, $conSian);
    }
    
   
    
    
}


class AnagraficaEnti extends DataClass {
    const TABLE_NAME = "public.AABRENTE_TAB";
    
    public $id_ente = 0;
    public $deco_tipo_ente = "";
    public $desc_ente = "";
    public $sigla_ente = "";
    public $data_iniz = "";
    public $data_fine = "";
    
    
    
    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id_ente']) && $src['id_ente'] > 0) {
                $this->_loadAndFillObject($src['id_ente'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {                             
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "id_ente");
        }
    }
     /**
     * Restituisce tutte le aziande per le quale l'ufficio caaf possiede un mandato per poter operare
     * @global type $conSian
     * @global type $LoggedAccount
     * @param type $soggetto se impostato a false restituisce solo l'array di prg_scheda
     * @return type array()
     */
    public function loadMandatiFromEnte($soggetto = true) {
        global $conSian, $LoggedAccount;
        $response = array();
        $filter = " id_ente = :id_ente and (data_fine_vali = '9999-12-31' or data_fine_vali is null) and flag_revo = 0 and flag_annu = 0 group by cuaa ";
        $filter = ($filter != "" ? " WHERE " : "") . $filter;
        $sql = "SELECT max(prg_scheda) as prg_scheda FROM " . AnagraficaMandato::TABLE_NAME . $filter;
        //echo $sql;exit();
        $query = $conSian->prepare($sql);                
        $query->bindParam(":id_ente", $LoggedAccount->ID_ENTE);
        $query->execute();                
        $row = $query->fetchAll(PDO::FETCH_ASSOC);
        //$aziende = array();
        
        foreach ($row as $value) {
            if($soggetto){
                $response[] = new AnagraficaSoggetto($value['prg_scheda']);
            }else{
                $response[] = $value['prg_scheda'];
            }
        }  
        
        
        return $response;
    }
    
    
    public function LoadDataTableCustom($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        global $conSian, $LoggedAccount;
        
        $tableJoin = AnagraficaSoggetto::TABLE_NAME." a inner join (SELECT max(b.prg_scheda) as prg_scheda, b.cuaa FROM public.SINC_FA_AABRMAND_TAB b where b.id_ente = ".$LoggedAccount->ID_ENTE." and (b.data_fine_vali = '9999-12-31' or b.data_fine_vali is null) and b.flag_revo = 0 and b.flag_annu = 0 group by b.cuaa)t on t.prg_scheda = a.prg_scheda and t.cuaa = a.codi_fisc ";
        
        //echo $tableJoin.' '.$searchQuery;
        
        return parent::_loadDataTableEx('a.*', $tableJoin , $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset, false, $conSian);
    }
    
   
    
    
}
