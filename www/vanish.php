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

  if ($_GET['type'] == 'editquestion' || ($_GET['type'] == 'deletequestion')) {
    $question_fetch = $bdd->prepare('SELECT * FROM questions WHERE id = ?;');
    $question_fetch->execute(array($_GET['id']));
    $question = $question_fetch->fetch();
  }

  if ($_GET['type'] == 'editresponse' || ($_GET['type'] == 'deleteresponse')) {
    $reponse_fetch = $bdd->prepare('SELECT * FROM reponses WHERE id = ?;');
    $reponse_fetch->execute(array($_GET['id']));
    $reponse = $reponse_fetch->fetch();
  }

    if (!isset($_GET['confirm'])) {

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
                  </a>
                  <span class="sr-only">(current)</span></a>
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
            if (!isset($_SESSION['id'])){
              header( "refresh:0;url=login.php?expired=true" );
            } else if (isset($_SESSION['validation']) && $_SESSION['validation'] == 1 && ($_SESSION['ban'] == NULL || $_SESSION['ban'] < $date)) {
              if (!isset($_GET['type']) || !isset($_GET['id'])) {
                echo '<div class="alert alert-danger fade show" role="alert">
                  <strong>Un problème est survenu.</strong> L\'élement a peut-être été supprimé. Si vous pensez qu\'il s\'agit d\'une erreur, contactez un administrateur.
                </div><br><br>';
              } else {

                if ($_SESSION['role'] >= 10 && ((($_SESSION['id'] != $question['auteur']) && ($_GET['type'] == 'editquestion' || $_GET['type'] == 'deletequestion')) || (($_SESSION['id'] != $reponse['auteur']) && ($_GET['type'] == 'editresponse' || $_GET['type'] == 'deleteresponse')))) {
                  echo '<br>
                  <div class="alert alert-warning fade show" role="alert">
                    <strong>Vous êtes un ultra-modérateur</strong>. Vous pouvez modifier ou supprimer des questions et réponses, mais ces options doivent être utilisées en dernier recours uniquement. Préférez l\'utilisation du ban plutôt que l\'interaction avec du contenu qui ne vous appartient pas. Toutes vos actions en tant qu\'ultra-modérateur sont enregistrées.';
                    if ($_GET['type'] == 'editquestion' || $_GET['type'] == 'deletequestion') {
                      echo '<br><a class = "btn btn-danger btn-lg btn-block" href = "irondome.php?type=q&action=ban&id=', $question['id'] ,'&user=', $question['auteur'] ,'">Bannir la question</a>';
                    } else if ($_GET['type'] == 'editresponse' || $_GET['type'] == 'deleteresponse') {
                      echo '<br><a class = "btn btn-danger btn-lg btn-block" href = "irondome.php?type=r&action=ban&id=', $reponse['id'] ,'&user=', $reponse['auteur'] ,'">Bannir la réponse</a>';
                    }
                    echo '
                  </div>';
                }

                if ($_GET['type'] == 'editquestion') {

                  if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 10) {
                    echo '<h1 class="my-4">Modifier une question</h1>
                    <form action="vanish.php?type=editquestion&confirm=true&id=',$_GET['id'],'" method="post">
                    <div class="form-group">
                      <label for="titre">Titre de la question</label>
                      <input type="text" name="titre" class="form-control" id="titre" placeholder="Pourquoi ... " value="', $question['titre'] ,'" required>
                    </div>
                    <div class="form-group">
                      <label for="contenu">Explication de la question</label>
                      <textarea name="contenu" class="form-control" id="contenu" placeholder="Détaillez le plus possible votre question..." rows="7" required>', $question['contenu'] ,'</textarea>
                    </div>
                    <div class="form-group">
                      <label for="matiere">Séléctionnez la matière</label>
                      <select name="matiere" class="form-control" id="matiere" required>';

                      $global_fetch = $bdd->prepare('SELECT * FROM matieres WHERE annee = 0;');
                      $global_fetch->execute();
                      echo '<optgroup label="CAMPUS">';
                      while($glomat = $global_fetch->fetch()) {
                        echo '<option value="', $glomat['id'] ,'" ', ($question['matiere'] == $glomat['id']) ? ('selected') : ('') ,'>', $glomat['nom'] ,'</option>';
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
                            echo '<option value="', $fullmaj['id'] ,'" ', ($question['matiere'] == $fullmaj['id']) ? ('selected') : ('') ,'>', $fullmaj['nom'] ,'</option>';
                          }
                          if ($inserted) {
                            echo '</optgroup>';
                          }

                        }
                      } else if ($majeure['id'] > 1) {
                        echo '<optgroup label="', $majeure['nom'] ,'">';
                        while($matmaj = $matmaj_fetch->fetch()) {
                          echo '<option value="', $matmaj['id'] ,'" ', ($question['matiere'] == $matmaj['id']) ? ('selected') : ('') ,'>', $matmaj['nom'] ,'</option>';
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

                            echo '<option value="', $matiere['id'] ,'" style="margin-left:23px;" ', ($question['matiere'] == $matiere['id']) ? ('selected') : ('') ,'>', $matiere['nom'] ,'</option>';
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
                      <button type="submit" class="btn btn-primary">Modifier la question</button>
                      </form><br><br>';
                  } else {
                    echo '<h1 class="my-4">Modifier une question</h1>
                    <div class="alert alert-danger fade show" role="alert">
                      <strong>Une erreur s\'est produite</strong>. Vous ne disposez pas des autorisations nécéssaires pour réaliser cette opération.
                    </div>';
                  }

                  } else if ($_GET['type'] == 'editresponse') {

                    if ($_SESSION['id'] == $reponse['auteur'] || $_SESSION['role'] >= 10) {
                      echo '<h1 class="my-4">Modifier une réponse</h1>
                      <form action="vanish.php?type=editresponse&confirm=true&id=',$_GET['id'],'" method="post">
                        <div class="form-group">
                          <label for="contenu">Réponse</label>
                          <textarea name="contenu" class="form-control" id="contenu" placeholder="Soyez pédagogue, n\'oubliez pas que d\'autres Efreiens s\'appuieront sur votre réponse pour mieux apprendre si elle est validée..." rows="7" required>', $reponse['contenu'] ,'</textarea>
                        </div><br>
                        <button type="submit" class="btn btn-primary">Modifier la réponse</button>
                      </form><br><br>';
                    } else {
                      echo '<h1 class="my-4">Modifier une réponse</h1>
                      <div class="alert alert-danger fade show" role="alert">
                        <strong>Une erreur s\'est produite</strong>. Vous ne disposez pas des autorisations nécéssaires pour réaliser cette opération.
                      </div>';
                    }

                  } else if ($_GET['type'] == 'deletequestion') {

                    if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 10) {
                      echo '<h1 class="my-4">Supprimer une question</h1>
                      <form action="vanish.php?type=deletequestion&confirm=true&id=',$_GET['id'],'" method="post">
                      <div class="form-group">
                        <label for="titre">Titre de la question</label>
                        <input type="text" name="titre" class="form-control" id="titre" placeholder="Pourquoi ... " value="', $question['titre'] ,'" disabled>
                      </div>
                      <div class="form-group">
                        <label for="contenu">Explication de la question</label>
                        <textarea name="contenu" class="form-control" id="contenu" placeholder="Détaillez le plus possible votre question..." rows="7" disabled>', $question['contenu'] ,'</textarea>
                      </div>
                      <div class="form-group">
                        <label for="matiere">Séléctionnez la matière</label>
                        <select name="matiere" class="form-control" id="matiere" disabled>';

                        $global_fetch = $bdd->prepare('SELECT * FROM matieres WHERE annee = 0;');
                        $global_fetch->execute();
                        echo '<optgroup label="CAMPUS">';
                        while($glomat = $global_fetch->fetch()) {
                          echo '<option value="', $glomat['id'] ,'" ', ($question['matiere'] == $glomat['id']) ? ('selected') : ('') ,'>', $glomat['nom'] ,'</option>';
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
                              echo '<option value="', $fullmaj['id'] ,'" ', ($question['matiere'] == $fullmaj['id']) ? ('selected') : ('') ,'>', $fullmaj['nom'] ,'</option>';
                            }
                            if ($inserted) {
                              echo '</optgroup>';
                            }

                          }
                        } else if ($majeure['id'] > 1) {
                          echo '<optgroup label="', $majeure['nom'] ,'">';
                          while($matmaj = $matmaj_fetch->fetch()) {
                            echo '<option value="', $matmaj['id'] ,'" ', ($question['matiere'] == $matmaj['id']) ? ('selected') : ('') ,'>', $matmaj['nom'] ,'</option>';
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

                              echo '<option value="', $matiere['id'] ,'" style="margin-left:23px;" ', ($question['matiere'] == $matiere['id']) ? ('selected') : ('') ,'>', $matiere['nom'] ,'</option>';
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
                        </select><br>
                        <div class="form-group">
                          <input type="checkbox" name="confirm" class="form-check-input" id="confirm" required>
                          <label class="form-check-label" for="confirm">Je confirme vouloir supprimer cette question et les réponses associées</label>
                        </div>
                      </div>
                        <button type="submit" class="btn btn-danger">Supprimer la question</button>
                      </form><br><br>';
                    } else {
                      echo '<h1 class="my-4">Supprimer une question</h1>
                      <div class="alert alert-danger fade show" role="alert">
                        <strong>Une erreur s\'est produite</strong>. Vous ne disposez pas des autorisations nécéssaires pour réaliser cette opération.
                      </div>';
                    }

                  } else if ($_GET['type'] == 'deleteresponse') {
                    if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 10) {
                      echo '<h1 class="my-4">Supprimer une réponse</h1>
                      <form action="vanish.php?type=deleteresponse&confirm=true&id=',$_GET['id'],'" method="post">
                        <div class="form-group">
                          <label for="contenu">Réponse</label>
                          <textarea name="contenu" class="form-control" id="contenu" placeholder="Soyez pédagogue, n\'oubliez pas que d\'autres Efreiens s\'appuieront sur votre réponse pour mieux apprendre si elle est validée..." rows="7" disabled>', $reponse['contenu'] ,'</textarea>
                        </div><br>
                        <div class="form-group">
                          <input type="checkbox" name="confirm" class="form-check-input" id="confirm" required>
                          <label class="form-check-label" for="confirm">Je confirme vouloir supprimer cette réponse</label>
                        </div>
                        <button type="submit" class="btn btn-danger">Supprimer la réponse</button>
                      </form><br><br>';
                  } else {
                    echo '<h1 class="my-4">Supprimer une réponse</h1>
                    <div class="alert alert-danger fade show" role="alert">
                      <strong>Une erreur s\'est produite</strong>. Vous ne disposez pas des autorisations nécéssaires pour réaliser cette opération.
                    </div>';
                  }
                  } else {
                    echo '<h1 class="my-4">Modifier ou supprimer un contenu</h1>';
                    echo '
                    <div class="alert alert-danger fade show" role="alert">
                      <strong>Une erreur s\'est produite</strong>. Vous ne disposez pas des autorisations nécéssaires pour réaliser cette opération.
                    </div>';
                  }
                }
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

    } else {
        if (!empty($_GET['type']) && !empty($_GET['id']) && !empty($_POST['contenu'])) {
          if ($_GET['type'] == 'editquestion') {
            if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 10) {
              if (!empty($_POST['titre']) && !empty($_POST['contenu']) && !empty($_POST['matiere'])) {
                $upd_question=$bdd->prepare('UPDATE questions SET titre = ?, contenu = ?, matiere = ? WHERE id = ?;');
                $upd_question->execute(array($_POST['titre'], $_POST['contenu'], $_POST['matiere'], $_GET['id']));

                if ($_SESSION['role'] >= 10) {
                  $date = date('Y-m-d H:i:s');
                  $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
                  $banhistory->execute(array(
                    'type' => 1,
                    'expiration' => $date,
                    'utilisateur' => $question['auteur'],
                    'delateur' => $_SESSION['id'],
                    'publication' => $_GET['id'],
                    'action' => 3
                  ));
                }

                header( "refresh:0;url=question.php?edited=true&id=" . $question['id'] );
              } else {
                header( "refresh:0;url=vanish.php?type=editquestion&id=" . $question['id'] );
              }

            } else {
              header( "refresh:0;url=vanish.php?type=editquestion&id=" . $question['id'] );
            }

          } else if ($_GET['type'] == 'editresponse') {
            if ($_SESSION['id'] == $reponse['auteur'] || $_SESSION['role'] >= 10) {
              $upd_reponse=$bdd->prepare('UPDATE reponses SET contenu = ? WHERE id = ?;');
              $upd_reponse->execute(array($_POST['contenu'], $_GET['id']));

              if ($_SESSION['role'] >= 10) {
                $date = date('Y-m-d H:i:s');
                $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
                $banhistory->execute(array(
                  'type' => 2,
                  'expiration' => $date,
                  'utilisateur' => $reponse['auteur'],
                  'delateur' => $_SESSION['id'],
                  'publication' => $_GET['id'],
                  'action' => 3
                ));
              }

              header( "refresh:0;url=question.php?redited=true&id=" . $reponse['question'] );
            } else {
              header( "refresh:0;url=vanish.php?type=editresponse&id=" . $reponse['id'] );
            }
          } else {
            header( "refresh:0;url=index.php?dperror=true" );
          }
        } else if (!empty($_GET['type']) && !empty($_GET['id']) && isset($_POST['confirm']) && $_POST['confirm'] == 'on') {
          if ($_GET['type'] == 'deletequestion') {
            if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 10) {
              $del_question=$bdd->prepare('DELETE FROM questions WHERE id = ?;');
              $del_question->execute(array($_GET['id']));

              $del_reponses=$bdd->prepare('DELETE FROM reponses WHERE question = ?;');
              $del_reponses->execute(array($_GET['id']));

              if ($_SESSION['role'] >= 10) {
                $date = date('Y-m-d H:i:s');
                $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
                $banhistory->execute(array(
                  'type' => 1,
                  'expiration' => $date,
                  'utilisateur' => $question['auteur'],
                  'delateur' => $_SESSION['id'],
                  'publication' => $_GET['id'],
                  'action' => 4
                ));
              }

              header( "refresh:0;url=question.php?deleted=true&id=" . $question['id'] );
            } else {
              header( "refresh:0;url=vanish.php?type=deletequestion&id=" . $question['id'] );
            }

          } else if ($_GET['type'] == 'deleteresponse') {
            if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 10) {
              $del_reponse=$bdd->prepare('DELETE FROM reponses WHERE id = ?;');
              $del_reponse->execute(array($_GET['id']));

              if ($_SESSION['role'] >= 10) {
                $date = date('Y-m-d H:i:s');
                $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
                $banhistory->execute(array(
                  'type' => 2,
                  'expiration' => $date,
                  'utilisateur' => $reponse['auteur'],
                  'delateur' => $_SESSION['id'],
                  'publication' => $_GET['id'],
                  'action' => 4
                ));
              }

              header( "refresh:0;url=question.php?rdeleted=true&id=" . $reponse['question'] );
            } else {
              header( "refresh:0;url=vanish.php?type=deleteresponse&id=" . $reponse['id'] );
            }
          } else {
            header( "refresh:0;url=index.php?dperror=true" );
          }

        } else {
          header( "refresh:0;url=index.php?ierror=true" );
        }

    }

}
else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
