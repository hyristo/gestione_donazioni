<!doctype html>
<?
include '../../../lib/api.php';

define("THIS_PERMISSION", array('MAGAZZINO', 'TRATTAMENTI'));
include_once ROOT . 'layout/include_permission.php';
$LoggedAccount->checkAuthShowAziendaPage();

$azienda = $LoggedAccount->AziendaQiam;

//Utils::print_array($azienda);
//Utils::print_array($aziende);
//exit();

$partita_iva =  $azienda['codi_fisc'];
$tipo_intervento = CodiciVari::Load(0, 'TIPO_INTERVENTO');
$stadiofenologico = CodiciVari::Load(0, 'STADIO_FENOLOGICO');
$operazione = CodiciVari::Load(0, 'OPERAZIONE');
$avversita = CodiciVari::Load(0, 'TIPO_AVVERSITA');
$magazzino = Magazzino::loadFromAzienda($partita_iva);


$prg_scheda = $azienda['prg_scheda'];
$isola = new Appezzamento();
$descrizione_specie = $isola->getTipoAppezzamento($prg_scheda);
        


$schede = Utils::filter_by_value($LoggedAccount->PrgSchedeAziende, "prg_scheda", $prg_scheda);
$particelle = $azienda['isoleParticelle'];//Utils::filter_by_value($schede[0]['isoleParticelle'], "id_pcg_vers", $prg_scheda);



