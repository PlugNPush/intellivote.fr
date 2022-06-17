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
          if (isset($_SESSION['validation']) && $_SESSION['validation'] == 1 && ($_SESSION['ban'] == NULL || $_SESSION['ban'] < $date)) {
            if (isset($_GET['id'])){
                $question_fetch = $bdd->prepare('SELECT * FROM questions WHERE id = ?;');
                $question_fetch->execute(array($_GET['id']));
                $question = $question_fetch->fetch();

                $auteur_question=$bdd->prepare('SELECT * FROM utilisateurs WHERE id = ?;');
                $auteur_question->execute(array($question['auteur']));
                $auteur = $auteur_question->fetch();

                $cours_question=$bdd->prepare('SELECT * FROM matieres WHERE id = ?;');
                $cours_question->execute(array($question['matiere']));
                $cours = $cours_question->fetch();

                $reponse_fetch = $bdd->prepare('SELECT * FROM reponses WHERE question = ? ORDER BY validation DESC, date ASC;');
                $reponse_fetch->execute(array($_GET['id']));

                echo'<h1 class="mt-4">' , $question['titre'], '</h1>';

                echo '<h4 class="mt-0 mb-4">', $cours['nom'];
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
                echo '</h4>';

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

                if (isset($_GET['vreport'])) {
                  echo '
                  <div class="alert alert-success fade show" role="alert">
                    <strong>Votre signalement a bien été enregistré !</strong> Notre équipe de modérateurs se chargera de vérifier votre signalement dans les prochains jours.
                  </div>';
                }

                if (isset($_GET['deleted'])) {
                  echo '
                  <div class="alert alert-success fade show" role="alert">
                    <strong>La question a bien été supprimée</strong>. La question ainsi que ses réponses associées ont été supprimées irréversiblement.
                  </div>';
                }

                if (isset($_GET['rdeleted'])) {
                  echo '
                  <div class="alert alert-success fade show" role="alert">
                    <strong>La réponse a bien été supprimée</strong>. La réponses a été supprimée irréversiblement.
                  </div>';
                }

                if (isset($_GET['edited'])) {
                  echo '
                  <div class="alert alert-success fade show" role="alert">
                    <strong>La question a bien été modifiée</strong>. Les modifications envoyées ont été enregistrées.
                  </div>';
                }

                if (isset($_GET['redited'])) {
                  echo '
                  <div class="alert alert-success fade show" role="alert">
                    <strong>La réponse a bien été modifiée</strong>. Les modifications envoyées ont été enregistrées.
                  </div>';
                }

                if (!$question) {
                  echo '
                  <div class="alert alert-danger fade show" role="alert">
                    <strong>Cette question n\'existe pas ou plus</strong>. Elle a peut-être été supprimée par son auteur ou un modérateur.<br><a class = "btn btn-secondary btn-lg btn-block" href = "index.php">Retour à l\'accueil</a>
                  </div>';
                } else {
                  if ($question['ban'] != 1 || $_SESSION['role'] >= 1 || $_SESSION['id'] == $question['auteur']) {

                    if ($question['ban'] == 1 && $_SESSION['role'] >= 1) {
                      echo '
                      <div class="alert alert-danger fade show" role="alert">
                        <strong>Cette question a été bannie</strong>. Seul vous, les autres modérateurs et l\'auteur pouvez y avoir accès, et le ban est valable à vie. <br><a class = "btn btn-warning btn-lg btn-block" href = "irondome.php?type=q&action=unban&id=', $question['id'] ,'&user=', $question['auteur'] ,'">Pardonner la question</a>
                      </div><br>';
                    } else if ($question['ban'] == 1 && $_SESSION['id'] == $question['auteur']) {
                      echo '
                      <div class="alert alert-danger fade show" role="alert">
                        <strong>Votre question a été bannie</strong>. Seul vous et les modérateurs pouvez y avoir accès, et le ban est valable à vie. Si besoin, contactez un modérateur avec votre adresse mail Efrei. <br><a class = "btn btn-secondary btn-lg btn-block" href = "index.php">Retour à l\'accueil</a>
                      </div><br>';
                    }

                    echo '<!-- Blog Post -->
                    <a href="newresponse.php?question=',$question['id'],'" class="btn btn-primary btn-lg btn-block">Répondre</a><br>
                    <div class="card mb-4">
                      <div class="card-body">
                        <p class="card-text">', nl2br($question['contenu']),'</p>
                      </div>
                      <div class="card-footer text-muted">
                        Publié le ', $question['date'],' par
                        <a href="account.php?id=', $auteur['id'] ,'">', $auteur['pseudo'],'</a><br>
                        ', $question['upvotes'],' upvotes <a href="vote.php?q=', $question['id'],'&action=upvote">(+)</a><br>';

                        if ($_SESSION['role'] >= 1) {
                          if ($question['ban'] == 0) {
                            echo '<br><a href="irondome.php?type=q&action=ban&id=', $question['id'] ,'&user=', $question['auteur'] ,'">Bannir la question</a>';
                          } else {
                            echo '<br><a href="irondome.php?type=q&action=unban&id=', $question['id'] ,'&user=', $question['auteur'] ,'">Pardonner la question</a>';
                          }
                        } else {
                          echo '<br><a href="irondome.php?type=q&action=report&id=', $question['id'] ,'&user=', $question['auteur'] ,'">Signaler la question</a>';
                        }

                        if ($_SESSION['role'] >= 10 || $_SESSION['id'] == $question['auteur']) {
                          echo '<br><a href="vanish.php?type=editquestion&id=', $question['id'] , '">Modifier la question</a> | <a href="vanish.php?type=deletequestion&id=', $question['id'] , '">Supprimer la question</a>';
                        }

                        echo '
                      </div>
                    </div>';

                    while($reponse = $reponse_fetch->fetch()){

                        $auteur_reponse=$bdd->prepare('SELECT * FROM utilisateurs WHERE id = ?;');
                        $auteur_reponse->execute(array($reponse['auteur']));
                        $auteur_rep = $auteur_reponse->fetch();

                        if ($reponse['ban'] != 1 || $_SESSION['role'] >= 1 || $_SESSION['id'] == $reponse['auteur']) {

                          echo '<!-- Blog Post -->
                          <div class="card mb-4">
                          <div class="card-body">';

                          if ($reponse['ban'] == 1 && $_SESSION['role'] >= 1) {
                            echo '
                            <div class="alert alert-danger fade show" role="alert">
                              <strong>Cette réponse a été bannie</strong>. Seul vous, les autres modérateurs et l\'auteur pouvez y avoir accès, et le ban est valable à vie. <br><a class = "btn btn-warning btn-lg btn-block" href = "irondome.php?type=r&action=unban&id=', $reponse['id'] ,'&user=', $reponse['auteur'] ,'">Pardonner la réponse</a>
                            </div>';
                          } else if ($reponse['ban'] == 1 && $_SESSION['id'] == $reponse['auteur']) {
                            echo '
                            <div class="alert alert-danger fade show" role="alert">
                              <strong>Votre réponse a été bannie</strong>. Seul vous et les modérateurs pouvez y avoir accès, et le ban est valable à vie. Si besoin, contactez un modérateur avec votre adresse mail Efrei. <br><a class = "btn btn-secondary btn-lg btn-block" href = "index.php">Retour à l\'accueil</a>
                            </div>';
                          }

                          echo '
                              <p class="card-text">', nl2br($reponse['contenu']),'</p>
                          </div>
                          <div class="card-footer text-muted">
                              Publié le ', $reponse['date'],' par
                              <a href="account.php?id=', $auteur_rep['id'] ,'">', $auteur_rep['pseudo'],'</a><br>
                              ', $reponse['upvotes'],' upvotes <a href="vote.php?q=', $question['id'] ,'&r=', $reponse['id'],'&action=upvote">(+)</a> | ', $reponse['downvotes'],' downvotes <a href="vote.php?q=', $question['id'] ,'&r=', $reponse['id'],'&action=downvote">(-)</a>';
                              if ($question['repondue'] != 1) {
                                if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 2) {
                                  echo '<a href="vote.php?q=',$question['id'],'&r=', $reponse['id'] ,'&action=validate" class="btn btn-success btn-lg btn-block">Marquer comme la bonne réponse</a>';
                                }
                              } else {
                                if ($reponse['validation'] == 1) {
                                  if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 2) {
                                    echo '<a href="vote.php?q=',$question['id'],'&r=', $reponse['id'] ,'&action=unvalidate" class="btn btn-danger btn-lg btn-block">Retirer la bonne réponse</a>';
                                  } else {
                                    echo '<button type="button" class="btn btn-success btn-lg btn-block" disabled>Élue bonne réponse</button>';
                                  }
                                }
                              }
                              if ($_SESSION['role'] >= 1) {
                                if ($reponse['ban'] == 0) {
                                  echo '<br><a href="irondome.php?type=r&action=ban&id=', $reponse['id'] ,'&user=', $reponse['auteur'] ,'">Bannir la réponse</a>';
                                } else {
                                  echo '<br><a href="irondome.php?type=r&action=unban&id=', $reponse['id'] ,'&user=', $reponse['auteur'] ,'">Pardonner la réponse</a>';
                                }
                              } else if ($reponse['ban'] == 0) {
                                echo '<br><a href="irondome.php?type=r&action=report&id=', $reponse['id'] ,'&user=', $reponse['auteur'] ,'">Signaler la réponse</a>';
                              }

                              if ($_SESSION['role'] >= 10 || $_SESSION['id'] == $reponse['auteur']) {
                                echo '<br><a href="vanish.php?type=editresponse&id=', $reponse['id'] , '">Modifier la réponse</a> | <a href="vanish.php?type=deleteresponse&id=', $reponse['id'] , '">Supprimer la réponse</a>';
                              }
                              echo '
                          </div>
                          </div>';
                        }
                    }
                    echo '<a href="newresponse.php?question=',$question['id'],'" class="btn btn-primary btn-lg btn-block">Répondre</a><br>';
                  } else {
                      echo '
                      <div class="alert alert-danger fade show" role="alert">
                        <strong>Cette question est bannie</strong>. Cette question ne respectait pas les standards de la communauté, et a donc été bannie à vie. <br><a class = "btn btn-secondary btn-lg btn-block" href = "index.php">Retour à l\'accueil</a>
                      </div><br>';
                  }
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
                    <input type="text" name="recherche" class="form-control" placeholder="Rechercher...">
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
              echo '<h1 class="my-4">Bienvenue sur Efrei Dynamo,
                <small>', $_SESSION['pseudo'], '</small>
              </h1>
              <div class="alert alert-danger fade show" role="alert">
                <strong>Vous avez été banni</strong>. Si besoin, contactez un modérateur avec votre adresse mail Efrei. Votre compte sera à nouveau utilisable à partir du ', $_SESSION['ban'] ,'.<br><a class = "btn btn-secondary btn-lg btn-block" href = "logout.php">Se déconnecter</a>
              </div><br>';
            } else {
              echo '<h1 class="my-4">Bienvenue sur Efrei Dynamo,
                <small>', $_SESSION['pseudo'], '</small>
              </h1>
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
