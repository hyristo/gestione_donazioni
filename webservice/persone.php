<?php
$action=$_REQUEST["action"];
switch($action){
    case 'search':
        $term = Utils::getFromReq("searchTerm", "");
        search($term);
    break;
    
    case 'load':
        load();
    break; 
    case "list":        
        listCard();
    break;
    case "save":
        save();
    break;
    case "delete":
        delete();
    break;
    case "riattiva":
        riattiva();
    break;
}

exit();

function search($term){ 
    session_write_close(); 
    $return = AnagraficaPersone::autocomplete($term);
    exit(html_entity_decode(json_encode($return)));
}

function load(){    
    $id = Utils::getFromReq("id",0);          
    $record = new AnagraficaPersone($id);
    $comune = new Comune();
    $com = $comune->LoadByIstat($record->ISTAT_NASCITA); 
    //Utils::print_array($com);
    $record->TXT_COMUNE_NASCITA = $com[0]['DESCRIZIONE'];
    
    exit(json_encode($record));
}

function listCard(){    
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    
    $cl = $_POST['columns'];
    $data = array();
    $searchArray = array();
    
    for($i = 0; $i<count($cl); $i++) {
        if($cl[$i]['search']['value']!==""){
            $searchArray[$cl[$i]['data']]= $cl[$i]['search']['value'];
            //echo $cl[$i]['data']." => ".$cl[$i]['search']['value']."</br>";
        }
    }
    //echo "<pre>".print_r($cl, true)."</pre>";
    //echo "<pre>".print_r($searchArray, true)."</pre>";
    
    //exit();
    $searchQuery = "";
    if(count($searchArray)>0){
        $searchQuery .= " AND ( ";
        $where="";
        foreach ($searchArray as $key => $value) {
            $searchQuery .="";
            $where .= ($where == "" ? "" : " OR ") .$key ." = :".$key;
        }
        
       $searchQuery .= $where." )";
    }
    
    //echo $searchQuery;
    
    ## Search     
    if($searchValue != ''){
       $searchQuery .= " AND ( lower( COGNOME ) LIKE :COGNOME) ";
       $searchArray['COGNOME'] = "%$searchValue%";
    }
    //echo $searchQuery;exit();
    $res = AnagraficaPersone::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    //Utils::print_array($res);
    foreach($res['empRecords'] as $row){
        
        $onclick = 'onclick="siparsFramework.takeCharge(\''.$row['ID'].'\',\'persone\', \'load\', \'\', \'form-edit-codice\'  )"';
        
        $fnAddMod='<a rel="'.RELUPDATE.'" data-toggle="modal" data-target="#editCodice" class="btn btn-primary" href="#editCodice" '.$onclick.' ><i class="fa fa-edit"></i> </a>';
        
        
        if($row['CANCELLATO']==0){
            $onclickDisable = 'onclick="siparsFramework.takeChargeConfirm(\''.$row['ID'].'\',\'persone\', \'delete\', \'#ListCard\' )"';
            $colorBtnDisable = 'btn-success';
            $iconBtnDisable = 'fas fa-toggle-on';
            
        } else {
            $onclickDisable = 'onclick="siparsFramework.takeChargeConfirm(\''.$row['ID'].'\',\'persone\', \'riattiva\', \'#ListCard\' )"';
            $colorBtnDisable = 'btn-danger';
            $iconBtnDisable = 'fas fa-toggle-off';
        }
        
        $fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'"  class="btn '.$colorBtnDisable.'" ><i class="'.$iconBtnDisable.'"></i></a>';
        $tipo = CodiciVari::Load($row['TIPO_PERSONA'], 'TIPO_PERSONA');
        $data[] = array(
            "ID"=> $row['ID'],
            "TIPO_PERSONA"=> $tipo['DESCRIZIONE'],
            "COGNOME"=> $row['COGNOME'],
            "NOME"=>$row['NOME'],            
            "DATA_NASCITA"=>Date::FormatDate($row['DATA_NASCITA']),            
            "modifica"=>$fnAddMod,
            "cancellato"=>$fnDisable/*,
            "invisibile"=>$fnDisable*/
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

function save(){
    $response = array();
    $rec = new AnagraficaPersone($_POST);    
    $response = $rec->Save();
    exit(json_encode($response));
}


function delete(){
    $id = Utils::getFromReq("id",0);          
    $record = new AnagraficaPersone($id);    
    $return = $record->LogicalDelete();
    exit(json_encode($return));
}

function riattiva(){
    $id = Utils::getFromReq("id",0);          
    $record = new AnagraficaPersone($id);    
    $record->CANCELLATO = 0;
    $return = $record->Save();
    exit(json_encode($return));
}
