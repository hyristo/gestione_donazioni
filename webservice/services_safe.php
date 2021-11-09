<?php
$action=$_REQUEST["action"];

switch($action){
    
    case 'listFasi':
        listFasi();
    break;    
}

exit();


function listFasi(){ 
    
    $response = array();    
    $codi_uso = intval($_POST['codi_uso']);
    if($codi_uso>0){
        $safe = new SafeFase();
        $response = $safe->GetFasiFenologiche($codi_uso);
    }
    //Utils::print_array($response);
    exit(json_encode($response));
    
}