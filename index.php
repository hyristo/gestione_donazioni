<!doctype html>
<?


include 'lib/api.php';

?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body >
        <?
        include_once ROOT . 'layout/header_home.php';
        //include_once ROOT . 'layout/menu.php'; 
        ?>
        <main role="main" >            
            <header class="masthead masthead-page">                
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>            
            <div class="container-fluid">
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-8">
                        <div class="jumbotron jumbotron-fluid">
                            <div class="container">                              
                              <?=$msg?>
                            </div>
                        </div>
                    </div>
                    <div class="col-2"></div>
                </div>
                
            </div>
        </main>		
        <? include_once ROOT . 'layout/footer.php'; ?>
    </body>

</html>
