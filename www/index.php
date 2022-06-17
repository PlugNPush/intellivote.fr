<?php
require_once dirname(__FILE__).'/../../config/config.php';
try
{
    $bdd = new PDO('mysql:host='.getDBHost().';dbname=efreidynamo', getDBUsername(), getDBPassword(), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}

function limit_text($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos   = array_keys($words);
        $text  = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
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

if (isset($_SESSION['id'])){

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
              <li class="nav-item active">
                <a class="nav-link" href="index.php">Répondre à des questions
                  <span class="sr-only">(current)</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="newquestion.php">Poser une question</a>
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
                <li class="nav-item active">
                  <a class="nav-link" href="login.php">Connexion
                  <span class="sr-only">(current)</span></a>
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

            if (isset($_GET['recherche'])){
              echo '<h1 class="my-4">Résultats de la recherche
              </h1>';
            }else{
              echo '<h1 class="my-4">Bienvenue sur Efrei Dynamo,
                <small>', $_SESSION['pseudo'], '</small>
              </h1>';
            }

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

            $date = date('Y-m-d H:i:s');
            if (isset($_SESSION['validation']) && $_SESSION['validation'] == 1 && ($_SESSION['ban'] == NULL || $_SESSION['ban'] < $date)) {
              if (isset($_GET['recherche'])){
                $fetch_question=$bdd->prepare('SELECT * FROM questions WHERE titre LIKE CONCAT("%", ?, "%") OR contenu LIKE CONCAT("%", ?, "%");');
                $fetch_question->execute(array($_GET['recherche'], $_GET['recherche']));
              } else if (isset($_GET['annee'])) {

                $fetch_question=$bdd->prepare('SELECT * FROM questions WHERE matiere IN (SELECT id FROM matieres WHERE annee = ?) ORDER BY date DESC;;');
                $fetch_question->execute(array($_GET['annee']));
              } else if (isset($_GET['module'])) {
                $fetch_question=$bdd->prepare('SELECT * FROM questions WHERE matiere IN (SELECT id FROM matieres WHERE module = ?) ORDER BY date DESC;;');
                $fetch_question->execute(array($_GET['module']));
              } else if (isset($_GET['majeure'])) {
                $fetch_question=$bdd->prepare('SELECT * FROM questions WHERE matiere IN (SELECT id FROM matieres WHERE majeure = ?) ORDER BY date DESC;');
                $fetch_question->execute(array($_GET['majeure']));
              } else {
                $fetch_question=$bdd->prepare('SELECT * FROM questions WHERE repondue != 1 ORDER BY date DESC;;');
                $fetch_question->execute();
              }

              while($temp_question=$fetch_question->fetch()){

                $auteur_question=$bdd->prepare('SELECT * FROM utilisateurs WHERE id = ?;');
                $auteur_question->execute(array($temp_question['auteur']));
                $auteur = $auteur_question->fetch();

                $cours_question=$bdd->prepare('SELECT * FROM matieres WHERE id = ?;');
                $cours_question->execute(array($temp_question['matiere']));
                $cours = $cours_question->fetch();

                if ($temp_question['ban'] != 1 || $_SESSION['role'] >= 1) {
                  echo '<!-- Blog Post -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <h2 class="card-title">', $temp_question['titre'],'</h2>
                      <p class="card-text">', limit_text($temp_question['contenu'], 80),'</p>
                      <a href="question.php?id=',$temp_question['id'],'" class="btn btn-primary">Voir plus &rarr;</a>
                    </div>
                    <div class="card-footer text-muted">
                      Publié le ', $temp_question['date'],' par
                      <a href="account.php?id=', $auteur['id'] ,'">', $auteur['pseudo'],'</a><br>';
                      echo $cours['nom'];
                      if ($cours['semestre'] != 0) {
                        echo ', semestre ', $cours['semestre'];
                      } else {
                        $majeure_question=$bdd->prepare('SELECT * FROM majeures WHERE id = ?;');
                        $majeure_question->execute(array($cours['majeure']));
                        $majeure = $majeure_question->fetch();

                        if ($cours['majeure'] == 1) {
                          echo ' (Campus)';
                        } else if ($cours['majeure'] > 1) {
                          echo ' (', $majeure['nom'] ,')';
                        }
                      }
                      echo '
                    </div>
                  </div>';
                }
              }

              echo '<!-- Pagination -->
              <ul class="pagination justify-content-center mb-4">
                <li class="page-item disabled">
                  <a class="page-link" href="#">&larr; Plus ancien</a>
                </li>
                <li class="page-item disabled">
                  <a class="page-link" href="#">Plus récent &rarr;</a>
                </li>
              </ul>

            </div>

            <!-- Sidebar Widgets Column -->
            <div class="col-md-4">

              <!-- Search Widget -->
              <div class="card my-4">
                <h5 class="card-header">Rechercher</h5>
                <div class="card-body">
                  <div class="input-group">
                    <form action="index.php" method="get">
                      <input type="text" name="recherche" class="form-control" placeholder="Rechercher..." ', (isset($_GET['recherche'])) ? ('value="' . $_GET['recherche'] . '"') : (""),'>
                      <span class="input-group-append">
                        <button class="btn btn-secondary" type="submit">Go !</button>
                      </span>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Categories Widget -->
              <div class="card my-4">
                <h5 class="card-header">Catégories</h5>
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-6">
                      <ul class="list-unstyled mb-0">
                        <li>
                          <a href="index.php?annee=1">L1</a>
                        </li>
                        <li>
                          <a href="index.php?annee=2">L2</a>
                        </li>
                        <li>
                          <a href="index.php?annee=3">L3</a>
                        </li>
                      </ul>
                    </div>
                    <div class="col-lg-6">
                      <ul class="list-unstyled mb-0">
                        <li>
                          <a href="index.php?module=16">M1</a>
                        </li>
                        <li>
                          <a href="index.php?module=16">M2</a>
                        </li>
                        <li>
                          <a href="index.php?module=20">Campus</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>';


              $nb_questions=$bdd->prepare('SELECT COUNT(*) FROM questions WHERE auteur = ?;');
              $nb_questions->execute(array($_SESSION['id']));
              $questions = $nb_questions->fetch();

              $nb_reponses=$bdd->prepare('SELECT COUNT(*) FROM reponses WHERE auteur = ?;');
              $nb_reponses->execute(array($_SESSION['id']));
              $reponses = $nb_reponses->fetch();

              $nb_repondues=$bdd->prepare('SELECT COUNT(*) FROM questions WHERE repondue = 0;');
              $nb_repondues->execute();
              $repondues = $nb_repondues->fetch();

              $nb_elues=$bdd->prepare('SELECT COUNT(*) FROM reponses WHERE auteur = ? AND validation = 1;');
              $nb_elues->execute(array($_SESSION['id']));
              $elues = $nb_elues->fetch();


              echo '

              <!-- Side Widget -->
              <div class="card my-4">
                <h5 class="card-header">Récapitulatif</h5>
                <div class="card-body">
                  Vous avez posé ', $questions['COUNT(*)'],' questions, et vous avez répondu à ', $reponses['COUNT(*)'],' questions sur Efrei Dynamo. ', $repondues['COUNT(*)'],' questions sont en attente de validation. Vous avez ', $elues['COUNT(*)'] ,' réponses qui ont été élues comme bonnes réponses.<br><br>En tout, vous avez ', $_SESSION['karma'],' points de Karma.
                </div>
              </div>';

            } else {
              if ($_SESSION['ban'] != NULL && $_SESSION['ban'] >= $date) {
                echo '
                <div class="alert alert-danger fade show" role="alert">
                  <strong>Vous avez été banni</strong>. Si besoin, contactez un modérateur avec votre adresse mail Efrei. Votre compte sera à nouveau utilisable à partir du ', $_SESSION['ban'] ,'.<br><a class = "btn btn-secondary btn-lg btn-block" href = "logout.php">Se déconnecter</a>
                </div><br>';
              } else {
                echo '
                <div class="alert alert-danger fade show" role="alert">
                  <strong>Hello ', $_SESSION['pseudo'], ' !</strong><br> Vous devez confirmer votre statut d\'Efreien pour accéder au site. Celui-ci n\'a pas encore pu être vérifié.<br><a class = "btn btn-primary" href = "validation.php">Lancer ou vérifier la procédure de validation</a>
                </div>';
              }

          }

            echo '

          </div>

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

    </html>
';

}
else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
