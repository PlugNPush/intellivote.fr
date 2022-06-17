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
    // Handle moderation and report here, then redirect to source

    if (isset($_GET['type']) && isset($_GET['id']) && isset($_GET['user']) && isset($_GET['action'])) {
      $interval = new DateInterval('P1M');
      $date = date('Y-m-d H:i:s');
      $bandate = new DateTime($date);
      $bandate->add($interval);
      $bandate = $bandate->format('Y-m-d H:i:s');
      if ($_GET['type'] == 'q') {
        if ($_GET['action'] == 'ban' && $_SESSION['role'] >= 1) {
          $ban = $bdd->prepare('UPDATE questions SET ban = 1 WHERE id = ?;');
          $ban->execute(array($_GET['id']));

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
          $banhistory->execute(array(
            'type' => 1,
            'expiration' => $date,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'publication' => $_GET['id'],
            'action' => 1
          ));

          $gatherdata = $bdd->prepare('SELECT * FROM questions WHERE id = ?;');
          $gatherdata->execute(array($_GET['id']));
          $data = $gatherdata->fetch();

          $karma = $bdd->prepare('UPDATE utilisateurs SET karma = karma - 10 WHERE id = ?;');
          $karma->execute(array($data['auteur']));
          header( "refresh:0;url=question.php?id=" . $_GET['id'] );
        } else if ($_GET['action'] == 'unban' && $_SESSION['role'] >= 1) {
          $ban = $bdd->prepare('UPDATE questions SET ban = 0 WHERE id = ?;');
          $ban->execute(array($_GET['id']));

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
          $banhistory->execute(array(
            'type' => 1,
            'expiration' => $date,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'publication' => $_GET['id'],
            'action' => 2
          ));

          $gatherdata = $bdd->prepare('SELECT * FROM questions WHERE id = ?;');
          $gatherdata->execute(array($_GET['id']));
          $data = $gatherdata->fetch();

          $karma = $bdd->prepare('UPDATE utilisateurs SET karma = karma + 10 WHERE id = ?;');
          $karma->execute(array($data['auteur']));
          header( "refresh:0;url=question.php?id=" . $_GET['id'] );
        } else if ($_GET['action'] == 'report'){

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
          $banhistory->execute(array(
            'type' => 1,
            'expiration' => $date,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'publication' => $_GET['id'],
            'action' => 0
          ));
          header( "refresh:0;url=question.php?vreport=true&id=" . $_GET['id'] );
        } else {
          header( "refresh:0;url=index.php?dperror=true" );
        }

      } else if ($_GET['type'] == 'r') {
        if ($_GET['action'] == 'ban' && $_SESSION['role'] >= 1) {
          $ban = $bdd->prepare('UPDATE reponses SET ban = 1 WHERE id = ?;');
          $ban->execute(array($_GET['id']));

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
          $banhistory->execute(array(
            'type' => 2,
            'expiration' => $date,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'publication' => $_GET['id'],
            'action' => 1
          ));

          $gatherdata = $bdd->prepare('SELECT * FROM reponses WHERE id = ?;');
          $gatherdata->execute(array($_GET['id']));
          $data = $gatherdata->fetch();

          $karma = $bdd->prepare('UPDATE utilisateurs SET karma = karma - 10 WHERE id = ?;');
          $karma->execute(array($data['auteur']));
          header( "refresh:0;url=question.php?id=" . $data['question'] );
        } else if ($_GET['action'] == 'unban' && $_SESSION['role'] >= 1) {
          $ban = $bdd->prepare('UPDATE reponses SET ban = 0 WHERE id = ?;');
          $ban->execute(array($_GET['id']));

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
          $banhistory->execute(array(
            'type' => 2,
            'expiration' => $date,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'publication' => $_GET['id'],
            'action' => 2
          ));

          $gatherdata = $bdd->prepare('SELECT * FROM reponses WHERE id = ?;');
          $gatherdata->execute(array($_GET['id']));
          $data = $gatherdata->fetch();

          $karma = $bdd->prepare('UPDATE utilisateurs SET karma = karma + 10 WHERE id = ?;');
          $karma->execute(array($data['auteur']));
          header( "refresh:0;url=question.php?id=" . $data['question'] );
        } else if ($_GET['action'] == 'report'){

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, publication, action) VALUES(:type, :expiration, :utilisateur, :delateur, :publication, :action);');
          $banhistory->execute(array(
            'type' => 2,
            'expiration' => $date,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'publication' => $_GET['id'],
            'action' => 0
          ));

          $gatherdata = $bdd->prepare('SELECT * FROM reponses WHERE id = ?;');
          $gatherdata->execute(array($_GET['id']));
          $data = $gatherdata->fetch();

          header( "refresh:0;url=question.php?vreport=true&id=" . $data['question'] );
        } else {
          header( "refresh:0;url=index.php?dperror=true" );
        }
      } else if ($_GET['type'] == 'u') {
        if ($_GET['action'] == 'ban' && $_SESSION['role'] >= 1) {
          $ban = $bdd->prepare('UPDATE utilisateurs SET ban = ? WHERE id = ?;');
          $ban->execute(array($bandate, $_GET['user']));

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, action) VALUES(:type, :expiration, :utilisateur, :delateur, :action);');
          $banhistory->execute(array(
            'type' => 3,
            'expiration' => $bandate,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'action' => 1
          ));

          $karma = $bdd->prepare('UPDATE utilisateurs SET karma = karma - 50 WHERE id = ?;');
          $karma->execute(array($_GET['user']));
          header( "refresh:0;url=account.php?id=" . $_GET['user'] );
        } else if ($_GET['action'] == 'unban' && $_SESSION['role'] >= 1) {
          $ban = $bdd->prepare('UPDATE utilisateurs SET ban = NULL WHERE id = ?;');
          $ban->execute(array($_GET['user']));

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, action) VALUES(:type, :expiration, :utilisateur, :delateur, :action);');
          $banhistory->execute(array(
            'type' => 3,
            'expiration' => $date,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'action' => 2
          ));

          $karma = $bdd->prepare('UPDATE utilisateurs SET karma = karma + 50 WHERE id = ?;');
          $karma->execute(array($_GET['user']));
          header( "refresh:0;url=account.php?id=" . $_GET['user'] );
        } else if ($_GET['action'] == 'report'){

          $banhistory = $bdd->prepare('INSERT INTO sanctions(type, expiration, utilisateur, delateur, action) VALUES(:type, :expiration, :utilisateur, :delateur, :action);');
          $banhistory->execute(array(
            'type' => 3,
            'expiration' => $date,
            'utilisateur' => $_GET['user'],
            'delateur' => $_SESSION['id'],
            'action' => 0
          ));
          header( "refresh:0;url=account.php?vreport=true&id=" . $_GET['user'] );
        } else {
          header( "refresh:0;url=index.php?dperror=true" );
        }
      } else {
        header( "refresh:0;url=index.php?ierror=true" );
      }
    } else {
      header( "refresh:0;url=index.php?ierror=true" );
    }


}
else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
