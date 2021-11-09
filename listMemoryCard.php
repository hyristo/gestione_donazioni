<!doctype html>
<?
include 'lib/api.php';
?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body >
        <?
        //include_once ROOT . 'layout/header_home.php';
        //include_once ROOT . 'layout/menu.php'; 
        ?>
        <style>
            body{
                padding-top: 1px !important
            }
                 #contentMemoryCard .card, #contentMemoryCard .card-body, #contentMemoryCard .card-body .row {
                    min-width:840px;
                 }

             </style>
        <main >            
            
            <?
            $pro_donazione = CodiciVari::Load(0, 'PRO_DONAZIONE');
            $donazioni = new Donazioni();           
            $dd = $donazioni->MaxData();
            $ultimo_aggiornamento = Date::FormatDate($dd['AGGIORNAMENTO']);
            

            $listMovmenti = $donazioni->Load();
            $card = new AnagraficaMemoryCard();
            $listCard = $card->Load();
            //Utils::print_array($listMovmenti);
            $array_movimenti = array();
            $array_anni = array();
            foreach ($listMovmenti as $value) {
                foreach ($value as $key => $val) {
                    if($key == "ANNO")
                        $array_anni[$key] = $val;
                    //echo $key." => ".$val."<br>";
                }
            }
            //Utils::print_array($array_anni);

            ?>            
             
            <div class="container-fluid" id="contentMemoryCard">
                
                <?foreach ($array_anni as $a => $anno) {?>
                    <div class="card" >
                        <div class="card-header">
                            <h5 class="card-title">Anno <?=$anno?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">Elenco donazioni tramite l'utilizzo delle Memory Card</h6>                            
                        </div>
                        <div class="card-body">                            
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="badge bg-card6">Dati aggiornati al <?=$ultimo_aggiornamento?></h6>
                                </div>
                                <div class="col-6 text-right">
                                    <small>Legenda destinazione donazioni:</small><br/>
                                    <?
                                    foreach ($pro_donazione as $value) {?>
                                        <small class="badge bg-card<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></small>
                                    <?}?>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1 p-0">MESI<i class="far fa-arrow-alt-circle-right"></i></br>CARD<i class="far fa-arrow-alt-circle-down"></i></div>
                                <div class="col-11">                
                                    <div class="row alert-primary">
                                        <?foreach ($MESI as $key => $value) {?>
                                            <div class="col-md-1 p-0 text-center"><small><?=$value?></small></div>
                                        <?}?>                            
                                    </div>
                                </div>
                            </div>
                            <? foreach ($listCard as $card) {?>
                            <div class="row">
                                <div class="col-1 p-1 alert-primary"><i class="far fa-credit-card"></i>&nbsp;<?=$card['CODICE']?></div>
                                <div class="col-11">
                                    <div class="row" style="border-top: 1px solid #ededed;">
                                    <?foreach ($MESI as $key => $value) {?>
                                            <div id="cella-<?=$anno?>-<?=$key?>-<?=$card['ID']?>" class="col-md-1 p-0 text-center"></div>
                                    <?}?>
                                    </div>  
                                </div>
                            </div>
                            <?}?>
                        </div>
                    </div>
                    
                <?}?>
            </div>
        </main>		
        <? include_once ROOT . 'layout/include_js.php'; ?>
    </body>
    <script type="text/javascript">
        $(document).ready(function () {
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });
            <?foreach ($listMovmenti as $value) {
                $pro = CodiciVari::Load($value['PRO_DONAZIONE'], 'PRO_DONAZIONE');
                $c = new AnagraficaMemoryCard($value['ID_CARD']);
                $txt_tooltip = $c->CODICE." - ".$pro['DESCRIZIONE']." - ".$MESI[$value['MESE']]." - ".$value['ANNO'];
                ?>                
               $('#cella-<?=$value['ANNO']?>-<?=$value['MESE']?>-<?=$value['ID_CARD']?>').append('<small data-toggle="tooltip" data-placement="top" title="<?=$txt_tooltip?>" class="badge bg-card<?=$value['PRO_DONAZIONE']?>">&euro;. <?=number_format($value['IMPORTO'],2)?></small><br/>');
            <?}?>
        });
    </script>

</html>
