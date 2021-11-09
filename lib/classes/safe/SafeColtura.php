<?php
require_once ROOT . "lib/classes/IDataClass.php";

/**
 * Classe di interfacciamento con la tabella delle coluture utilizzata da SEFE
 *
 * @author Anselmo
 */
class SafeColtura extends DataClass {
    
    const TABLE_NAME = "public.coltura";
    
    public $id = 0;
    public $specie = "";
    public $cat = "";
    public $idGruppo = 0;
    public $eliminato = 0;
    public $codi_uso = "";
    public $P = 0;
    public $PStress = 0;
    //put your code here
    
    public function __construct($src = null) {
        global $conSafe;
        if ($src == null)
            return;
        if (is_array($src)) {
            $this->_loadByRow($src, $stripSlashes);
        } else {
            // Carichiamo tramite ID
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id = :id";
            $query = $conSafe->prepare($sql);
            $query->bindParam(":id", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                Utils::FillObjectFromRow($this, $it[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
    }
    
    public function LoadUso($cod_uso = null) {         
        global $conSafe;
        $result = Utils::initDefaultResponse();
        if(!empty($cod_uso)){
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE codi_uso = :codi_uso";
            $query = $conSafe->prepare($sql);
            $result = [];
            $query->bindParam(":codi_uso", $cod_uso);
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
