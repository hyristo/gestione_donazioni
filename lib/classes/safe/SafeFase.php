<?php
require_once ROOT . "lib/classes/IDataClass.php";

/**
 * Classe di interfacciamento con la tabella delle Fase utilizzata da SEFE
 *
 * @author Anselmo
 */
class SafeFase extends DataClass {
    
    const TABLE_NAME = "public.fase";
    
    public $idColtura = 0;
    public $num = "";
    public $faseFeno = "";    
    public $eliminato = 0;
    //put your code here
    
    public function __construct($src = null) {
        global $conSafe;
        if ($src == null)
            return;
        if (is_array($src)) {
            $this->_loadByRow($src, $stripSlashes);
        } 
    }
    
    public function GetFasiFenologiche($codi_uso = null) {         
        global $conSafe;
        $result = Utils::initDefaultResponse();
        if(!empty($codi_uso)){
            //$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE idColtura = :idColtura";
            $sql = 'select distinct f.* from '. self::TABLE_NAME .' f inner join '. SafeColtura::TABLE_NAME .' c on c.id = f."idColtura" where c.codi_uso = :codi_uso and c.eliminato = 0 and f.eliminato = 0 ';
            
            $query = $conSafe->prepare($sql);
            $result = [];
            $query->bindParam(":codi_uso", $codi_uso);
            try {
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                Utils::FillObjectFromRow($this, $result[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
        return $result;
    }
    
    
}
