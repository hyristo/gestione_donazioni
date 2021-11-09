<!doctype html>
<? include 'lib/api_s.php'; ?>
<html lang="en">
    <?
    include_once ROOT . 'layout/head.php';    
    ?>
    <body>
        <?
        include_once ROOT . 'layout/header_home.php';    
        include_once ROOT . 'layout/loader.php';
        ?>
        <header class="mastheadlogin masthead-page mb-5 ">
            
            <svg style="pointer-events: none" class="wave" width="100%" height="50px" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1920 75">
                <defs>
                <style>
                    .a {
                        fill: none;
                    }

                    .b {
                        clip-path: url(#a);
                    }

                    .c,
                    .d {
                        fill: #f9f9fc;
                    }

                    .d {
                        opacity: 0.5;
                        isolation: isolate;
                    }
                </style>
                <clipPath id="a"><rect class="a" width="1920" height="75"></rect></clipPath>
                </defs>                        
                <g class="b"><path class="c" d="M1963,327H-105V65A2647.49,2647.49,0,0,1,431,19c217.7,3.5,239.6,30.8,470,36,297.3,6.7,367.5-36.2,642-28a2511.41,2511.41,0,0,1,420,48"></path></g>
                <g class="b"><path class="d" d="M-127,404H1963V44c-140.1-28-343.3-46.7-566,22-75.5,23.3-118.5,45.9-162,64-48.6,20.2-404.7,128-784,0C355.2,97.7,341.6,78.3,235,50,86.6,10.6-41.8,6.9-127,10"></path></g>
                <g class="b"><path class="d" d="M1979,462-155,446V106C251.8,20.2,576.6,15.9,805,30c167.4,10.3,322.3,32.9,680,56,207,13.4,378,20.3,494,24"></path></g>
                <g class="b"><path class="d" d="M1998,484H-243V100c445.8,26.8,794.2-4.1,1035-39,141-20.4,231.1-40.1,378-45,349.6-11.6,636.7,73.8,828,150"></path></g>
                </svg>
            
        </header>
         <main role="main">
        <form class="form-signin text-center" id="form-login" action="javascript:login()">
            <h1 class="h3 mb-3 font-weight-normal">Benvenuto</h1>

            <div class="input-group">
                <label for="inputEmail" class="sr-only">Email</label>
                <input type="email" id="email" class="form-control" name="email" placeholder="Email" required autofocus>
            </div>
            <div class="input-group">                
                <input type="password" id="inputPassword" name="password" class="form-control" id="inputPassword" placeholder="Password" required>
                <span class="input-group-btn">
                    <button class="btn btn-default reveal" type="button"><i class="fas fa-eye-slash"></i></button>
                </span>
            </div>
            <button class="btn btn-lg btn-primary btn-block"  id="submitValidation" type="submit">Entra</button>
            <br>
            <div class="row">
                <div class="col-sm-12 text-center">
                    <a  href="#" data-toggle="modal" data-target="#exampleModal"><b>Hai dimenticato la password?</b></a>
                </div>
            </div>            
        </form>
        <input type="hidden" id="token"  name="token" class="form-control"/>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Hai dimenticato la password?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <small id="emailHelp" class="form-text text-muted">Inserisci la tua mail per ricevere un link per il reset della password</small>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="email" class="form-control" name="emailRecupero" id="emailRecupero" aria-describedby="emailHelp" placeholder="Indirizzo email">
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                        <button type="button" onclick="sendPassword()" class="btn btn-primary">Invia</button>
                    </div>
                </div>
            </div>
        </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script src="js/utility.js" type="text/javascript"></script>
        <script src="js/login.js" type="text/javascript"></script>
    </body>
</html>
