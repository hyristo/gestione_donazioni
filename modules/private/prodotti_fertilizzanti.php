<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'prodotti_fertilizzanti');
define("THIS_PERMISSION", array('FERTILIZZANTI'));
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
                                <li class="breadcrumb-item active" aria-current="page">Prodotti fertilizzanti</li>
                            </ol>
                        </nav>
                    </div>
                </div>                                    

                <div class="modal fade" id="editFertilizzante" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 70%;"  >
                        <div id="ecommerce-products" class="modal-content">
                            <div class="modal-header">
                                <h4>Prodotto <span id="view_NUMERO_REGISTRO" class="badge badge-success" ></span></h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <h4 id="view_prodotto"></h4>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p>Stato: <span  id="view_CANCELLATO" class="text-success"></span></p>
                                        <p>Nome commerciale: <span  id="view_NOME_COMMERCIALE" class="text-success"></span></p>
                                        <p>Tipologia concime: <span  id="view_TIPOLOGIA_CONCIME" class="text-success"></span></p>
                                        <p>Fabbricante: <span  id="view_FABBRICANTE" class="text-success"></span></p>
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
                                    <span class="navbar-brand mb-0 h1">Elenco dei fertilizzanti</span><br/>
                                    <small>Elenco dei fertilizzanti da utilizzare per il quaderno di campaga</small>
                                </li>
                            </ul>                                
                        </nav>
                    </div>
                </div><br>

                <div class="card">
                    <div class="card-body">
                        <table id="ListFertilizzanti" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Numero registo</th>
                                    <th>Prodotto</th>                                    
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

                
                listFertilizzanti();


            });
            
            
            function listFertilizzanti(){
                
                var dt = $('#ListFertilizzanti');
                
                if ($.fn.DataTable.isDataTable( '#ListFertilizzanti' )){
                    dt.DataTable().ajax.reload(function (json) {});
                } else {
                    dtListFertilizzanti = dt.DataTable({
                        'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=list'
                    },
                    'columns': [
                        {data: 'id'},
                        {data: 'numero_registro'},
                        {data: 'nome_commerciale'},
                        //{data: 'descrizione_formulazione'},
                        //{data: 'sostanze_attive'},
                        {data: 'modifica'},
                        {data: 'cancellato'}
                    ],
                        'initComplete': function (settings, json) {
                            
                        }
                    });
                }
            }
            
            


        </script>
    </body>
</html>