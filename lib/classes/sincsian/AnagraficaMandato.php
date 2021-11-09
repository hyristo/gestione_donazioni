<?php

require_once ROOT."lib/classes/IDataClass.php";
/**
 * La tabella contiene i dati relativi al mandato
 *
 * @author Anselmo
 */
class AnagraficaMandato extends DataClass {
    const TABLE_NAME = "public.SINC_FA_AABRMAND_TAB"; // 
    
    public $prg_scheda = 0;
    public $id_mand = 0;
    public $id_fasc = 0;
    public $cuaa = 0;
    public $id_ente = 0;
    public $id_ufficio = 0;
    public $protocollo_mand = 0;
    public $data_sott_mand = null;
    public $data_scad_mand = null;
    public $flag_revo = 0;
    public $flag_annu = 0;
    public $data_iniz_vali = null;
    public $data_fine_vali = null;
    
    
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
    
    
    
    
    
}
