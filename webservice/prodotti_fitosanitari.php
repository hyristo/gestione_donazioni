<?php
$action=$_REQUEST["action"];

switch($action){
        case 'search':
            $term = Utils::getFromReq("term", "");
            search($term);
        break;
        case 'load':
            load();
        break; 
        case "list":
            listProdotti();
        break;
        case "import":
            aggiornaProdotti();
            break;
}
function load(){    
    $id = Utils::getFromReq("id",0);    
    $record = new ProdottiFitosanitari($id);
    exit(json_encode($record));
}

function search($term){ 
    session_write_close(); 
    $return = ProdottiFitosanitari::autocomplete($term);
    exit(html_entity_decode(json_encode($return)));
}

function listProdotti(){
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
    ## Search     
    if($searchValue != ''){
       $searchQuery = " AND (( lower( prodotto ) LIKE :prodotto) OR ( lower( descrizione_formulazione ) LIKE :formulazione ) OR ( lower( sostanze_attive ) LIKE :sostanze_attive ) )";
       $searchArray = array( 
           'PRODOTTO'=>"%$searchValue%",
           'FORMULAZIONE'=>"%$searchValue%",
           'SOSTANZE_ATTIVE'=>"%$searchValue%"
       );
    }
    
    $res = ProdottiFitosanitari::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    foreach($res['empRecords'] as $row){
        
        $onclick = 'onclick="siparsFramework.takeChargeView(\''.$row['ID'].'\',\'prodotti_fitosanitari\', \'load\' )"';

        $fnAddMod='<a rel="'.RELUPDATE.'" data-toggle="modal" data-target="#editFitofarmaco" class="btn btn-primary" href="#editFitofarmaco" '.$onclick.' ><i class="fa fa-edit"></i> </a>';
        $data[] = array(
            "id"=>$row['ID'],            
            "numero_registrazione"=>$row['NUMERO_REGISTRAZIONE'],
            "prodotto"=>$row['PRODOTTO'],
            "descrizione_formulazione"=>$row['DESCRIZIONE_FORMULAZIONE'],
            "sostanze_attive"=>$row['SOSTANZE_ATTIVE'],
            "modifica"=>$fnAddMod,
            "stato"=>$row['STATO_AMMINISTRATIVO']
        );
    }
    ## Response
    $response = array(
       "draw" => intval($draw),
       "iTotalRecords" => $res['iTotalRecords'],
       "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
       "aaData" => $data
    );

    exit(json_encode($response));
    
    
}

function aggiornaProdotti(){
    global $LoggedAccount;
    $response = Utils::initDefaultResponse();
    
    if($LoggedAccount->IsAmministratore()){
        if($_FILES['listino']['tmp_name']!=""){
            $response = ImportDati::AggiornaProdottiFitosanitari();
        }
    }
    
    exit(json_encode($response));
}



