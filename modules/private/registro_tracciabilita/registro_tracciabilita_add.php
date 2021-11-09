<!doctype html>
<?
include '../../../lib/api.php';

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
                                    <a class="nav-link " href="#" tabindex="-1" onclick="goToAzienda('registro_tracciabilita', '<?= $partita_iva ?>');" aria-disabled="true"><i class="fas fa-angle-right"></i> Registro di tracciabilità</a>
                                    <a class="nav-link disabled" href="#"  tabindex="-1" aria-disabled="true"><i class="fas fa-angle-right"></i> Nuovo movimento</a>
                                </div>
                            </div>
                        </nav>
                        <hr>
                        <!-- END USABILITYBAR -->
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="javascript:saveMovimento()" id="add-form-movimento">
                                    <input type="hidden" value="<?= $partita_iva ?>" name="CUAA">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="NOME">Tipo operazione<span class="text-danger">*</span></label>
                                                <select class="form-control" required="" name="TIPO_OPERAZIONE" id="TIPO_OPERAZIONE">
                                                    <option ></option>
                                                    <option value="<?= CARICO ?>" >CARICO</option>
                                                    <option value="<?= SCARICO ?>" >SCARICO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="DESCRIZIONE">Data operazione<span class="text-danger">*</span></label>
                                                <input type="date" required="" required="" autocomplete="off" class="form-control"   name="DATA_OPERAZIONE"  placeholder="Descrizione">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <?
                                                $specie_vegetali = MaterialiForestali::getTiplogia();
                                                ?>
                                                <label for="COMUNE">Specie vegetale<span class="text-danger">*</span></label>
                                                <select name="SPECIE_VEGETALE"  required="" class="form-control" id="SPECIE_VEGETALE">
                                                    <option></option>                                           
                                                    <?
                                                    foreach ($specie_vegetali as $value) {
                                                        ?>
                                                        <option value="<?= trim($value['TIPOLOGIA']) ?>" <?= (trim($movimento->specie_vegetale) == trim($value['TIPOLOGIA']) ? 'selected' : '') ?> ><?= trim($value['TIPOLOGIA']) ?></option>
                                                    <? } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="INDIRIZZO">Nome Botanico<span class="text-danger">*</span></label>
                                                <input type="text" required=""  required="" autocomplete="off" class="form-control" id="NOME_BOTANICO_V"   name="NOME_BOTANICO_V"  placeholder="Nome botanico">
                                                <input type="hidden" required="" required=""  autocomplete="off" class="form-control" id="NOME_BOTANICO"   name="NOME_BOTANICO"  placeholder="Indirizzo">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Nome Commerciale<span class="text-danger">*</span></label>
                                                <input type="text" required="" autocomplete="off" class="form-control"   name="NOME_COMMERCIALE"  placeholder="Nome Commerciale">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <div class="carico">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <p class="text-success" >Riportare in questo riquadro le operazioni di carico relative all’acquisto, produzione, acquisizione a qualunque titolo dei materiali vegetali oggetto di registrazione.</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-group">
                                                                <label for="exampleInputPassword1">Provenienza del materiale<span class="text-danger">*</span></label>
                                                                <input type="text" required="" id="PAESE_PROVENIENZA" autocomplete="off" class="form-control"   name="PAESE_PROVENIENZA"  placeholder="Indicare la provenienza del materiale in carico">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="scarico">
                                                    <p class="text-danger" >Riportare in questo riquadro le operazioni di scarico, relative alla vendita o cessione a qualunque titolo dei materiali vegetali oggetto di registrazione.</p>
                                                    <div class="form-group">
                                                        <label for="exampleInputPassword1">Destinazione del materiale<span class="text-danger">*</span></label>
                                                        <input type="text" required="" id="PAESE_DESTINAZIONE" autocomplete="off" class="form-control"   name="PAESE_DESTINAZIONE"  placeholder="Indicare la destinazione (luogo e destinatario) dei materiali oggetto di scarico">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Quantià<span class="text-danger">*</span></label>
                                                <input type="text" required="" autocomplete="off" class="form-control"  name="QUANTITA"  placeholder="Quantità">
                                            </div>
                                        </div>
                                        <?
                                        $unita_misura = CodiciVari::Load('', 'UNITA_MISURA');
                                        ?>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Unità di misura<span class="text-danger">*</span></label>
                                                <select  required=""  class="form-control"  name="UNITA_MISURA"  >
                                                    <option></option>
                                                    <? foreach ($unita_misura as $value) { ?>
                                                        <option value="<?= trim($value['ID_CODICE']) ?>" <?= (intval($movimento->unita_misura) == intval($value['ID_CODICE']) ? 'selected' : '') ?> ><?= trim($value['DESCRIZIONE']) ?></option>
                                                    <? } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Passaporto<span class="text-danger">*</span></label>
                                                <input type="text" required="" autocomplete="off" class="form-control"   name="NUMERO_PASSAPORTO"  placeholder="di cui al Titolo V del D. Lgs. 214/2005, se presente">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Note<span class="text-danger">*</span></label>
                                                <textarea rows="8" class="form-control" name="NOTE"></textarea>
                                            </div>
                                        </div>
                                    </div>     
                                    <div class="carico">
                                        <div  id='fileCaricamento'  >
                                            <div class="row" >
                                                <div class="col-lg-12">
                                                    <label for="PATH_FILE">Carica il documento d'acquisto</label>
                                                    <input type="file" accept="application/pdf" class="form-control-file"  name="PATH_FILE">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-sm btn-danger" type="button" onclick="goToAzienda('registro_tracciabilita', '<?= $partita_iva ?>')" title="Annulla" >Annulla</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Salva</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script src="../../../js/registro_tracciabilita.js" type="text/javascript"></script>
        <script type="text/javascript">
                                            $(document).ready(function () {


<? /* if ($movimento->tipo_operazione == 'CARICO') { ?>
  $('.carico').show();
  $('.scarico').hide();
  <? } elseif ($movimento->tipo_operazione == 'SCARICO') { ?>
  $('.carico').hide();
  $('.scarico').show();
  <? } else { ?>
  $('.carico').hide();
  $('.scarico').hide();
  <? } */ ?>
                                                $('.carico').hide();
                                                $('.scarico').hide();
//                $('#TIPO_OPERAZIONE').on('change', function (e) {
//                    var str = "";
//                    $("#TIPO_OPERAZIONE option:selected").each(function () {
//                        str += $(this).text();
//                    });
//                    console.log("=============>" + str);
//                    if (str ===<?= CARICO ?>) {
//                        $('.carico').show();
//                        $('.scarico').hide();
//                    } else if (str === <?= SCARICO ?>) {
//                        $('.carico').hide();
//                        $('.scarico').show();
//                    } else {
//                        $('.carico').hide();
//                        $('.scarico').hide();
//                    }
//                });
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
                                                        {data: 'progressivo', orderable: false},
                                                        {data: 'data_operazione', orderable: false},
                                                        {data: 'prodotto', orderable: false},
                                                        {data: 'tipo_operazione', orderable: false},
                                                        {data: 'modifica', orderable: false}
                                                    ]

                                                });
                                            });
        </script>
    </body>
</html>
