<?php
require_once dirname(__FILE__).'/../config.php';


if (isset($_SESSION['id'])){
    if (isset($_GET['ajout']) OR isset($_GET['ajoutcandidat'])){

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
                        <form action="election.php" method="post">

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

                                <label for="party">Partie</label>
                                <input type="text" name="party" class="form-control';
                                if (isset($_GET['partyerror'])){
                                    echo ' is-invalid';
                                }
                                echo ' " id="party" placeholder="Saisissez le nom de son partie." required>

                                <label for="programme">Programme</label>
                                <input type="text" name="programme" class="form-control';
                                if (isset($_GET['programmeerror'])){
                                    echo ' is-invalid';
                                }
                                echo ' " id="programme" placeholder="Saisissez la description de son programme." required>

                                <label for="idmairie">Saisissez l\'ID de la mairie</label>
                                <input type="text" name="idmairie" class="form-control" id="idmairie" placeholder="Saisissez l\'ID de la mairie." required>

                                <label for="election">Election</label><br>
                                <select id="election" name="election" required>
                                    <optgroup label="Election">';
                                        $election_fetch = $bdd->prepare('SELECT * FROM election;');
                                        $election_fetch->execute();
                                        while ($elections = $election_fetch->fetch()) {
                                            echo '<option value="'.$elections['id'].'">'.$elections["description"].'</option>';
                                        }
                                    echo '
                                    </optgroup>
                                </select>

                            </div>

                            <button type="submit" class="btn btn-primary">Ajouter le candidat</button>

                        </form><br><br>';

                    } else {
                        echo '
                        <h2><a>Afficher une élection</a></h2>';

                        $date = date('Y-m-d H:i:s');

                        $req = $bdd->prepare('SELECT * FROM election WHERE begindate<'.$date.' AND enddate >'.$date = date('Y-m-d H:i:s').';');
                        $req->execute(array($_POST['electionencours']));
                        $electionencours = $req->fetch();

                        if ($electionencours) {
                            // Afficher les résultats de chaque ligne
                            while($row = $electionencours->fetch_assoc()) {
                              echo "id: " . $row["id"]. " - Nom: " . $row["begindate"]. " - prénom: " . $row["enddate"]. "<br>";
                            }
                          } else {
                            echo "0 results";
                          }

                        $req = $bdd->prepare('SELECT * FROM election WHERE enddate <'.$date.';');
                        $req->execute(array($_POST['electionpassees']));
                        $electionpassees = $req->fetch();

                        if ($electionpassees) {
                            // Afficher les résultats de chaque ligne
                            while($row = $electionpassees->fetch_assoc()) {
                              echo "id: " . $row["id"]. " - Nom: " . $row["begindate"]. " - prénom: " . $row["enddate"]. "<br>";
                            }
                          } else {
                            echo "0 results";
                          }

                        $req = $bdd->prepare('SELECT * FROM election WHERE begindate>'.$date.';');
                        $req->execute(array($_POST['electionavenir']));
                        $electionavenir = $req->fetch();

                        if ($_POST['electionavenir']) {
                            // Afficher les résultats de chaque ligne
                            while($row = $electionavenir->fetch_assoc()) {
                              echo "id: " . $row["id"]. " - Nom: " . $row["begindate"]. " - prénom: " . $row["enddate"]. "<br>";
                            }
                          } else {
                            echo "0 results";
                          }

                        echo '
                        <form action="election.php" method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" id="recherche" name="recherche" placeholder="Saisissez votre Recherche">
                            <select id="tri">
                                <optgroup label="Tri">
                                    <option value="begindate">Date de création+</option>
                                    <option value="enddate">Date de création-</option>
                                </optgroup>
                            </select>
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                            <button type="reset" class="btn btn-danger" onclick="location.href=\'election.php\'">Annuler</button>
                        </div>
                        </form><br><br>';

                    }



                    echo '
                    <a class = "btn btn-danger" href = "index.php">Annuler</a>
                    <br><br>';

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
        else if ($_POST['begindate']>$_POST['enddate']){ // Date de fin qu'à partir de la date de début
            header( "refresh:0;url=election.php?ajout=true&enderror=true" );
        }
        else{

          $req=$bdd->prepare('INSERT INTO election (description, begindate, enddate) VALUES (:description, :begindate, :enddate);');
          $req->execute(array(
            'description'=> $_POST['description'],
            'begindate'=> $_POST['begindate'],
            'enddate'=> $_POST['enddate']
          ));

          header( "refresh:0;url=index.php?successelection=true");

        }

    }

} else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
