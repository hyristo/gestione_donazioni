<?
include 'lib/api.php';


if(isset($_REQUEST['token']) && trim($_REQUEST['token'])!=""){
    
    $token = trim($_REQUEST['token']);
    
    $account = SPID::loginQIAM($token);        
    if ($account->ID > 0) {
        
        switch ($account->GetRoleCode()) {
            case ROLE_CODE_LEGALE_RAPPRESENTANTE:
                Header('Location: ' . PRIVATE_SECTION . 'dashboard.php');
                break;
            case ROLE_CODE_CAA:
                
                break;
            case ROLE_CODE_ADMIN:
                
                break;  
            default:

                Utils::RedirectTo(BASE_HTTP . 'logout.php');
                break;
        }
        
    }
    
    
}
