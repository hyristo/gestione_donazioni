<!doctype html>
<?
include '../../lib/api.php';

define("THIS_PERMISSION", array('MAGAZZINO', 'REGISTRO_TRACCIABILITA'));
include_once ROOT . 'layout/include_permission.php';
$LoggedAccount->checkAuthShowAziendaPage();

$azienda = $LoggedAccount->AziendaQiam;

$partita_iva = $azienda['codi_fisc'];

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
                            <h2>REGISTRO DI TRACCIABILITA' DELLE SPECIE</h2>
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
                                  <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"><i class="fas fa-angle-right"></i> Registro di tracciabilità</a>
                                </div>
                            </div>
                        </nav>
                        <hr>
                        <!-- END USABILITYBAR -->
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="card">
                                    <div class="card-body">
                                        <nav class="navbar navbar-expand-lg navbar-light bg-light">                                
                                            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                                <li class="nav-item active">
                                                  <span class="navbar-brand mb-0 h1">MOVIMENTI DI CARICO E SCARICO DELLE SPECIE</span>
                                                </li>
                                            </ul>
                                            <span class="navbar-text">
                                                <button class="btn btn-sm btn-primary" type="button" onclick="goMovimento('<?= $partita_iva ?>')" title="Nuovo movimento" ><i class="fas fa-warehouse"></i> Nuovo movimento</button>                                                

                                            </span>
                                        </nav>
                                        <table id="listSemine" class="table table-striped table-bordered" >
                                            <thead>
                                                <tr>
                                                    <th>ID operazione</th>
                                                    <th>Data operazione</th>
                                                    <th>Tipo operazione</th>
                                                    <th>Prodotto</th>
                                                    <th>Visualizza/Modifica</th>
                                                </tr>
                                            </thead>                               
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <nav class="navbar navbar-expand-lg navbar-light bg-light">                                
                                            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                                <li class="nav-item active">
                                                  <span class="navbar-brand mb-0 h1">GIACENZE DELLE SPECIE</span>
                                                </li>
                                            </ul>                                            
                                        </nav>
                                        <table id="listGiacenze" class="table table-striped table-bordered" >
                                            <thead>
                                                <tr>
                                                    <th>Specie vegetale</th>
                                                    <th>Prodotto</th>
                                                    <th>Giacenza</th>
                                                    <th>Unità di misura</th>
                                                </tr>
                                            </thead>                               
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script src="<?= BASE_HTTP ?>js/registro_tracciabilita.js" type="text/javascript"></script>
        <script type="text/javascript">
                $(document).ready(function () {
                    $('#listSemine').DataTable({
                        'processing': true,
                        'serverSide': true,
                        'serverMethod': 'post',
                        "order": [[0, 'desc']],
                        'ajax': {
                            'url': WS_CALL,
                            'data': {
                                'module': "registro_tracciabilita",
                                'action': "list",
                                'cuaa': '<?= $partita_iva ?>'

                            }
                        },
                        'columns': [
                            {data: 'id', orderable: false},
                            {data: 'data_operazione', orderable: false},
                            {data: 'tipo_operazione', orderable: false},
                            {data: 'prodotto', orderable: false},
                            {data: 'modifica', orderable: false}
                        ]

                    });
                });

                $('#listGiacenze').DataTable({
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': WS_CALL,
                        'data': {
                            'module': "registro_tracciabilita",
                            'action': "listgiacenze",
                            'cuaa': '<?= $partita_iva ?>'

                        }
                    },
                    'columns': [
                        {data: 'specie_vegetale', orderable: false},
                        {data: 'prodotto', orderable: false},
                        {data: 'giacenza'},
                        {data: 'unita_misura', orderable: false}
                    ]
                });
        </script>
    </body>
</html>
