<?php
require_once dirname(__FILE__).'/../config.php';

if (!empty($_POST['email']) AND !empty($_POST['mdp'])){
  // Hachage du mot de passe
  $pass_hache = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

  // Vérification des identifiants
  $req = $bdd->prepare('SELECT * FROM individual WHERE email = ?;');
  $req->execute(array($_POST['email']));
  $test = $req->fetch();


  $verify = password_verify($_POST['mdp'], $test['password']);
  if ($verify)
  {
      session_start();
      $_SESSION['id'] = $test['id'];
      $_SESSION['name'] = $test['name'];
      $_SESSION['surname'] = $test['surname'];
      $_SESSION['birthdate'] = $test['birthdate'];
      $_SESSION['birthplace'] = $test['birthplace'];
      $_SESSION['registered'] = $test['registered'];
      $_SESSION['email'] = $test['email'];


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

          echo '<h1 class="my-4">Connexion Espace électeur</h1>';
          if (isset($_GET['deleted'])) {
            echo '
            <div class="alert alert-success fade show" role="alert">
              <strong>Votre compte a bien été supprimé</strong>. Si vous n\'êtes pas satisfait du service, n\'hésitez pas à faire remonter vos tracas auprès d\'un modérateur.
            </div>';
          }
          if (isset($_GET['expired'])) {
            echo '
            <div class="alert alert-info fade show" role="alert">
              <strong>Votre session a expiré</strong>. Pour votre sécurité, votre session a expiré. Veuillez vous reconnecter pour continuer.
            </div>';
          }

          echo '
          <form action="login.php" method="post">
            <div class="form-group">
              <label for="email">Saisissez votre adresse e-mail</label>
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

        echo '</div>

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

  </html>';

}

?>
