<!doctype html>
<?
include '../../lib/api.php';

define("THIS_PERMISSION", array('MAGAZZINO'));
include_once ROOT . 'layout/include_permission.php';


$LoggedAccount->checkAuthShowAziendaPage();

$azienda = $LoggedAccount->AziendaQiam;

$partita_iva = $azienda['codi_fisc'];

//$LoggedAccount->checkAuthShowAziendaPage();

//$azienda = $_POST['azienda'];
$tabActive = $_POST['tab'];

//$azienda = new AnagraficaSoggetto($azienda);
//$partita_iva = $azienda->codi_fisc;
//Utils::print_array($azienda);

$magazzino = Magazzino::loadFromAzienda($partita_iva);

//Utils::print_array($magazzino);

?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body>
        <?
        include_once ROOT . 'layout/header.php';
        ?>
        <main role="main">
            <header class="masthead masthead-page">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-sm-3 text-right">
                            <i style="font-size: 50px" class="fas fa-tractor"></i>
                        </div>
                        <div class="col-lg-9 col-sm-9">
                            <h5><?=$azienda['desc_ragi_soci']?></h5>
                            <h6><?=$azienda['codi_fisc']?></h6>
                            <h2>GESTIONE MAGAZZINO</h2>
                        </div>
                    </div>                 
                </div>
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>

            <div class="container-fluid">
                <!-- BEGIN USABILITYBAR -->
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand" href="#"><i class="fas fa-truck-monster"></i></a>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                          <a class="nav-link active" href="#" onclick="goToDashboard('index')">Pagina iniziale</a>                                                                
                          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"><i class="fas fa-angle-right"></i> Magazzino</a>
                        </div>
                    </div>
                </nav>
                <hr>
                <!-- END USABILITYBAR -->
                <div class="row flex-xl-nowrap">
                    <?
                    include_once ROOT . 'layout/navbar.php';
                    
                    ?>
                    <div class="<?= $colCssContainer ?> bd-content">                        
                        
                        <ul class="nav nav-tabs bg-primary" id="magazzinoTab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <a class="nav-link active" id="anagrafica-tab" data-toggle="tab" href="#anagrafica" role="tab" aria-controls="anagrafica" aria-selected="true">Magazzino</a>
                            </li>
                            <?
                            if($magazzino->ID > 0){
                            ?>
                            <li class="nav-item" role="presentation">
                              <a class="nav-link" id="giacenze-tab" data-toggle="tab" href="#giacenze" role="tab" aria-controls="giacenze" aria-selected="false">Giacenze</a>
                            </li>
                            <li class="nav-item" role="presentation">
                              <a class="nav-link" id="movimenti-tab" data-toggle="tab" href="#movimenti" role="tab" aria-controls="movimenti" aria-selected="false">Movimenti</a>
                            </li>                            
                            <?}?>
                        </ul>
                        <div class="tab-content" id="magazzinoTabContent">
                            <div class="tab-pane fade show active" id="anagrafica" role="tabpanel" aria-labelledby="anagrafica-tab">
                                <form action="javascript:saveMagazzino()" id="add-form-magazzino">
                                <div class="card">                            
                                    <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="NOME">Nome</label>
                                                        <input type="text" required="" autocomplete="off" class="form-control" id="magazzinoNOME" name="NOME"  placeholder="Nome">
                                                        <input type="hidden" autocomplete="off" class="form-control" id="CUAA" name="CUAA" value="<?=$partita_iva?>" ><!--Per ora imposto la ditta a 1-->
                                                        <input type="hidden" autocomplete="off" class="form-control" id="magazzinoID" name="ID"  ><!--Per ora imposto la ditta a 1-->
                                                    </div>                                        
                                                </div>
                                                <div  class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="DESCRIZIONE">Descrizione</label>
                                                        <input type="text" required="" autocomplete="off" class="form-control" id="magazzinoDESCRIZIONE" name="DESCRIZIONE"  placeholder="Descrizione">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="COMUNE">Comune</label>
                                                        <input type="text" required="" autocomplete="off" class="form-control" id="magazzinoCOMUNE" name="COMUNE"   placeholder="Comune">
                                                    </div>                                            
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="INDIRIZZO">Indirizzo</label>
                                                        <input type="text" required="" autocomplete="off" class="form-control" id="magazzinoINDIRIZZO" name="INDIRIZZO"  placeholder="Indirizzo">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Civico</label>
                                                        <input type="text" required="" autocomplete="off" class="form-control" id="magazzinoCIVICO" name="CIVICO"  placeholder="Civico">
                                                    </div>
                                                </div>
                                            </div>

                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-lg-12 text-right">
                                                <button type="submit" class="btn btn-sm btn-primary">Salva</button>
                                            </div>
                                        </div>
                                    </div>
                                    </form>

                                </div>
                                
                            </div>
                            <?
                            if($magazzino->ID > 0){
                            ?>
                            <div class="tab-pane fade" id="giacenze" role="tabpanel" aria-labelledby="giacenze-tab">
                                <div class="card">
                                    <nav class="navbar navbar-expand-lg navbar-light bg-light">                                
                                        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                            <li class="nav-item active">
                                              <span class="navbar-brand mb-0 h1">ELENCO GIACENZE</span>
                                            </li>
                                        </ul>                                
                                    </nav>
                                    <div class="card-body">                                        
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table id="listGiacenze" class="table table-striped table-bordered" style="width:100%" >
                                                    <thead>
                                                        <tr>
                                                            <th>Tipo</th>
                                                            <th>Articolo</th>
                                                            <th>Q.ta</th>
                                                            <th>U.M.</th>                                                            
                                                        </tr>
                                                    </thead>                               
                                                </table>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="movimenti" role="tabpanel" aria-labelledby="movimenti-tab">
                                <div class="card">
                                    <nav class="navbar navbar-expand-lg navbar-light bg-light">                                
                                        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                            <li class="nav-item active">
                                              <span class="navbar-brand mb-0 h1">ELENCO MOVIMENTI</span>
                                            </li>
                                        </ul>
                                        <span class="navbar-text">

                                            <button class="btn btn-sm btn-primary" type="button" onclick="goToMovimento('movimento_magazzino', '<?= $partita_iva ?>', '<?=TIPO_CARICO?>')" title="Nuovo movimento di carico" ><i class="fas fa-warehouse"></i> Nuovo carico</button>

                                            <button class="btn btn-sm btn-danger" type="button" onclick="goToMovimento('movimento_magazzino', '<?= $partita_iva ?>', '<?=TIPO_SCARICO?>')" title="Nuovo movimento di scarico" ><i class="fas fa-warehouse"></i> Nuovo scarico</button>
                                        </span>
                                    </nav>
                                    <div class="card-body">                                        
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table id="listMovimenti" class="table table-striped table-bordered"  style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>N.ro</th>
                                                            <th>Tipo</th>
                                                            <th>Causale</th>
                                                            <th>Q.ta</th>
                                                            <th>Data</th>
                                                            <th>Articolo</th>
                                                            <th>Uso</th>
                                                            <th>&nbsp;</th>
                                                        </tr>
                                                    </thead>                               
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                            <?}?>
                        </div>
                        
                    </div>
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script src="../../js/magazzino.js" type="text/javascript"></script>
        <script type="text/javascript">

            $(document).ready(function () {
                loadMagazzino();
                $('.nav-tabs a').on('click', function (e) {
                    e.preventDefault();
                    $(this).tab('show');
                });
                <?
                if(!empty($tabActive)){
                ?>
                
                $('#magazzinoTab a[href="<?=$tabActive?>"]').tab('show');
                
                <?
                }
                ?>
                        
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {                    
                    
                    switch (e.target.id) {
                        case 'giacenze-tab':
                            listGiacenze();
                            break;
                        case 'movimenti-tab':
                            listMovimenti();
                            break;
                        default:

                            break;
                    }
                    console.log(e.target.id);
                    //e.relatedTarget // previous active tab*/
                });
                
            });
            var id = '<?= $magazzino->ID ?>';
            var azienda = '<?= $partita_iva ?>';
            
            function loadMagazzino(lastId = 0) {
                $('#loader').show();
                if(lastId > 0){
                    goToMagazzino('magazzino', azienda, '#movimenti');
                    return;
                }
                var object = {
                    id: id,
                    module: 'magazzino',
                    action: 'load'
                };
                postdataClassic(WS_CALL, object, function (response) {
                    $('#loader').hide();
                    var prefix = "magazzino";
                    var risp = jQuery.parseJSON(response);
                    var form = document.getElementById('add-form-magazzino');
                    form.reset();
                    //listGiacenze();
                    Object.entries(risp).forEach(([key, value]) => {
                        $("#" + prefix + key).val(value);
                    });
                });
                
            }
            
            var dtGiacenze = null;
            var dtListMagazzino = null;            
            function listGiacenze(){

                var dt = $('#listGiacenze');
                
                if ( $.fn.dataTable.isDataTable( '#listGiacenze' ) ) {
                        dt.DataTable().ajax.reload(function (json) {});
                }else{
                    dtGiacenze = dt.DataTable({
                        'processing': true,
                        'searching': false,
                        'pageLength': 10,
                        'serverSide': true,
                        'serverMethod': 'post',
                        'ajax': {
                            'url': WS_CALL,
                            'data':{
                                'module': 'magazzino',
                                'action': 'listGiacenze',
                                'id_magazzino': id
                            }
                        },
                        'columnDefs': [                                                                                                                                                                    
                            {'width': '110px', 'targets': 3}
                        ],
                        'columns': [
            //               { data: 'edit' },
                           { data: 'tipo' },
                           { data: 'articolo' },
                           { data: 'quantita' },
                           { data: 'um' }
                        ],
                        'initComplete': function (settings, json) {
                            
                        }
                    });    
                }
            }
            
            
            function listMovimenti(){
                
                var dt = $('#listMovimenti');
                
                if ($.fn.DataTable.isDataTable( '#listMovimenti' )){
                    dt.DataTable().ajax.reload(function (json) {});
                } else {
                    dtListMagazzino = dt.DataTable({
                        'processing': true,
                        'searching': false,
                        'pageLength': 10,
                        'serverSide': true,
                        'serverMethod': 'post',
                        'ajax': {
                            'url': WS_CALL,
                            'data':{
                                'module': 'magazzino',
                                'action': 'listMovimenti',
                                'azienda': azienda,
                                'id_magazzino': id
                            }
                        },
                        'columns': [                           
                           { data: 'id' },
                           { data: 'tipo_articolo' },
                           { data: 'causale' },
                           { data: 'qta' },
                           { data: 'data_movimento'},
                           { data: 'articolo'},
                           { data: 'uso'},
                           { data: 'edit'}
                           

            //               { data: 'articolo' },
            //               { data: 'data' },
            //               { data: 'quantita' },
            //               { data: 'um' },
            //               { data: 'prezzo' },
            //               { data: 'costo' }
                        ],
                        'initComplete': function (settings, json) {
                            
                        }
                    });
                }
            }
            
            
            

        </script>
    </body>
</html>
