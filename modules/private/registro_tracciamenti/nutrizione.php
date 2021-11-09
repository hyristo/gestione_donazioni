<div class="row  nutrizione "  >
    <div class="col lg 12">
        <br>
        <div class="card">
            <div class="card-body">
                <br>
                <div class="row">
                    <div class=" col lg 6">
                        <label>Operazione</label>
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
                </div>
                <br>
                <div class="row">
                    <div class="col lg 4">
                        <i class="fas fa-edit"></i> Composizione Azoto(%) 
                        <input type="text" class="form-control" name="COMPOSIZIONE_AZOTO"  id="COMPOSIZIONE_AZOTO" >
                    </div>
                    <div class="col lg 4">
                        <i class="fas fa-edit"></i> Fosforo(%) 
                        <input type="text" class="form-control" name="COMPOSIZIONE_FOSFORO"  id="COMPOSIZIONE_FOSFORO" >
                    </div>
                    <div class="col lg 4">
                        <i class="fas fa-edit"></i> Potassio(%) 
                        <input type="text" class="form-control" name="COMPOSIZIONE_POTASSIO"  id="COMPOSIZIONE_POTASSIO" >
                    </div>
                </div>
                <br>
                <?
                include 'dif_nutri.php';
                ?>

            </div>
        </div>
    </div>
</div>