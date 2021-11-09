<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'prodotti_fitosanitari');
define("THIS_PERMISSION", array('FITOSANITARI'));
include_once ROOT . '/layout/include_permission.php';
?>

<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body>
        <? include_once ROOT . 'layout/header.php'; ?>
        <main role="main" class="<?= $cssColumNavBar ?>" >
            <div class="container-fluid">
                <? include_once ROOT . 'layout/menu.php'; ?>
                <div class="row">
                    <div class="col-sm-12">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Prodotti fitosanitari</li>
                            </ol>
                        </nav>
                    </div>
                </div>                                    

                <div class="modal fade" id="editFitofarmaco" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 70%;"  >
                        <div id="ecommerce-products" class="modal-content">
                            <div class="modal-header">
                                <h4>Prodotto <span id="view_NUMERO_REGISTRAZIONE" class="badge badge-success" ></span></h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <h4 id="view_prodotto"></h4>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5 id="view_SOSTANZE_ATTIVE"></h5>
                                        <p>Stato: <span  id="view_STATO_AMMINISTRATIVO" class="text-success"></span></p>
                                        <p>Formulazione: <span  id="view_DESCRIZIONE_FORMULAZIONE" class="text-success"></span></p>
                                        <p>Codice formulazione: <span  id="view_CODICE_FORMULAZIONE" class="text-success"></span></p>
                                        <p>Data decreto revoca: <span  id="view_DATA_DECRETO_REVOCA" class="text-success"></span></p>
                                        <p>Data decorrenza revoca: <span  id="view_DATA_DECORRENZA_REVOCA" class="text-success"></span></p>
                                        <p>Motivo della revoca: <span  id="view_MOTIVO_DELLA_REVOCA" class="text-success"></span></p>                                        
                                        <p>Attivita': <span  id="view_ATTIVITA" class="text-success"></span></p>
                                        <p>Sostanze attive: <span  id="view_SOSTANZE ATTIVE" class="text-success"></span></p>
                                        <p>Contenuto per 100g di prodotto: <span  id="view_CONTENUTO_PER_CENTOG_DI_PRODOTTO" class="text-success"></span></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p>Impresa: <span  id="view_IMPRESA" class="text-success"></span></p>
                                        <p>Sede legale: <span  id="view_SEDE_LEGALE_IMPRESA" class="text-success"></span> - <span  id="view_CAP_SEDE_LEGALE_IMPRESA" class="text-success"></span> - <span  id="view_CITTA_SEDE_LEGALE_IMPRESA" class="text-success"> </span> - <span  id="view_PROVINCIA_SEDE_LEGALE_IMPRESA" class="text-success"></span></p>
                                        <p>Sede amministrativa: <span  id="view_SEDE_AMMINISTRATIVA_IMPRESA" class="text-success"></span> - <span  id="view_CAP_SEDE_AMMINISTRATIVA_IMPRESA" class="text-success"></span> - <span  id="view_CITTA_SEDE_AMMINISTRATIVA_IMPRESA" class="text-success"> </span> - <span  id="view_PROVINCIA_SEDE_AMMINISTRATIVA_IMPRESA" class="text-success"></span></p>
                                        <p>Data registrazione: <span  id="view_DATA_AUTORIZZAZIONE" class="text-success"></span></p>
                                        <p>Scadenza autorizzazione: <span  id="view_SCADENZA_AUTORIZZAZIONE" class="text-success"></span></p>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-12">
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">                                
                            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                <li class="nav-item active">
                                    <span class="navbar-brand mb-0 h1">Elenco dei fitofarmaci</span><br/>
                                    <small>Elenco dei fitofarmaci da utilizzare per il quaderno di campaga</small>
                                </li>
                            </ul>                                
                        </nav>
                    </div>
                </div><br>

                <div class="card">
                    <div class="card-body">

                        <?
                        if ($LoggedAccount->IsAmministratore()) {
                            ?>
                            <nav class="navbar navbar-expand-lg navbar-light bg-light">                                
                                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                    <li _class="nav-item">
                                        <small>Per aggiornare l'anagrafica scaricare il file CSV del dataset contenente l'elenco completo dei Prodotti Fitosanitari autorizzati dal Ministero della Salute:</small><br/>
                                        <span class="badge badge-success">1</span>&nbsp;&nbsp;<small><a href="<?=LINK_FITOSANITARI?>" target="_blanck">Scaricare il listino aggiornato</a></small><br>
                                        <span class="badge badge-success">2</span>&nbsp;&nbsp;<small>Verificare l'ordine corretto delle colonne come indicato nella guida</small><br>
                                        <span class="badge badge-success">3</span>&nbsp;&nbsp;<small>Selezionare e il file scaricato con il pulsante "Scegli file"</small><br>
                                        <span class="badge badge-success">4</span>&nbsp;&nbsp;<small>Cliccare sul pulsante "Aggiorna" per avviare la procedura</small><br>
                                        <span class="badge badge-success">5</span>&nbsp;&nbsp;<small>Non chiudere la pagina Attendere la fine della procedura</small><br>
                                        <span class="badge badge-success">6</span>&nbsp;&nbsp;<small>Al fine della procedura verranno visualizzati i prodotti processati</small>
                                    </li>                                    
                                    <li>
                                        <span id="processati"></span><br>
                                        <span id="prodottiOK"></span><br>
                                        <span id="prodottiKO"></span><br>
                                        <span id="prodNuovi"></span>
                                    </li>
                                </ul>
                            </nav>
                            <br/>                            
                            <form id="form-document" action="javascript:aggiornaListino();" >
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input class="form-control" type="file" id="listino" name="listino" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <button id="alleneaProdotti"  class="btn btn-sm btn-success" type="submit"  title="Aggiorna listino" ><i class="fas fa-refresh"></i> AGGIORNA</button>
                                    </div>
                                </div>
                            </form>
                            <br/>  
                        
                        <? } ?>

                        <table id="ListFitofarmaci" class="table table-striped table-bordered" style="width:100%">                                                                            
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Numero registrazione</th>
                                    <th>Prodotto</th>
                                    <th>Formulazione</th>
                                    <th>Sostanze attive</th>
                                    <th>Visualizza</th>
                                    <th>Stato</th>
                                </tr>
                            </thead>
                        </table>                            

                    </div>
                </div>
            </div>
            
        </main>
        <!-- BEGIN: Footer-->
        <?php include_once ROOT . 'layout/footer.php'; ?>
        <!-- END: Footer-->
        <script type="text/javascript">
            $(document).ready(function () {

                
                listFitofarmaci();


            });
            
            
            function listFitofarmaci(){
                
                var dt = $('#ListFitofarmaci');
                
                if ($.fn.DataTable.isDataTable( '#ListFitofarmaci' )){
                    dt.DataTable().ajax.reload(function (json) {});
                } else {
                    dtListFitofarmaci = dt.DataTable({
                        'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=list'
                    },
                    'columns': [
                        {data: 'id'},
                        {data: 'numero_registrazione'},
                        {data: 'prodotto'},
                        {data: 'descrizione_formulazione'},
                        {data: 'sostanze_attive'},
                        {data: 'modifica'},
                        {data: 'stato'}
                    ],
                        'initComplete': function (settings, json) {
                            
                        }
                    });
                }
            }
            
            
            
            function aggiornaListino() {
                    $('#loader').show();
                    
                    var file = document.getElementById("listino");
                    var estensione = file.files[0].type;
                    console.log(estensione);
                    if (estensione == "application/csv" || estensione == "application/vnd.ms-excel") {
                        var form = document.getElementById('form-document');
                        var formInvio = new FormData(form);
                        formInvio.append('module', 'prodotti_fitosanitari');
                        formInvio.append('action', 'import');
                        postdata(WS_CALL, formInvio, function (response) {
                    //    postdata(WS_CALL + "?module=domanda&action=saveDocumento", formInvio, function (response) {
                            $('#loader').hide();
                            var risp = jQuery.parseJSON(response);
                            if (risp.esito === 1) {
                                $('#loader').hide();
                                siparsFramework.MessageSuccess("Operazione completata con successo!", "Salvataggio record", false, true);

                                $('#processati').html('Articoli processati:<b>' + risp.processati + '</b>;');
                                $('#prodottiOK').html('Articoli salvati:<b>' + risp.prodottiOK + '</b>;');
                                $('#prodottiKO').html('Errori:<b>' + risp.prodottiKO + '</b>;');
                                $('#prodNuovi').html('Nuovi articoli:<b>' + risp.prodNuovi + '</b>;');
                                $('#ultimoAggiornamento').empty();
                                $('#ultimoAggiornamento').html(risp.ultimoAggiornamento);
                                listFitofarmaci();
                                
                            } else {
                                $('#loader').hide();
                                functionSwall('error', risp.erroreDescrizione, "");
                            }
                        });
                    } else {
                        $('#loader').hide();
                        functionSwall('error', "Estensione del file non valida", "");
                    }
                    
                    
                    /*
                    $.ajax({
                        url: WS_CALL + '?module=prodotti_fitosanitari&action=import',
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        beforeSend: function (data) {
                        },
                        success: function (response) {
                            var res = jQuery.parseJSON(response);
                            var risposta = res.esito;
                            //console.log(res);
                            //document.location.reload();
                            $('#loader').hide();
                            if (risposta == 1) {
                                //listRichiesteImport(true);
                                siparsFramework.MessageSuccess("Operazione completata con successo!", "Salvataggio record", false, true);

                                $('#processati').html('Articoli processati:<b>' + res.processati + '</b>;');
                                $('#prodottiOK').html('Articoli salvati:<b>' + res.prodottiOK + '</b>;');
                                $('#prodottiKO').html('Errori:<b>' + res.prodottiKO + '</b>;');
                                $('#prodNuovi').html('Nuovi articoli:<b>' + res.prodNuovi + '</b>;');
                                $('#ultimoAggiornamento').empty();
                                $('#ultimoAggiornamento').html(res.ultimoAggiornamento);
                            } else {
                                siparsFramework.MessageError(descrizioneErrore, 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            siparsFramework.MessageError("An AJAX error occured: " + status + "\nError: " + error);
                            //console.log(xhr);
                            $('#loader').hide();
                        }
                    });*/

                }


        </script>
    </body>
</html>