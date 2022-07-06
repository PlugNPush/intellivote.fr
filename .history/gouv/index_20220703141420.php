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

    <title>Intellivote - Espace Gouvernement</title>

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

          echo '<h1 class="my-4">Espace Gouvernement</h1>';

          $gouv_fetch = $bdd->prepare('SELECT * FROM governor WHERE individual = ? AND verified = 1;');
          $gouv_fetch->execute(array($_SESSION['id']));
          $gouv = $gouv_fetch->fetch();

          if (!$gouv) {
            echo '<div class="alert alert-danger fade show" role="alert">
              <strong>L\'espace Gouvernement n\'est pas accessible depuis l\'extérieur.</strong> Par sécurité, vous devez utiliser l\'interface de gestion interne d\'Intellivote pour pouvoir administrer le service, la connexion à distance n\'est pas possible. Intellivote ne vous demandera jamais vos identifiants ni codes de vérifications, ne les communiquez jamais.
            </div><br><br>';
          } else {
            echo '
                <h2><a>Inscrire un électeur :</a></h2>
                <form action="index.php" method="post">

                  <div class="form-group">
                    <label for="token">Saisissez le code à usage unique</label>
                    <input type="text" name="token" class="form-control';

                    if (isset($_GET['tokenerror'])){
                      echo ' is-invalid';
                    }

                    echo '" id="token" placeholder="Token de confirmation (20 caractères)" required>';

                    if (isset($_GET['tokenerror'])){
                      echo '<div class="invalid-feedback">
                        Token incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que le token soit correct.
                      </div>';
                    }

                    echo ' <small id="emailHelp" class="form-text text-muted">
                      Vous pouvez récupérer la clé dans votre espace électeur après sa vérification. En cas de problème, contactez un administrateur.
                    </small>

                    <label for="number">Saisissez le numéro d\'électeur</label>
                    <input type="text" name="number" class="form-control';

                    echo '" id="number" placeholder="Saisissez le numéro électoral." required>

                    <small id="emailHelp" class="form-text text-muted">
                      Vérifiez le numéro sur les listes électorales avant.
                    </small>
                  </div>

                  <button type="submit" class="btn btn-primary">Vérifier l\'authenticité du compte</button>

                </form><br><br>';


                echo '
                <h2><a>Inscrire un maire :</a></h2>
                <form action="index.php" method="post">

                  <div class="form-group">
                    <label for="token">Saisissez le code à usage unique</label>
                    <input type="text" name="token" class="form-control';

                    if (isset($_GET['individualerror'])){
                      echo ' is-invalid';
                    }

                    echo '" id="individual" placeholder="Id du maire required>';

                    if (isset($_GET['individualerror'])){
                      echo '<div class="invalid-feedback">
                        Id du maire incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que l\'ID soit correct.
                      </div>';
                    }

                    echo ' <small id="emailHelp" class="form-text text-muted">
                      Vous pouvez récupérer la clé dans votre espace électeur après sa vérification. En cas de problème, contactez un administrateur.
                    </small>

                    <label for="idmairie">Saisissez le numéro d\'électeur</label>
                    <input type="text" name="idmairie" class="form-control';

                    echo '" id="idmairie" placeholder="Saisissez l\'ID de votre mairie." required>

                  </div>

                  <button type="submit" class="btn btn-primary">Envoyer vos identifiants de maire</button>

                </form><br><br>';
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
