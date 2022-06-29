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

      <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; child-src \'none\';">

      <title>Intellivote - Espace électeur</title>

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
        <a class="navbar-brand" href="index.php">intellivote.fr</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span id="new-dark-navbar-toggler-icon" class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="https://www.intellivote.fr">Espace électeur<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="https://mairie.intellivote.fr">Espace mairie</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="https://admin.intellivote.fr">Espace Administrateur</a>
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

              echo '<h1 class="my-4">Bienvenue sur Intellivote,
                <small>', $_SESSION['surname'], ' ', $_SESSION['name'], '</small>
              </h1>';


            if (isset($_GET['ierror'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Une erreur interne inattendue s\'est produite</strong>. Un paramètre attendu n\'est pas parvenu à sa destination. Veuillez réesayer puis contacter un modérateur si l\'erreur se reproduit.
              </div>';
            }
            if (isset($_GET['dperror'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Une erreur s\'est produite</strong>. Vous ne disposez pas des autorisations nécéssaires pour réaliser cette opération.
              </div>';
            }

              if ($_SESSION['verified'] != 1) {
                echo '
                <div class="alert alert-danger fade show" role="alert">
                  <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Vous devez confirmer votre compte pour accéder au site. Celui-ci n\'a pas encore pu être vérifié.<br><a class = "btn btn-primary" href = "validation.php">Lancer ou vérifier la procédure de validation</a>
                </div>';
              } else {
                echo '
                <div class="alert alert-info fade show" role="alert">
                  <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Votre compte est prêt.<br>
                </div>';

                $gatherdata = $bdd->prepare('SELECT * FROM elector WHERE individual = ? AND verified = 1;');
                $gatherdata->execute(array($_SESSION['id']));
                $data = $gatherdata->fetch();

                if ($data) {
                  echo '
                  <div class="alert alert-info fade show" role="alert">
                    <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Pas d\'élections à venir.<br>
                  </div>';
                } else {
                  if(isset($_GET['verifmairie'])) {
                    
                    $gatherdataverif = $bdd->prepare('SELECT * FROM elector WHERE type = 1 AND individual = ?');
                    $gatherdataverif->execute(array($_SESSION['id']));
                    $dataverif = $gatherdataverif->fetch();
                    if(!$dataverif){
                        $token = generateRandomString(20);
                          $date = date('Y-m-d H:i:s');

                          $newtoken = $bdd->prepare('INSERT INTO validations(type, individual, token, date) VALUES(:type, :individual, :token, :date);');
                          $newtoken->execute(array(
                            'type' => 1,
                            'individual' => $_SESSION['id'],
                            'token' => $token,
                            'date' => $date
                          ));
                        }else {
                          $token=$dataverif['token'];
                        }

                        echo $token;
                    

                  } else {
                    echo '
                  <div class="alert alert-warning fade show" role="alert">
                    <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Vous devez maintenant vous authentifier en tant qu\'électeur, donc relier votre identité numérique à votre identité physique. Lancez une pré-demande en ligne ou rendez-vous en mairie.<br><a class = "btn btn-primary" href = "index.php?verifmairie=true">Relier mon identité physique</a><br>
                    <br>Vous representez une mairie ? Votre demande devra être traitée par <a href="https://admin.intellivote.fr">un représentant de l\'État</a>.
                  </div>';
                  }
                  
                }
              }

            echo '

            <a class = "btn btn-secondary" href = "logout.php">Se déconnecter</a><br><br>

          </div>

        </div>
        <!-- /.row -->

      </div>
      <!-- /.container -->

      <!-- Footer -->
      <footer class="py-5 bg-dark">
        <div class="container">
          <p class="m-0 text-center text-white">&copy; 2022 Intellivote. Tous droits reservés. <a href="/legal.php">Mentions légales</a>.</p>
        </div>
        <!-- /.container -->
      </footer>

      <!-- Bootstrap core JavaScript -->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    </body>

    </html>
';

} else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
