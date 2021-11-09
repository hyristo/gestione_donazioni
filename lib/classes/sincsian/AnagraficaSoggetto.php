<?php

require_once ROOT . "lib/classes/IDataClass.php";

/**
 * Description of RappresentanteLegale
 *
 * @author Anselmo
 */
class AnagraficaSoggetto extends DataClass {

    const TABLE_NAME = "public.SINC_FA_AABRSOGG_TAB";

    public $prg_scheda = 0; // prendere sembre il valore MAX del prg_scheda come key
    public $id_sogg = 0;
    public $codi_fisc = 0;
    public $flag_pers_fisi = 0;
    public $deco_form_giur = 0;
    public $desc_cogn = 0;
    public $desc_nome = 0;
    public $desc_ragi_soci = 0;
    public $data_nasc = null;
    public $codi_belf_nasc = 0;
    public $codi_sigl_prov_nasc = 0;
    public $desc_comu_nasc = 0;
    public $codi_ista_comu = 0;
    public $codi_ista_prov = 0;
    public $codi_sess = 0;
    public $data_mort = null;
    public $deco_font_dato = 0;
    public $deco_font_data_mort = 0;
    public $deco_live_vali = 0;
    public $data_at = null;
    public $recapiti = array();
    public $recapitiTelefonici = array();
    public $recapitiPec = array();
    public $isoleParticelle = array();

    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['prg_scheda']) && $src['prg_scheda'] > 0) {
                $this->_loadAndFillObject($src['prg_scheda'], self::TABLE_NAME, $src, $conSian, "prg_scheda");
            } else {
                $this->_loadByRow($src);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "prg_scheda");
        }
        $this->recapiti = RecapitiGeograficiSoggetto::load($this->prg_scheda);
        $this->recapitiTelefonici = RecapitiTelefoniciSoggetto::load($this->prg_scheda);
        $this->recapitiPec = RecapitiPecSoggetto::load($this->prg_scheda);
        $this->isoleParticelle = Isola::load($this->prg_scheda);
    }

    public static function Load($prg_scheda = 0, $flag_pers_fisi = 0) {
        global $conSian;
        $anagrafica = new AnagraficaSoggetto();
        if ($prg_scheda > 0) {
            $filter = " prg_scheda = :prg_scheda and flag_pers_fisi = :flag_pers_fisi and (data_mort = '9999-12-31' or data_mort is null)";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            $query = $conSian->prepare($sql);
            $query->bindParam(":prg_scheda", $prg_scheda);
            $query->bindParam(":flag_pers_fisi", $flag_pers_fisi);
            try {
                $query->execute();
                $rows = $query->fetch(PDO::FETCH_ASSOC);
                $anagrafica->_loadByRow($rows);
                $anagrafica->recapiti = RecapitiGeograficiSoggetto::load($anagrafica->prg_scheda);
                $anagrafica->recapitiTelefonici = RecapitiTelefoniciSoggetto::load($anagrafica->prg_scheda);
                $anagrafica->recapitiPec = RecapitiPecSoggetto::load($anagrafica->prg_scheda);
                $anagrafica->isoleParticelle = Isola::load($anagrafica->prg_scheda);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $anagrafica;
    }

    /**
     * 
     * @global type $conSian
     * @global type $LoggedAccount
     * @param type $cfLegale
     * @return type
     */
    public function GetPrgScheda($cfLegale = null, $soggetto = false) {
        global $conSian, $LoggedAccount;
        $response = array();
        if (!empty($cfLegale)) {
            $codice_fiscle = $cfLegale;
        } else {
            $codice_fiscle = $LoggedAccount->CODICE_FISCALE;
        }
        if (!empty($codice_fiscle)) {

            $sql = "select max(a.prg_scheda) as prg_scheda from " . AnagraficaSoggetto::TABLE_NAME . " a                             
                    where a.codi_fisc = :codi_fisc and (a.data_mort = '9999-12-31' or a.data_mort is null)";
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
     * Restituisce l'anagrafica dell'azienda del cuaa passato, fa il controllo se l'utente è autorizzato a visualizzare e se il post è scaduto
     * @global type $LoggedAccount
     * @return type
     */
    public static function GetAziendaFromPost(){
        global $LoggedAccount;
        
        $cuaa = $_POST['azienda'];
        if($cuaa!=""){            
            $anagraficaSoggetto = new AnagraficaSoggetto();
            $azienda = $anagraficaSoggetto->GetPrgScheda($cuaa, true);
            $LoggedAccount->checkAuthShowAziendaPage($azienda->prg_scheda);
        }else{            
            Utils::RedirectTo(HTTP_PRIVATE_SECTION);
        }
        return $azienda;
    }
    /**
     * Recupero il legale rappresentante da un fascicolo
     * @param type $prg_sc
     * @return type
     */
    public  static function GetLegaleRappresententeFromPrgScheda($prg_sc = null){
        //$response = array();
        
        if (!empty($prg_sc)) {
            $prg_scheda = $prg_sc;
        } /* else {
            $prg_scheda = $this->prg_scheda;
        } */
        $response = AnagraficaSoggetto::Load(intval($prg_scheda), 1);
        
       return  $response;
    }

}

class RecapitiGeograficiSoggetto extends DataClass {

    const TABLE_NAME = "public.SINC_FA_AABRRECA_TAB";

    public $prg_scheda = 0;
    public $id_reca = 0;
    public $id_sogg = 0;
    public $deco_tipo_reca = 0;
    public $deco_font_dato = 0;
    public $deco_live_vali = 0;
    public $data_iniz_reca = null;
    public $data_fine_reca = null;
    public $desc_geog_strd = 0;
    public $desc_geog_civi = 0;
    public $codi_geog_belf = 0;
    public $codi_geog_capp = 0;
    public $desc_geog_fraz = 0;
    public $codi_geog_sigl_prov = 0;
    public $desc_geog_comu = 0;
    public $deco_stato = 0;
    public $flag_noti = 0;

    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id_sogg']) && $src['id_sogg'] > 0) {
                $this->_loadAndFillObject($src['id_sogg'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "id_sogg");
        }
    }

    public static function load($prg_scheda = 0, $flag_noti = 1) {
        global $conSian;
        $rows = array();

        if (!empty($prg_scheda)) {
            $filter = " prg_scheda = :prg_scheda and flag_noti = :flag_noti and (data_fine_reca = '9999-12-31' or data_fine_reca is null) and deco_stato = " . SINC_FA_STATO_VALIDO;
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            $query = $conSian->prepare($sql);
            $query->bindParam(":prg_scheda", $prg_scheda);
            $query->bindParam(":flag_noti", $flag_noti);

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

class RecapitiTelefoniciSoggetto extends DataClass {

    const TABLE_NAME = "public.SINC_FA_AABRTELE_TAB";

    public $prg_scheda = 0;
    public $id_tele = 0;
    public $id_sogg = 0;
    public $deco_tipo_reca = 0;
    public $desc_nume_tele = 0;
    public $deco_font_dato = 0;
    public $deco_stato = 0;
    public $deco_live_vali = 0;
    public $data_iniz_tele = null;
    public $data_fine_tele = null;

    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id_sogg']) && $src['id_sogg'] > 0) {
                $this->_loadAndFillObject($src['id_sogg'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "id_sogg");
        }
    }

    public static function load($prg_scheda = 0) {
        global $conSian;
        $rows = array();

        if (!empty($prg_scheda)) {
            $filter = " prg_scheda = :prg_scheda and (data_fine_tele = '9999-12-31' or data_fine_tele is null) and deco_stato = " . SINC_FA_STATO_VALIDO;
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            $query = $conSian->prepare($sql);
            $query->bindParam(":prg_scheda", $prg_scheda);

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

class RecapitiPecSoggetto extends DataClass {

    const TABLE_NAME = "public.SINC_FA_AABRMAIL_TAB";

    public $prg_scheda = 0;
    public $id_mail = 0;
    public $id_sogg = 0;
    public $deco_tipo_reca = 0;
    public $deco_font_dato = 0;
    public $deco_live_vali = 0;
    public $data_iniz_mail = null;
    public $data_fine_mail = null;
    public $desc_mail_acnt = '';
    public $flag_mail_cert = 0;

    public function __construct($src = null) {
        global $conSian;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['id_sogg']) && $src['id_sogg'] > 0) {
                $this->_loadAndFillObject($src['id_sogg'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true, $conSian, "id_sogg");
        }
    }

    public static function load($prg_scheda = 0) {
        global $conSian;
        $rows = array();

        if (!empty($prg_scheda)) {
            $filter = " prg_scheda = :prg_scheda and (data_fine_mail = '9999-12-31' or data_fine_mail is null)";
            $filter = ($filter != "" ? " WHERE " : "") . $filter;
            $sql = "SELECT * FROM " . self::TABLE_NAME . $filter;
            //echo $sql;
            $query = $conSian->prepare($sql);
            $query->bindParam(":prg_scheda", $prg_scheda);

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
