<?php

require_once ROOT."lib/classes/IDataClass.php";
/**
 * Description of RappresentanteLegale
 *
 * @author Anselmo
 */
class AnagraficaFascicolo extends DataClass {
    const TABLE_NAME = "public.SINC_FA_AABRFASC_TAB";
    
    public $prg_scheda = 0;
    public $id_fasc  = 0;
    public $cuaa = '';
    public $id_sogg = 0;
    public $id_orpa = 0;
    public $desc_orpa = '';
    public $id_ente = 0;
    public $deco_tipo_ente = 0;
    public $codi_ente = '';
    public $desc_ente = '';
    public $codi_regi = '';
    public $data_iniz_vali = null;
    public $data_fine_vali = null;
    public $deco_stat_vali = 0;
    public $data_ulti_vali = null;
    public $deco_stato = 0;
    public $id_ufficio = 0;
    public $desc_ufficio = 0;
    
    
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
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "prg_scheda");
        }
    }
    
    
    
     /**
     * Restituisce il MAX del fascicolo in corso
     * @global type $conSian
     * @global type $LoggedAccount
     * @return type
     */
    public function GetPrgScheda($cuaa = null){
        global $conSian, $LoggedAccount;
        $response = array();
        if (!empty($cuaa)) {
            $sql = "select max(a.prg_scheda) as prg_scheda from ".self::TABLE_NAME." a 
                    where a.cuaa = :cuaa and (a.data_fine_vali = '9999-12-31' or a.data_fine_vali is null) and a.deco_stato = ".SINC_FA_STATO_VALIDO;
            //echo $sql;
            $query = $conSian->prepare($sql);                
            $query->bindParam(":cuaa", $cuaa);

            try {
                $query->execute();                
                $row = $query->fetch(PDO::FETCH_ASSOC);
                $response = $row;
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $response;
        
    }
   
    
    
}
