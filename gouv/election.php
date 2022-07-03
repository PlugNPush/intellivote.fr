<?php
require_once dirname(__FILE__).'/../config.php';


if (isset($_SESSION['id'])){

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
                      <label for="name_election">Saisissez le nom de l\'élection</label>
                      <input type="text" name="name_election" class="form-control';

                      if (isset($_GET['nameerror'])){
                        echo ' is-invalid';
                      }

                      echo ' "id="name_election" placeholder="Saisissez le nom de l\'élection" required> ';

                      if (isset($_GET['nameerror'])){
                        echo '<div class="invalid-feedback">
                          Nom de l\'élection incorrect ! Une élection à ce nom est déjà en cours.
                        </div>';
                      }

                      echo ' <small id="IDHelp" class="form-text text-muted">
                        Vous ne pouvez pas utiliser le nom d\'une élection déjà en cours.
                      </small>

                      <label for="dates">Choisissez les dates de l\'élection</label>

                      <input type="text" name="begindate" class="form-control';
                      if (isset($_GET['beginerror'])){
                        echo ' is-invalid';
                      }
                      echo '" id="begindate" placeholder="Saisissez la date de début." required>

                      <input type="text" name="enddate" class="form-control';
                      if (isset($_GET['enderror'])){
                        echo ' is-invalid';
                      }
                      echo '" id="enddate" placeholder="Saisissez la date de fin." required>

                      <label for="description">Saisissez une description</label>
                      <input type="text" name="description" class="form-control';

                      if (isset($_GET['descriptionerror'])){
                        echo ' is-invalid';
                      }

                      echo ' "id="description" placeholder="Saisissez une description" required> ';

                      if (isset($_GET['descriptionerror'])){
                        echo '<div class="invalid-feedback">
                          La description est trop longue ! Vous n\'avez le droit qu\'à 255 caractères.
                        </div>';
                      }

                    echo '
                    </div>

                    <button type="submit" class="btn btn-primary">Créer l\'élection</button>

                  </form><br><br>';
              } else {

                echo '
                  <h2><a>Vérification :</a></h2>
                  <form action="index.php" method="post">

                    <div class="form-group">
                      <label for="name_election">Confirmez vous les données ?</label>
                      <input type="hidden" name="name_election" class="form-control';

                      if (isset($_GET['nameerror'])){
                        echo ' is-invalid';
                      }
                      
                      echo '" id="name_election" value="';echo(array($_POST['name_election']));echo'" required>';

                      if (isset($_GET['nameerror'])){
                        echo '<div class="invalid-feedback">
                          ID du maire incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que l\'ID soit correct.
                        </div>';
                      }

                      echo '<input type="hidden" name="idmairie" class="form-control';

                      if (isset($_GET['nameerror'])){
                        echo ' is-invalid';
                      }
                      
                      echo '" id="idmairie" value="';echo(array($_POST['idmairie']));echo'" required>';

                      if (isset($_GET['inameerror'])){
                        echo '<div class="invalid-feedback">
                          ID du maire incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que l\'ID soit correct.
                        </div>';
                      }
                      
                    echo '</div>

                    <button type="submit" class="btn btn-primary">Confirmer les données</button>

                  </form><br>

                  <form action="index.php" method="post">

                    <button type="submit" class="btn btn-primary">Retour en arrière</button>

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
  header( "refresh:0;url=login.php?expired=true" );
}

?>