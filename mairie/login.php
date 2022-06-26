<?php
require_once dirname(__FILE__).'/../config.php';

if (!empty($_POST['email']) AND !empty($_POST['mdp'])){
  // Hachage du mot de passe
  $pass_hache = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

  // Vérification des identifiants
  $req = $bdd->prepare('SELECT * FROM utilisateurs WHERE email = ?;');
  $req->execute(array($_POST['email']));
  $test = $req->fetch();


  $verify = password_verify($_POST['mdp'], $test['mdp']);
  if ($verify)
  {
      session_start();
      $_SESSION['id'] = $test['id'];
      $_SESSION['pseudo'] = $test['pseudo'];
      $_SESSION['email'] = $test['email'];
      $_SESSION['role'] = $test['role'];
      $_SESSION['annee'] = $test['annee'];
      $_SESSION['majeure'] = $test['majeure'];
      $_SESSION['validation'] = $test['validation'];
      $_SESSION['karma'] = $test['karma'];
      $_SESSION['inscription'] = $test['inscription'];
      $_SESSION['photo'] = $test['photo'];
      $_SESSION['linkedin'] = $test['linkedin'];
      $_SESSION['ban'] = $test['ban'];


      header( "refresh:0;url=index.php" );
  } else {
      header( "refresh:0;url=login.php?passworderror=true" );
  }
} else {
  echo '<!DOCTYPE html>
  <html lang="fr">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; child-src \'none\';">

    <title>Efrei Dynamo</title>

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

          echo '<h1 class="my-4">Connexion Espace Mairie</h1>';
          if (isset($_GET['deleted'])) {
            echo '
            <div class="alert alert-success fade show" role="alert">
              <strong>Votre compte a bien été supprimé</strong>. Cette suppression se repercute tous les espaces Intellivote.
            </div>';
          }
          if (isset($_GET['expired'])) {
            echo '
            <div class="alert alert-info fade show" role="alert">
              <strong>Votre session a expiré</strong>. Pour votre sécurité, votre session a expiré. Veuillez vous reconnecter pour continuer.
            </div>';
          }

          if (!isset($_POST['departement'])) {
            echo '<h3 class="my-4">Étape 1</h3>';
            echo '<form action="login.php" method="post">
              <div class="form-group">
                <label for="departement">Saisissez votre numéro de département
                </label>
                <input type="text" name="departement" class="form-control" id="departement" placeholder="Département" required>
                <small id="help" class="form-text text-muted">
                ATTENTION, départements spéciaux :<br>
                201 : CORSE DU SUD<br>
                202 : HAUTE CORSE<br>
                981 : NOUVELLE CALEDONIE<br>
                982 : POLYNESIE FRANCAISE<br>
                99 : ETRANGER<br>
                </small>
              </div>
              <button type="submit" class="btn btn-primary">Suivant</button>
              </form><br><br>
              ';
          } else {
            $req = $bdd->prepare('SELECT * FROM departements WHERE numero = ?;');
            $req->execute(array($_POST['departement']));
            $test = $req->fetch();

            if ($test) {
              echo '<h3 class="my-4">Étape 2</h3>';
              echo '<form action="login.php" method="post">
              <div class="alert alert-info fade show" role="alert">
                <strong>Numéro INSEE oublié ?</strong>. Vous pouvez le retrouver sur le site gouvernemental des données publiques ou sur le site de l\'INSEE. Rendez-vous sur <a href="https://www.insee.fr/fr/information/5057840">https://www.insee.fr/fr/information/5057840</a>.
              </div>
              <h4 class="my-4">Identification de votre mairie à ' . $test['nom'] . '</h4>
              <input type="text" name="departement" class="form-control" id="departement" placeholder="Département" value="'. $test["numero"] .'">
                <div class="form-group">
                  <label for="insee">Saisissez le numéro INSEE de la commune de votre mairie</label>
                  <input type="text" name="insee" class="form-control" id="insee" placeholder="INSEE" required>
                </div>
                <button type="submit" class="btn btn-primary">Suivant</button>
                </form><br><br>
                ';
            } else {
              echo '<h3 class="my-4">Échec de l\'étape 1</h3>';
              echo '<form action="login.php" method="post">
                <h4 class="my-4">Le département n\'a pas pu être trouvé.</h4>
                <button type="submit" class="btn btn-primary">Réessayer</button>
                </form><br><br>
                ';
            }


          }

          /*

          echo '
          <form action="login.php" method="post">
            <div class="form-group">
              <label for="email">Saisissez votre adresse adresse e-mail</label>
              <input type="text" name="email" class="form-control" id="email" placeholder="Courriel" required>
            </div>
            <div class="form-group">
              <label for="mdp">Saisissez votre mot de passe</label>
              <input type="password" name="mdp" class="form-control';

              if (isset($_GET['passworderror'])){
                echo ' is-invalid';
              }

              echo '" id="mdp" placeholder="Mot de passe" required>';

              if (isset($_GET['passworderror'])){
                echo '<div class="invalid-feedback">
                  Mot de passe incorrect ! Besoin d\'aide ? Contactez un administrateur.
                </div>';
              }

              echo '
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
            <br>Pas encore inscrit ? <a class="btn btn-secondary" href=/register.php>Inscrivez-vous maintenant !</a>
            </form><br><br>';
            */

        echo '</div>

      </div>
      <!-- /.row -->

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <footer class="py-5 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">&copy; 2022 Intellivote. Tous droits reservés. <a href="https://www.intellivote.fr/legal.php">Mentions légales</a>.</p>
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
