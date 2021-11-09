
<div class="row">       
    <div class="col-lg-4">
        <div class="form-group">
            <label for="DATA_MOVIMENTO">Data del movimento</label>
            <input type="date" required="" autocomplete="off" class="form-control" id="movDATA_MOVIMENTO" name="DATA_MOVIMENTO">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="QUANTITA">Quantità</label>
            <input type="number" min="0"  required="" autocomplete="off" class="form-control" id="movQUANTITA" name="QUANTITA" >
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="UNITA_MISURA">Unita di misura</label>
            <input type="text" readonly=""  name="UNITA_MISURA_A" class="form-control"  id="movUNITA_MISURA_A" class="validate" required/>
            <input type="hidden"  name="UNITA_MISURA" id="movUNITA_MISURA" />
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <label for="NOTE">Note</label>
            <textarea type="text" autocomplete="off" class="form-control" id="movNOTE" name="NOTE"></textarea>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-sm btn-danger" type="button" onclick="goToMagazzino('magazzino', '<?= $prg_scheda ?>')" title="Nuovo movimento di scarico" >Annulla</button>
    <?if($movimento->ID_QDC == ''){?>
        <button type="submit" class="btn btn-sm btn-primary">Salva</button>
    <?}?>
</div>
