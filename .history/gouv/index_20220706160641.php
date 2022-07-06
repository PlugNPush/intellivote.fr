<?php
require_once dirname(__FILE__).'/../config.php';


if (isset($_SESSION['id'])){
  if(!isset($_POST['individual'])){

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
                <strong>Vous n\'avez pas accès à l\'espace Gouvernement.</strong> Assurez-vous d\'avoir bien validé votre compte <a href="https://www.intellivote.fr/validation.php">en cliquant ici</a>. D\'ici là, par sécurité, vous devez utiliser l\'interface de gestion interne d\'Intellivote pour pouvoir administrer le service, la connexion à distance n\'est pas possible. Intellivote ne vous demandera jamais vos identifiants ni codes de vérifications, ne les communiquez jamais.
              </div><br><br>';
            } else {
              if (!isset($_GET['verify'])){

                if (isset($_GET['success'])) {
                  echo '
                  <div class="alert alert-success fade show" role="alert">
                    <strong>Le maire a bien été affilié à la mairie.</strong>
                  </div>';
                }

                if (isset($_GET['successelection'])) {
                  echo '
                  <div class="alert alert-success fade show" role="alert">
                    <strong>L\'élection a bien été ajouté. Pensez à remplir les candidats.</strong>
                  </div>';
                }

                echo '
                  <h2><a>Inscrire un employé municipal :</a></h2>
                  <form action="index.php" method="post">

                    <div class="form-group">
                      <label for="individual">Saisissez l\'ID de l\'individu employé à la mairie</label>
                      <input type="text" name="individual" class="form-control';

                      if (isset($_GET['individualerror'])){
                        echo ' is-invalid';
                      }

                      echo ' "id="individual" placeholder="Saisissez l\'ID de l\'employé de la mairie à déclarer" required> ';

                      if (isset($_GET['individualerror'])){
                        echo '<div class="invalid-feedback">
                          ID du maire incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que l\'ID soit correct.
                        </div>';
                      }

                      echo ' <small id="IDHelp" class="form-text text-muted">
                        Vous pouvez récupérer la clé dans l\'espace électeur ou dans l\'espace mairie après sa vérification. En cas de problème, contactez un modérateur.
                      </small><br>

                      <label for="idmairie">Saisissez l\'ID de la mairie correspondante</label>
                      <input type="text" name="idmairie" class="form-control';

                      echo '" id="idmairie" placeholder="Saisissez l\'ID de la mairie" required>
                      <small id="IDHelp" class="form-text text-muted">
                        Vous pouvez récupérer la clé dans l\'espace mairie du demandeur. En cas de problème, contactez le demandeur ou un modérateur.
                      </small>

                    </div>

                    <button type="submit" class="btn btn-primary">Déclarer un employé municipal</button>

                  </form><br><br>';

                  echo '
                  <h2><a>Gestion des élections :</a></h2>

                  <br><a class="btn btn-primary" href="election.php?ajout=true">Ajouter une élection</a>

                  <br><a class="btn btn-primary" href="election.php?ajoutcandidat=true">Ajouter un candidat à une élection</a><br>

                  <form action="election.php?affiche=true" method="post">
                  <button type="submit" class="btn btn-primary">Afficher/Supprimer une élection</button>
                  </form><br><br>
                  
                  <br><a class="btn btn-primary" href="election.php?ajout=true">Afficher/Supprimer une élection</a>';

              } else {

                echo '
                  <h2><a>Vérification :</a></h2>
                  <form action="index.php" method="post">';

                  $req = $bdd->prepare('SELECT * FROM mairies WHERE id = ? ;');
                  $req->execute(array($_GET['idmairie']));
                  $mairie = $req->fetch();

                  $req = $bdd->prepare('SELECT * FROM individual WHERE id = ? ;');
                  $req->execute(array($_GET['individual']));
                  $indiv = $req->fetch();

                    echo '<div class="form-group">
                      <label for="individual">Confirmez-vous les données ?<br>
                      <div class="alert alert-info fade show" role="alert">
                      - <strong>ID du maire :</strong> ' . $_GET['individual'] . ' | Nom : '. $indiv['name'] . ' | Prénom : ' . $indiv['surname'];
                      echo '<br> - <strong>ID de la mairie :</strong> ' . $_GET['idmairie'] . ' | Nom : ' . $mairie['nom'] . ' | INSEE : ' . $mairie['insee'];
                      echo '</div>
                      </label>
                      <input type="hidden" name="individual" class="form-control';

                      if (isset($_GET['individualerror'])){
                        echo ' is-invalid';
                      }

                      echo '" id="individual" value="';echo($_GET['individual']);echo'" required>';

                      if (isset($_GET['individualerror'])){
                        echo '<div class="invalid-feedback">
                          ID du maire incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que l\'ID soit correct.
                        </div>';
                      }

                      echo '<input type="hidden" name="idmairie" class="form-control';

                      if (isset($_GET['individualerror'])){
                        echo ' is-invalid';
                      }

                      echo '" id="idmairie" value="';echo($_GET['idmairie']);echo'" required>';

                      if (isset($_GET['individualerror'])){
                        echo '<div class="invalid-feedback">
                          ID du maire incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que l\'ID soit correct.
                        </div>';
                      }

                      echo '<input type="hidden" name="verify" class="form-control';

                      if (isset($_GET['verifyerror'])){
                        echo ' is-invalid';
                      }

                      echo '" id="verify" value="';echo($_GET['verify']);echo'" required>';

                      if (isset($_GET['verifyerror'])){
                        echo '<div class="invalid-feedback">
                          Verify incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que l\'ID soit correct.
                        </div>';
                      }

                    echo '</div>

                    <button type="submit" class="btn btn-primary">Confirmer les données</button>

                  </form><br>

                  <form action="index.php" method="post">

                    <button type="submit" class="btn btn-danger">Retour en arrière</button>

                  </form><br><br>';

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

  } else{

    $req = $bdd->prepare('SELECT * FROM mairies WHERE id = ?;');
    $req->execute(array($_POST['idmairie']));
    $test = $req->fetch();

    $req = $bdd->prepare('SELECT * FROM individual WHERE id = ?;');
    $req->execute(array($_POST['individual']));
    $test2 = $req->fetch();

    if (!$test OR !$test2){
      header( "refresh:0;url=index.php?individualerror=true" );
    }
    else if (!isset($_POST['verify'])){
      header( "refresh:0;url=index.php?verify=true&individual=".$_POST['individual']."&idmairie=".$_POST['idmairie']);
    }
    else{

      $req=$bdd->prepare('INSERT INTO mayor(mairie, individual, verified, verifiedon) VALUES(:mairie, :individual, :verified, :verifiedon)');
      $date = date('Y-m-d H:i:s');
      $req->execute(array(
        'mairie'=> $_POST['idmairie'],
        'individual'=> $_POST['individual'],
        'verified'=> 1,
        'verifiedon' => $date
      ));

      header( "refresh:0;url=index.php?success=true" );

    }
  }

} else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
