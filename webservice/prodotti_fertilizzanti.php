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
            //aggiornaProdotti();
            break;
}
function load(){    
    $id = Utils::getFromReq("id",0);    
    $record = new ProdottiFertilizzanti($id);
    $record->CANCELLATO = ($record->CANCELLATO == 0 ? 'Attivo' : 'Cancellato' );
    exit(json_encode($record));
}

function search($term){ 
    session_write_close(); 
    $return = ProdottiFertilizzanti::autocomplete($term);
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
       $searchQuery = " AND (( lower( nome_commerciale ) LIKE :search) OR ( lower( tipologia_concime ) LIKE :search ) OR ( lower( fabbricante ) LIKE :search ) )";
       $searchArray = array( 
           'SEARCH'=>"%$searchValue%"
       );
    }
    
    $res = ProdottiFertilizzanti::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    foreach($res['empRecords'] as $row){
        
        $onclick = 'onclick="siparsFramework.takeChargeView(\''.$row['ID'].'\',\'prodotti_fertilizzanti\', \'load\' )"';

        $fnAddMod='<a rel="'.RELUPDATE.'" data-toggle="modal" data-target="#editFertilizzante" class="btn btn-primary" href="#editFertilizzante" '.$onclick.' ><i class="fa fa-edit"></i> </a>';
        $data[] = array(
            "id"=>$row['ID'],            
            "numero_registro"=>$row['NUMERO_REGISTRO'],
            "nome_commerciale"=>$row['NOME_COMMERCIALE'],
            "tipologia_concime"=>$row['TIPOLOGIA_CONCIME'],
            "fabbricante"=>$row['FABBRICANTE'],
            "modifica"=>$fnAddMod,
            "cancellato"=>($row['CANCELLATO'] == 0 ? 'Attivo' : 'Cancellato' )
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
            //$response = ImportDati::AggiornaProdottiFitosanitari();
        }
    }
    
    exit(json_encode($response));
}



