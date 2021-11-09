<?php
ob_start();
//ini_set('session.gc_maxlifetime', 3600);
//session_set_cookie_params(3600);
define("SESSION_NAME", 'AppManager');
session_name(SESSION_NAME);
session_start([
    'read_and_close' => true,
]);

require_once "app.config.php";
require_once ROOT . "lib/constants.php";
require_once ROOT . "lib/classes/Menu.php";

/* CLASSI DI UTILITA' */
require_once ROOT . "lib/import/common.php";

/* APPLICATIVO */
require_once ROOT . "lib/classes/Database.php";
require_once ROOT . "lib/classes/Application.php";
require_once ROOT . "lib/classes/Account.php";
require_once ROOT . "lib/classes/AccountGruppi.php";
require_once ROOT . "lib/classes/UidFirebaseAdmin.php";
require_once ROOT . "lib/classes/Module.php";
require_once ROOT . "lib/classes/Moduli.php";
require_once ROOT . "lib/classes/AccountAnagrafica.php";
require_once ROOT . "lib/classes/AccessLog.php";
if ($config['Faq']) {
    require_once ROOT . "lib/classes/Faq.php";
}
if ($config['SPID']) {
    require_once ROOT . "lib/classes/SPID.php";    
}
if ($config['QDC']) {
    require_once ROOT . "lib/import/qdc.php";
}
if ($config['MAGAZZINO']) {
    require_once ROOT . "lib/import/magazzino.php";
}
if ($config['GESTIONALE']) {
    require_once ROOT . "lib/import/gestionale.php";    
}


/* CLASSI SIAN */
if ($config['SIAN']) {
    require_once ROOT . "lib/import/sian.php";    
}
/* CLASSI SAFE */
if ($config['SAFE']) {
    require_once ROOT . "lib/import/safe.php";
}

if ($config['CARD']) {
    require_once ROOT . "lib/import/card.php";    
}



/* START APP */
if (TEST_INTRUSIONE) {
    $account = SPID::loginFakeStressTest(); /// TEST INTRUSIONE
}
$statoSportello = Utils::checkAuthStatoSportello();

$app = new Application();

switch ($app->lastEsito) {
    case Application::SUCCESS:
        $LoggedAccount = $app->getAccount();            

        
        $con = $app->getCon();
        $conSpid = $app->getConSpid();
        break;
    case Application::ACCESS_ERR:
        exit(json_encode(Utils::initDefaultResponseDataTable(-999, $app->lastMessageError)));
//            exit(json_encode(Utils::initDefaultResponse(-999, $app->lastMessageError)));
        break;
    case Application::ACCESS_WS_ERR:
        exit(0);
        break;
}

global $LoggedAccount, $con, $conSpid, $MENUITEMS, $NAVITEMS, $statoSportello, $PROVINCE_SICILIA, $GRUPPI_CODICIVARI, $CODICI_ERRORE_UPLOAD_P7M, $codiciErroreNoControll,$DESCRIZIONEMOVIMENTO, $MESI;

