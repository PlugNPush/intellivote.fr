<?php
require_once dirname(__FILE__).'/../config.php';

if(empty($_POST['mdp']) OR empty($_POST['vmdp'])){

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
      <a class="navbar-brand" href="index.php">mairie.intellivote.fr</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span id="new-dark-navbar-toggler-icon" class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="https://www.intellivote.fr">Espace élécteur<span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="https://mairie.intellivote.fr">Espace mairie</a>
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

          echo '<h1 class="my-4">Inscription</h1>';

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
          if (isset($_GET['passworderror'])) {
            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Echec de la validation du mot de passe.</strong> Le mot de passe et la confirmation ne correspondent pas.
            </div>';
          }
          if (isset($_GET['emailexists'])) {
            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Echec de la validation du mail</strong>. Un compte avec cette adresse mail existe déjà.
            </div>';
          }
          if (isset($_GET['pseudoexists'])) {
            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Echec de la validation du pseudo</strong>. Un compte avec ce pseudo existe déjà.
            </div>';
          }

          echo '<div class="alert alert-danger fade show" role="alert">
            <strong>Efrei Dynamo ferme ses portes le 31 décembre 2021</strong>. Afin de préparer la fermeture de notre plateforme, il n\'est désormais plus possible de s\'y inscrire depuis le 1er octobre 2021.
          </div>';

          echo '
          <form action="register.php" method="post">
            <div class="form-group">
              <label for="titre">Adresse email de connexion</label>
              <input type="text" name="email" class="form-control';
              if (isset($_GET['emailexists'])){
                echo ' is-invalid';
              }
              echo '" id="email" placeholder="Email pour la connexion" required>';
              if (isset($_GET['emailexists'])){
                echo '<div class="invalid-feedback">
                  Echec de la validation du mail. Un compte existe déjà avec cette adresse.
                </div>';
              }
              echo '
              <small id="emailHelp" class="form-text text-muted">
                Attention : cette adresse e-mail sera vérifiée, assurez-vous d\'être en mesure de recevoir des e-mails dessus.
              </small>
            </div>
            <div class="form-group">
              <label for="titre">Votre nom</label>
              <input type="text" name="name" class="form-control" id="name" placeholder="Nom" required>
            </div>
            <div class="form-group">
              <label for="titre">Vos prénoms</label>
              <input type="text" name="prenom" class="form-control" id="name" placeholder="Vos prénoms" required>
            </div>
            <div class="form-group">
              <label for="titre">Votre mot de passe</label>
              <input type="password" name="mdp" class="form-control';
              if (isset($_GET['passworderror'])){
                echo ' is-invalid';
              }
              echo '" id="mdp" placeholder="Prenez un mot de passe sûr" required>';
              if (isset($_GET['passworderror'])){
                echo '<div class="invalid-feedback">
                  Echec de la validation du mot de passe. Le mot de passe et la confirmation ne correspondent pas.
                </div>';
              }
              echo '
            </div>
            <div class="form-group">
              <label for="titre">Confirmez le mot de passe</label>
              <input type="password" name="vmdp" class="form-control';
              if (isset($_GET['passworderror'])){
                echo ' is-invalid';
              }
              echo '" id="vmdp" placeholder="Confirmation" required>';
              if (isset($_GET['passworderror'])){
                echo '<div class="invalid-feedback">
                  Echec de la validation du mot de passe. Le mot de passe et la confirmation ne correspondent pas.
                </div>';
              }
              echo '
            </div>

            <div class="form-group">
              <label for="titre">Date de naissance</label>
              <input type="text" name="birthdate" class="form-control" id="birthdate" placeholder="Format MM-DD-YYYY" required>
            </div>

            <div class="form-group">
              <label for="departement">Département de naissance
              </label>
              <select name="birthplace" class="form-control" id="birthplace" required>';

              $departement_fetch = $bdd->prepare('SELECT * FROM departements ORDER BY id ASC;');
              $departement_fetch->execute();

              while ($departement = $departement_fetch->fetch()) {
                echo '<option value="', $departement['id'] ,'">', $departement['id'], ' - ', $departement['nom'] ,'</option>';
              }

              echo '
              </select>
              <small id="help" class="form-text text-muted">
              ATTENTION, départements spéciaux :<br>
              201 : CORSE DU SUD<br>
              202 : HAUTE CORSE<br>
              981 : NOUVELLE CALEDONIE<br>
              982 : POLYNESIE FRANCAISE<br>
              99 : ETRANGER<br>
              </small>
            </div>


            <button type="submit" class="btn btn-primary">S\'inscrire maintenant !</button>
            </form><br><br>';


        echo '</div>

      </div>
      <!-- /.row -->

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <footer class="py-5 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">&copy; 2021 Efrei Dynamo. Tous droits reservés. <a href="/legal.php">Mentions légales</a>.</p>
      </div>
      <!-- /.container -->
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  </body>

  </html>';

}else{

  $mail_fetch = $bdd->prepare('SELECT * FROM utilisateurs WHERE email = ?;');
  $mail_fetch->execute(array($_POST['email']));
  $mail = $mail_fetch->fetch();

  $pseudo_fetch = $bdd->prepare('SELECT * FROM utilisateurs WHERE pseudo = ?;');
  $pseudo_fetch->execute(array($_POST['pseudo']));
  $pseudo = $pseudo_fetch->fetch();

  if ($mail) {
    header( "refresh:0;url=register.php?emailexists=true" );
  } else if ($pseudo) {
    header( "refresh:0;url=register.php?pseudoexists=true" );
  } else {
    if (!empty($_POST['mdp']) AND !empty($_POST['vmdp']) AND $_POST['mdp'] == $_POST['vmdp']) {
      $hash=password_hash($_POST['mdp'], PASSWORD_DEFAULT);
      $date = date('Y-m-d H:i:s');
      $req=$bdd->prepare('INSERT INTO utilisateurs(pseudo, email, mdp, role, annee, majeure, inscription) VALUES(:pseudo, :email, :mdp, :role, :annee, :majeure, :inscription);');
      $req->execute(array(
        'pseudo'=> $_POST['pseudo'],
        'email'=> $_POST['email'],
        'mdp'=> $hash,
        'role'=> $_POST['role'],
        'annee'=> $_POST['annee'],
        'majeure'=> $_POST['majeure'],
        'inscription'=> $date
      ));

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
        header( "refresh:0;url=register.php?ierror=true" );
      }

    }else{
      header( "refresh:0;url=register.php?passworderror=true" );
    }
  }

}
?>
