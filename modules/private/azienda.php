<!doctype html>
<?
include '../../lib/api.php';

$prg_scheda = $_POST['prg_scheda'];

$LoggedAccount->checkAuthShowAziendaPage($prg_scheda);

$pf =  AnagraficaSoggetto::Load($prg_scheda, 1);
$pg =  AnagraficaSoggetto::Load($prg_scheda, 0);

if($pf->flag_pers_fisi == 1){
    $anagraficaRappresentante = $pf;        
}else{
    $rappresentanteLegale = new RappresentanteLegale($pf->id_sogg);                  
    $anagraficaRappresentante = $rappresentanteLegale->GetAnagraficaSoggetto($soggetto->codi_fisc);
}
$aziende = ($pg->id_sogg > 0 ? array($pg) : array($pf));

//Utils::print_array($aziende);

//exit();

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
                        <div class="col-lg-3 col-sm-3">
                            <i style="font-size: 155px" class="fas fa-tractor"></i><br/><br/>
                            <button class="btn btn-sm btn-info" type="button" onclick="goTo('dashboard_caaf')" title="torna all'elenco delle aziende mandatarie" ><i class="fas fa-th-list"></i> Elenco aziende mandatarie</button>
                        </div>
                        <div class="col-lg-5 col-sm-5">
                            <div class="card border-light mb-3 text-primary">
                                <div class="card-header">
                                    <h5> Rappresentante legale [<?= $anagraficaRappresentante->prg_scheda?>]</h5>
                                    <ul class="nav nav-tabs card-header-tabs" id="tabLegale" role="tablist" >
                                      <li class="nav-item" role="presentation">
                                          <a class="nav-link active"  id="nominativoRL-tab" href="#nominativoRL" role="tab" aria-controls="nominativoRL" aria-selected="true">
                                              <i class="fas fa-id-card"></i>
                                          </a>
                                      </li>
                                      <li class="nav-item" role="presentation">
                                          <a class="nav-link"  id="mapsRL-tab" href="#mapsRL" role="tab" aria-controls="mapsRL" aria-selected="false">
                                              <i class="fas fa-map-marked-alt"></i>                                              
                                          </a>
                                      </li>
                                      <li class="nav-item" role="presentation">
                                          <a class="nav-link"  id="contattiRL-tab" href="#contattiRL" role="tab" aria-controls="contattiRL" aria-selected="false">                                              
                                              <i class="fas fa-mail-bulk"></i>
                                          </a>
                                      </li>
                                      
                                    </ul>
                                </div>
                                <div class="tab-content" style="min-height: 110px;" id="tabLegaleContent">
                                    <div class="card-body tab-pane fade show active" id="nominativoRL" role="tabpanel" aria-labelledby="nominativoRL-tab">
                                        <h6 class="card-title"><b>Nominativo:</b> <?= $anagraficaRappresentante->desc_cogn . ' ' . $anagraficaRappresentante->desc_nome?></h6>
                                        <h6 class="card-title"><b>Codice fiscale:</b> <?= $anagraficaRappresentante->codi_fisc; ?> </h6>
                                    </div>
                                    <div class="card-body tab-pane fade" id="mapsRL" role="tabpanel" aria-labelledby="mapsRL-tab">
                                        <?foreach ($anagraficaRappresentante->recapiti as  $v) {?>
                                        <small><b>Indirizzo:</b> <?=$v['desc_geog_strd']?>, <?=$v['codi_geog_capp']?> - <?=$v['desc_geog_comu']?> (<?=$v['codi_geog_sigl_prov']?>)</small><br/>     
                                        <?}?>
                                    </div>
                                    <div class="card-body tab-pane fade" id="contattiRL" role="tabpanel" aria-labelledby="contattiRL-tab">                                        
                                        <small>
                                        <?if(count($anagraficaRappresentante->recapitiTelefonici)>0){
                                            echo "<b>Telefono:</b>";
                                        }else{
                                            echo "<b>Nessun telefono memorizzato</b><br/>";
                                        }?>
                                        <?foreach ($anagraficaRappresentante->recapitiTelefonici as  $v) {?>
                                             <?= trim($v['desc_nume_tele'])?> - 
                                        <?}?>
                                        </small>
                                        <small>
                                        <?
                                        if(count($anagraficaRappresentante->recapitiPec)>0){
                                            echo "<b>Pec:</b>";
                                        }else{
                                            echo "<b>Nessuna pec memorizzata</b>";                                            
                                        }
                                        foreach ($anagraficaRappresentante->recapitiPec as  $v) {?>
                                             <?=strtolower($v['desc_mail_acnt'])?> -  
                                        <?}?>
                                        </small>
                                    </div>
                                </div>
                                <?/*?>
                                <div class="card-footer">
                                    
                                        <button class="btn btn-sm btn-block btn-outline-primary" type="button" onclick="visualizzaReg('<?= intval($anagraficaRappresentante->prg_scheda) ?>');" title="Visualizza dati anagrafici di registrazione" ><i class="fas fa-user-edit"></i> Visualizza dati anagrafici</button>
                                    
                                </div>
                                 <?*/?>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-4 text-right">
                            <h2>QUADERNO DI CAMPAGNA</h2>
                            <h4>Il software online per l'agricoltura sostenibile</h4>
                            <small>ti aiuta a gestire la tua azienda agricola, <br/>rendendola efficiente e conforme alla normativa vigente</small>
                        </div>
                        
                    </div>
                </div>
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>
            <div class="container-fluid">
                <div class="row flex-xl-nowrap">
                    <?
                    include_once ROOT . 'layout/navbar.php';
                    ?>
                    <div class="<?=$colCssContainer?> bd-content">
                        <?
                        //Utils::print_array($anagraficaRappresentante);
                        ?>
                        <div class="row">                    
                            <?
                            foreach ($aziende as $k => $azienda) {
                                
                                $magazzino = Magazzino::loadFromAzienda($aziende[$k]->codi_fisc);
                                
                                ?>
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">                                            
                                            <i class="fas fa-seedling"></i> &nbsp; <?=($aziende[$k]->desc_ragi_soci !="" ? $aziende[$k]->desc_ragi_soci  : $aziende[$k]->desc_cogn." ".$aziende[$k]->desc_nome  )?>
                                            <ul class="nav nav-tabs card-header-tabs" id="tabLegale-<?=$k?>" role="tablist" >
                                              <li class="nav-item" role="presentation">
                                                  <a class="nav-link active"  id="nominativoA-<?=$k?>-tab" href="#nominativoA-<?=$k?>" role="tab" aria-controls="nominativoA-<?=$k?>" aria-selected="true">
                                                      <i class="fas fa-id-card"></i>
                                                  </a>
                                              </li>
                                              <li class="nav-item" role="presentation">
                                                  <a class="nav-link"  id="mapsA-<?=$k?>-tab" href="#mapsA-<?=$k?>" role="tab" aria-controls="mapsA-<?=$k?>" aria-selected="false">
                                                      <i class="fas fa-map-marked-alt"></i>                                              
                                                  </a>
                                              </li>
                                              <li class="nav-item" role="presentation">
                                                  <a class="nav-link"  id="contattiA-<?=$k?>-tab" href="#contattiA-<?=$k?>" role="tab" aria-controls="contattiA-<?=$k?>" aria-selected="false">                                              
                                                      <i class="fas fa-mail-bulk"></i>
                                                  </a>
                                              </li>
                                              <li class="nav-item" role="presentation">
                                                    <a class="nav-link"  id="particelleA-<?=$k?>-tab" href="#particelleA-<?=$k?>" role="tab" aria-controls="particelleA-<?=$k?>" aria-selected="false">                                              
                                                        <i class="fas fa-layer-group"></i>
                                                    </a>
                                                </li>
                                            </ul>                                      
                                        </div>
                                        <div class="tab-content" style="min-height: 110px;" id="tabA<?=$k?>Content">
                                            <div class="card-body tab-pane fade show active" id="nominativoA-<?=$k?>" role="tabpanel" aria-labelledby="nominativoA-<?=$k?>-tab">
                                                <h6 class="card-title"><?=($aziende[$k]->desc_ragi_soci !="" ? "<b>Rag. Soc.:</b> ".$aziende[$k]->desc_ragi_soci  : "<b>Nominativo:</b> ".$aziende[$k]->desc_cogn." ".$aziende[$k]->desc_nome  )?></h6>
                                                <h6 class="card-title"><b>CUAA:</b> <?=$aziende[$k]->codi_fisc?> </h6>
                                            </div>
                                            <div class="card-body tab-pane fade" id="mapsA-<?=$k?>" role="tabpanel" aria-labelledby="mapsA-<?=$k?>-tab">
                                                <?foreach ($aziende[$k]->recapiti as  $v) {?>
                                                <small><b>Indirizzo:</b> <?=$v['desc_geog_strd']?>, <?=$v['codi_geog_capp']?> - <?=$v['desc_geog_comu']?> (<?=$v['codi_geog_sigl_prov']?>)</small><br/>     
                                                <?}?>
                                            </div>
                                            <div class="card-body tab-pane fade" id="contattiA-<?=$k?>" role="tabpanel" aria-labelledby="contattiA-<?=$k?>-tab">
                                                <small>
                                                <?if(count($aziende[$k]->recapitiTelefonici)>0){
                                                    echo "<b>Telefono:</b>";
                                                }else{
                                                    echo "<b>Nessun telefono memorizzato</b><br/>";
                                                }?>
                                                <?foreach ($aziende[$k]->recapitiTelefonici as  $v) {?>
                                                     <?=$v['desc_nume_tele']?> - 
                                                <?}?>
                                                </small>
                                                <small>
                                                <?
                                                if(count($aziende[$k]->recapitiPec)>0){
                                                    echo "<b>Pec:</b>";
                                                }else {
                                                    echo "<b>Nessuna pec memorizzata</b>";
                                                }
                                                foreach ($aziende[$k]->recapitiPec as  $v) {?>
                                                     <?=strtolower($v['desc_mail_acnt'])?> -  
                                                <?}?>
                                                </small>
                                            </div>
                                            <div class="card-body tab-pane fade" id="particelleA-<?=$k?>" role="tabpanel" aria-labelledby="particelleA-<?=$k?>-tab">
                                                <?
                                                if(count($aziende[$k]->isoleParticelle)>0){
                                                ?>
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Particella</th>
                                                            <th scope="col">sez.</th>
                                                            <th scope="col">foglio</th>
                                                            <th scope="col">tipo</th>
                                                            <th scope="col">maps</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                
                                                    <?foreach ($aziende[$k]->isoleParticelle as  $v) {?>
                                                        <tr>
                                                            <th scope="row"><small><?=$v['codice_isola']?></small></th>
                                                            <td><small><?=$v['sezione']?></small></td>
                                                            <td><small><?=$v['foglio']?></small></td>
                                                            <td><small><?=$v['tipo_isola']?></small></td>
                                                            <td><button data-target="#mapsModal" onclick="ViewMaps('<?=base64_encode($v['id_isol'])?>')" data-toggle="modal" id="btnMaps<?=$k?>'" type="button" class="btn btn-primary"><i class="fas fa-map-marked-alt"></i></button></td>
                                                            
                                                        </tr>
                                                    <?}?>
                                                    </tbody>
                                                </table>
                                                <?}else{
                                                    echo "<b>Nessuna particella associata</b>";
                                                }
                                                ?>
                                                
                                                
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group" role="group">                                                
                                                <button class="btn btn-sm btn-info" type="button" onclick="goToMagazzino('magazzino', '<?= $aziende[$k]->codi_fisc ?>')" title="Entra nel Magazzino" ><i class="fas fa-warehouse"></i> Magazzino</button>
                                                <?if($magazzino->ID > 0){?>
                                                    <button class="btn btn-sm btn-dark" type="button" onclick="goTo('registro_tracciamenti', '<?= $aziende[$k]->codi_fisc ?>')" title="Entra nel registro dei trattamenti e concimazioni" ><i class="fas fa-book"></i> Trattamenti e Concimazioni</button>
                                                <?}else{?>
                                                    <button class="btn btn-sm btn-dark" type="button" title="Per accedere al registro dei trattamenti devi registrare il magazzino" ><i class="fas fa-book"></i> Trattamenti e Concimazioni</button>
                                                <?}?>
                                                <button class="btn btn-sm btn-success" type="button" onclick="goTo('registro_tracciabilita', '<?= $aziende[$k]->codi_fisc ?>')" title="Entra nel registro trattamenti" ><i class="fas fa-book"></i>Registro Tracciabilità</button>
                                                <button class="btn btn-sm btn-danger" data-target="#mapsModal" type="button" onclick="ViewAllParticelleMaps('<?= base64_encode($aziende[$k]->prg_scheda) ?>', '<?= ($aziende[$k]->desc_ragi_soci != "" ? base64_encode($aziende[$k]->desc_ragi_soci) : base64_encode($aziende[$k]->desc_cogn . " " . $aziende[$k]->desc_nome) ) ?>')" data-toggle="modal" id="btnMaps<?= $k ?>'" title="Entra nella mappa delle particelle" ><i class="fas fa-map-marked-alt"></i> Particelle</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?}?>
                            
                        </div>
                    </div>
                    
                    <div id="mapsModal" class="modal fade"  tabindex="-1" aria-labelledby="mapsModalLabel" aria-hidden="true"> 
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div id="mapsid" class="modal-content white-text"></div>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script type="text/javascript">
            $(document).ready(function () {
                //$('.modal').modal();
                $('.nav-tabs a').on('click', function (e) {
                    e.preventDefault();
                    $(this).tab('show');
                });
                
                //$('.nav-tabs').tab('show');
                
            });

            function visualizzaReg(id_bando, id_domanda) {
                var object = {
                    code: ID_ACCOUNT,
                    bando: id_bando,
                    domanda: id_domanda
                };
                $.redirect("account.php", object);
            }

            function goTo(url, azienda) {
                var object = {
                    azienda: azienda
                };
                $.redirect(url + ".php", object);
            }
            
            
            function ViewMaps(isola) {
                var isola = atob(isola);                
                var id = 'test';
                //console.log(feauters);
                $('#mapTitle').empty();// Svuoto il titolo
                $('#mapsid').empty();// Svuoto il div della maps
                var divMaps = document.createElement('div');
                divMaps.setAttribute('id', 'mapid-' + id);
                divMaps.setAttribute('class', 'mapid');
                $('#mapsid').append('<div class="modal-header"><h5 id="mapTitle" class=" white-text"></h5></div>');
                $('#mapsid').append(divMaps);
                //$('#mapid').empty();         
                
                $.ajax({
                    type: "POST",
                    url: WS_CALL,
                    data: "module=isola&action=geojson&id=" + isola,
                    dataType: "text",
                    success: function (msg)
                    {
                        
                        //console.log(msg);
                        var popup = L.popup();
                        var gpsDecode = JSON.parse(msg);
                        var geoDecode = JSON.parse(gpsDecode[0].feature);
                        $('#mapTitle').append(geoDecode.features[0].properties.codice_isola + ' - ' +geoDecode.features[0].properties.tipo_isola);
                        var lat = geoDecode.features[0].geometry.coordinates[0][0][0][1];
                        var lng = geoDecode.features[0].geometry.coordinates[0][0][0][0];
                        //console.log(geoDecode.features[0].geometry.coordinates[0][0][0]);
                        //L.map('mapid').stop();
                        var mymap = L.map('mapid-' + id);
                        //14.4349094384902, 36.9140974123678
                        var latlng = L.latLng(lat,lng);
                        
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 18,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(mymap);
                        
                        mymap.setView(latlng, 16);
                        //console.log(geoDecode);
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
                            },onEachFeature: function (feature, layer) {                                
                                var info = "";
                                
                                if(feature.properties.codice_isola){
                                    var codice = feature.properties.codice_isola;
                                    var cuua = codice.split('/');
                                    
                                    if(cuua[1]){
                                        info+='<b>Cuua</b>: '+cuua[1]+'</br>';
                                    }
                                    
                                    info+='<b>Isola</b>: '+feature.properties.codice_isola+'</br>';
                                }
                                if(feature.properties.superficie){
                                    info+='<b>Superfice</b>: '+feature.properties.superficie+' mq</br>';
                                }
                                if(feature.properties.foglio){
                                    info+='<b>Foglio</b>: '+feature.properties.foglio + ' <b>Sez.</b>:'+feature.properties.sezione;
                                }
                                layer.bindPopup(info);
                            }
                        }).addTo(mymap);
                        
                        //L.geoJSON(geoDecode).addTo(mymap);
                        
                        
                        
                        /*var marker = L.marker(gpsDecode[0]).addTo(mymap);
                        marker.bindPopup(NomeDitta);
                        if (gpsDecode.length > 1) {
                            gpsDecode.splice(0, 1);
                            var polygon = L.polygon([gpsDecode]).addTo(mymap);
                        }*/
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
                    },
                    error: function ()
                    {
                        alert("Chiamata fallita, si prega di riprovare...");
                    }
                });
                
            }
            
            
            function ViewAllParticelleMaps(scheda, ragsoc) {
                
                var scheda = atob(scheda);
                var ragsoc = atob(ragsoc);
                //console.log(feauters);
                $('#mapTitle').empty();// Svuoto il titolo
                $('#mapsid').empty();// Svuoto il div della maps
                var divMaps = document.createElement('div');
                divMaps.setAttribute('id', 'mapid-' + scheda);
                divMaps.setAttribute('class', 'mapid');
                $('#mapsid').append('<div class="modal-header"><h5 id="mapTitle" class=" white-text"></h5></div>');
                $('#mapsid').append(divMaps);
                //$('#mapid').empty();         
                
                $.ajax({
                    type: "POST",
                    url: WS_CALL,
                    data: "module=isola&action=geojson&prg_scheda=" + scheda,
                    dataType: "text",
                    success: function (msg)
                    {
                        
                        //console.log(msg);
                        var popup = L.popup();
                        var gpsDecode = JSON.parse(msg);
                        var geoDecode = JSON.parse(gpsDecode[0].geomjson);
                        
                        $('#mapTitle').append(ragsoc);
                        var lat = geoDecode.features[0].geometry.coordinates[0][0][0][1];
                        var lng = geoDecode.features[0].geometry.coordinates[0][0][0][0];
                        //console.log(geoDecode.features[0].geometry.coordinates[0][0][0]);
                        //L.map('mapid').stop();
                        var mymap = L.map('mapid-' + scheda);
                        //14.4349094384902, 36.9140974123678
                        var latlng = L.latLng(lat,lng);
                        
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 18,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(mymap);
                        
                        mymap.setView(latlng, 16);
                        console.log(geoDecode);
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
                            },onEachFeature: function (feature, layer) {   
                                var info = "";
                                
                                if(feature.properties.codice_isola){
                                    var codice = feature.properties.codice_isola;
                                    var cuua = codice.split('/');
                                    
                                    if(cuua[1]){
                                        info+='<b>Cuua</b>: '+cuua[1]+'</br>';
                                    }
                                    
                                    info+='<b>Isola</b>: '+feature.properties.codice_isola+'</br>';
                                }
                                if(feature.properties.superficie){
                                    info+='<b>Superfice</b>: '+feature.properties.superficie+' mq</br>';
                                }
                                if(feature.properties.foglio){
                                    info+='<b>Foglio</b>: '+feature.properties.foglio + ' <b>Sez.</b>:'+feature.properties.sezione;
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
                    },
                    error: function ()
                    {
                        alert("Chiamata fallita, si prega di riprovare...");
                    }
                });
                
            }
            
            
            
        </script>
    </body>
</html>
