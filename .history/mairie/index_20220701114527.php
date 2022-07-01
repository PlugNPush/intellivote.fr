<?php
require_once dirname(__FILE__).'/../config.php';


if (!isset($_SESSION['id'])) {
  header( "refresh:0;url=login.php?expired=true" );
} else if(isset($_POST['token'])){


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
              <a class="nav-link" href="https://www.intellivote.fr">Espace électeur</a>
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
              <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Vous devez confirmer votre compte pour accéder au site. Celui-ci n\'a pas encore pu être vérifié.<br><a class = "btn btn-primary" href = "https://www.intellivote.fr/index.php">Lancer ou vérifier la procédure de validation</a>
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

              if (isset($_GET['success'])) {
                echo '
                <div class="alert alert-success fade show" role="alert">
                  <strong>L\'électeur a bien été rajouté dans votre mairie.</strong>
                </div>';
              }

                echo '
                <form action="index.php" method="post">

                  <div class="form-group">
                    <label for="token">Saisissez le code à usage unique.</label>
                    <input type="text" name="token" class="form-control"';

                    if (isset($_GET['tokenerror'])){
                      echo ' is-invalid';
                    }

                    echo 'id="token" placeholder="Saisissez le code reçu sur votre adresse mail" required>';

                    if (isset($_GET['tokenerror'])){
                      echo '<div class="invalid-feedback">
                        Token incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que le token soit correct.
                      </div>';
                    }

                    echo ' <small id="emailHelp" class="form-text text-muted">
                      Vous pouvez récupérer la clé dans votre espace électeur après sa vérification. En cas de problème, contactez un modérateur.
                    </small>
                  </div>

                  <button type="submit" class="btn btn-primary">Vérifier l\'authenticité du compte</button>

                </form><br><br>';

              


              echo '
              <div class="alert alert-info fade show" role="alert">
                <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Pas d\'élections à venir.<br>
              </div>';
              
            } else {

              echo '
              <div class="alert alert-warning fade show" role="alert">
                <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Notre système ne vous a pas détecté en tant que responsable au sein de la mairie de ' . $test['nom'] . '. Votre demande de certification devra être traitée par <a href="https://gouv.intellivote.fr">un représentant de l\'État</a>. Cette procédure ne peut pas être automatisée pour des raisons de sécurité.<br><a class = "btn btn-primary" href = "https://www.intellivote.fr/">Retour à l\'espace électeur</a>
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

}else{

    $req = $db->prepare('SELECT * FROM validations WHERE token = ? AND verify = 0 AND type = 1;');
    $req->execute(array($_POST['token']));
    $test = $req->fetch();

    if (!$test){
      header( "refresh:0;url=index.php?tokenerror=true" );
    } else {

      $req=$bdd->prepare('INSERT INTO elector(number, individual, mairie, verified, verifiedon) VALUES(:number, :individual, :mairie, :verified, :verifiedon)');
      $date = date('Y-m-d H:i:s');
      $req->execute(array(
        'number'=> $_POST['number'],
        'individual'=> $test['individual'],
        'mairie'=> $_SESSION['idmairie'],
        'verified'=> 1,
        'verifiedon' => $date
      ));
      $validation = $bdd->prepare('UPDATE validations SET verified = 1 WHERE id = ?;');
      $validation->execute(array($test['id']));

      header( "refresh:0;url=index.php?success=true" );
    }

}
?>