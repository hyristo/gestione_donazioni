<!doctype html>
<?
include '../../lib/api.php';


define("THIS_PERMISSION", array('MAGAZZINO', 'TRATTAMENTI'));
include_once ROOT . 'layout/include_permission.php';
$LoggedAccount->checkAuthShowAziendaPage();

$azienda = $LoggedAccount->AziendaQiam;
//$azienda = AnagraficaSoggetto::GetAziendaFromPost();
$partita_iva =  $azienda['codi_fisc'];
$prg_scheda = $azienda['prg_scheda'];
//$prg_scheda = 202537;

//$LoggedAccount->checkAuthShowAziendaPage($prg_scheda);
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
                        <div class="col-9 text-right">
                            <h2>QUADERNO DI CAMPAGNA</h2>
                            <h3>Il software online per l'agricoltura sostenibile</h3>
                            <small>ti aiuta a gestire la tua azienda agricola, <br/>rendendola efficiente e conforme alla normativa vigente</small>
                        </div>
                        <div class="col-3">
                            <i style="font-size: 155px" class="fas fa-tractor"></i>
                        </div>                    
                    </div>                  
                </div>
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>

            <div class="container-fluid">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand" href="#"><i class="fas fa-truck-monster"></i></a>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                            <a class="nav-link active" href="#" onclick="goToDashboard('index')">Pagina iniziale</a>                                                                
                            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"><i class="fas fa-angle-right"></i> Registro Trattamenti</a>
                            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"><i class="fas fa-angle-right"></i> <?=$partita_iva?></a>
                        </div>
                    </div>
                </nav>
                <div class="row flex-xl-nowrap">
                    <?
                    include_once ROOT . 'layout/navbar.php';
                    
                    ?>
                    <div class="<?= $colCssContainer ?> bd-content">
                        
                        <div class="row">
                            <div class="col-lg-12">                                
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <div class="row p-0">
                                            <div class="col-lg-10">
                                                <ul class="nav nav-tabs card-header-tabs" id="tabList" role="tablist" >
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link active"  id="difesaContent-tab" data-toggle="tab" href="#difesaContent" role="tab" aria-controls="difesaContent" aria-selected="true">
                                                            <i class="fas fa-shield-alt"></i>&nbsp;Difesa
                                                        </a>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link"  data-toggle="tab" id="nutrizioneContent-tab" href="#nutrizioneContent" role="tab" aria-controls="nutrizioneContent" aria-selected="false">
                                                            <i class="fab fa-nutritionix"></i>&nbsp;Nutrizione                                         
                                                        </a>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link"  id="irrigazioneContent-tab" data-toggle="tab" href="#irrigazioneContent" role="tab" aria-controls="irrigazioneContent" aria-selected="false">                                              
                                                            <i class="fas fa-shower"></i>&nbsp;Irrigazione
                                                        </a>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link"  id="operazioneContent-tab" data-toggle="tab" href="#operazioneContent" role="tab" aria-controls="operazioneContent" aria-selected="false">                                              
                                                            <i class="fas fa-exchange-alt"></i>&nbsp;Operazione
                                                        </a>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link"  id="raccoltaContent-tab" data-toggle="tab" href="#raccoltaContent" role="tab" aria-controls="raccoltaContent" aria-selected="false">                                              
                                                            <i class="fas fa-carrot"></i>&nbsp;Raccolta
                                                        </a>
                                                    </li>                                            
                                                </ul>
                                            </div>
                                            <div class="col-lg-2 text-right">
                                                <button type="button" class="btn btn-sm btn-warning"  onclick="goMovimento('<?= $partita_iva ?>', '<?=$prg_scheda?>')" ><i class="fas fa-plus-square"></i>&nbsp;Nuovo </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content"  id="tabListContent">
                                        <div class="card-body tab-pane fade show active" id="difesaContent" role="tabpanel"  aria-labelledby="difesaContent-tab">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>
                                                        Elenco dei trattamenti di difesa<br>
                                                    </label>
                                                </div>                                                
                                            </div>
                                            <table id="listDifesa" class="table table-striped table-bordered" style="width:100%" >
                                                <thead>
                                                    <tr>
                                                        <th>Specie</th>
                                                        <th>Data intervento</th>
                                                        <th>Tipo intervento</th>
                                                        <th>Gestione</th>
                                                    </tr>
                                                </thead>                               
                                            </table>
                                        </div>
                                        <div class="card-body tab-pane fade" id="nutrizioneContent" role="tabpanel" aria-labelledby="nutrizioneContent-tab">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>
                                                        Elenco dei trattamenti di nutrizione<br>
                                                    </label>
                                                </div>                                                
                                            </div>
                                            <table id="listNutrizione" class="table table-striped table-bordered" style="width:100%" >
                                                <thead>
                                                    <tr>
                                                        <th>Specie</th>
                                                        <th>Data intervento</th>
                                                        <th>Tipo intervento</th>
                                                        <th>Gestione</th>
                                                    </tr>
                                                </thead>                               
                                            </table>
                                        </div>
                                        <div class="card-body tab-pane fade" id="irrigazioneContent" role="tabpanel" aria-labelledby="irrigazioneContent-tab">                                        
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>
                                                        Elenco dei trattamenti di irrigazione<br>
                                                    </label>
                                                </div>                                                
                                            </div>

                                            <table id="listIrrigazione" class="table table-striped table-bordered" style="width:100%" >
                                                <thead>
                                                    <tr>
                                                        <th>Specie</th>
                                                        <th>Data intervento</th>
                                                        <th>Tipo intervento</th>
                                                        <th>Gestione</th>
                                                    </tr>
                                                </thead>                               
                                            </table>
                                        </div>
                                        <div class="card-body tab-pane fade" id="operazioneContent" role="tabpanel" aria-labelledby="operazioneContent-tab">                                        
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>
                                                        Elenco dei trattamenti di operazioni varie<br>
                                                    </label>
                                                </div>                                                
                                            </div>
                                            <table id="listOperazione" class="table table-striped table-bordered" style="width:100%" >
                                                <thead>
                                                    <tr>
                                                        <th>Specie</th>
                                                        <th>Data intervento</th>
                                                        <th>Tipo intervento</th>
                                                        <th>Gestione</th>
                                                    </tr>
                                                </thead>                               
                                            </table>
                                        </div>
                                        <div class="card-body tab-pane fade" id="raccoltaContent" role="tabpanel" aria-labelledby="raccoltaContent-tab">                                        
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label>
                                                        Elenco dei raccolti<br>
                                                    </label>
                                                </div>
                                            </div>
                                            <table id="listRaccolta" class="table table-striped table-bordered" style="width:100%" >
                                                <thead>
                                                    <tr>
                                                        <th>Specie</th>
                                                        <th>Data raccolta</th>
                                                        <th>Tipo intervento</th>
                                                        <th>Q.ta (Kg.)</th>
                                                        <th>Gestione</th>
                                                    </tr>
                                                </thead>                               
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <input type="hidden" id="elementDelete">
                        </div>
                        <div id="mapsModal" class="modal fade"  tabindex="-1" aria-labelledby="mapsModalLabel" aria-hidden="true"> 
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div id="mapsid" class="modal-content white-text"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="dettaglioModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document"  style="width: 75%" >
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Trattamenti Effettuati</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class='row'>
                                            <div class="col-lg-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div id="containerDettaglio">
                                                        </div><br>
                                                        <div class="row">
                                                            <div class="col-lg-12 text-center">
                                                                <button type="button" class="btn btn-danger text-center" onclick="deleteTreatment()">Cancella Trattamento</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script src="../../js/registro_tracciamenti.js" type="text/javascript"></script>
        <script type="text/javascript">
                                                                    $(document).ready(function () {
                                                                        loadTrattamentiPage();
                                                                        $('.nav-tabs a').on('click', function (e) {
                                                                            e.preventDefault();
                                                                            $(this).tab('show');

                                                                        });
                                                                        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                                                                            switch (e.target.id) {
                                                                                case 'difesaContent-tab':
                                                                                    loadTrattamentiPage();
                                                                                    break;
                                                                                case 'nutrizioneContent-tab':
                                                                                    loadTrattamentiNutrizione();
                                                                                    break;
                                                                                case 'irrigazioneContent-tab':
                                                                                    loadTrattamentiIrrigazione();
                                                                                    break;
                                                                                case 'operazioneContent-tab':
                                                                                    loadTrattamentiOperazione();
                                                                                    break;
                                                                                case 'raccoltaContent-tab':
                                                                                    loadRaccolta();
                                                                                    break;
                                                                                default:
                                                                                    break;
                                                                            }
                                                                            console.log(e.target.id);
                                                                            //e.relatedTarget // previous active tab*/
                                                                        });

                                                                    });
                                                                    function loadRaccolta() {
                                                                        var dt = $('#listRaccolta');

                                                                        if ($.fn.dataTable.isDataTable('#listRaccolta')) {
                                                                            dt.DataTable().ajax.reload(function (json) {});
                                                                        } else {
                                                                            dtRegistro = dt.DataTable({
                                                                                'processing': true,
                                                                                'searching': false,
                                                                                'pageLength': 10,
                                                                                'serverSide': true,
                                                                                'serverMethod': 'post',
                                                                                'ajax': {
                                                                                    'url': WS_CALL,
                                                                                    'data': {
                                                                                        'module': "registro_tracciamenti",
                                                                                        'action': "list",
                                                                                        'cuaa': '<?= $partita_iva ?>',
                                                                                        'tipo': '<?= RACCOLTA ?>'
                                                                                    }
                                                                                },
                                                                                'columnDefs': [                                                                                                                                                                    
                                                                                    {'width': '110px', 'targets': 3}
                                                                                ],
                                                                                'columns': [
                                                                                    //               { data: 'edit' },
                                                                                    {data: 'specie'},
                                                                                    {data: 'data', orderable: false},
                                                                                    {data: 'tipo_intervento', orderable: false},
                                                                                    {data: 'qta', orderable: false},
                                                                                    {data: 'visualizza', orderable: false}
                                                                                ],
                                                                                'initComplete': function (settings, json) {

                                                                                }
                                                                            });
                                                                        }
                                                                    }
                                                                    function loadTrattamentiOperazione() {
                                                                        var dt = $('#listOperazione');

                                                                        if ($.fn.dataTable.isDataTable('#listOperazione')) {
                                                                            dt.DataTable().ajax.reload(function (json) {});
                                                                        } else {
                                                                            dtRegistro = dt.DataTable({
                                                                                'processing': true,
                                                                                'searching': false,
                                                                                'pageLength': 10,
                                                                                'serverSide': true,
                                                                                'serverMethod': 'post',
                                                                                'ajax': {
                                                                                    'url': WS_CALL,
                                                                                    'data': {
                                                                                        'module': "registro_tracciamenti",
                                                                                        'action': "list",
                                                                                        'cuaa': '<?= $partita_iva ?>',
                                                                                        'tipo': '<?= OPERAZIONE ?>'
                                                                                    }
                                                                                },
                                                                                'columnDefs': [                                                                                                                                                                    
                                                                                    {'width': '110px', 'targets': 3}
                                                                                ],
                                                                                'columns': [
                                                                                    //               { data: 'edit' },
                                                                                    {data: 'specie'},
                                                                                    {data: 'data', orderable: false},
                                                                                    {data: 'tipo_intervento', orderable: false},
//                                                                                    {data: 'particelle_collegate', orderable: false},
                                                                                    {data: 'visualizza', orderable: false}
                                                                                ],
                                                                                'initComplete': function (settings, json) {

                                                                                }
                                                                            });
                                                                        }
                                                                    }
                                                                    function loadTrattamentiIrrigazione() {
                                                                        var dt = $('#listIrrigazione');

                                                                        if ($.fn.dataTable.isDataTable('#listIrrigazione')) {
                                                                            dt.DataTable().ajax.reload(function (json) {});
                                                                        } else {
                                                                            dtRegistro = dt.DataTable({
                                                                                'processing': true,
                                                                                'searching': false,
                                                                                'pageLength': 10,
                                                                                'serverSide': true,
                                                                                'serverMethod': 'post',
                                                                                'ajax': {
                                                                                    'url': WS_CALL,
                                                                                    'data': {
                                                                                        'module': "registro_tracciamenti",
                                                                                        'action': "list",
                                                                                        'cuaa': '<?= $partita_iva ?>',
                                                                                        'tipo': '<?= IRRIGAZIONE ?>'
                                                                                    }
                                                                                },
                                                                                'columnDefs': [                                                                                                                                                                    
                                                                                    {'width': '110px', 'targets': 3}
                                                                                ],
                                                                                'columns': [
                                                                                    //               { data: 'edit' },
                                                                                    {data: 'specie'},
                                                                                    {data: 'data', orderable: false},
                                                                                    {data: 'tipo_intervento', orderable: false},
//                                                                                    {data: 'particelle_collegate', orderable: false},
                                                                                    {data: 'visualizza', orderable: false}
                                                                                ],
                                                                                'initComplete': function (settings, json) {

                                                                                }
                                                                            });
                                                                        }
                                                                    }
                                                                    function loadTrattamentiNutrizione() {
                                                                        var dt = $('#listNutrizione');

                                                                        if ($.fn.dataTable.isDataTable('#listNutrizione')) {
                                                                            dt.DataTable().ajax.reload(function (json) {});
                                                                        } else {
                                                                            dtRegistro = dt.DataTable({
                                                                                'processing': true,
                                                                                'searching': false,
                                                                                'pageLength': 10,
                                                                                'serverSide': true,
                                                                                'serverMethod': 'post',
                                                                                'ajax': {
                                                                                    'url': WS_CALL,
                                                                                    'data': {
                                                                                        'module': "registro_tracciamenti",
                                                                                        'action': "list",
                                                                                        'cuaa': '<?= $partita_iva ?>',
                                                                                        'tipo': '<?= NUTRIZIONE ?>'
                                                                                    }
                                                                                },
                                                                                'columnDefs': [                                                                                                                                                                    
                                                                                    {'width': '110px', 'targets': 3}
                                                                                ],
                                                                                'columns': [
                                                                                    //               { data: 'edit' },
                                                                                    {data: 'specie'},
                                                                                    {data: 'data', orderable: false},
                                                                                    {data: 'tipo_intervento', orderable: false},
//                                                                                    {data: 'particelle_collegate', orderable: false},
                                                                                    {data: 'visualizza', orderable: false}
                                                                                ],
                                                                                'initComplete': function (settings, json) {

                                                                                }
                                                                            });
                                                                        }
                                                                    }
                                                                    function loadTrattamentiPage() {
                                                                        var dt = $('#listDifesa');

                                                                        if ($.fn.dataTable.isDataTable('#listDifesa')) {
                                                                            dt.DataTable().ajax.reload(function (json) {});
                                                                        } else {
                                                                            dtRegistro = dt.DataTable({
                                                                                'processing': true,
                                                                                'searching': false,
                                                                                'pageLength': 10,
                                                                                'serverSide': true,
                                                                                'serverMethod': 'post',
                                                                                'ajax': {
                                                                                    'url': WS_CALL,
                                                                                    'data': {
                                                                                        'module': "registro_tracciamenti",
                                                                                        'action': "list",
                                                                                        'cuaa': '<?= $partita_iva ?>',
                                                                                        'tipo': '<?= DIFESA ?>'
                                                                                    }
                                                                                },
                                                                                'columnDefs': [                                                                                                                                                                    
                                                                                    {'width': '110px', 'targets': 3}
                                                                                ],
                                                                                'columns': [
                                                                                    //               { data: 'edit' },
                                                                                    {data: 'specie'},
                                                                                    {data: 'data', orderable: false},
                                                                                    {data: 'tipo_intervento', orderable: false},
//                                                                                    {data: 'particelle_collegate', orderable: false},
                                                                                    {data: 'visualizza', orderable: false}
                                                                                ],
                                                                                'initComplete': function (settings, json) {

                                                                                }
                                                                            });
                                                                        }
                                                                    }
                                                                    function viewAppezzamenti(id)
                                                                    {
                                                                        var object = {
                                                                            'module': 'registro_tracciamenti',
                                                                            'action': 'listAppezzamenti',
                                                                            'id': id,
                                                                            'prg_scheda': '<?=$prg_scheda?>'

                                                                        };
                                                                        
                                                                        $('#mapTitle').empty();// Svuoto il titolo
                                                                        $('#mapsid').empty();// Svuoto il div della maps
                                                                        var divMaps = document.createElement('div');
                                                                        divMaps.setAttribute('id', 'mapid-<?=$prg_scheda?>');
                                                                        divMaps.setAttribute('class', 'mapid');
                                                                        $('#mapsid').append('<div class="modal-header"><h5 id="mapTitle" class=" white-text"></h5></div>');
                                                                        $('#mapsid').append(divMaps);
                                                                        
                                                                        postdataClassic(WS_CALL, object, function (response) {
                                                                            //var risp = jQuery.parseJSON(response);
                                                                            $('#loader').hide();
                                                                            //console.log(msg);
                                                                            var popup = L.popup();
                                                                            var gpsDecode = jQuery.parseJSON(response);
                                                                            
                                                                            var geoDecode = jQuery.parseJSON(gpsDecode[0].geomjson);
                                                                            console.log(gpsDecode);
                                                                            console.log(geoDecode);
                                                                            $('#mapTitle').append('Appezzamenti interessati');
                                                                            var lat = geoDecode.features[0].geometry.coordinates[0][0][0][1];
                                                                            var lng = geoDecode.features[0].geometry.coordinates[0][0][0][0];
                                                                            //console.log(geoDecode.features[0].geometry.coordinates[0][0][0]);
                                                                            //L.map('mapid').stop();
                                                                            var mymap = L.map('mapid-<?=$prg_scheda?>');
                                                                            //14.4349094384902, 36.9140974123678
                                                                            var latlng = L.latLng(lat, lng);

                                                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                                                maxZoom: 18,
                                                                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                                                            }).addTo(mymap);

                                                                            mymap.setView(latlng, 16);
                                                                            
                                                                            L.geoJSON(geoDecode, {
                                                                                style: function (feature) {
                                                                                    return {
                                                                                        stroke: true,
                                                                                        color: '#000000',
                                                                                        weight: 3,
                                                                                        fill: true,
                                                                                        fillColor: '#ff5200',
                                                                                        fillOpacity: 1
                                                                                    };
                                                                                }, onEachFeature: function (feature, layer) {
                                                                                    var info = "";

                                                                                    if (feature.properties.codice_isola) {
                                                                                        var codice = feature.properties.codice_isola;
                                                                                        var cuua = codice.split('/');

                                                                                        if (cuua[1]) {
                                                                                            info += '<b>Cuua</b>: ' + cuua[1] + '</br>';
                                                                                        }

                                                                                        info += '<b>Isola</b>: ' + feature.properties.codice_isola + '</br>';
                                                                                    }
                                                                                    if (feature.properties.superficie) {
                                                                                        info += '<b>Superfice</b>: ' + feature.properties.superficie + ' mq</br>';
                                                                                    }
                                                                                    if (feature.properties.foglio) {
                                                                                        info += '<b>Foglio</b>: ' + feature.properties.foglio + '</br>';
                                                                                    }                                
                                                                                    if (feature.properties.comune) {
                                                                                        info += '<b>Comune</b>: ' + feature.properties.comune + ' ('+feature.properties.provincia+')';
                                                                                    }
                                                                                    layer.bindPopup(info);
                                                                                }
                                                                            }).addTo(mymap);

                                                                            document.getElementById('mapsModal').style.display = 'block';
                                                                            setTimeout(function () {
                                                                                mymap.invalidateSize();
                                                                                mymap.on('click', function (e) {
                                                                                    popup
                                                                                            .setLatLng(e.latlng)
                                                                                            .setContent("Hai fatto clic sulla mappa in " + e.latlng.toString())
                                                                                            .openOn(mymap);
                                                                                });
                                                                            }, 100);
        
        
        
                                                                            /*
                                                                            $('#container').empty();
                                                                            var i = 1;
                                                                            Object.entries(risp).forEach(([key, value]) => {
                                                                                $('#container').append('<b>' + i + '</b> - <label >' + value.ID_APPEZZAMENTO + '</label> ').append('<br>');
                                                                                i++;
                                                                            });
                                                                            $('#viewAp').modal('toggle');*/
                                                                        });
                                                                    }
                                                                    function viewOperation(id)
                                                                    {
                                                                        var object = {
                                                                            'module': 'registro_tracciamenti',
                                                                            'action': 'load',
                                                                            'id': id

                                                                        };
                                                                        postdataClassic(WS_CALL, object, function (response) {
                                                                            var risp = jQuery.parseJSON(response);
                                                                            $('#containerDettaglio').empty();
                                                                            Object.entries(risp).forEach(([key, value]) => {
                                                                                var intestazione = "";
                                                                                if (key == "ID") {
                                                                                    $('#elementDelete').val(value);
                                                                                }
                                                                                if (key != "CUAA" && key != "ID" && key != "ID_OPERATORE" && key != "CANCELLATO") {
                                                                                    if (value != "" && value != null && value != '0.00') {
                                                                                        intestazione = changeLabel(key);
                                                                                        $('#containerDettaglio').append('<b>' + intestazione + '</b> <label >' + value + '</label> ').append('<br>');
                                                                                    }
                                                                            }
                                                                            });
                                                                            $('#dettaglioModal').modal('toggle');
                                                                        });
                                                                    }




        </script>
    </body>
</html>
