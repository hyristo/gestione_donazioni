<?php

require_once ROOT."lib/classes/IDataClass.php";
/**
 * Description of RappresentanteLegale
 *
 * @author Anselmo
 */
class RappresentanteLegale extends DataClass {
    const TABLE_NAME = "public.SINC_FA_AABRRLEG_TAB";
    
    public $prg_scheda = 0;
    public $id_inca = 0;
    public $id_sogg_fisi = 0;
    public $id_sogg_giur = 0;
    public $deco_tipo_inca = 0;
    public $deco_font_dato = 0;
    public $deco_live_vali = 0;
    public $data_iniz_inca = null;
    public $data_fine_inca = null;
    public $deco_stato = 0;
    public $flag_pubbl = 0;
    public $flag_dete_cont = 0;
    public $data_iniz_dete_cont = null;
    public $data_fine_dete_cont = null;
    
    
    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id_sogg_fisi']) && $src['id_sogg_fisi'] > 0) {
                $this->_loadAndFillObject($src['id_sogg_fisi'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "id_sogg_fisi");
        }
    }
    
    /**
     * Restituisce il MAX del fascicolo in corso
     * @global type $conSian
     * @global type $LoggedAccount
     * @return type
     */
    public function GetPrgScheda($cfLegale = null, $soggetto = false){
        global $conSian, $LoggedAccount;
        $response = array();
        if(!empty($cfLegale)){
            $codice_fiscle = $cfLegale;
        }else{
            $codice_fiscle = $LoggedAccount->CODICE_FISCALE;
        }

        if (!empty($codice_fiscle)) {
            
            $sql = "select max(a.prg_scheda) as prg_scheda, max(b.id_sogg_fisi) as id_sogg_fisi from ".AnagraficaSoggetto::TABLE_NAME." a 
                            JOIN ".RappresentanteLegale::TABLE_NAME." b on b.id_sogg_fisi = a.id_sogg
                    where a.codi_fisc = :codi_fisc";
            echo $sql;
            $query = $conSian->prepare($sql);                
            $query->bindParam(":codi_fisc", $codice_fiscle);

            try {
                $query->execute();                
                $row = $query->fetch(PDO::FETCH_ASSOC);
                if ($soggetto) {
                    $response = new AnagraficaSoggetto($row['prg_scheda']);
                } else {
                    $response = $row;
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $response;
        
    }
    
    /**
     * Restituisce tutte le anagrafiche delle aziende collegato al rappresentante legale
     * @global type $conSian
     * @global type $LoggedAccount
     * @param type $cfLegale
     * @param type $soggetto se impostato a false restituisce solo l'elenco delle prg_scheda collegate
     * @return type
     */
    public static function loadAziendeCollegateFromCodiceFiscale($cfLegale = null, $soggetto = true){        
        global $conSian, $LoggedAccount;
        $response = array();
        if(!empty($cfLegale)){
            $codice_fiscle = $cfLegale;
        }else{
            $codice_fiscle = $LoggedAccount->CODICE_FISCALE;
        }

        if (!empty($codice_fiscle)) {
            
            $sql = "select max(a.prg_scheda) as prg_scheda from ".AnagraficaFascicolo::TABLE_NAME." a where 
                    (a.data_fine_vali = '9999-12-31' or a.data_fine_vali is null) and a.deco_stato = ".SINC_FA_STATO_VALIDO." 
                    and (
                        a.id_sogg in(
                            select b.id_sogg_giur from ".AnagraficaSoggetto::TABLE_NAME." a 
                                JOIN ".self::TABLE_NAME." b on b.id_sogg_fisi = a.id_sogg and (b.data_fine_inca = '9999-12-31' or b.data_fine_inca is null)
                                where a.codi_fisc = :codi_fisc 
                                and a.id_sogg in (
                                    select aaa.id_sogg_fisi from ".self::TABLE_NAME." aaa where aaa.prg_scheda = (
                                        select max(aa.prg_scheda) from ".self::TABLE_NAME." aa where aa.id_sogg_giur = b.id_sogg_giur
                                    )
                                )
                                and (a.data_mort = '9999-12-31' or a.data_mort is null)
                            group by b.id_sogg_giur
                        )
                        or a.id_sogg in(
                                select id_sogg from ".AnagraficaFascicolo::TABLE_NAME." where cuaa = :codi_fisc group by id_sogg
                            )
                    )
                    group by a.cuaa ";
            //echo $sql;
            $query = $conSian->prepare($sql);                
            $query->bindParam(":codi_fisc", $codice_fiscle);

            try {
                $query->execute();                
                $row = $query->fetchAll(PDO::FETCH_ASSOC);                
                foreach ($row as $value) {
                    if($soggetto){
                        $response[] = new AnagraficaSoggetto($value['prg_scheda']);                    
                    }else{
                        $response[] = $value['prg_scheda'];
                    }
                }                
                
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $response;
        
    }
    
    /**
     * Restituisce l'anagrafica del soggetto 
     * @global type $conSian
     * @global type $LoggedAccount
     * @param type $cfLegale
     * @return type
     */
    public static function GetAnagraficaSoggetto($cfLegale = null){        
        global $conSian, $LoggedAccount;
        $response = array();
        if(!empty($cfLegale)){
            $codice_fiscle = $cfLegale;
        }else{
            $codice_fiscle = $LoggedAccount->CODICE_FISCALE;
        }
        $sql = "select max(a.prg_scheda) as prg_scheda from ".AnagraficaSoggetto::TABLE_NAME." a 
                    JOIN ".RappresentanteLegale::TABLE_NAME." b on b.id_sogg_fisi = a.id_sogg and (b.data_fine_inca = '9999-12-31' or b.data_fine_inca is null)
                    where a.codi_fisc = :codi_fisc group by a.id_sogg ";
            $query = $conSian->prepare($sql);                
            $query->bindParam(":codi_fisc", $codice_fiscle);
            try {
                $query->execute();                
                $row = $query->fetch(PDO::FETCH_ASSOC);                
                //Utils::print_array($row);
                $response = AnagraficaSoggetto::Load(intval($row['prg_scheda']), 1);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        return $response;
    }
    
    
    
}
