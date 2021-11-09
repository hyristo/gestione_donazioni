<!doctype html>
<?
include '../../lib/api.php';
//$LoggedAccount->checkAuthShowAziendaPage();
/*
$url = "https://spacesapis.avayacloud.com/api/anonymous/auth";

$data = array(
        "displayname" => "anonymous ab",
        "username" => "aUniqueNameABC"
    );

$res = Utils::CallAPI('POST', $url, json_encode($data));
$res = json_decode($res);

$decode = base64_decode($res->token);

Utils::print_array($decode);

 
 */

Utils::print_array($LoggedAccount);
exit();
$ufficioCaaf = new AnagraficaUffici(intval($LoggedAccount->ID_ENTE));
//$aziende = $rappresentanteLegale->loadMandatiFromUfficio();
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
                            <i style="font-size: 155px" class="fas fa-tractor"></i>
                        </div>
                        <div class="col-lg-5 col-sm-5">
                            <div class="card border-light mb-3 text-primary">
                                <div class="card-header">
                                    <h5> <?= $ufficioCaaf->descrestuff ?></h5>                                    
                                </div>                                
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
                    <div class="<?= $colCssContainer ?> bd-content">
                    <?
                    //Utils::print_array($anagraficaRappresentante);
                    //Utils::print_array($aziende);exit();
                    ?>
                        <div class="card bg-light">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-8 ">
                                        <i class="fas fa-briefcase"></i>
                                        &nbsp;&nbsp;Elenco aziende mandatarie
                                    </div>                                    
                                </div>
                            </div>
                            <div class="card-body bg-light">
                                <table id="lista_aziende" class="table table-striped table-bordered" style="width:100%">
                                    <thead>                                        
                                        <tr>
                                            <th>id_soggetto</th>                                           
                                            <th>Fascicolo</th>                                           
                                            <th style="white-space:nowrap;">Nominativo</th>                    
                                            <th style="white-space:nowrap;">CUAA</th>
                                            <th style="white-space:nowrap;">Comune</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>                                    
                                </table>
                            </div>                            
                        </div>    
                    </div>
                </div>
            </div>
        </main>
<? include_once ROOT . 'layout/footer.php'; ?>
        <script type="text/javascript">
            $(document).ready(function () {
                loadAziende();
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
                    prg_scheda: azienda
                };
                $.redirect(url + ".php", object);
            }
            var dtAziende = null;
            function loadAziende(reload) {

                var dt = $('#lista_aziende');

                if (!reload) {

                    dtAziende = dt.DataTable({
                        'searching': true,
                        'processing': true,
                        'serverSide': true,
                        'paging': true,
                        "info": true,
                        'serverMethod': 'post',
                        "order": [[1, 'ASC']],
                        'ajax': {
                            'url': WS_CALL,
                            'data': {

                                'module': 'cuaa',
                                'action': 'list'
                            }
                        },

                        'columns': [
                            {data: 'id_sogg', orderable: false, "visible": false},
                            {"width": "15%",data: 'prg_scheda', orderable: false},                            
                            {"width": "45%", data: 'nominativo', orderable: false},
                            {"width": "15%", data: 'codi_fisc', orderable: false},
                            {"width": "15%", data: 'comune', orderable: false},
                            {"width": "10%", data: 'OP', orderable: false}
                        ],
                        'initComplete': function (settings, json) {
                            
                        }
                    });

                } else {
                    dt.DataTable().ajax.reload(function (json) {
                        
                    });
                }

            }
            
            
            
        </script>
    </body>
</html>
