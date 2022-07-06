<?php
require_once dirname(__FILE__).'/../config.php';


if (isset($_SESSION['id'])){
    if (isset($_GET['ajout']) OR isset($_GET['ajoutcandidat']) OR isset($_GET['affiche']) OR isset($_GET['delete'])) {

        echo '<!DOCTYPE html>
        <html lang="fr">

        <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; style-src https://* \'self\' \'unsafe-inline\' child-src \'none\';">

        <title>Intellivote - Espace Gouvernement - Election</title>

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

                echo '<h1 class="my-4">Espace Gouvernement - Election</h1>';

                $gouv_fetch = $bdd->prepare('SELECT * FROM governor WHERE individual = ? AND verified = 1;');
                $gouv_fetch->execute(array($_SESSION['id']));
                $gouv = $gouv_fetch->fetch();

                if (!$gouv) {
                  echo '<div class="alert alert-danger fade show" role="alert">
                    <strong>Vous n\'avez pas accès à l\'espace Gouvernement.</strong> Assurez-vous d\'avoir bien validé votre compte <a href="https://www.intellivote.fr/validation.php">en cliquant ici</a>. D\'ici là, par sécurité, vous devez utiliser l\'interface de gestion interne d\'Intellivote pour pouvoir administrer le service, la connexion à distance n\'est pas possible. Intellivote ne vous demandera jamais vos identifiants ni codes de vérifications, ne les communiquez jamais.
                  </div><br><br>';
                } else {
                    if (isset($_GET['ajout'])){

                        echo '
                        <h2><a>Ajouter une élection :</a></h2>
                        <form action="election.php" method="post">';

                        if (isset($_GET['beginerror'])) {
                            echo '
                            <div class="alert alert-danger fade show" role="alert">
                              <strong>Élection insuffisamment anticipée !<br></strong>Vous devez déclarer vos élections au minimum 90 jours à l\'avance. Assurez-vous également d\'avoir des dates de début et fin cohérentes entre elles.
                            </div>';
                        }

                        if (isset($_GET['enderror'])) {
                            echo '
                            <div class="alert alert-danger fade show" role="alert">
                              <strong>Élection trop courte !<br></strong>Vous devez imérativement laisser au minimum 8 heures s\'écouler entre le début et la fin de l\'élection.
                            </div>';
                        }

                        echo '

                            <div class="form-group">
                                <label for="description">Saisissez le nom de l\'élection</label>
                                <input type="text" name="description" class="form-control';

                                if (isset($_GET['descriptionerror'])){
                                    echo ' is-invalid';
                                }

                                echo ' "id="description" placeholder="Saisissez le nom de l\'élection" required> ';

                                if (isset($_GET['descriptionerror'])){
                                    echo '<div class="invalid-feedback">
                                    Nom de l\'élection incorrect ! Une élection à ce nom est déjà en cours.
                                    </div>';
                                }

                                echo ' <small id="NameHelp" class="form-text text-muted">
                                    Vous ne pouvez pas utiliser le nom d\'une élection déjà en cours.
                                </small>

                                <label for="dates">Choisissez les dates de l\'élection</label>
                                <div>
                                    <a>~ Date de début :</a>
                                    <input type="datetime-local" name="begindate" class="form-control';
                                    if (isset($_GET['beginerror'])){
                                        echo ' is-invalid';
                                    }
                                    echo '" id="begindate" placeholder="Saisissez la date de début." required>
                                    <a>~ Date de fin :</a>
                                    <input type="datetime-local" name="enddate" class="form-control';
                                    if (isset($_GET['enderror'])){
                                        echo ' is-invalid';
                                    }
                                    echo '" id="enddate" placeholder="Saisissez la date de fin." required>
                                </div>
                                <small id="DateHelp" class="form-text text-muted">
                                    Date de début qu\'à partir de demain, et date de fin qu\'à partir de la date de début.
                                </small>


                            </div>

                            <button type="submit" class="btn btn-primary">Créer l\'élection</button>

                        </form><br><br>';
                    } else if (isset($_GET['ajoutcandidat'])) {

                        if (isset($_GET['successcandidat'])) {
                            echo '
                            <div class="alert alert-success fade show" role="alert">
                              <strong>Le candidat a bien été ajouté.</strong>
                            </div>';
                        }

                        if (isset($_GET['candidaterror'])) {
                            echo '
                            <div class="alert alert-danger fade show" role="alert">
                              <strong>Le candidat a déjà été ajouté.<br>Si vous souhaitez modifier une information, vous devez d\'abord supprimer le candidat à cette élection.</strong>
                            </div>';
                        }

                        echo '
                        <h2><a>Ajouter un candidat</a></h2>';
                        echo '
                        <form action="election.php" method="post">
                            <div class="form-group">
                                <label for="election">Election</label><br>
                                <select class="form-control" id="election" name="election" required>
                                    <option disabled selected value> </option>';
                                        $election_fetch = $bdd->prepare('SELECT * FROM election WHERE begindate>?;');
                                        $election_fetch->execute(array(date("Y-m-d H:i", strtotime(" + 90 days"))));
                                        while ($elections = $election_fetch->fetch()) {
                                            echo '<option value="'.$elections['id'].'">'.$elections["description"].'</option>';
                                        }
                                    echo '
                                    </optgroup>
                                </select>

                                <label for="name">Nom</label>
                                <input type="text" name="name" class="form-control';
                                if (isset($_GET['nameerror'])){
                                    echo ' is-invalid';
                                }
                                echo ' " id="name" placeholder="Saisissez son nom." required>

                                <label for="surname">Prénom</label>
                                <input type="text" name="surname" class="form-control';
                                if (isset($_GET['surnameerror'])){
                                    echo ' is-invalid';
                                }
                                echo ' " id="surname" placeholder="Saisissez son prénom." required>

                                <label for="party">Parti</label>
                                <input type="text" name="party" class="form-control';
                                if (isset($_GET['partyerror'])){
                                    echo ' is-invalid';
                                }
                                echo ' " id="party" placeholder="Saisissez le nom de son parti." required>

                                <label for="programme">Programme</label>
                                <input type="text" name="programme" class="form-control';
                                if (isset($_GET['programmeerror'])){
                                    echo ' is-invalid';
                                }
                                echo ' " id="programme" placeholder="Saisissez la description de son programme." required>

                                <label for="idmairie">Saisissez l\'ID de la mairie</label>
                                <input type="text" name="idmairie" class="form-control" id="idmairie" placeholder="Saisissez l\'ID de la mairie." required>

                            </div>

                            <button type="submit" class="btn btn-primary">Ajouter le candidat</button>

                        </form><br><br>';

                    } else if (isset($_GET['delete'])) {
                      echo '
                        <h2><a>Vérification :</a></h2>
                        <form action="election.php" method="post">';

                        $req = $bdd->prepare('SELECT * FROM election WHERE id = ?;');
                        $req->execute(array($_GET['delete']));
                        $elec = $req->fetch();

                          echo '<div class="form-group">
                            <label for="individual">Confirmez-vous les données ?<br>
                            <div class="alert alert-info fade show" role="alert">
                            - <strong>ID de l\'élection :</strong> ' . $elec['id'] . ' | Nom : '. $elec['description'];
                            echo '<br> - <strong>Dates de l\'élection :</strong> Début : ' . date('d/m/Y à H:i', strtotime($elec['begindate'])) . ' | fin : ' . date('d/m/Y à H:i', strtotime($elec['enddate']));
                            $getcandidates = $bdd->prepare('SELECT * FROM candidate WHERE election = ? ');
                            $getcandidates->execute(array($elec['id']));
                            echo '<br> - <strong>Candidats :</strong><br> ';
                            $j = 0;
                            while ($candidates = $getcandidates->fetch()){ //case 1 or many candidates
                              echo '
                                - <i> ', $candidates['surname'],' ',$candidates['name'], '</i> (' . $candidates['party'] . ')<br>';
                              $j++;
                            };
                            if ($j==0) { //case no candidates
                              echo '
                              <p>Pas de candidats.</p>';
                            }
                            echo '</div>
                            </label>
                            <input type="hidden" name="delete" class="form-control';
                            echo '" id="delete" value="' . $_GET['delete'] . '" required>';

                            echo '<input type="hidden" name="cdelete" class="form-control';
                            echo '" id="cdelete" value="true" required>';

                          echo '</div>

                          <button type="submit" class="btn btn-danger">Confirmer la suppression de l\'élection</button>

                        </form><br>

                        <a href="election.php?affiche=true" class="btn btn-primary">Retour en arrière</a>
                        <br><br>';
                    } else {
                        echo '
                        <h2><a>Liste des élections</a></h2>';

                        if (isset($_GET['deleteerror'])) {
                            echo '
                            <div class="alert alert-danger fade show" role="alert">
                              <strong>Erreur lors de la suppression !<br></strong>Une élection ne peut pas être supprimée 7 jours avant et après celle-ci.
                            </div>';
                        }

                        if (isset($_GET['deletesuccess'])) {
                            echo '
                            <div class="alert alert-success fade show" role="alert">
                              <strong>Suppression réussie !<br></strong>Toutes les données relatives à l\'élection ont bien été supprimées.
                            </div>';
                        }

                        $date = date('Y-m-d H:i');

                        $electionavenir = $bdd->prepare('SELECT * FROM election WHERE begindate>? ORDER BY begindate DESC;');
                        $electionavenir->execute(array($date));
                        echo '<h3>Elections à venir ('.$electionavenir->rowCount().')</h3>';
                        while($row = $electionavenir->fetch()) {
                            echo '
                            <div class="alert alert-info fade show" role="alert">
                                <strong>L\'élection ' . $row['description'] . ' est à venir</strong><br>
                                <p>Dates : '.date('d/m/Y à H:i', strtotime($row['begindate'])).' - '.date('d/m/Y à H:i', strtotime($row['enddate'])).'</p>';

                                if (date('Y-m-d H:i', strtotime($row['begindate'] . ' - 7 days'))>date('Y-m-d H:i')) {
                                  echo '<a class = "btn btn-danger" href = "election.php?delete=' . $row['id'] . '">Annuler l\'élection</a>';
                                } else {
                                  echo '<strong>L\'annulation de cette élection n\'est plus possible car elle débutera dans moins de 7 jours.</strong>';
                                }

                                echo '
                            </div>';
                        }

                        $electionencours = $bdd->prepare('SELECT * FROM election WHERE begindate<=? AND enddate>? ORDER BY enddate DESC;');
                        $electionencours->execute(array($date, $date));
                        echo '<h3>Elections en cours ('.$electionencours->rowCount().')</h3>';
                        while($row = $electionencours->fetch()) {
                            echo '
                            <div class="alert alert-info fade show" role="alert">
                                <strong>L\'élection ' . $row['description'] . ' est en cours</strong><br>
                                <p>Dates : '.date('d/m/Y à H:i', strtotime($row['begindate'])).' - '.date('d/m/Y à H:i', strtotime($row['enddate'])).'</p>
                            </div>';
                        }

                        $electionpassees = $bdd->prepare('SELECT * FROM election WHERE enddate<=? ORDER BY enddate DESC;');
                        $electionpassees->execute(array($date));
                        echo '<h3>Elections terminées ('.$electionpassees->rowCount().')</h3>';
                        while($row = $electionpassees->fetch()) {
                            echo '<div class="alert alert-info fade show" role="alert">';
                            echo '<strong>Résultats de l\'élection ' . $row['description'] . '</strong><br>';
                              $getResult=$bdd->prepare('SELECT COUNT(candidate) AS score, candidate FROM votes WHERE election=? GROUP BY candidate;');
                              $getResult->execute(array($row['id']));

                              while ($result=$getResult->fetch()){
                                $getcandidates = $bdd->prepare('SELECT * FROM candidate WHERE id=?');
                                $getcandidates->execute(array($result["candidate"]));
                                $getCandidates = $getcandidates->fetch();
                                if (!empty($result["candidate"])) {
                                  echo '<p> Candidat ' . $getCandidates["name"] . ' ' . $getCandidates["surname"] . ' (' . $getCandidates["party"] . ') a obtenu ' . $result["score"] . ' voix</p>';
                                } else {
                                  echo '<p> Votes blancs: ' . $result["score"] . '</p>';
                                }

                              }
                              echo '<p>Dates : '.date('d/m/Y à H:i', strtotime($row['begindate'])).' - '.date('d/m/Y à H:i', strtotime($row['enddate'])).'</p>';
                              if (date('Y-m-d H:i', strtotime($row['enddate'] . ' + 7 days'))<=date('Y-m-d H:i')) {
                                echo '<a class = "btn btn-danger" href = "election.php?delete=' . $row['id'] . '">Supprimer l\'élection</a>';
                              } else {
                                echo '<strong>La suppression d\'une élection terminée sera possible 7 jours après sa fin.</strong>';
                              }

                            echo '</div>';
                        }


                    }


                    if (!isset($_GET['delete'])){
                      echo '
                      <a class = "btn btn-danger" href = "index.php">Annuler</a>
                      <br><br>';
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

    } else if (isset($_POST['name'])){

        // Pas d'inscription en double
        $req = $bdd->prepare('SELECT * FROM candidate WHERE party=? AND name=? AND surname=? AND election=?;');
        $req->execute(array($_POST['party'],$_POST['name'],$_POST['surname'],$_POST['election']));
        $test = $req->fetch();

        if ($test){
          header( "refresh:0;url=election.php?ajoutcandidat=true&candidaterror=true" );
        } else {

            $req=$bdd->prepare('INSERT INTO candidate (party, name, surname, programme, election, mairie) VALUES (:party, :name, :surname, :programme, :election, :mairie);');
            $req->execute(array(
                'party'=> $_POST['party'],
                'name'=> $_POST['name'],
                'surname'=> $_POST['surname'],
                'programme'=> $_POST['programme'],
                'election'=> $_POST['election'],
                'mairie'=> $_POST['idmairie']
            ));

            header( "refresh:0;url=election.php?ajoutcandidat=true&successcandidat=true" );
        }

    } else if (isset($_POST['cdelete'])) {
      $req = $bdd->prepare('SELECT * FROM election WHERE id = ?;');
      $req->execute(array($_POST['delete']));
      $test = $req->fetch();

      $date = date('Y-m-d H:i');

      if (($test["begindate"]>$date && date('Y-m-d H:i', strtotime($test['begindate'] . ' - 7 days'))>date('Y-m-d H:i')) || ($test["enddate"]<=$date && date('Y-m-d H:i', strtotime($row['enddate'] . ' + 7 days'))<=date('Y-m-d H:i'))) {
        $del1 = $bdd->prepare('DELETE FROM votes WHERE election = ?;');
        $del1->execute(array($_POST['delete']));

        $del2 = $bdd->prepare('DELETE FROM voted WHERE election = ?;');
        $del2->execute(array($_POST['delete']));

        $del3 = $bdd->prepare('DELETE FROM candidate WHERE election = ?;');
        $del3->execute(array($_POST['delete']));

        $del4 = $bdd->prepare('DELETE FROM election WHERE id = ?;');
        $del4->execute(array($_POST['delete']));

        header( "refresh:0;url=election.php?affiche=true&deletesuccess=true" );
      } else {
        header( "refresh:0;url=election.php?affiche=true&deleteerror=true" );
      }


    } else {

        // Pas de description/nom en double parmis ceux non finis
        $req = $bdd->prepare('SELECT * FROM election WHERE description = ?;');
        $req->execute(array($_POST['description']));
        $test = $req->fetch();

        if ($test){
          header( "refresh:0;url=election.php?ajout=true&descriptionerror=true" );
        }
        else if ($_POST['begindate']<date('Y-m-d H:i', strtotime(' + 90 days'))){ // Date de début qu'à partir de demain
          header( "refresh:0;url=election.php?ajout=true&beginerror=true" );
        }
        else if ($_POST['begindate']>=date('Y-m-d H:i', strtotime($_POST['enddate'].' + 8 hours'))){ // Date de fin qu'à partir de la date de début
            header( "refresh:0;url=election.php?ajout=true&enderror=true" );
        }
        else{

          $req=$bdd->prepare('INSERT INTO election (description, begindate, enddate, type) VALUES (:description, :begindate, :enddate, :type);');
          $req->execute(array(
            'description'=> $_POST['description'],
            'begindate'=> $_POST['begindate'],
            'enddate'=> $_POST['enddate'],
            'type'=> 1
          ));

          header( "refresh:0;url=index.php?successelection=true");

        }

    }

} else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
