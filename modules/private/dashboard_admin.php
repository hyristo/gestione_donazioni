<!doctype html>
<?
include '../../lib/api.php';

define("THIS_PERMISSION", array('CODICI_VARI', 'ANAG_PERSONA'));


//Utils::print_array($LoggedAccount);exit();

include_once ROOT . 'layout/include_permission.php';
?>

<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body>
        <?
        include_once ROOT . 'layout/header.php';
        ?>
        <main role="main">
            <header class="masthead masthead-page">                
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>
            <div class="container-fluid">
                <div class="row flex-xl-nowrap">
                    <div class="<?= $colCssContainer ?> bd-content">
                        <?=$mainMenu->RenderStaticDashboard('DashAdmin')?>                        
                    </div>                    
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>

    </body>
</html>