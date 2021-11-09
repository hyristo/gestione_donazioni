<?php

define("DATE_FORMAT_ITA", 1);
define("DATE_FORMAT_ISO", 2);
define("DATE_FORMAT_ITA_WITHOUT_SEP", 3);
define("ATTIVA_NAVBAR", false);
define("ATTIVA_MENUBAR", true);

/* ERRORI METODO GOOGLE */
define("EMAIL_EXISTS", "Email già registrata");
define("OPERATION_NOT_ALLOWED", "Operazione non permessa");
define("TOO_MANY_ATTEMPTS_TRY_LATER", "Operazione bloccata a causa di diversi tentativi inusuali da questo dispositivo");
define("EMAIL_NOT_FOUND", "Email non presente");
define("INVALID_PASSWORD", "Password errata");
define("USER_DISABLED", "Utente Disabilitato");
define("INVALID_ID_TOKEN", "E' necessario rieseguire l'autenticazione al sistema");
define("USER_NOT_FOUND", "Utente non trovato");
define("WEAK_PASSWORD", "La password deve essere di almeno 8 caratteri");
//define("KEY_AZIENDA", "sip@rs_B@nd0!2020");
define("CONTRIBUTO_MAX_10_7_1", 60);
define("CONTRIBUTO_MAX_10_8_1", 30);
define("PERCENTUALE", 40);
define("MESI", 12);
define("MESI_RIFERIMENTO", 2);
define("SECRET_JWT_KEY", "marco");
define("REGIME_FORFETTARIO", 0);
define("REGIME_ORDINARIO", 1);
define("LUNGHEZZA_CAP", 5);
define("CONTROLLO_BIRTHDATE", true); // Attiva il controlla sulla data di nascita (Non si possono inserire date inferiori alla data di nascita dell'utente loggato)
define("CONTROLLO_TODATE", true); // Attiva il controlla sulla data di oggi (Non si possono inserire date superiori alla data DATA_CONTROLLO_ATTUALE )
define("CONTROLLO_TITOLO_STUDIO", true); // true = controlla solo il titolo di studio più alto;  false = controlla tutti i titoli di studio in base alla posizione in anagrafica
define("DATA_CONTROLLO_ATTUALE", date("Y-m-d"));
define("DOWNLOAD_PDFMERGE", TRUE); // SE è TRUE SI PUò FARE IL MERGE DEI FILE, FALSE NO
define("CONTROLLO_ANZIANITA_POSIZIONE", true); // se impostato a true viene effettuato il controllo al login sull'anzianita di posizione
define("MIN_ANZIANITA_POSIZIONE", 36); // Possono partecipare alla PEO chi ha almeno 36 mesi di anzianità nella posizione alla data del DATA_FINE_CALCOLO_ESPERIENZA  




$PROVINCE_SICILIA = array(
    'PA' => array("sigla" => "PA", "nome" => "PALERMO"),
    'AG' => array("sigla" => "AG", "nome" => "AGRIGENTO"),
    'TP' => array("sigla" => "TP", "nome" => "TRAPANI"),
    'CT' => array("sigla" => "CT", "nome" => "CATANIA"),
    'CL' => array("sigla" => "CL", "nome" => "CALTANISSETTA"),
    'EN' => array("sigla" => "EN", "nome" => "ENNA"),
    'ME' => array("sigla" => "ME", "nome" => "MESSINA"),
    'RG' => array("sigla" => "RG", "nome" => "RAGUSA"),
    'SR' => array("sigla" => "SR", "nome" => "SIRACUSA")
);


/*
  #####################################################################################
  ##  ATTRIBUTO CHE IDENTIFICA SU QUALE OGGETTO APPLICARE I CONTROLLI DEI PERMESSI   ##
  #####################################################################################
 */
define('RELPREPARADOMANDA', "siparsPreparaDomanda");
define('RELDCLICKDAY', "siparsClickDay");


/*
  ######################################
  ##  CONFIGURAZIONI PER IL MAGAZZINO ##
  ######################################
 */
define('TIPO_CARICO', 1);
define('TIPO_SCARICO', -1);
$DESCRIZIONEMOVIMENTO[TIPO_CARICO]= 'CARICO';
$DESCRIZIONEMOVIMENTO[TIPO_SCARICO]= 'SCARICO';

define('CLASSE_FITOSANITARI', 'ProdottiFitosanitari');
define('CLASSE_FERTILIZZANTI', 'ProdottiFertilizzanti');
$TIPOLOGIA_PRODOTTO = array(
    CLASSE_FERTILIZZANTI => 'Fertilizzanti e concimi',
    CLASSE_FITOSANITARI => 'Fitosanitari'
);


/*
  ########################
  ## GRUPPI CODICI VARI ##
  ########################
 */
$GRUPPI_CODICIVARI = array(
    "TIPO_INTERVENTO" => "Tipo intervento",
    "STADIO_FENOLOGICO" => "Stadio fenologico",
    "TIPO_AVVERSITA" => "Tipo avversità",
    "UNITA_MISURA" => "Unita di misura",
    "OPERAZIONE" => "Operazione",
    "CAUSALI_MOVIMENTO" => "Causali movimento",
    "STATO_REFERTO_CAMPIONI" => "Stato referto",
    "TIPO_PRELIEVO_ACQUA" => "Tipo prelievo",
    "TIPO_DONAZIONE" => "Tipo donazione",
    "PRO_DONAZIONE" => "Pro donazione"
);


/* CONFIGURAZIONE REGISTRO TRACCIABILITA' */
define("CARICO", 1);
define("SCARICO", 2);

/* CONFIGURAZIONE REGISTRO TRACCIAMENTO */

define("DIFESA", 2);
define("IRRIGAZIONE", 3);
define("NUTRIZIONE", 1);
define("OPERAZIONE", 4);
define("RACCOLTA", 5);

$STADIO_FENALOGICO = array(
    1 => "ACCRESCIMENTO",
    2 => "ALLEGAGIONE",
    3 => "FIORITURA",
    4 => "GERMOGLIAMENTO",
    5 => "INGROSSAMENTO ACINO",
    6 => "MATURAZIONE",
    7 => "PRE FIORITURA",
    8 => "SCAMICIATURA"
);

define("SCARICO_GIACIANZE", true); // se impostata a true nei movimenti di scarico manuali di magazzino il sistema visualizza solo gli articoli presenti nelle giacenze

define("EMAIL_SEGNALAZIONI", 'agri.protocolloeurp@regione.sicilia.it');



$MESI = array(
    '1' => 'Gennaio',
    '2' => 'Febbraio',
    '3' => 'Marzo',
    '4' => 'Aprile',
    '5' => 'Maggio',
    '6' => 'Giugno',
    '7' => 'Luglio',
    '8' => 'Agosto',
    '9' => 'Settembre',    
    '10' => 'Ottobre',    
    '11' => 'Novembre',    
    '12' => 'Dicembre'
);