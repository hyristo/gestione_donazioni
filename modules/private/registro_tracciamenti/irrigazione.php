<div class="row irrigazione">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header"><h6>Dati di irrigazione</h6></div>
            <div class="card-body">                
                <div class="row">
                    
                    <div class="col-lg-3">
                        <label for="TIPO_PRELIEVO_ACQUA"><span class="text-danger">*&nbsp;</span>Tipo prelievo</label>
                        <select class="form-control" name="TIPO_PRELIEVO_ACQUA" id="TIPO_PRELIEVO_ACQUA" >
                            <option></option>
                            <?
                            $tipo_prelievo = CodiciVari::Load(0, 'TIPO_PRELIEVO_ACQUA');                            
                            foreach ($tipo_prelievo as $value) {
                                ?>
                                <option value="<?= $value['ID_CODICE'] ?>"><?= $value['DESCRIZIONE'] ?></option>
                            <?}?>
                        </select>                        
                    </div>
                    <div class="col-lg-3">
                        <label for="DURATA_IRRIGAZIONE"><span class="text-danger">*&nbsp;</span>Durata</label>
                        <input type="text" class="form-control" name="DURATA_IRRIGAZIONE" id="DURATA_IRRIGAZIONE" >
                    </div>
                    <div class="col-lg-3">
                        <label for="PORTATA_IRRIGAZIONE"><span class="text-danger">*&nbsp;</span>Portata</label>
                        <select class="form-control" name="PORTATA_IRRIGAZIONE" id="PORTATA_IRRIGAZIONE" >
                            <option></option>
                            <option>l/s</option>
                            <option>mc/h</option>
                            <option>l/min</option>

                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label for="QUANTITA_IRRIGAZIONE"><span class="text-danger">*&nbsp;</span>Quantità</label>
                        <input type="text" class="form-control" name="QUANTITA_IRRIGAZIONE" id="QUANTITA_IRRIGAZIONE" >
                    </div>
                </div>                                
            </div>
        </div>
    </div>
</div>