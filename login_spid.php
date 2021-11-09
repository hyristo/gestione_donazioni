<?
include 'lib/api.php';


if (FAKE_SPID) {
    $account = SPID::loginFake();
} else {
    $account = SPID::login();
}
//Utils::print_array($account);exit();
if ($account->ID > 0 && $account->CanLogIn>0) {
    
    if($account->ID_ENTE > 0){
        Header('Location: ' . PRIVATE_SECTION . 'dashboard_caaf.php');
    }else{
        if ($account->AUTORIZZAZIONE_TRATTAMENTO == 1) {
            Header('Location: ' . PRIVATE_SECTION . 'dashboard.php');
        } else {
            Header('Location: ' . PRIVATE_SECTION . 'account.php');
        }
    }
    
    
}elseif($account->CanLogIn<=0){
    session_name(SESSION_NAME);
    session_start();
    session_unset();
    session_destroy();
    $is_error = "<i class='fas fa-user-lock'></i><br>Gentile utente, siamo spiacenti ma non possiede le autorizzazioni o i requisiti necessari per proseguire nella presente piattaforma informatica.";
} else {
    $is_error = "<i class='fas fa-user-lock'></i><br>Non è stato possibile completare l'operazione.";
}

if ($is_error != '') {
    
    include_once ROOT . 'layout/head.php';
    
    include_once ROOT . 'layout/header_home.php';
    ?>
    <html lang="en">
        <body>
            <div class="container">
                <br><br><br><br><br><br>
                <div class='row'>
                    <div class="col-md-12 text-center">
                        <div class="alert alert-primary" role="alert"><h3><?php echo $is_error; ?></h3></div><br>
                        <h4>Torna alla pagina di <br><a href="loginspid.php" class="btn btn-primary"> Login</a></h4><br>
                    </div>
                </div>
            </div>
            <? include_once ROOT . 'layout/footer.php'; ?>
        </body>
    </html>
<? } ?>