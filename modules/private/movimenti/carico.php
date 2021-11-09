<div class="row">                                                
    <div class="col-lg-5">
        <div class="form-group">
            <label for="FORNITORE">Fornitore</label>
            <input type="text" required="" autocomplete="off" class="form-control" id="movFORNITORE" name="FORNITORE"  placeholder="Ragione sociale del Fornitore">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="DATA_ACQUISTO">Data di acquisto</label>
            <input type="date" required="" autocomplete="off" class="form-control" id="movDATA_ACQUISTO" name="DATA_ACQUISTO">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="DOCUMENTO_ACQUISTO">Riferimenti del documento</label>
            <input type="text" required="" autocomplete="off" class="form-control" id="movDOCUMENTO_ACQUISTO" name="DOCUMENTO_ACQUISTO">
        </div>
    </div>
</div>
<div class="row">                                            
    <div class="col-lg-3">
        <div class="form-group">
            <label for="DESTINAZIONE_USO">Destinazione d'uso</label>
            <select required=""  class="form-control" id="movDESTINAZIONE_USO" name="DESTINAZIONE_USO">
                <?
                foreach ($destinazione_uso as $value){
                ?>
                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                <?}?>
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="UNITA_MISURA">Unita di misura</label>
            <input type="text"  name="UNITA_MISURA_A" class="form-control"  id="movUNITA_MISURA_A" class="validate" required/>
            <input type="hidden"  name="UNITA_MISURA" id="movUNITA_MISURA" />

        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="QUANTITA">Quantità</label>
            <input type="number" min="0" step="0.01"  required="" autocomplete="off" class="form-control" id="movQUANTITA" name="QUANTITA" >
        </div>
    </div>    
    <div class="col-lg-3">
        <div class="form-group">
            <label for="COSTO">Costo</label>
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">&euro;</div>
                </div>
                <input type="number" required="" step="0.01" autocomplete="off" class="form-control" id="movCOSTO" name="COSTO">
            </div>

        </div>
    </div>
</div>
<div class="row">       
    <div class="col-lg-3">
        <div class="form-group">
            <label for="DATA_MOVIMENTO">Data del movimento</label>
            <input type="date" required="" autocomplete="off" class="form-control" id="movDATA_MOVIMENTO" name="DATA_MOVIMENTO">
        </div>
    </div>
    <div class="col-lg-9">
        <div class="form-group">
            <label for="NOTE">Note</label>
            <textarea type="text" autocomplete="off" class="form-control" id="movNOTE" name="NOTE"></textarea>
        </div>
    </div>
</div>
<div id='eliminaVisuale' style="display:none" >
    <div class="row">
        <div class="col-lg-6">
            <div class="card"   >
                <div class="card-body" id='bodyFile'>
                    <h5 class="card-title">Download File</h5>
                    <p class="card-text">Scarica il File per visualizzare il documento caricato </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card"   >
                <div class="card-body" id='bodyFileDelete'>
                    <h5 class="card-title">Elimina File</h5>
                    <p class="card-text">Attenzione la seguente operazione comporterà l'eliminazione del file </p>
                </div>
            </div>
        </div>
    </div><br>
</div>
<div  id='fileCaricamento' >
    <div class="row"  >
        <div class="col-lg-12">
            <label for="PATH_FILE">Carica il documento d'acquisto</label>
            <input type="file" accept="application/pdf" class="form-control-file"  name="PATH_FILE">
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-sm btn-danger" type="button" onclick="goToMagazzino('magazzino', '<?= $prg_scheda ?>')" title="Nuovo movimento di scarico" >Annulla</button>
    <? if ($movimento->ID_QDC == '') { ?>
        <button type="submit" class="btn btn-sm btn-primary">Salva</button>
    <? } ?>
</div>