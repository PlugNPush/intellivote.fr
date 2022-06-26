<?php
require_once dirname(__FILE__).'/../config.php';


if (!isset($_SESSION['id'])) {
  header( "refresh:0;url=login.php?expired=true" );
} else {

  echo '<!DOCTYPE html>
  <html lang="fr">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; child-src \'none\';">

    <title>Intellivote - Espace Mairie</title>

    <link href="css/custom.css" rel="stylesheet">

<!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/blog-home.css" rel="stylesheet">

  </head>

  <body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="index.php">mairie.intellivote.fr</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span id="new-dark-navbar-toggler-icon" class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="https://www.intellivote.fr">Espace élécteur</a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="https://mairie.intellivote.fr">Espace mairie<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="https://gouv.intellivote.fr">Espace Gouvernement</a>
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

        $req = $bdd->prepare('SELECT * FROM mairies WHERE id = ?;');
        $req->execute(array($_SESSION['idmairie']));
        $test = $req->fetch();
          if ($test) {
            echo '<h1 class="my-4">Espace Mairie de ' . $test['nom'] . '</h1>';
          } else {
            echo '<h1 class="my-4">Espace Mairie</h1>';
          }


          if ($_SESSION['verified'] != 1) {
            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Vous devez confirmer votre compte pour accéder au site. Celui-ci n\'a pas encore pu être vérifié.<br><a class = "btn btn-primary" href = "https://www.intellivote.fr/validation.php">Lancer ou vérifier la procédure de validation</a>
            </div>';
          } else {
            echo '
            <div class="alert alert-info fade show" role="alert">
              <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Votre compte est prêt.<br>
            </div>';

            $gatherdata = $bdd->prepare('SELECT * FROM mayor WHERE individual = ? AND mairie = ? AND verified = 1;');
            $gatherdata->execute(array($_SESSION['id'], $_SESSION['idmairie']));
            $data = $gatherdata->fetch();

            if ($data) {
              echo '
              <div class="alert alert-info fade show" role="alert">
                <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Pas d\'élections à venir.<br>
              </div>';
            } else {

              echo '
              <div class="alert alert-warning fade show" role="alert">
                <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Notre système ne vous a pas détecté en tant que résponsable au sein de la mairie de ' . $test['nom'] . '. Votre demande de certification devra être traitée par <a href="https://gouv.intellivote.fr">un représentant de l\'État</a>. Cette procédure ne peut pas être automatisée pour des raisons de sécurité.<br><a class = "btn btn-primary" href = "https://www.intellivote.fr/">Retour à l\'espace électeur</a>
              </div>';
            }
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
    <footer class="py-5 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">&copy; 2022 Intellivote.fr. Tous droits reservés. <a href="https://www.intellivote.fr/legal.php">Mentions légales</a>.</p>
      </div>
      <!-- /.container -->
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  </body>

  </html>';


}

?>
