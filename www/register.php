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
          if (isset($_GET['wrongdepartement'])) {
            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Echec de la validation du département de naissance</strong>. Vérifiez votre saisie.
            </div>';
          }
          $electionEnCours = false;
          $date = date('Y-m-d H:i:s');
          $election_fetch = $bdd->prepare('SELECT * FROM election;');
                    $election_fetch->execute();

                    while ($election = $election_fetch->fetch()) {
                      if ($election['begindate']>strtotime('+90 days') && $election['enddate']>$date){//si la date du jour +90 est apres l'élection et si l'election n'est pas fini
                        $electionEnCours = true;
                      }

                    }
                    
          if ($electionEnCours == true) {
            echo'
              <div class="alert alert-danger fade show" role="alert">
              <strong>Echec de la création du compte</strong>. Le delai entre l\'inscription et la prochaine élection est inférieur à 90 jours
              </div>';
          }else{
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
              <input type="text" name="surname" class="form-control" id="surname" placeholder="Prénoms" required>
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
              <input type="text" name="birthdate" class="form-control" id="birthdate" placeholder="Format AAAA-MM-JJ" required>
              <small id="help" class="form-text text-muted">
              ATTENTION : format AAAA-MM-JJ<br>
              Par exemple, si vous êtes né le 25 août 1971, indiquez 1971-08-25.
              </small>
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
              </small>';
              if (isset($_GET['wrongdepartement'])){
                echo '<div class="invalid-feedback">
                  Ne vous amusez pas à sélectionner un département non listé.
                </div>';
              }
              echo '
            </div>


            <button type="submit" class="btn btn-primary">S\'inscrire maintenant !</button>
            </form><br><br>';}


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

}else{

  $mail_fetch = $bdd->prepare('SELECT * FROM individual WHERE email = ?;');
  $mail_fetch->execute(array($_POST['email']));
  $mail = $mail_fetch->fetch();

  $departement_fetch = $bdd->prepare('SELECT * FROM departements WHERE id = ?;');
  $departement_fetch->execute(array($_POST['birthplace']));
  $departement = $departement_fetch->fetch();

  if ($mail) {
    header( "refresh:0;url=register.php?emailexists=true" );
  } else if (!$departement_fetch) {
    header( "refresh:0;url=register.php?wrongdepartement=true" );
  } else {
    if (!empty($_POST['mdp']) AND !empty($_POST['vmdp']) AND $_POST['mdp'] == $_POST['vmdp']) {
      $hash=password_hash($_POST['mdp'], PASSWORD_DEFAULT);
      $date = date('Y-m-d H:i:s');
      $req=$bdd->prepare('INSERT INTO individual(email, password, name, surname, birthdate, birthplace, registered) VALUES(:email, :password, :name, :surname, :birthdate, :birthplace, :registered);');
      $req->execute(array(
        'email'=> $_POST['email'],
        'password'=> $hash,
        'name'=> $_POST['name'],
        'surname'=> $_POST['surname'],
        'birthdate'=> $_POST['birthdate'],
        'birthplace' => $_POST['birthplace'],
        'registered'=> $date
      ));

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
          $_SESSION['verified'] = $test['verified'];

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
