<?php

define('SUPER_USER', 1);

define('GRUPPO_AZIENDE', 34);
define('GRUPPO_CAA', 35);
define('GRUPPO_AMMINISTRATORE', 1);
define('GRUPPO_CARD', 2);
define("ROLE_CODE_FAKE", ''); // IMPOSTARE IL GRUPPO SOLO PER BYPASSARE IL ROLE_CODE DELLO IAM


$MENUITEMS[] = array(
    "id" => "DASHBOARD",
    "text" => "Dashboard",
    "description" => "La tua pagina iniziale",
    "icon" => "fas fa-tachometer-alt",
    "url" => HTTP_PRIVATE_SECTION . "index.php",    
    "handler" => "",
    "super_user" => 0,
    "group_id" => array()
);
/*$MENUITEMS[] = array(
    "id" => "FAQ",
    "text" => "F.A.Q.",
    "description" => "Elenco delle F.A.Q.",
    "icon" => "fas fa-tasks",
    "url" => HTTP_PRIVATE_SECTION . "faq.php",
    "handler" => "javascript:checkViewConsent('" . HTTP_PRIVATE_SECTION . "faq.php" . "')"
);*/
/*
$MENUITEMS[] = array(
    "id" => "DASHBOARDAZIENDA",
    "text" => "QdC",
    "description" => "Vai al quaderno di campagna dell'azienda scelta",
    "icon" => "fas fa-tractor",
    "url" => HTTP_PRIVATE_SECTION . "dashboard.php",    
    "handler" => "",
    "super_user" => SUPER_USER,
    "group_id" => array(GRUPPO_AMMINISTRATORE),
);
*/
$MENUITEMS[] = array(
    "id" => "ANAGRAFICHE",
    "text" => "Anagrafiche",
    "description" => "Gestione anagrafiche",
    "icon" => "fas fa-cogs",
    "url" => "#",
    "handler" => "",
    "super_user" => SUPER_USER,
    "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_CARD),
    "children" => array(        
        array(
            "id" => "CODICI",
            "text" => "Codici vari",
            "description" => "Anagrafica codici vari",
            "icon" => "fas fa-code",
            "url" => HTTP_PRIVATE_SECTION . "codici_vari.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_CARD)
        ),array(
            "id" => "CARD",
            "text" => "Mamory Card",
            "description" => "Anagrafica memory card",
            "icon" => "fas fa-code",
            "url" => HTTP_PRIVATE_SECTION . "memory_card.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_CARD)
        ),array(
            "id" => "DONAZIONI",
            "text" => "Gestione donazioni",
            "description" => "Gestione delle donazioni con le card",
            "icon" => "fas fa-code",
            "url" => HTTP_PRIVATE_SECTION . "donazioni.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE,GRUPPO_CARD)
        )/*,
        array(
            "id" => "FITOSANITARI",
            "text" => "Prodotti fitosanitari",
            "description" => "Anagarfica prodotti fitosanitari",
            "icon" => "fas fa-laptop-code",
            "url" => HTTP_PRIVATE_SECTION . "prodotti_fitosanitari.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE)
        ),
        array(
            "id" => "FERTILIZZANTI",
            "text" => "Prodotti fertilizzanti",
            "description" => "Anagarfica prodotti fertilizzanti",
            "icon" => "fas fa-laptop-code",
            "url" => HTTP_PRIVATE_SECTION . "prodotti_fertilizzanti.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE)
        )*/
    )
);

$MENUITEMS[] = array(
    "id" => "ADMIN",
    "text" => "Amministratore",
    "description" => "Gestione sistema",
    "icon" => "fas fa-cogs",
    "url" => "#",
    "handler" => "",
    "super_user" => SUPER_USER,
    "group_id" => array(GRUPPO_OPERATORI),
    "children" => array(
        array(
            "id" => "OPERATORI",
            "text" => "Operatori",
            "description" => "Elenco degli Operatori",
            "icon" => "fas fa-users",
            "url" => BASE_HTTP . "modules/operatori/operatori.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "fase" => 0,
            "group_id" => array(GRUPPO_OPERATORI)
        ),
        array(
            "id" => "LOGSFE",
            "text" => "Logs",
            "description" => "Visualizzazione Logs",
            "icon" => "fas fa-stream",
            "url" => BASE_HTTP . "modules/logs/logs.php",
            "handler" => "",
            "super_user" => SUPER_USER,
            "fase" => 0,
            "group_id" => array(GRUPPO_LOGS)
        )
    )
);


$MENUITEMS[] = array(
    "id" => "LOGOUT",
    "text" => "Esci",
    "description" => "Esci dal sistema",
    "icon" => "fas fa-sign-out-alt",
    "url" => BASE_HTTP . "logout.php",
    "handler" => "",
    "super_user" => 0,
    "group_id" => array()
    
);