?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <style>
        #map {
                            width: 100%;
                            height: 100%;
                    }

                    #was
    {


        text-align:center;
        border-radius:5px;
        background: rgba(255,255,0,0.7);
        position:absolute;
        top:10px;
        left:calc(50% - 300px);
        margin:auto;
            z-index: 2000;
        display: inline-block;

    }

    .input.maps input {
        padding: 0 35px;
    }

    #places {
        float: left;

        width: 500px;
        border: 3px solid #d5eff6;
        -webkit-border-radius: 6px;
        border-radius: 6px;
        background-color: #fff;

    }

            #map { width: 100%;
                            height: 100%; }
    .info { padding: 6px 8px; font: 14px/16px Arial, Helvetica, sans-serif; background: white; background: rgba(255,255,255,0.8); box-shadow: 0 0 15px rgba(0,0,0,0.2); border-radius: 5px; } .info h4 { margin: 0 0 5px; color: #777; }
    .legend { text-align: left; line-height: 18px; color: #555; } .legend i { width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.7; }

    .scroler{

            width: 250px;
            height: 400px;
            z-index: 999;
            overflow-y: scroll;
    }

    .left-inner-addon {
        position: relative;
    }
    .left-inner-addon input {
        padding-left: 30px;    
    }
    .left-inner-addon i {
        position: absolute;
        padding: 10px 12px;
        pointer-events: none;
    }
        
    </style>
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
                            <a class="nav-link active" href="#" onclick="goReg('<?= $partita_iva ?>')"><i class="fas fa-angle-right"></i> Registro Trattamento</a>
                            <a class="nav-link disabled" href="#" ><i class="fas fa-angle-right"></i><?=$partita_iva?></a>
                            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"><i class="fas fa-angle-right"></i> Aggiungi  Trattamento</a>
                            

                        </div>
                    </div>
                </nav>
                <div class="row flex-xl-nowrap">
                    <?
                    include_once ROOT . 'layout/navbar.php';
                    ?>
                    <div class="<?= $colCssContainer ?> bd-content">
                        
                        <br/>
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="add-form-tracciamento" action="javascript:saveMovimento()">
                                    <input type="hidden" name="CUAA" value="<?= $partita_iva ?>">
                                    <input type="hidden" class="form-control" id="TIPO_ARTICOLO" name="TIPO_ARTICOLO">
                                    
                                    <div class="card">

                                        <div class="card-header"><h6>Nuovo trattamento</h6></div>

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">                                                    
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="card">                                                                
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-lg-6">
                                                                            <label for="SPECIE_TXT"><span class="text-danger">*&nbsp;</span>Selezionare la specie per proseguire<br/>
                                                                                <small>Le specie sono filtrate in base agli appezzamenti di terreno associati all'azienda di riferimento</small>
                                                                            </label>
                                                                            <select required="" class="form-control" id="SPECIE_TXT" onchange="openAppezzamenti(this, '<?= $prg_scheda ?>')" name="SPECIE_TXT" >
                                                                                <option value="0"></option>
                                                                                <? foreach ($descrizione_specie as $value) {
                                                                                    ?>
                                                                                    <option value="<?= $value['codi_uso'] ?>"><?= $value['desc_prod'] ?></option>
                                                                                <? } ?>
                                                                            </select>
                                                                            <input type="hidden" class="form-control" id="specie" name="SPECIE">
                                                                        </div>

                                                                        <div class="col-lg-6">

                                                                            <label for="DATA_INTERVENTO"><span class="text-danger">*&nbsp;</span>Data Intervento</label>
                                                                            <input type="date" required="" class="form-control" id="data_intervento" name="DATA_INTERVENTO">

                                                                        </div>
                                                                    </div>
                                                                    <br/>
                                                                    <div class="row visualizzaFitApp">
                                                                        <div class="col-lg-12">

                                                                            <label><span class="text-danger">*&nbsp;</span>Seleziona gli appezzamenti sui cui registrare il trattamento</label>
                                                                            <div id="mapsModal" style="height:500px" class="card">                                                                                
                                                                                  <div id="mapsid" class="card-body white-text"></div>
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <br/>
                                                                        <div class="col-lg-12">
                                                                            <label for="TIPO_INTERVENTO"><span class="text-danger">*&nbsp;</span>Tipo d'intervento </label>

                                                                            <select class="form-control" onchange="changeEvent(this)" id="TIPO_INTERVENTO" name="TIPO_INTERVENTO" >
                                                                                <option value=""></option>
                                                                                <?
                                                                                foreach ($tipo_intervento as $value) {
                                                                                    ?>
                                                                                    <option value="<?= $value['ID_CODICE'] ?>"><?= $value['DESCRIZIONE'] ?></option>
                                                                                <? } ?>
                                                                            </select>
                                                                        </div>
                                                                        <br/>
                                                                    </div>
                                                                    
                                                                    <div style="display:none" >
                                                                        <div class='row'>
                                                                            <div class="col-lg-12">
                                                                                <label>Scegli l'appezzamento </label>
                                                                                <div class="card">
                                                                                    <div class="card-body">
                                                                                        <div id="container"></div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <br>
                                                                    </div>
                                                                    
                                                                    <div class="row" >
                                                                        
                                                                        <div class="col-lg-12" id="div_ProdottiFertilizzanti" style="display:none">
                                                                            <div class="form-group">
                                                                                <label for="ProdottiFertilizzanti">Articolo</label>
                                                                                <select style="width: 100%" <? /* required="" */ ?> class="form-control cfinput" id="ProdottiFertilizzanti" name="ProdottiFertilizzanti"></select>                                                
                                                                            </div>                                        
                                                                        </div>                                            
                                                                        <div class="col-lg-12"  id="div_ProdottiFitosanitari" style="display:none">
                                                                            <div class="form-group">
                                                                                <label for="ProdottiFitosanitari">Articolo</label>
                                                                                <select style="width: 100%"  <? /* required="" */ ?> class="form-control cfinput" id="ProdottiFitosanitari" name="ProdottiFitosanitari"></select>
                                                                            </div>                                            
                                                                        </div>

                                                                    </div>
                                                                    <div class="row" id="div_quantita_misura" style="display:none">
                                                                        <div class="col-lg-4">
                                                                            <label for="UNITA_MISURA_SIGLA">Unità di misura</label>
                                                                            <input type="text" class="form-control" readonly="" id="unita_misura_sigla" name="UNITA_MISURA_SIGLA" >
                                                                            <input type="hidden" class="form-control" id="unita_misura" name="UNITA_MISURA">
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <label for="giacenze">Giacenze in magazzino</label>
                                                                            <input type="text" class="form-control" readonly="" id="giacenze" name="giacenze" >                                                                            
                                                                        </div>
                                                                        <div class="col-lg-4">
                                                                            <label for="QUANTITA_UTILIZZATA">Quantitativo utilizzato </label>
                                                                            <input type="text" class="form-control" id="QUANTITA_UTILIZZATA" name="QUANTITA_UTILIZZATA" >
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" class="form-control" id="ID_PRODOTTO_FITOSANITARIO" name="ID_PRODOTTO_FITOSANITARIO"  >                                                                    
                                                                    <br/>
                                                                    <?
                                                                    include 'difesa_nutrizione.php';
                                                                    include 'irrigazione.php';                                                                    
                                                                    include 'operazione.php';
                                                                    include 'raccolta.php';
                                                                    ?>
                                                                    <hr>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            Note
                                                                            <textarea class="form-control" rows="6" name="NOTE" ></textarea>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <span class="text-danger small">*&nbsp;Campo obbligatorio</span><br>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-lg-12 text-right">
                                                    <button type="button"  class="btn btn-md btn-danger" onclick="goBack()"><i class="fas fa-undo"></i>&nbsp;Annulla</button>
                                                    <button type="submit" class="btn btn-primary btn-md " ><i class="fas fa-save"></i>&nbsp;Salva</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </main>
    <? include_once ROOT . 'layout/footer.php'; ?>
    <script src="../../../js/registro_tracciamenti.js" type="text/javascript"></script>
    <script type="text/javascript">
                $(document).ready(function () {
                     $('#add-form-tracciamento :input').each(function () {
                            $(this).change(function () {
                                isModified = true;
                                $(this).addClass('is-valid');  
                            });
                        });
                        
                    
                }); 
                
                var isModified = false;
                
                function goBack() {
                    if (isModified) {
                        Swal.fire({
                            title: "Attenzione",
                            text: "Sicuro di volere procedere ? Eventuali modifiche non salvate andranno perse.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.value) {
                                $.redirect(HTTP_PRIVATE_SECTION + "dashboard.php");
                            }
                        });
                    } else {
                        $.redirect(HTTP_PRIVATE_SECTION + "dashboard.php");
                    }
                }
                

                $("#ProdottiFitosanitari").select2({
                    language: "it",
                    minimumInputLength: 3,
                    ajax: {
                        url: WS_CALL + "?module=registro_tracciamenti&action=searchFitosanitari&id_magazzino=<?= $magazzino->ID ?>",
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function (response) {
                            
                            return {

                                results: response

                            };
                        },
                        cache: true
                    }
                });
                $("#ProdottiFertilizzanti").select2({
                    language: "it",
                    minimumInputLength: 3,
                    ajax: {
                        url: WS_CALL + "?module=registro_tracciamenti&action=searchFertilizzanti&id_magazzino=<?= $magazzino->ID ?>",
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: response
                            };
                        },
                        cache: true
                    }
                });
                

            function generaMappaAppezzamenti(risp, prg_scheda){
                
                
                
                
                /*
                document.getElementById('mapsModal').style.display = 'block';
                setTimeout(function () {
                    map.invalidateSize();
                    map.on('click', function (e) {
                        popup
                                .setLatLng(e.latlng)
                                .setContent("Hai fatto clic sulla mappa in " + e.latlng.toString())
                                .openOn(map);
                    });
                }, 100);
                */
                /*################################*/
            } 
            
            var dellayer = function(a){}
            
            function resetDiv(){
                
                $("#div_ProdottiFitosanitari").hide();
                $("#div_ProdottiFertilizzanti").hide();
                $("#div_quantita_misura").hide();
                $(".difesa").hide();
                $("#difesa_nutrizione").hide();
                $(".nutrizione").hide();
                $(".irrigazione").hide();
                $(".operazione").hide();
                $(".raccolta").hide();
                
                $("#TIPO_INTERVENTO").val('');
                
            }
            function openAppezzamenti(ricevuto, prg_scheda) {
                    resetDiv();
                    if(ricevuto.value<=0){
                        $(".visualizzaFitApp").hide();
                        $('#container').empty();
                        $('#mapTitle').empty();// Svuoto il titolo
                        $('#mapsid').empty();// Svuoto il div della maps
                        return;
                    }
                    $(".visualizzaFitApp").show();
                    //console.log(ricevuto);
                    //console.log($("#SPECIE_TXT option:selected" ).text());
                    $("#specie").val($("#SPECIE_TXT option:selected" ).text());
                    var object = {
                        'module': 'registro_tracciamenti',
                        'action': 'listSelect',
                        'specie': ricevuto.value,
                        'prg_scheda': prg_scheda

                    };
                    $('#loader').show();
                    postdataClassic(WS_CALL, object, function (response) {
                        var risp = jQuery.parseJSON(response);
                        var descrizione = "";
                        $('#container').empty();
                        
                        
                        /*### PLUGIN #####*/
                       var stylelayer={
                                defecto:{
                                        stroke: true,
                                        color: '#000000',
                                        weight: 1,
                                        fill: true,
                                        fillColor: '#ff5200',
                                        fillOpacity: 1
                                }
                                ,
                                reset:{
                                        troke: true,
                                        color: '#000000',
                                        weight: 1,
                                        fill: true,
                                        fillColor: '#ff5200',
                                        fillOpacity: 1
                                }
                                ,
                                highlight:{
                                        weight: 5,
                                        color: '#0D8BE7',
                                        dashArray: '',
                                        fillOpacity: 0.7
                                }
                                ,
                                selected:{
                                        color: "blue",
                                        opacity: 0.3,
                                        weight: 0.5
                                }

                        };

                        /*##############################*/
                        $('#mapTitle').empty();// Svuoto il titolo
                        $('#mapsid').empty();// Svuoto il div della maps
                        var divMaps = document.createElement('div');
                        divMaps.setAttribute('id', 'mapid-' + prg_scheda);
                        divMaps.setAttribute('class', 'mapid');
                        //$('#mapsid').append('<div class="modal-header"><h5 id="mapTitle" class=" white-text"></h5></div>');
                        $('#mapsid').append(divMaps);
                        //$('#mapid').empty();     

                        var popup = L.popup();                        
                        var geoDecode = JSON.parse(risp.point[0].geomjson);

                        //$('#mapTitle').append(ragsoc);
                        var lat = geoDecode.features[0].geometry.coordinates[0][0][0][1];
                        var lng = geoDecode.features[0].geometry.coordinates[0][0][0][0];
                        //console.log(geoDecode.features[0].geometry.coordinates[0][0][0]);
                        //L.map('mapid').stop();
                        var map = L.map('mapid-' + prg_scheda);
                        //14.4349094384902, 36.9140974123678
                        var latlng = L.latLng(lat, lng);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 18,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                            id: 'mapbox.light'
                        }).addTo(map);

                        map.setView(latlng, 16);

                        var placenames = new Array();
                        var zipcodes = new Object();
                        //$.getJSON( '/getAjaxProducts', null,
                        // function ( jsonData )
                        //{
                        $.each(geoDecode.features, function ( index, feature )
                        {
                                //var name=`${feature.properties.codice_belfiore} Foglio: ${feature.properties.foglio} sez. ${feature.properties.sezione}  ( ${feature.properties.desc_prod})`;
                                var name=`${feature.properties.cod_appe}`;
                                placenames.push(name);
                                zipcodes[name] = feature.properties.cod_appe;
                        } );

                        /*$('#places').typeahead({
                            source:placenames,
                            afterSelect: function(b) {
                                           redraw(b);
                            } 
                        });*/

                        var arrayBounds = [];
                        function redraw(b){

                            geojson.eachLayer(function(layer) {
                                      if(layer.feature.properties.cod_appe==zipcodes[b]){
                                                   selectTypeaheadFeature(layer);
                                      }
                            });

                        }

                        var geojson = L.geoJSON(geoDecode, {
                            style: stylelayer.defecto,
                            onEachFeature: onEachFeature
                        }).addTo(map);

                        function onEachFeature(feature, layer) {
                                layer.on({
                                        mouseover: highlightFeature,
                                        mouseout: resetHighlight,
                                        click: zoomToFeature
                                        //dblclick : selectFeature
                                });
                        }
                        var popupLayer;
                        function highlightFeature(e){
                            var layer = e.target;
                            layer.setStyle(stylelayer.highlight);
                            info.update(layer.feature.properties);
                        }

                        function resetHighlight(e){			
                            var layer=e.target;
                            var feature=e.target.feature;

                            if(checkExistsLayers(feature)){
                                    setStyleLayer(layer,stylelayer.highlight);

                            }else{
                                    setStyleLayer(layer,stylelayer.defecto);
                            }
                        }
                        var featuresSelected=[];
                        function zoomToFeature(e){

                            var layer=e.target;
                            var feature=e.target.feature;

                            if(checkExistsLayers(feature)){
                                    removerlayers(feature,setStyleLayer,layer,stylelayer.defecto);
                                    removeBounds(layer);                                    
                            }else{
                                    addLayers(feature,setStyleLayer,layer,stylelayer.highlight);
                                    addBounds(layer);                                    
                            }
                            selCheck(feature.properties.cod_appe);
//                            map.fitBounds(arrayBounds);
                            detailsselected.update(featuresSelected);

                        }
                        var initbounds = L.latLngBounds(lat,lng);
                        function selectTypeaheadFeature(layer){

                                var layer=layer;
                                var feature=layer.feature;

                                if(checkExistsLayers(feature)){
                                        removerlayers(feature,setStyleLayer,layer,stylelayer.defecto);
                                        removeBounds(layer);
                                }else{
                                        addLayers(feature,setStyleLayer,layer,stylelayer.highlight);
                                        addBounds(layer);
                                }
                                selCheck(feature.properties.cod_appe);
                                //map.fitBounds(arrayBounds.length!=0 ? arrayBounds: initbounds);
                                detailsselected.update(featuresSelected);

                        }
                        
                        var arrayBounds = [];
                        function addBounds(layer){
                                arrayBounds.push(layer.getBounds());
                        }
                        function removeBounds(layer){                           
                            
                                arrayBounds = arrayBounds.filter(bounds => bounds!= layer.getBounds());
                        }


                        function setStyleLayer(layer,styleSelected){
                                layer.setStyle(styleSelected);
                        }

                        function removerlayers(feature,callback){
                                featuresSelected = featuresSelected.filter(obj => obj.cod_appe!= feature.properties.cod_appe)
                                callback(arguments[2],arguments[3]);
                        }

                        function addLayers(feature,callback){
                                featuresSelected.push({
                                        cod_appe: feature.properties.cod_appe,
                                        feature: feature
                                });
                                callback(arguments[2],arguments[3]);
                        }

                        function checkExistsLayers(feature){

                            var result=false
                            for (var i = 0; i < featuresSelected.length; i++) {
                                    if(featuresSelected[i].cod_appe==feature.properties.cod_appe){
                                            result=true;
                                            break;
                                    }

                            };
                            return result;
                        }

                        var info = L.control({
                                        position:'bottomleft'
                                });

                        info.onAdd = function (map) {
                            this._div = L.DomUtil.create('div', 'info');
                            this.update();
                            return this._div;
                        };

                        info.update = function (properties) {
                            this._div.innerHTML =
                                '<b>Proprietà</b><br/>' +  (properties ?
                                `
                                        <b>Foglio:</b> ${properties.foglio}<br>
                                        <b>sez.:</b> ${properties.sezione}<br>
                                        <b>Superfice:</b> ${properties.supe_appe} mq<br>
                                        <b>Uso:</b> ${properties.desc_prod}<br>
                                        <b>Comune:</b>  ${properties.comune}(${properties.provincia})
                                                `
                                : 'Passa il mouse sulla particella');
                                 ;
                        };
                        info.addTo(map);

                        var detailsselected = L.control();

                        detailsselected.onAdd = function (map) {
                            this._div = L.DomUtil.create('div', 'info scroler');
                            this.update();
                            return this._div;
                        };
                        
                        dellayer = function(cod_appe){

                                   geojson.eachLayer(function(layer) {
                                          if(layer.feature.properties.cod_appe==cod_appe){
                                                       selectTypeaheadFeature(layer);
                                          }
                                });
                           };


                        var detailshow=function (){
                                var result='';
                                var total=0;
                                for (var i = 0; i < featuresSelected.length; i++) {

                                        var properties=featuresSelected[i].feature.properties
                                        result+=
                                        `
                                <b>Foglio:</b> ${properties.foglio} 
                                <b>sez.:</b> ${properties.sezione}<br>
                                <b>Superfice:</b> ${properties.supe_appe} mq<br>
                                <b>Uso:</b> ${properties.desc_prod}<br>
                                <b>Comune:</b> ${properties.comune} (${properties.provincia}) <br>
                                <button type="button" class="btn btn-danger btn-sm" onclick='dellayer(${properties.cod_appe})'>Elimina</button>
                                <hr>`;
                                total+=	properties.supe_appe


                                }
                                
                                return {result:result,total:total};
                        };

                        detailsselected.update = function (arrayselected) {

                            var details=detailshow();

                            this._div.innerHTML ='<b>TOTALE: '+ details.total+ ' mq</b><hr>'+  details.result;
                            $('#suma', window.parent.document).val(details.total);

                        };

                        detailsselected.addTo(map);

                        
                        Object.entries(risp).forEach(([key, value]) => {
                            if(!isNaN(key)){
                                var divform = document.createElement('div');
                                divform.setAttribute('class', 'form-check form-check-inline');
                                var checkbox = document.createElement('input');
                                checkbox.setAttribute('type', 'checkbox');
                                checkbox.setAttribute('class', 'form-check-input');
                                checkbox.setAttribute('name', 'id_isol[' + risp[key].cod_appe + ']');
                                checkbox.setAttribute('value', risp[key].cod_appe);

                                var label = document.createElement('label');
                                label.setAttribute('class', 'form-check-label');                            
                                label.append(risp[key].cod_appe + " " + risp[key].desc_prod);
                                console.log(risp);
                                divform.append(checkbox);
                                divform.append(label);
                            
                            //divform.append('<input type="checkbox" class="form-check-input"   name="id_isol[' + risp[key].id_appe + ']" value="' + risp[key].id_appe + '">');
                            //divform.append('<label class="form-check-label" for="inlineCheckbox1">' + risp[key].cod_appe + " " + risp[key].desc_prod + '</label>');
                                $('#container').append(divform);
                            }
                            $('#loader').hide();
                        });
                    });
                }
                
                function selCheck(chk){
                    var inputChk = $('input[name="id_isol['+chk+']"]');
                    
                    if(inputChk.is(':checked')){
                        inputChk.attr('checked', false);
                    }else{
                        inputChk.attr('checked', true);
                    }
                    
                }
                    



    </script>
</body>
</html>
