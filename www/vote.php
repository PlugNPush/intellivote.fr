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

    // Back-end only
    // Handle upvote, downvote or validation here, then redirect to source
    if (isset($_GET['q'])){
      if (isset($_GET['action'])) {
        $question_fetch = $bdd->prepare('SELECT * FROM questions WHERE id = ?;');
        $question_fetch->execute(array($_GET['q']));
        $question = $question_fetch->fetch();

        if (isset($_GET['r'])) {

          $reponse_fetch = $bdd->prepare('SELECT * FROM reponses WHERE id = ?;');
          $reponse_fetch->execute(array($_GET['r']));
          $reponse = $reponse_fetch->fetch();

          if ($_GET['action'] == 'upvote') {
            $upvote = $bdd->prepare('UPDATE reponses SET upvotes = upvotes + 1 WHERE id = ?;');
            $upvote->execute(array($_GET['r']));

            $karmaplus = $bdd->prepare('UPDATE utilisateurs SET karma = karma + 1 WHERE id = ?;');
            $karmaplus->execute(array($reponse['auteur']));
            header( "refresh:0;url=question.php?id=" . $_GET['q'] );
          } else if ($_GET['action'] == 'downvote') {
            $downvote = $bdd->prepare('UPDATE reponses SET downvotes = downvotes + 1 WHERE id = ?;');
            $downvote->execute(array($_GET['r']));

            $karmamoins = $bdd->prepare('UPDATE utilisateurs SET karma = karma - 1 WHERE id = ?;');
            $karmamoins->execute(array($reponse['auteur']));
            header( "refresh:0;url=question.php?id=" . $_GET['q'] );
          } else if ($_GET['action'] == 'validate') {
            $question_fetch = $bdd->prepare('SELECT * FROM questions WHERE id = ?;');
            $question_fetch->execute(array($_GET['q']));
            $question = $question_fetch->fetch();

            if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 2) {
              $validate = $bdd->prepare('UPDATE reponses SET validation = 1 WHERE id = ?;');
              $validate->execute(array($_GET['r']));

              $answered = $bdd->prepare('UPDATE questions SET repondue = 1 WHERE id = ?;');
              $answered->execute(array($_GET['q']));

              $karmamax = $bdd->prepare('UPDATE utilisateurs SET karma = karma + 10 WHERE id = ?;');
              $karmamax->execute(array($reponse['auteur']));
              header( "refresh:0;url=question.php?id=" . $_GET['q'] );
            } else {
              header( "refresh:0;url=question.php?dperror=true&id=" . $_GET['q'] );
            }

          } else if ($_GET['action'] == 'unvalidate'){

            if ($_SESSION['id'] == $question['auteur'] || $_SESSION['role'] >= 2) {
              $unvalidate = $bdd->prepare('UPDATE reponses SET validation = 0 WHERE id = ?;');
              $unvalidate->execute(array($_GET['r']));

              $unanswered = $bdd->prepare('UPDATE questions SET repondue = 0 WHERE id = ?;');
              $unanswered->execute(array($_GET['q']));

              $karmaunmax = $bdd->prepare('UPDATE utilisateurs SET karma = karma - 10 WHERE id = ?;');
              $karmaunmax->execute(array($reponse['auteur']));
              header( "refresh:0;url=question.php?id=" . $_GET['q'] );
            } else {
              header( "refresh:0;url=question.php?dperror=true&id=" . $_GET['q'] );
            }

          } else {
            header( "refresh:0;url=question.php?dperror=true&id=" . $_GET['q'] );
          }
        } else {
          if ($_GET['action'] == 'upvote') {
            $upvote = $bdd->prepare('UPDATE questions SET upvotes = upvotes + 1 WHERE id = ?;');
            $upvote->execute(array($_GET['q']));

            $karmaplus = $bdd->prepare('UPDATE utilisateurs SET karma = karma + 1 WHERE id = ?;');
            $karmaplus->execute(array($question['auteur']));
            header( "refresh:0;url=question.php?id=" . $_GET['q'] );
          } else {
            header( "refresh:0;url=question.php?dperror=true&id=" . $_GET['q'] );
          }
        }
      } else {
        header( "refresh:0;url=question.php?ierror=true&id=" . $_GET['q'] );
      }

    } else {
      header( "refresh:0;url=index.php?ierror=true" );
    }


}
else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
