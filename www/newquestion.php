<?php
require_once dirname(__FILE__).'/../../config/config.php';
  try {
    $bdd = new PDO('mysql:host='.getDBHost().';dbname=efreidynamo', getDBUsername(), getDBPassword(), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
  } catch(Exception $e) {
    exit ('Erreur while connecting to database: '.$e->getMessage());
  }
  session_start();
  if (isset($_SESSION['id'])) {
    $req = $bdd->prepare('SELECT * FROM utilisateurs WHERE id = ?;');
    $req->execute(array($_SESSION['id']));
    $test = $req->fetch();
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
  }
if(empty($_POST['titre']) OR empty($_POST['contenu']) OR empty($_POST['matiere'])){

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
        <a class="navbar-brand" href="index.php">Projet Efrei Dynamo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span id="new-dark-navbar-toggler-icon" class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Répondre à des questions
              </a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="newquestion.php">Poser une question
              <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="account.php">Mon compte</a>
            </li>';

            if (isset($_SESSION['id'])) {
              echo '
              <li class="nav-item">
                <a class="nav-link" href="logout.php">Se déconnecter</a>
              </li>';
            } else {
              echo '
              <li class="nav-item">
                <a class="nav-link" href="login.php">Connexion</a>
              </li>';
            }

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

        $date = date('Y-m-d H:i:s');
        if (!isset($_SESSION['id'])){
          header( "refresh:0;url=login.php?expired=true" );
        } else if (isset($_SESSION['validation']) && $_SESSION['validation'] == 1 && ($_SESSION['ban'] == NULL || $_SESSION['ban'] < $date)) {
          echo '<h1 class="my-4">Nouvelle question</h1>
          <form action="newquestion.php" method="post">
            <div class="form-group">
              <label for="titre">Titre de la question</label>
              <input type="text" name="titre" class="form-control" id="titre" placeholder="Pourquoi ... " required>
            </div>
            <div class="form-group">
              <label for="contenu">Explication de la question</label>
              <textarea name="contenu" class="form-control" id="contenu" placeholder="Détaillez le plus possible votre question..." rows="7" required></textarea>
            </div>
            <div class="form-group">
              <label for="matiere">Séléctionnez la matière</label>
              <select name="matiere" class="form-control" id="matiere" required>';

              $global_fetch = $bdd->prepare('SELECT * FROM matieres WHERE annee = 0;');
              $global_fetch->execute();
              echo '<optgroup label="CAMPUS">';
              while($glomat = $global_fetch->fetch()) {
                echo '<option value="', $glomat['id'] ,'">', $glomat['nom'] ,'</option>';
              }
              echo '</optgroup>';

              $majeure_fetch = $bdd->prepare('SELECT * FROM majeures WHERE id = ?;');
              $majeure_fetch->execute(array($_SESSION['majeure']));
              $majeure = $majeure_fetch->fetch();

              $matmaj_fetch = $bdd->prepare('SELECT * FROM matieres WHERE semestre = 0 AND majeure = ?;');
              $matmaj_fetch->execute(array($_SESSION['majeure']));

              if ($_SESSION['annee'] >= 7) {
                $fullmajeure_fetch = $bdd->prepare('SELECT * FROM majeures;');
                $fullmajeure_fetch->execute();

                while ($fullmajeure = $fullmajeure_fetch->fetch()) {
                  $fullmaj_fetch = $bdd->prepare('SELECT * FROM matieres WHERE semestre = 0 AND annee >= 7 AND majeure = ?;');
                  $fullmaj_fetch->execute(array($fullmajeure['id']));
                  $inserted = false;

                  while($fullmaj = $fullmaj_fetch->fetch()) {
                    if (!$inserted) {
                      echo '<optgroup label="', $fullmajeure['nom'] ,'">';
                      $inserted = true;
                    }
                    echo '<option value="', $fullmaj['id'] ,'">', $fullmaj['nom'] ,'</option>';
                  }
                  if ($inserted) {
                    echo '</optgroup>';
                  }

                }
              } else if ($majeure['id'] > 1) {
                echo '<optgroup label="', $majeure['nom'] ,'">';
                while($matmaj = $matmaj_fetch->fetch()) {
                  echo '<option value="', $matmaj['id'] ,'">', $matmaj['nom'] ,'</option>';
                }
                echo '</optgroup>';
              }

              $maxsemestre_fetch = $bdd->prepare('SELECT MAX(semestre) FROM matieres WHERE annee <= ?;');
              $maxsemestre_fetch->execute(array($_SESSION['annee']));
              $maxsemestre = $maxsemestre_fetch->fetch();

              for ($semestre = $maxsemestre['MAX(semestre)']; $semestre>=1; $semestre--) {
                $semestre_inserted = FALSE;

                $module_fetch = $bdd->prepare('SELECT * FROM modules;');
                $module_fetch->execute();

                while($module = $module_fetch->fetch()){
                  $matieres_fetch = $bdd->prepare('SELECT * FROM matieres WHERE annee <= ? AND module = ? AND semestre = ?;');
                  $matieres_fetch->execute(array($_SESSION['annee'], $module['id'], $semestre));
                  $inserted = FALSE;

                  while ($matiere = $matieres_fetch->fetch()) {
                    if(!$semestre_inserted){
                      $semestre_inserted = TRUE;
                      echo '<optgroup label="SEMESTRE ',$semestre,'">';
                    }
                    if(!$inserted){
                      $inserted = TRUE;
                      echo '<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;',$module['nom'],'">';
                    }

                    echo '<option value="', $matiere['id'] ,'" style="margin-left:23px;">', $matiere['nom'] ,'</option>';
                  }
                  if($inserted){
                    echo '</optgroup>';
                  }
                }
                if($semestre_inserted){
                  echo '</optgroup>';
                }
              }


              echo '
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer la question</button>
            </form><br><br>';

        } else {
          if ($_SESSION['ban'] != NULL && $_SESSION['ban'] >= $date) {
            echo '<h1 class="my-4">Nouvelle question</h1>
            <div class="alert alert-danger fade show" role="alert">
              <strong>Vous avez été banni</strong>. Si besoin, contactez un modérateur avec votre adresse mail Efrei. Votre compte sera à nouveau utilisable à partir du ', $_SESSION['ban'] ,'.<br><a class = "btn btn-secondary btn-lg btn-block" href = "logout.php">Se déconnecter</a>
            </div><br>';
          } else {
            echo '<h1 class="my-4">Nouvelle question</h1>
            <div class="alert alert-danger fade show" role="alert">
              <strong>Hello ', $_SESSION['pseudo'], ' !</strong><br> Vous devez confirmer votre statut d\'Efreien pour accéder au site. Celui-ci n\'a pas encore pu être vérifié.<br><a class = "btn btn-primary" href = "validation.php">Lancer ou vérifier la procédure de validation</a>
            </div>';
          }
        }

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
  $req=$bdd->prepare('INSERT INTO questions(auteur, titre, contenu, matiere, date) VALUES(:auteur, :titre, :contenu, :matiere, :date)');
  if (isset($_SESSION['id'])) {
    $date = date('Y-m-d H:i:s');
    $req->execute(array(
      'auteur'=> $_SESSION['id'],
      'titre'=> $_POST['titre'],
      'contenu'=> $_POST['contenu'],
      'matiere'=> $_POST['matiere'],
      'date'=> $date
    ));
    $karmaplus = $bdd->prepare('UPDATE utilisateurs SET karma = karma + 1 WHERE id = ?;');
    $karmaplus->execute(array($_SESSION['id']));
    header( "refresh:0;url=index.php" );
  }else{
    header( "refresh:0;url=login.php?expired=true" );
  }
}
?>
