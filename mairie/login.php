<?php
require_once dirname(__FILE__).'/../config.php';

if (!empty($_POST['email']) AND !empty($_POST['mdp'] AND !empty($_POST['idmairie']))){
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
      $_SESSION['verified'] = $test['verified'];
      $_SESSION['idmairie'] = $_POST['idmairie'];


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

    <title>Intellivote - Espace Mairie</title>

    <link href="css/custom.css" rel="stylesheet">

<!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/blog-home.css" rel="stylesheet">

  </head>

  <body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container">
      <a class="navbar-brand" href="index.php"><img src="image/logo.png" width="160" height="30"></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span id="new-light-navbar-toggler-icon" class="navbar-toggler-icon"></span>
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

          if ((isset($_POST['insee']) && isset($_POST['departement'])) || isset($_POST['idmairie'])) {

            if (isset($_POST['idmairie'])) {
              $req = $bdd->prepare('SELECT * FROM mairies WHERE id = ?;');
              $req->execute(array($_POST['idmairie']));
              $test = $req->fetch();
            } else {
              $req = $bdd->prepare('SELECT * FROM mairies WHERE departement = ? AND insee = ?;');
              $req->execute(array($_POST['departement'], $_POST['insee']));
              $test = $req->fetch();
            }


            if ($test) {
              if (!isset($_POST['idmairie'])) {
                echo '<h3 class="my-4">Étape 3</h3>';
              } else {
                echo '<h3 class="my-4">Connexion rapide</h3>';
              }
              echo '
              <form action="login.php" method="post">
              <h4 class="my-4">Connexion à votre mairie à ' . $test['nom'] . ' (' . $test['departement'] . ')</h4>';

              if (!isset($_POST['idmairie'])) {
                echo '<div class="alert alert-info fade show" role="alert">
                  <strong>L\'identifiant de votre mairie est ' . $test["id"] . '.</strong> Pensez à le noter pour une connexion plus rapide.
                </div>';
              }

              echo '
              <input type="hidden" name="idmairie" class="form-control" id="idmairie" placeholder="Courriel" value = "' . $test["id"] .'">
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
                <br>Pas encore inscrit ? <a class="btn btn-secondary" href=https://www.intellivote.fr/register.php>Inscrivez-vous maintenant !</a>
                </form><br><br>';
            } else {
              echo '<h3 class="my-4">Échec de l\'étape 2</h3>';
              echo '<form action="login.php" method="post">
                <h4 class="my-4">La commune de votre mairie n\'a pas pu être trouvée.</h4>
                <button type="submit" class="btn btn-primary">Retour à l\'étape 1</button>
                </form><br><br>';
            }



          } else if (!isset($_POST['departement'])) {

            echo '
            <form action="login.php" method="post">
            <h4 class="my-4">Connexion Rapide</h4>
              <div class="form-group">
                <label for="email">Saisissez l\'identifiant de votre mairie</label>
                <input type="text" name="idmairie" class="form-control" id="idmairie" placeholder="Identifiant mairie" required>
              </div>
              <button type="submit" class="btn btn-primary">Connexion rapide</button>
              </form><br><br>';

            echo '<h4 class="my-4">Vous n\'avez pas l\'identifiant ? Laissez vous guider.</h4>';

            echo '<h3 class="my-4">Étape 1</h3>';
            echo '<form action="login.php" method="post">
              <div class="form-group">
                <label for="departement">Saisissez votre numéro de département
                </label>
                <select name="departement" class="form-control" id="departement" required>';

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
              <button type="submit" class="btn btn-primary">Suivant</button>
              </form><br><br>
              ';
          } else {
            $req = $bdd->prepare('SELECT * FROM departements WHERE id = ?;');
            $req->execute(array($_POST['departement']));
            $test = $req->fetch();

            if ($test) {
              echo '<h3 class="my-4">Étape 2</h3>';

              if ($_POST['departement'] == 99) {
                echo '<form action="login.php" method="post">
                <div class="alert alert-warning fade show" role="alert">
                  <strong>Vote à l\'étranger</strong>. Les français de l\'étranger ne votent pas sur Intellivote mais sur une plateforme en ligne dédiée, sous la tutelle du Ministère des Affaires Étrangères. Rendez-vous sur <a href="https://www.diplomatie.gouv.fr/fr/services-aux-francais/voter-a-l-etranger/">https://www.diplomatie.gouv.fr/fr/services-aux-francais/voter-a-l-etranger/</a> pour plus d\'informations.
                </div>
                  <button type="submit" class="btn btn-primary">Retour à l\'étape 1</button>
                  </form><br><br>';
              } else {
                echo '<form action="login.php" method="post">
                <div class="alert alert-info fade show" role="alert">
                  <strong>Numéro INSEE oublié ?</strong> Vous pouvez le retrouver sur le site gouvernemental des données publiques ou sur le site de l\'INSEE. Rendez-vous sur <a href="https://www.insee.fr/fr/information/5057840">https://www.insee.fr/fr/information/5057840</a> pour plus d\'informations.
                </div>
                <h4 class="my-4">Identification de votre mairie à ' . $test['nom'] . '</h4>
                <input type="hidden" type="text" name="departement" class="form-control" id="departement" placeholder="Département" value="'. $test["id"] .'">
                  <div class="form-group">
                    <label for="insee">Saisissez le numéro INSEE de la commune de votre mairie</label>
                    <select name="insee" class="form-control" id="insee" required>';

                    $insee_fetch = $bdd->prepare('SELECT * FROM mairies WHERE departement = ? ORDER BY id ASC;');
                    $insee_fetch->execute(array($test['id']));

                    while ($insee = $insee_fetch->fetch()) {
                      echo '<option value="', $insee['insee'] ,'">', $insee['insee'], ' - ', $insee['nom'] ,'</option>';
                    }

                    echo '
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary">Suivant</button>
                  </form><br><br>
                  ';

              }

            } else {
              echo '<h3 class="my-4">Échec de l\'étape 1</h3>';
              echo '<form action="login.php" method="post">
                <h4 class="my-4">Le département n\'a pas pu être trouvé.</h4>
                <button type="submit" class="btn btn-primary">Réessayer</button>
                </form><br><br>
                ';
            }


          }

        echo '</div>

      </div>
      <!-- /.row -->

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <footer class="py-5 bg-light">
      <div class="container">
        <p class="m-0 text-center text-black">&copy; 2022 Intellivote. Tous droits reservés. <a href="https://www.intellivote.fr/legal.php" class="link-dark">Mentions légales</a>.</p>
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
