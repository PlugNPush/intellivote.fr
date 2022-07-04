<?php
require_once dirname(__FILE__).'/../config.php';


if (isset($_SESSION['id'])){
    if (!isset($_GET['ajout']) AND !isset($_GET['descriptionerror']) AND !isset($_GET['beginerror']) AND !isset($_GET['enderror'])){
        echo '<!DOCTYPE html>
        <html lang="fr">

        <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; style-src https://* \'self\' \'unsafe-inline\' child-src \'none\';">

        <title>Intellivote - Espace Gouvernement - Election</title>

        <link href="css/custom.css" rel="stylesheet">

        <!-- Bootstrap core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/blog-home.css" rel="stylesheet">

        </head>

        <body>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-danger">
            <div class="container">
            <a class="navbar-brand" href="index.php"><img src="image/logo.png" width="160" height="30"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span id="new-dark-navbar-toggler-icon" class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="https://www.intellivote.fr">Espace électeur</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://mairie.intellivote.fr">Espace mairie</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="https://gouv.intellivote.fr">Espace Gouvernement<span class="sr-only">(current)</span></a>
                </li>';

                echo '
                </ul>
            </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container">

            <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">';

                echo '<h1 class="my-4">Espace Gouvernement - Election</h1>';

                $gouv_fetch = $bdd->prepare('SELECT * FROM governor WHERE individual = ? AND verified = 1;');
                $gouv_fetch->execute(array($_SESSION['id']));
                $gouv = $gouv_fetch->fetch();

                if (!$gouv) {
                echo '<div class="alert alert-danger fade show" role="alert">
                    <strong>L\'espace Gouvernement n\'est pas accessible depuis l\'extérieur.</strong> Par sécurité, vous devez utiliser l\'interface de gestion interne d\'Intellivote pour pouvoir administrer le service, la connexion à distance n\'est pas possible. Intellivote ne vous demandera jamais vos identifiants ni codes de vérifications, ne les communiquez jamais.
                </div><br><br>';
                } else {
                    if (!isset($_GET['verify'])){

                        /*if (isset($_GET['success'])) {
                        echo '
                        <div class="alert alert-success fade show" role="alert">
                            <strong>LE maire a bien été affilié à la mairie.</strong>
                        </div>';
                        }*/

                        echo '
                        <h2><a>Afficher une élection :</a></h2>
                        <br><br>';
                    }

                    echo '
                    <a class = "btn btn-secondary" href = "index.php">Retour</a>
                    <br><br>';

                }

                echo '
                <a class = "btn btn-secondary" href = "logout.php">Se déconnecter</a>
                <br><br>';

            echo '</div>

            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

        <!-- Footer -->
        <footer class="py-5" style="background-color: #e04a51;">
            <div class="container">
            <p class="m-0 text-center text-white">&copy; 2022 Intellivote. Tous droits reservés. <a href="https://www.intellivote.fr/legal.php" style="color: lightcyan;">Mentions légales</a>.</p>
            </div>
            <!-- /.container -->
        </footer>

        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        </body>

        </html>';
    }
    else if(!isset($_POST['description'])){

        echo '<!DOCTYPE html>
        <html lang="fr">

        <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; style-src https://* \'self\' \'unsafe-inline\' child-src \'none\';">

        <title>Intellivote - Espace Gouvernement - Election</title>

        <link href="css/custom.css" rel="stylesheet">

        <!-- Bootstrap core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/blog-home.css" rel="stylesheet">

        </head>

        <body>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-danger">
            <div class="container">
            <a class="navbar-brand" href="index.php"><img src="image/logo.png" width="160" height="30"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span id="new-dark-navbar-toggler-icon" class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="https://www.intellivote.fr">Espace électeur</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://mairie.intellivote.fr">Espace mairie</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="https://gouv.intellivote.fr">Espace Gouvernement<span class="sr-only">(current)</span></a>
                </li>';

                echo '
                </ul>
            </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container">

            <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">';

                echo '<h1 class="my-4">Espace Gouvernement - Election</h1>';

                $gouv_fetch = $bdd->prepare('SELECT * FROM governor WHERE individual = ? AND verified = 1;');
                $gouv_fetch->execute(array($_SESSION['id']));
                $gouv = $gouv_fetch->fetch();

                if (!$gouv) {
                echo '<div class="alert alert-danger fade show" role="alert">
                    <strong>L\'espace Gouvernement n\'est pas accessible depuis l\'extérieur.</strong> Par sécurité, vous devez utiliser l\'interface de gestion interne d\'Intellivote pour pouvoir administrer le service, la connexion à distance n\'est pas possible. Intellivote ne vous demandera jamais vos identifiants ni codes de vérifications, ne les communiquez jamais.
                </div><br><br>';
                } else {
                    if (!isset($_GET['verify'])){

                        /*if (isset($_GET['success'])) {
                        echo '
                        <div class="alert alert-success fade show" role="alert">
                            <strong>LE maire a bien été affilié à la mairie.</strong>
                        </div>';
                        }*/

                        echo '
                        <h2><a>Ajouter une élection :</a></h2>
                        <form action="election.php" method="post">

                            <div class="form-group">
                                <label for="description">Saisissez le nom de l\'élection</label>
                                <input type="text" name="description" class="form-control';

                                if (isset($_GET['descriptionerror'])){
                                    echo ' is-invalid';
                                }

                                echo ' "id="description" placeholder="Saisissez le nom de l\'élection" required> ';

                                if (isset($_GET['descriptionerror'])){
                                    echo '<div class="invalid-feedback">
                                    Nom de l\'élection incorrect ! Une élection à ce nom est déjà en cours.
                                    </div>';
                                }

                                echo ' <small id="NameHelp" class="form-text text-muted">
                                    Vous ne pouvez pas utiliser le nom d\'une élection déjà en cours.
                                </small>

                                <label for="dates">Choisissez les dates de l\'élection</label>

                                <input type="date" name="begindate" class="form-control';
                                if (isset($_GET['beginerror'])){
                                    echo ' is-invalid';
                                }
                                echo '" id="begindate" placeholder="Saisissez la date de début." required>

                                <input type="date" name="enddate" class="form-control';
                                if (isset($_GET['enderror'])){
                                    echo ' is-invalid';
                                }
                                echo '" id="enddate" placeholder="Saisissez la date de fin." required>

                                <small id="DateHelp" class="form-text text-muted">
                                    Date de début qu\'à partir de demain, et date de fin qu\'à partir de la date de début.
                                </small>

                            </div>

                            <button type="submit" class="btn btn-primary">Créer l\'élection</button>

                        </form><br><br>';
                    }

                    echo '
                    <a class = "btn btn-secondary" href = "index.php">Retour</a>
                    <br><br>';

                }

                echo '
                <a class = "btn btn-secondary" href = "logout.php">Se déconnecter</a>
                <br><br>';

            echo '</div>

            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

        <!-- Footer -->
        <footer class="py-5" style="background-color: #e04a51;">
            <div class="container">
            <p class="m-0 text-center text-white">&copy; 2022 Intellivote. Tous droits reservés. <a href="https://www.intellivote.fr/legal.php" style="color: lightcyan;">Mentions légales</a>.</p>
            </div>
            <!-- /.container -->
        </footer>

        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        </body>

        </html>';

    } else {

        // Pas de description/nom en double parmis ceux non finis
        $req = $bdd->prepare('SELECT * FROM election WHERE description = ?;');
        $req->execute(array($_POST['description']));
        $test = $req->fetch();
    
        if ($test){
          header( "refresh:0;url=election.php?descriptionerror=true" );
        }
        else if ($_POST['begindate']<=date('d/m/Y')){ // Date de début qu'à partir de demain
          header( "refresh:0;url=election.php?beginerror=true" );
        }
        else if ($_POST['begindate']>$_POST['enddate']){ // Date de fin qu'à partir de la date de début
            header( "refresh:0;url=election.php?enderror=true" );
        }
        else{
    
          $req=$bdd->prepare('INSERT INTO election (description, begindate, enddate) VALUES (:description, :begindate, :enddate);');
          $req->execute(array(
            'description'=> $_POST['description'],
            'begindate'=> $_POST['begindate'],
            'enddate'=> $_POST['enddate']
          ));
    
          header( "refresh:0;url=index.php?successelection=true" );
          
        }
      
    }

} else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>