<!doctype html>
<?
include '../../lib/api.php';
define("THIS_PERMISSION", array('MAGAZZINO'));
include_once ROOT . 'layout/include_permission.php';
$LoggedAccount->checkAuthShowAziendaPage();

$azienda = $LoggedAccount->AziendaQiam;

$partita_iva = $azienda['codi_fisc'];

$id_movimento = $_POST['id_movimento'];
$tipo_movimento = $_POST['tipo_movimento'];

//Utils::print_array($azienda);

$magazzino = Magazzino::loadFromAzienda($partita_iva);

$movimento = new Movimento($id_movimento);
$destinazione_uso = CodiciVari::Load(0, 'DESTINAZIONE_USO');
//Utils::print_array($movimento);
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
                            <h5><?= $azienda['desc_ragi_soci'] ?></h5>
                            <h6><?= $azienda['codi_fisc'] ?></h6>
                            <h2><?= $magazzino->NOME ?> - MOVIMENTO DI <?= $DESCRIZIONEMOVIMENTO[$tipo_movimento] ?> </h2>
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
                    <div class="<?= $colCssContainer ?> bd-content">
                        <!-- BEGIN USABILITYBAR -->
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">
                            <a class="navbar-brand" href="#"><i class="fas fa-truck-monster"></i></a>
                            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                                <div class="navbar-nav">
                                    <a class="nav-link active" href="#" onclick="goToDashboard('index')">Pagina iniziale</a>                              
                                    <a class="nav-link" href="#" onclick="goToMagazzino('magazzino', '<?= $partita_iva ?>')" ><i class="fas fa-angle-right"></i> Magazzino</a>
                                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"><i class="fas fa-angle-right"></i> Movimento di <?= $DESCRIZIONEMOVIMENTO[$tipo_movimento] ?></a>
                                </div>
                            </div>
                        </nav>
                        <hr>
                        <!-- END USABILITYBAR -->
                        <?
                        if ($magazzino->ID > 0) {
                            ?>                        
                            <div class="card">                            
                                <div class="card-body">
                                    <?
                                    if ($movimento->ID == 0) {
                                        ?>
                                        <div class="row" >                                            
                                            <div class="col-lg-5">
                                                <span>Seleziona la tipologia di articolo da movimentare</span>
                                            </div>
                                            <div class="col-lg-7">
                                                <div class="form-group">                                                
                                                    <? foreach ($TIPOLOGIA_PRODOTTO as $key => $value) { ?>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tipoProdotto" id="tipoProdotto<?= $key ?>" value="<?= $key ?>">
                                                            <label class="form-check-label" for="inlineRadio<?= $key ?>"><?= $value ?></label>
                                                        </div>
                                                    <? } ?>
                                                </div>
                                            </div>                                        
                                        </div>
                                        <hr>                          
                                        <div class="row" id="div_ProdottiFertilizzanti" style="display:none">                                              
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="ProdottiFertilizzanti">Articolo</label><br/>
                                                    <small>Nota bene nel caso in cui il prodotto non è presente all'interno dell'anagrafica fare la segnalazione al seguente indirizzo email: <a class="text-primary" href="mailto:<?= EMAIL_SEGNALAZIONI ?>" target="_blank"><?= EMAIL_SEGNALAZIONI ?></a></small>
                                                    <select style="width: 100%"  required="" class="form-control cfinput" id="ProdottiFertilizzanti" name="ProdottiFertilizzanti"></select>
                                                </div>                                        
                                            </div>                                            
                                        </div>

                                        <div class="row" id="div_ProdottiFitosanitari" style="display:none">                                            
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="ProdottiFitosanitari">Articolo</label><br/>
                                                    <small>Nota bene nel caso in cui il prodotto non è presente all'interno dell'anagrafica fare la segnalazione al seguente indirizzo email: <a class="text-primary" href="mailto:<?= EMAIL_SEGNALAZIONI ?>" target="_blank"><?= EMAIL_SEGNALAZIONI ?></a></small>
                                                    <select style="width: 100%"  required="" class="form-control cfinput" id="ProdottiFitosanitari" name="ProdottiFitosanitari"></select>
                                                </div>                                            
                                            </div> 
                                        </div>
                                    <? } ?>
                                    <div class="row">
                                        <div id="dett_ProdottiFertilizzanti" class="col-lg-6"></div>
                                        <div id="giacenza_ProdottiFertilizzanti" class="col-lg-6"></div>
                                        <div id="dett_ProdottiFitosanitari" class="col-lg-6"></div>
                                        <div id="giacenza_ProdottiFitosanitari" class="col-lg-6"></div>
                                    </div>    

                                    <div id="div_DettaglioMovimento" style="display:none">
                                        <hr>
                                        <form action="javascript:saveMovimento()" id="movimento-form-magazzino">
                                            <input type="hidden" class="form-control" id="movID_MAGAZZINO" name="ID_MAGAZZINO" value="<?= intval($magazzino->ID) ?>"  >
                                            <input type="hidden" class="form-control" id="movID" name="ID"  >
                                            <input type="hidden" class="form-control" id="movID_ARTICOLO" name="ID_ARTICOLO"  >
                                            <input type="hidden" class="form-control" id="movTIPO_ARTICOLO" name="TIPO_ARTICOLO"  >
                                            <input type="hidden" class="form-control" id="movTIPO" name="TIPO" value="<?= $tipo_movimento ?>" >
                                            <input type="hidden" class="form-control" id="movID_CAUSALE" name="ID_CAUSALE" value="<?= $tipo_movimento ?>" >
                                            <?
                                            if ($tipo_movimento == TIPO_CARICO) {
                                                include_once 'movimenti/carico.php';
                                            } else if ($tipo_movimento == TIPO_SCARICO) {
                                                include_once 'movimenti/scarico.php';
                                            }
                                            ?>
                                        </form>
                                    </div>

                                </div>
                            </div>

                            <?
                        }
                        ?>

                    </div>
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>        
        <script type="text/javascript">

            $(document).ready(function () {
            loadMovimento();
            $('.nav-tabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
            });
            $(".form-check-input").click(function () {

            if ($(this).val() == 'ProdottiFertilizzanti') {
            $("#div_ProdottiFertilizzanti").show();
            $("#div_ProdottiFitosanitari").hide();
            } else if ($(this).val() == 'ProdottiFitosanitari') {
            $("#div_ProdottiFertilizzanti").hide();
            $("#div_ProdottiFitosanitari").show();
            }
            viewDettagliMovimento(false);
            });
            });
            var id = '<?= $magazzino->ID ?>';
            var id_mov = '<?= $id_movimento ?>';
            function loadMovimento() {
            $('#loader').show();
            var object = {
            id: id_mov,
                    module: 'magazzino',
                    action: 'loadMovimento'
            };
            postdataClassic(WS_CALL, object, function (response) {
            $('#loader').hide();
            var prefix = "mov";
            var risp = jQuery.parseJSON(response);
            if (risp.ID > 0) {
            viewDettagliMovimento();
            var objart = {
            id: risp.ID_ARTICOLO,
                    articolo: risp.ARTICOLO_OBJ,
                    tipo_articolo: risp.TIPO_ARTICOLO
            };
            viewDettaglioArticolo(objart);
            var form = document.getElementById('movimento-form-magazzino');
            form.reset();
            //listGiacenze();
            Object.entries(risp).forEach(([key, value]) => {
            if (key == "PATH_FILE" && (value != "" && value !== null)){
            $('#eliminaVisuale').show();
            $('#fileCaricamento').hide();

            var btn = document.createElement("button");
            btn.setAttribute("type", "button");
            btn.innerHTML = "Download File";
            btn.setAttribute("class", "btn btn-info");
            $('#button').attr("button")

                    btn.onclick = function () {
                    openStreamer(value);
                    };
            $('#bodyFile').append(btn);
            var btn1 = document.createElement("button");
            btn1.setAttribute("type", "button");
            btn1.setAttribute("class", "btn btn-danger");
            btn1.innerHTML = "Elimina ";
            $('#button').attr("button");
            btn1.onclick = function () {
            deleteFile(value);
            };
            $('#bodyFileDelete').append(btn1);
            }
            $("#" + prefix + key).val(value);
            });
            }
            });
            }


            function deleteFile(value){
            Swal.fire({
            title: 'Attenzione',
                    text: "Si vuole procedere con l'operazione? Azione irreversibile",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'SI,confermo!'
            }).then((result) => {
            console.log(result);
            if (result.value) {
            var id = $('#movID').val();
            var file = value;
            var objectDelete = {
            id: id,
                    file: file,
                    module: 'magazzino',
                    action: 'deleteFile'

            }
            postdataClassic(WS_CALL, objectDelete, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            if (risp.esito === 1) {
            goToMagazzino('magazzino', '<?= $partita_iva ?>', '#movimenti');
            } else {
            functionSwall('error', risp.erroreDescrizione, '');
            }
            });
            }
            })



            }

            function openStreamer(value)
            {
            var object = {
            value: value,
                    module: 'streamerFile',
                    action: 'download'
            };
            $.redirect(WS_CALL, object, "POST");
            }


            function saveMovimento() {
            $('#loader').show();
            var form = document.getElementById('movimento-form-magazzino');
            var formInvio = new FormData(form);
            formInvio.append('module', 'magazzino');
            formInvio.append('action', 'saveMovimento');
            postdata(WS_CALL, formInvio, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            if (risp.esito === 1) {
            Swal.fire({
            type: 'success',
                    title: 'OK',
                    text: "Operazione completata con successo",
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
            }).then((result) => {
            // loadMovimento();
            goToMagazzino('magazzino', '<?= $partita_iva ?>', '#movimenti');
            });
            } else {
            functionSwall('error', risp.erroreDescrizione, '');
            }
            });
            }


            $("#ProdottiFitosanitari").select2({
            language: "it",
                    minimumInputLength: 3,
                    ajax: {
<? if ($tipo_movimento == TIPO_CARICO) { ?>
                        url: WS_CALL + "?module=magazzino&action=searchFitosanitari",
<? } else if ($tipo_movimento == TIPO_SCARICO && SCARICO_GIACIANZE) { ?>
                        url: WS_CALL + "?module=magazzino&action=searchFitosanitari&id_magazzino=" + id,
<? } else if ($tipo_movimento == TIPO_SCARICO && !SCARICO_GIACIANZE) { ?>
                        url: WS_CALL + "?module=magazzino&action=searchFitosanitari",
<? } ?>
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

<? if ($tipo_movimento == TIPO_CARICO) { ?>
                        url: WS_CALL + "?module=magazzino&action=searchFertilizzanti",
<? } else if ($tipo_movimento == TIPO_SCARICO && SCARICO_GIACIANZE) { ?>
                        url: WS_CALL + "?module=magazzino&action=searchFertilizzanti&id_magazzino=" + id,
<? } else if ($tipo_movimento == TIPO_SCARICO && !SCARICO_GIACIANZE) { ?>
                        url: WS_CALL + "?module=magazzino&action=searchFertilizzanti",
<? } ?>
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
            function viewDettagliMovimento(v = true) {
            //var form = document.getElementById('movimento-form-magazzino');
            //form.reset();
            $('#dett_ProdottiFitosanitari').empty();
            $('#giacenza_ProdottiFitosanitari').empty();
            $('#dett_ProdottiFertilizzanti').empty();
            $('#giacenza_ProdottiFertilizzanti').empty();
            if (v) {
            $("#div_DettaglioMovimento").show();
            } else {
            $("#div_DettaglioMovimento").hide();
            }
            }

            function viewDettaglioArticolo(data){
            console.log(data);
            $('#dett_' + data.tipo_articolo).empty(); // Svuoto il div                 
            //var divArticolo = document.createElement('div');                
            //divArticolo.setAttribute('class', 'mapid');

            var icon, bgcolor;
            if (data.tipo_articolo == 'ProdottiFitosanitari'){
            icon = '<i class="fas fa-syringe"></i>';
            bgcolor = 'bg-primary';
            } else{
            icon = '<i class="fas fa-vial"></i>';
            bgcolor = 'bg-info';
            }


            var htmlArticolo = '<div class="card ' + bgcolor + ' text-white mb-3">';
            htmlArticolo += '<div class="card-header">' + icon + '&nbsp;&nbsp;Scheda tecnica</div>';
            htmlArticolo += '<div class="card-body">';
            if (data.tipo_articolo == 'ProdottiFitosanitari'){
            htmlArticolo += '<b>Numero registrazione:</b> ' + data.articolo.NUMERO_REGISTRAZIONE + '<br/>';
            htmlArticolo += '<b>Attività:</b> ' + data.articolo.ATTIVITA + '<br/>';
            htmlArticolo += '<b>Formulazione:</b> ' + data.articolo.DESCRIZIONE_FORMULAZIONE + '<br/>';
            htmlArticolo += '<b>Fabbricante:</b> ' + data.articolo.IMPRESA + '<br/>';
            htmlArticolo += '<b>Indicazioni:</b> ' + data.articolo.INDICAZIONI_DI_PERICOLO + '<br/>';
            htmlArticolo += '<b>Sostanze attive:</b> ' + data.articolo.SOSTANZE_ATTIVE + '<br/>';
            } else if (data.tipo_articolo == 'ProdottiFertilizzanti'){
            htmlArticolo += '<b>Numero di registro:</b> ' + data.articolo.NUMERO_REGISTRO + '<br/>';
            htmlArticolo += '<b>Nome commerciale:</b> ' + data.articolo.NOME_COMMERCIALE + '<br/>';
            htmlArticolo += '<b>Fabbricante:</b> ' + data.articolo.FABBRICANTE + '<br/>';
            htmlArticolo += '<b>Tipo concime:</b> ' + data.articolo.TIPOLOGIA_CONCIME + '<br/>';
            }
            htmlArticolo += '</div></div>';
            $('#dett_' + data.tipo_articolo).append(htmlArticolo);
            loadGiacenzeArticolo(data);
            }

            function loadGiacenzeArticolo(data){
            $('#loader').show();
            $('#giacenza_' + data.tipo_articolo).empty(); // Svuoto il div                 
            var object = {
            id_magazzino: id,
                    id_articolo: data.articolo.ID,
                    tipo_articolo: data.tipo_articolo,
                    module: 'magazzino',
                    action: 'loadGiacenze'
            };
            postdataClassic(WS_CALL, object, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            var icon, bgcolor;
            icon = '<i class="fas fa-store"></i>';
            if (risp.length > 0 && risp[0].QUANTITA > 0){
            bgcolor = 'bg-success';
            } else{
            bgcolor = 'bg-danger';
            }


            var htmlArticolo = '<div class="card ' + bgcolor + ' text-white mb-3">';
            htmlArticolo += '<div class="card-header">' + icon + '&nbsp;&nbsp;Giacenze di magazzino</div>';
            htmlArticolo += '<div class="card-body">';
            if (risp.length > 0){
            $("#movUNITA_MISURA_A").val(risp[0].UNITA_MISURA_A);
            $("#movUNITA_MISURA").val(risp[0].UNITA_MISURA);
            htmlArticolo += '<h4><b>Quantità:</b> ' + risp[0].QUANTITA + '</h4>';
            htmlArticolo += '<h5><b>Unita di misura:</b> ' + risp[0].UNITA_MISURA_A + '</h5>';
            } else{
            htmlArticolo += '<h4>In magazzino non risultano giacenze per questo articolo</h4>';
            }

            htmlArticolo += '</div></div>';
            $('#giacenza_' + data.tipo_articolo).append(htmlArticolo);
            });
            }

            $('#ProdottiFertilizzanti').on('select2:select', function (e) {
            var data = e.params.data;
            var object = {
            id_articolo: data.id,
                    tipo_articolo: 'ProdottiFertilizzanti',
                    module: 'magazzino',
                    action: 'loadArticolo'
            };
            postdataClassic(WS_CALL, object, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            var res = {};
            //console.log(risp);
            if (risp) {
            res.tipo_articolo = 'ProdottiFertilizzanti';
            res.articolo = risp;
            $("#movID_ARTICOLO").val(risp.ID);
            $("#movTIPO_ARTICOLO").val('ProdottiFertilizzanti');
            viewDettagliMovimento();
            viewDettaglioArticolo(res);
            } else {
            functionSwall('error', "Selezionare un articolo presente nella lista sottostante!", 'error');
            $("#movID_ARTICOLO").val("");
            $("#movTIPO_ARTICOLO").val("");
            viewDettagliMovimento(false);
            $('#dett_ProdottiFertilizzanti').empty();
            }

            });
            });
            $('#ProdottiFitosanitari').on('select2:select', function (e) {

            var data = e.params.data;
            var object = {
            id_articolo: data.id,
                    tipo_articolo: 'ProdottiFitosanitari',
                    module: 'magazzino',
                    action: 'loadArticolo'
            };
            postdataClassic(WS_CALL, object, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            var res = {};
            //console.log(risp);
            if (risp) {
            res.tipo_articolo = 'ProdottiFitosanitari';
            res.articolo = risp;
            $("#movID_ARTICOLO").val(risp.ID);
            $("#movTIPO_ARTICOLO").val('ProdottiFitosanitari');
            viewDettagliMovimento();
            viewDettaglioArticolo(res);
            } else {
            functionSwall('error', "Selezionare un articolo presente nella lista sottostante!", 'error');
            $("#movID_ARTICOLO").val("");
            $("#movTIPO_ARTICOLO").val("");
            viewDettagliMovimento(false);
            $('#dett_ProdottiFitosanitari').empty();
            }

            });
            });
            $("#movUNITA_MISURA_A").autocomplete({
            source: WS_CALL + '?module=codici_vari&action=search&gruppo=UNITA_MISURA',
                    minLength: 1,
                    change: function (event, ui) {
                    try {
                    $("#movUNITA_MISURA_A").val(ui.item.label);
                    $("#movUNITA_MISURA").val(ui.item.id);
                    } catch (er) {
                    //console.warn(er);
                    functionSwall('error', "Inserire un valore valido!", 'error');
                    $("#movUNITA_MISURA").val("");
                    }
                    }
            });


        </script>
    </body>
</html>
