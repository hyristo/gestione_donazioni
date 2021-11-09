<?
### CAMPI DIFESA
?>
<div class="row difesa  m-1" >
    <div class="col-lg-12">        
        <div class="card">
            <div class="card-header">
                <label>Stadio</label>
                <select class="form-control " name="STADIO_FENOLOGICO" id="STADIO_FENOLOGICO"></select>
            </div>
            <div class="card-body">                
                <div class="row">
                    <div class="col-lg-6">
                        <label for="AVVERSITA">Avversità</label> 
                        <select  name="AVVERSITA"  id="AVVERSITA" class="form-control" >
                            <option value=""></option>
                            <?
                            foreach ($avversita as $value) {
                                ?>
                                <option value="<?= $value['ID_CODICE'] ?>"><?= $value['DESCRIZIONE'] ?></option>
                            <? }
                            ?>
                        </select>                        
                    </div>
                    <div class="col-lg-6">
                        <label for="PRINCIPIO_ATTIVO">Principio Attivo </label>
                        <input type="text" class="form-control" id="PRINCIPIO_ATTIVO" name="PRINCIPIO_ATTIVO" >
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>
<?
### CAMPI NUTRIZIONE
?>
<div class="row  nutrizione m-1"  >
    <div class="col-lg-12">        
        <div class="card">
            <div class="card-header">
                <label for="ID_OPERAZIONE">Operazione</label>
                <select     name="ID_OPERAZIONE" class="form-control"  id="ID_OPERAZIONE" >
                    <option value=""></option>
                    <?
                    foreach ($operazione as $value) {
                        ?>
                        <option value="<?= $value['ID_CODICE'] ?>"><?= $value['DESCRIZIONE'] ?></option>
                    <? }
                    ?>
                </select>
            </div>
            <div class="card-body">                                
                <div class="row">
                    <div class="col-lg-4">
                        <label for="COMPOSIZIONE_AZOTO">Composizione Azoto(%) </label>
                        <input type="text" class="form-control" name="COMPOSIZIONE_AZOTO"  id="COMPOSIZIONE_AZOTO" >
                    </div>
                    <div class="col-lg-4">
                        <label for="COMPOSIZIONE_FOSFORO">Fosforo(%)</label> 
                        <input type="text" class="form-control" name="COMPOSIZIONE_FOSFORO"  id="COMPOSIZIONE_FOSFORO" >
                    </div>
                    <div class="col-lg-4">
                        <label for="COMPOSIZIONE_POTASSIO">Potassio(%) </label>
                        <input type="text" class="form-control" name="COMPOSIZIONE_POTASSIO"  id="COMPOSIZIONE_POTASSIO" >
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
<?
### CAMPI IN COMUNE TRA DIFESA E NUTRIZIONE
?>
<div id="difesa_nutrizione" class="row m-1" style="display: none">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header"><h6>Altri dati</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Dose Utilizzata </label>                        
                        <input type="text" autocomplete="off"  class="form-control" name="DOSE_UTILIZZATA" id="DOSE_UTILIZZATA" >
                    </div>                    
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="DOSE_HA">Dose/Ha </label>
                        <input type="text" autocomplete="off"  class="form-control" name="DOSE_HA" id="DOSE_HA"  >
                    </div>
                    <div class="col-lg-6">
                        <label for="TEMPO_RIENTRO">Intervallo di Sicurezza(gg)</label>
                        <input type="text" autocomplete="off"  class="form-control" name="TEMPO_RIENTRO" id="TEMPO_RIENTRO">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="VOLUME_L_HA">Volume(l/ha) </label>
                        <input type="text" autocomplete="off"  class="form-control" name="VOLUME_L_HA" id="VOLUME_L_HA" >
                    </div>
                    <div class="col-lg-6">
                        <label for="VOLUME_ACQUA_UTILIZZATA">Volume Acqua Utilizzata </label>
                        <input type="text" autocomplete="off"  class="form-control" name="VOLUME_ACQUA_UTILIZZATA" id="VOLUME_ACQUA_UTILIZZATA"  >
                    </div>
                </div>                
                <div class="row">
                    <div class="col-lg-6">
                        <label for="AUTORIZZAZIONE_TECNICA">Autorizzazione Tecnica</label>
                        <select class="form-control" name="AUTORIZZAZIONE_TECNICA" id="AUTORIZZAZIONE_TECNICA">
                            <option></option>
                            <option>SI</option>
                            <option>NO</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="ADDETTO_AL_TRATTAMENTO">Addetto al trattamento</label>
                        <input type="text"  autocomplete="off"  class="form-control" name="ADDETTO_AL_TRATTAMENTO" id="ADDETTO_AL_TRATTAMENTO"  >
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="METODO_MACCHINA">Metodo o Macchina</label>
                        <select class="form-control" name="METODO_MACCHINA" id="METODO_MACCHINA">
                            <option></option>
                            <option value="METODO">METODO</option>
                            <option value="MACCHINA">MACCHINA</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label for="ACQUA_RISCIACQUO_ECCESSO">Acqua di Risciaquo/Eccesso</label>
                        <input type="text" class="form-control" name="ACQUA_RISCIACQUO_ECCESSO" id="ACQUA_RISCIACQUO_ECCESSO" >
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
