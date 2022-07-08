<?php
require_once dirname(__FILE__).'/../config.php';


if (!isset($_SESSION['id'])) {
  header( "refresh:0;url=login.php?expired=true" );
} else if(!isset($_POST['token'])){


  echo '<!DOCTYPE html>
  <html lang="fr">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; style-src https://* \'self\' \'unsafe-inline\' child-src \'none\';">

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

        $req = $bdd->prepare('SELECT * FROM mairies WHERE id = ?;');
        $req->execute(array($_SESSION['idmairie']));
        $test = $req->fetch();
          if ($test) {
            echo '<h1 class="my-4">Espace Mairie de ' . $test['nom'] . '</h1>';
          } else {
            echo '<h1 class="my-4">Espace Mairie</h1>';
          }


          if ($_SESSION['verified'] != 1) {

            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Bonjour '.$_SESSION['surname'].' !</strong><br> Vous devez confirmer votre compte pour accéder au site. Celui-ci n\'a pas encore pu être vérifié.<br><a class = "btn btn-primary" href = "https://www.intellivote.fr/index.php">Lancer ou vérifier la procédure de validation</a>
            </div>';

          } else {

            $gatherdata = $bdd->prepare('SELECT * FROM mayor WHERE individual = ? AND mairie = ? AND verified = 1;');
            $gatherdata->execute(array($_SESSION['id'], $_SESSION['idmairie']));
            $data = $gatherdata->fetch();

            $electionEnCours = false;
            $date = date('Y-m-d H:i:s');
            $election_fetch = $bdd->prepare('SELECT * FROM election;');
            $election_fetch->execute();

            while ($election = $election_fetch->fetch()) {
              if (strtotime('+7 days')>strtotime($election['begindate']) && $date<$election['enddate']){//si la date du jour +7 est apres l'élection et si l'election n'est pas fini
                $electionEnCours = true;
              }
            }

            if ($data AND !$electionEnCours) {

              if (isset($_GET['success'])) {
                echo '
                <div class="alert alert-success fade show" role="alert">
                  <strong>L\'électeur a bien été rajouté dans votre mairie.</strong>
                </div>';
              }

                echo '
                <h2><a>Inscrire un électeur :</a></h2>
                <form action="index.php" method="post">

                  <div class="form-group">
                    <label for="token">Saisissez le code à usage unique</label>
                    <input type="text" name="token" class="form-control';

                    if (isset($_GET['tokenerror'])){
                      echo ' is-invalid';
                    }

                    echo '" id="token" placeholder="Token de confirmation" required>';

                    if (isset($_GET['tokenerror'])){
                      echo '<div class="invalid-feedback">
                        Token incorrect ! Besoin d\'aide ? Contactez l\'électeur afin de vérifier que le token soit correct.
                      </div>';
                    }

                    echo ' <small id="emailHelp" class="form-text text-muted">
                      Le token de confirmation doit vous être communiqué par le demandeur suite à sa pré-demande sur son espace Intellivote. En cas de difficultés, pensez à lui proposer une assistance dans votre mairie.
                    </small>
                    <br>
                    <label for="number">Saisissez le numéro d\'électeur</label>
                    <input type="text" name="number" class="form-control';

                    echo '" id="number" placeholder="Saisissez le numéro électoral." required>

                    <small id="emailHelp" class="form-text text-muted">
                      Vérifiez bien la correspondance du numéro d\'électeur sur votre liste électorale
                    </small>
                  </div>

                  <button type="submit" class="btn btn-primary">Inscrire l\'électeur à '. $test['nom'] . '</button>

                </form><br><br>';



            } else if (!$data) {

              echo '
              <div class="alert alert-warning fade show" role="alert">
                <strong>Bonjour '.$_SESSION['surname'].' !</strong><br> Notre système ne vous a pas détecté en tant que responsable au sein de la mairie de ' . $test['nom'] . '. Votre demande de certification devra être traitée par <a href="https://gouv.intellivote.fr">un représentant de l\'État</a>. Cette procédure ne peut pas être automatisée pour des raisons de sécurité.<br>
                  Veuillez communiquer à un représentant du gouvernement les informations suivantes, accompagnés de tous les justificatifs nécéssaires :<br><br><strong>Identifiant Unique :</strong> ' . $_SESSION['id'] . '<br><strong>Identifiant Mairie :</strong> ' . $_SESSION['idmairie'] . '<br><br><a class = "btn btn-primary" href = "https://www.intellivote.fr/">Retour à l\'espace électeur</a>
              </div>';

            } else if ($electionEnCours) {

              echo '
              <div class="alert alert-warning fade show" role="alert">
                <strong>Bonjour '.$_SESSION['surname'].' !</strong><br><a>Une élection aura lieu dans moins de 7 jours, vous ne pouvez pas inscrire un électeur.</a>
              </div>';

            }

            if ($data) {
                            //get actual time in paris
                            echo '
                            <h2><a>Liste des élections</a></h2>';

                            $date = date('Y-m-d H:i');

                            $electionavenir = $bdd->prepare('SELECT * FROM election WHERE begindate>? ORDER BY begindate DESC;');
                            $electionavenir->execute(array($date));
                            echo '<h3>Elections à venir ('.$electionavenir->rowCount().')</h3>';
                            while($row = $electionavenir->fetch()) {
                                echo '
                                <div class="alert alert-info fade show" role="alert">
                                    <strong>L\'élection ' . $row['description'] . ' est à venir</strong><br>
                                    <p>Dates : '.date('d/m/Y à H:i', strtotime($row['begindate'])).' - '.date('d/m/Y à H:i', strtotime($row['enddate'])).'</p>';

                                    //display all candidates
                                    $getcandidates = $bdd->prepare('SELECT * FROM election JOIN candidate ON candidate.election= ? GROUP BY candidate.surname , candidate.name ');
                                    $getcandidates->execute(array($row['id']));

                                    $j = 0;
                                    while ($candidates = $getcandidates->fetch()){ //case 1 or many candidates
                                      echo '
                                      <div class="alert alert-info fade show" role="alert">
                                        <strong> '.$candidates['surname'].' '.$candidates['name'].' : ';

                                        if (empty($candidates['programme'])) {
                                          echo 'Pas de programme.';
                                        } else {
                                          echo '<a href="'.$candidates['programme'].'" target="_blank" rel="noopener noreferrer"> Voir le programme.</a>';
                                        }

                                        echo ' </strong><br>
                                        <p> Parti : '.$candidates['party'].'</p>
                                        </div>';
                                      $j++;
                                    };
                                    if ($j==0) { //case no candidates
                                      echo '
                                      <div>
                                      <p>Pas de candidats.</p>
                                      </div>';
                                    }

                                    echo '
                                </div>';
                            }

                            $electionencours = $bdd->prepare('SELECT * FROM election WHERE begindate<=? AND enddate>? ORDER BY enddate DESC;');
                            $electionencours->execute(array($date, $date));
                            echo '<h3>Elections en cours ('.$electionencours->rowCount().')</h3>';
                            $i = 0;
                            while($row = $electionencours->fetch()) {
                              $i++;
                                echo '
                                <div class="alert alert-info fade show" role="alert">
                                    <strong>L\'élection ' . $row['description'] . ' est en cours</strong><br>
                                    <p>Dates : '.date('d/m/Y à H:i', strtotime($row['begindate'])).' - '.date('d/m/Y à H:i', strtotime($row['enddate'])).'</p>';

                                    //display all candidates
                                    $getcandidates = $bdd->prepare('SELECT * FROM election JOIN candidate ON candidate.election= ? GROUP BY candidate.surname , candidate.name ');
                                    $getcandidates->execute(array($row['id']));

                                    $j = 0;
                                    while ($candidates = $getcandidates->fetch()){ //case 1 or many candidates
                                      echo '
                                      <div class="alert alert-info fade show" role="alert">
                                        <strong> '.$candidates['surname'].' '.$candidates['name'].' : ';

                                        if (empty($candidates['programme'])) {
                                          echo 'Pas de programme.';
                                        } else {
                                          echo '<a href="'.$candidates['programme'].'" target="_blank" rel="noopener noreferrer"> Voir le programme.</a>';
                                        }

                                        echo ' </strong><br>
                                        <p> Parti : '.$candidates['party'].'</p>
                                        </div>';
                                      $j++;
                                    };
                                    if ($j==0) { //case no candidates
                                      echo '
                                      <div>
                                      <p>Pas de candidats.</p>
                                      </div>';
                                    }

                                    echo '
                                </div>';
                            }
                            if ($i==0) { //case no ongoing election
                              echo '
                              <strong>Pas d\'élections en cours.</strong></div><br><br>';
                            }

                            $electionpassees = $bdd->prepare('SELECT * FROM election WHERE enddate<=? ORDER BY enddate DESC;');
                            $electionpassees->execute(array($date));
                            echo '<h3>Elections terminées ('.$electionpassees->rowCount().')</h3>';
                            while($row = $electionpassees->fetch()) {
                                echo '<div class="alert alert-info fade show" role="alert">';
                                echo '<strong>Résultats de l\'élection ' . $row['description'] . '</strong><br>';
                                  $getResult=$bdd->prepare('SELECT COUNT(token) AS score, candidate FROM votes WHERE mairie=? AND election=? GROUP BY candidate;');
                                  $getResult->execute(array($_SESSION['idmairie'],$row['id']));

                                  while ($result=$getResult->fetch()){
                                    $getcandidates = $bdd->prepare('SELECT * FROM candidate WHERE id=?');
                                    $getcandidates->execute(array($result["candidate"]));
                                    $getCandidates = $getcandidates->fetch();
                                    if (!empty($result["candidate"])) {
                                      echo '<p> Candidat ' . $getCandidates["surname"] . ' ' . $getCandidates["name"] . ' (' . $getCandidates["party"] . ') a obtenu ' . $result["score"] . ' voix</p>';
                                    } else {
                                      echo '<p> Votes blancs: ' . $result["score"] . '</p>';
                                    }

                                  }
                                  echo '<p>Dates : '.date('d/m/Y à H:i', strtotime($row['begindate'])).' - '.date('d/m/Y à H:i', strtotime($row['enddate'])).'</p>';

                                  //display all candidates
                                  $getcandidates = $bdd->prepare('SELECT * FROM election JOIN candidate ON candidate.election= ? GROUP BY candidate.surname , candidate.name ');
                                  $getcandidates->execute(array($row['id']));

                                  $j = 0;
                                  while ($candidates = $getcandidates->fetch()){ //case 1 or many candidates
                                    echo '
                                    <div class="alert alert-info fade show" role="alert">
                                      <strong> '.$candidates['surname'].' '.$candidates['name'].' : ';

                                      if (empty($candidates['programme'])) {
                                        echo 'Pas de programme.';
                                      } else {
                                        echo '<a href="'.$candidates['programme'].'" target="_blank" rel="noopener noreferrer"> Voir le programme.</a>';
                                      }

                                      echo ' </strong><br>
                                      <p> Parti : '.$candidates['party'].'</p>
                                      </div>';
                                    $j++;
                                  };
                                  if ($j==0) { //case no candidates
                                    echo '
                                    <div>
                                    <p>Pas de candidats.</p>
                                    </div>';
                                  }

                                  echo '
                                  </div>';
          }

          echo '
          <div class="alert alert-success fade show" role="alert">

            <strong>Liste des électeurs inscrits sur la e-liste électorale de la mairie de ' . $test['nom'] . '</strong><br>';
            $getelecteurs = $bdd->prepare('SELECT *, elector.verified AS verif FROM elector JOIN individual ON elector.individual = individual.id WHERE mairie=? ORDER BY name;');
            $getelecteurs->execute(array($_SESSION['idmairie']));
            while ($electeur=$getelecteurs->fetch()){
              if ($electeur['verif'] == 1) {
                echo $electeur['name'] . " " . $electeur['surname'] . " - " . $electeur['number'] . "<br>";
              } else {
                echo $electeur['name'] . " " . $electeur['surname'] . " <strong>(SUSPENDU)</strong>". " - " . $electeur['number'] . "<br>";
              }

            }
            echo '
            </div>';
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
    <footer class="py-5" style="background-color:#ebecec;">
      <div class="container">
        <p class="m-0 text-center text-black">&copy; 2022 Intellivote. Tous droits reservés. <a href="https://www.intellivote.fr/legal.php" style="color:darkslategray;">Mentions légales</a>.</p>
      </div>
      <!-- /.container -->
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  </body>

  </html>';

}else{

  $gatherdata = $bdd->prepare('SELECT * FROM mayor WHERE individual = ? AND mairie = ? AND verified = 1;');
  $gatherdata->execute(array($_SESSION['id'], $_SESSION['idmairie']));
  $data = $gatherdata->fetch();

  if ($data) {
    $req = $bdd->prepare('SELECT * FROM validations WHERE token = ? AND validated = 0 AND type = 1;');
    $req->execute(array($_POST['token']));
    $test = $req->fetch();

    if (!$test){
      header( "refresh:0;url=index.php?tokenerror=true" );
    } else {

      $updatecheck = $bdd->prepare('SELECT * FROM elector WHERE individual = ?');
      $updatecheck->execute(array($test['individual']));
      $update = $updatecheck->fetch();

      if (empty($update['id'])) {
        $req=$bdd->prepare('INSERT INTO elector(number, individual, mairie, verified, verifiedon) VALUES(:number, :individual, :mairie, :verified, :verifiedon)');
        $date = date('Y-m-d H:i:s');
        $req->execute(array(
          'number'=> $_POST['number'],
          'individual'=> $test['individual'],
          'mairie'=> $_SESSION['idmairie'],
          'verified'=> 1,
          'verifiedon' => $date
        ));
      } else {
        $req=$bdd->prepare('UPDATE elector SET number = :number, mairie = :mairie, verified = :verified, verifiedon = :verifiedon WHERE individual = :individual');
        $date = date('Y-m-d H:i:s');
        $req->execute(array(
          'number'=> $_POST['number'],
          'individual'=> $test['individual'],
          'mairie'=> $_SESSION['idmairie'],
          'verified'=> 1,
          'verifiedon' => $date
        ));
      }


      $validation = $bdd->prepare('UPDATE validations SET validated = 1 WHERE id = ?;');
      $validation->execute(array($test['id']));

      header( "refresh:0;url=index.php?success=true" );
    }
  } else {
    header( "refresh:0;url=index.php" );
  }

}
?>
