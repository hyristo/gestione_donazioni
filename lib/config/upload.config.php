<?php
/* PATH */
define("ROOT_UPLOAD", ROOT . "upload_peo/");
define("ROOT_IMPORT", ROOT . "import/");
define("ROOT_UPLOAD_PRESENTAZIONE", ROOT_UPLOAD . "presentazione/");
define("ROOT_UPLOAD_PRESENTAZIONE_TMP", ROOT_UPLOAD . "presentazionetmp/");
define("ROOT_UPLOAD_PRESENTAZIONE_LOGS_P7M", ROOT_UPLOAD . "presentazionelogsp7m/");
define("ROOT_UPLOAD_DOCUMENTI", ROOT . "upload/");
//define("ROOT_UPLOAD_PREPARAZIONE", ROOT_UPLOAD . "preparazione/");
//define("ROOT_UPLOAD_PREPARAZIONE_TMP", ROOT_UPLOAD . "preparazionetmp/");
//define("ROOT_UPLOAD_EROGAZIONE", ROOT_UPLOAD . "erogazione/");
//define("ROOT_UPLOAD_EROGAZIONE_TMP", ROOT_UPLOAD . "erogazionetmp/");

/* SIZE */
define("MAX_NUM_UPLOAD_OPZ", 5); //MAX Numero file opzionali caricabili
define("MAX_FILE_SIZE", 10485760); // 10 MB
define("MAX_SIZE_UPLOAD_OPZ", 5242880); // 5 MB
define("MAX_SIZE_UPLOAD", 10485760); // 10 MB
define("MAX_SIZE_FILE", 10485760); // 10 MB
define("MAX_SIZE_FILE_P7M", 10485760); //20971520 -> 20MB

/*  ################## GESTIONE CONTROLLI SU FILE p7M ################### */
define("DEMO_UPLOAD_P7M", 0); // Disable check upload file p7m
define('DISABLED_HASH_CONTROL', false); // se impostata a true viene omesso il controllo sull'hash del file pdf
define('SBLOCCO_UPLOAD_FILE_P7M', false); // se impostata a true vengono bypassati tutti i controlli relativi alla verifica del file p7m
$CODICI_ERRORE_UPLOAD_P7M[202079] = "Il codice fiscale del soggetto firmatario del certificato di firma non è corretto";
$CODICI_ERRORE_UPLOAD_P7M[2020100] = "Certificato di firma digitale scaduto";
$CODICI_ERRORE_UPLOAD_P7M[2020276] = "Al momento non è possibile completare il processo di verifica del file firmato";
$CODICI_ERRORE_UPLOAD_P7M[2020277] = "Al momento non è possibile estrarre il contenuto del file firmato";
$CODICI_ERRORE_UPLOAD_P7M[2020280] = "L'hash del file caricato non corrisponde all'hash del file estratto dalla piattaforma. Esportare nuovamente il file nel caso la domanda sia stata oggetto di modifica o nel caso in cui sia stato nuovamente esportato il file pdf.";
$codiciErroreNoControll = array(202079, 2020100, 2020276, 2020280); // se SBLOCCO_UPLOAD_FILE_P7M è impostata a true vengono bypassati i controlli per i codici di errore inseriti nell'array

/* TIPI RICHISTA FILE SIZE (MODULI FASE COMPLETAMENTO DOMANDA) */
$TIPO_RICHIESTA_FILESIZE = array(
    0 => "5",
    1 => "5",
    2 => "10",
    3 => "20"
);

/* DEFINIZIONE DELLE FASI PER LA TABELLA DOCUMENTI BANDO */
define('DOCUMENTI_FASE_1' ,1);
define('DOCUMENTI_FASE_2' ,2);
define('DOCUMENTI_FASE_3' ,3);

/* DEFINIZIONE DEI TIPI DI DOCUMENTO CARICATI IN TABELLA ALLEGATI */
define('TIPO_UPLOAD_OBBLIGATORIO' ,1);
define('TIPO_UPLOAD_OPZIONALE' ,2);
define('TIPO_UPLOAD_ISTANZA' ,3);
define('TIPO_UPLOAD_CONTRIBUTO' ,4);

?>