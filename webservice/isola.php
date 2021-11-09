<?php

$mode = $_REQUEST['action'];
switch ($mode) {
    case 'geojson':
        getgeojson();
        break;
}

function getgeojson() {
    global $LoggedAccount;    
    $id_isol = $_POST['id'];
    $id_isol_app = $_POST['id_isol'];
    $prg_scheda = $_POST['prg_scheda'];
    $response = array();
    if($LoggedAccount->checkAuthorized()){
        if($id_isol!=""){
            $geo = Isola::loadGeoJESONByID($id_isol);
            
        }else if($prg_scheda !="" && $id_isol_app == ''){
            $geo = Isola::loadGeoJESONByScheda($prg_scheda);
        }else if($id_isol_app !="" && $prg_scheda !=""){
            $geo = Appezzamento::loadGeoJESONByIsola($id_isol_app, '', $prg_scheda);
        }
        $response = $geo;
    }
    
    exit(json_encode($response));
}
 