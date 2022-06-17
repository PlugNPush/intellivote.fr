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

if (isset($_GET['id'])){
  $compte = $_GET['id'];
} else if (isset($_SESSION['id'])){
  $compte = $_SESSION['id'];
} else {
  $compte = 0;
}

if (!isset($_GET['edit']) && !isset($_GET['pdelete'])) {

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
              <a class="nav-link" href="index.php">Répondre à des questions</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="newquestion.php">Poser une question</a>
            </li>';

            if (isset($_SESSION['id']) && $_SESSION['id'] == $compte){
              echo '
              <li class="nav-item active">
                <a class="nav-link" href="account.php">Mon compte
                <span class="sr-only">(current)</span></a>
              </li>';
            } else {
              echo '
              <li class="nav-item">
                <a class="nav-link" href="account.php">Mon compte</a>
              </li>';
            }


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

        if (!isset($_SESSION['id'])){
          header( "refresh:0;url=login.php?expired=true" );
          echo 'Votre session a expiré.';
        } else {
          // Informations standard + modification
          $gather = $bdd->prepare('SELECT * FROM utilisateurs WHERE id = ?;');
          $gather->execute(array($compte));
          $data = $gather->fetch();

          echo '<h1 class="my-4">Espace utilisateur : ', $data['pseudo'] ,'</h1>';
          if (!isset($_SESSION['validation']) || $_SESSION['validation'] != 1){
            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Votre statut d\'Efreien n\'a pa encore été vérifié.</strong>. Si besoin, contactez un modérateur avec votre adresse mail Efrei.<br><a class = "btn btn-primary" href = "validation.php">Lancer ou vérifier la procédure de validation</a>
            </div><br><br>';
          }

          if (isset($_GET['deleted'])) {
            echo '
            <div class="alert alert-success fade show" role="alert">
              <strong>Le compte d\'utilisateur compte a bien été supprimé</strong>. Si vous n\'êtes pas satisfait du service, n\'hésitez pas à faire remonter vos tracas auprès d\'un modérateur.
            </div>';
          }

          if (!$data){
            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Une erreur s\'est produite</strong>. Il semblerait que le compte d\'utilisateur que vous cherchiez n\'existe plus. Essayez de <a href="login.php">vous reconnecter ici</a>.
            </div>';
          } else {

            $date = date('Y-m-d H:i:s');
            if (isset($_SESSION['ban']) && $_SESSION['ban'] >= $date){
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Vous avez été banni</strong>. Si besoin, contactez un modérateur avec votre adresse mail Efrei. Votre compte sera à nouveau utilisable à partir du ', $_SESSION['ban'] ,'.<br><a class = "btn btn-secondary btn-lg btn-block" href = "logout.php">Se déconnecter</a>
              </div><br>';
            } else if (isset($data['ban']) && $data['ban'] >= $date) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Ce compte a été banni</strong>. Ce compte sera à nouveau utilisable à partir du ', $data['ban'] ,'.<br>';
                if ($_SESSION['role'] >= 1) {
                  echo '<a class = "btn btn-warning btn-lg btn-block" href = "irondome.php?type=u&action=unban&id=', $data['id'] ,'&user=', $data['id'] ,'">Débannir le compte</a>';
                }
                echo '
              </div><br>';
            } else if ($_SESSION['role'] >= 1 && $_SESSION['id'] != $data['id'] && $data['ban'] < $date) {
              echo '
              <div class="alert alert-success fade show" role="alert">
                <strong>Ce compte est en règle !</strong> Nous vous affichons ce message car vous êtes un modérateur d\'Efrei Dynamo. Ce compte n\'a actuellement aucune restriction. Si ce compte s\'est mal comporté, vous pouvez le bannir pour une durée d\'un mois.<br><a class = "btn btn-danger btn-lg btn-block" href = "irondome.php?type=u&action=ban&id=', $data['id'] ,'&user=', $data['id'] ,'">Bannir ce compte</a>
              </div><br>';
            }

            if ($_SESSION['role'] >= 50) {
              echo '
              <div class="alert alert-warning fade show" role="alert">
                <strong>Vous êtes un administrateur</strong>. Vous pouvez modifier l\'ensemble des réglages à votre guise, mais soyez résponsable et ne modifiez que le strict nécéssaire. Vous pourriez bloquer le site si vous modifiez les mauvais paramètres.
              </div>';
            } else if ($_SESSION['role'] >= 10) {
              echo '
              <div class="alert alert-warning fade show" role="alert">
                <strong>Vous êtes un ultra-modérateur</strong>. Vous pouvez modifier la quasi-totalité des réglages à votre guise, mais soyez résponsable et ne modifiez que le strict nécéssaire.
              </div>';
            } else if ($_SESSION['role'] >= 3) {
              echo '
              <div class="alert alert-warning fade show" role="alert">
                <strong>Vous êtes un super-modérateur</strong>. Vous pouvez modifier une grande partie des réglages à votre guise, mais soyez résponsable et ne modifiez que le strict nécéssaire.
              </div>';
            }

            if (isset($_GET['authfailure'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Echec de l\'authentification !</strong> Le mot de passe n\'a pas pu être changé car le mot de passe d\'origine ne correspond pas.
              </div>';
            }

            if (isset($_GET['passfailure'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Impossible d\'actualiser le mot de passe !</strong> Le nouveau mot de passe saisi ne correspond pas avec la confirmation saisie.
              </div>';
            }

            if (isset($_GET['everythingworked'])) {
              echo '
              <div class="alert alert-success fade show" role="alert">
                <strong>Le compte a été mis à jour !</strong> Les informations nécéssaires ont été enregistrées dans la base de données.
              </div>';
            }

            if (isset($_GET['vreport'])) {
              echo '
              <div class="alert alert-success fade show" role="alert">
                <strong>Votre signalement a bien été enregistré !</strong> Notre équipe de modérateurs se chargera de vérifier votre signalement dans les prochains jours.
              </div>';
            }

            if (isset($_GET['derror'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Une erreur s\'est produite</strong>. Il semblerait que vous ayez oublié de cocher une case de confirmation pour effectuer cette opération.
              </div>';
            }

            if (isset($_GET['dperror'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Une erreur s\'est produite</strong>. Vous ne disposez pas des autorisations nécéssaires pour réaliser cette opération.
              </div>';
            }

            if (isset($_GET['ierror'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Une erreur interne inattendue s\'est produite</strong>. Un paramètre attendu n\'est pas parvenu à sa destination. Veuillez réesayer puis contacter un modérateur si l\'erreur se reproduit.
              </div>';
            }

            if (isset($_GET['emailexists'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Echec de la validation du mail.</strong> Un compte avec cette adresse mail existe déjà.
              </div>';
            }

            if (isset($_GET['pseudoexists'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Echec de la validation du pseudo.</strong> Un compte avec ce pseudo existe déjà.
              </div>';
            }

            if (isset($_GET['invalidrole'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Impossible d\'enregistrer le rôle.</strong> Vous ne pouvez pas définir un rôle supérieur au vôtre !
              </div>';
            }

            echo '
            <a href="mailto:', $data['email'] ,'"><button class="btn btn-primary">Contacter ', $data['pseudo'] ,' par mail</button></a><br><br>
            <h2>Informations sur le compte</h2>
            <form action="account.php?edit=true&id=', $compte ,'" method="post">
            <div class="form-group">
              <label for="id">Identifiant interne</label>
              <input type="number" name="id" class="form-control" min="1" id="id" value="', $data['id'] ,'" ', ($_SESSION['role']>=50) ? ('') : ('disabled'), '>
              <small id="emailHelp" class="form-text text-muted">
                L\'identifiant interne est immuable et vaut ', $data['id'] ,'
              </small>
            </div>
            <div class="form-group">
              <label for="inscription">Date d\'inscription</label>
              <input type="text" name="inscription" class="form-control" id="inscription" value="', $data['inscription'] ,'" ', ($_SESSION['role']>=50) ? ('') : ('disabled'), '>
              <small id="emailHelp" class="form-text text-muted">
                La date d\'inscription est immuable et vaut ', $data['inscription'] ,'
              </small>
            </div>
            <div class="form-group">
              <label for="role">Rôle</label>
              <input type="number" name="role" class="form-control';
              if (isset($_GET['invalidrole'])){
                echo ' is-invalid';
              }
              echo '" id="role" min="0" max="', $_SESSION['role'] ,'" value="', $data['role'] ,'" ', ($_SESSION['role']>=10) ? ('') : ('disabled'), '>';
              if (isset($_GET['invalidrole'])){
                echo '<div class="invalid-feedback">
                  Echec de la validation du rôle. Vous ne pouvez pas définir un rôle supérieur au vôtre !
                </div>';
              }
              echo '
              <small id="emailHelp" class="form-text text-muted">
                ', ($_SESSION['role']>=10) ? ('En tant qu\'ultra-modérateur, vous pouvez modifier le rôle. ') : (''), 'Le rôle actuel est ', $data['role'] ,'
              </small>
            </div>
            <div class="form-group">
              <label for="karma">Karma</label>
              <input type="number" name="karma" class="form-control" id="karma" value="', $data['karma'] ,'" ', ($_SESSION['role']>=3) ? ('') : ('disabled'), '>
              <small id="emailHelp" class="form-text text-muted">
                ', ($_SESSION['role']>=3) ? ('En tant que super-modérateur, vous pouvez modifier le solde Karma. ') : (''), 'Le solde de Karma est ', $data['karma'] ,'
              </small>
            </div>
            <div class="form-group">
              <label for="validation">Validation Efrei</label>
              <input type="number" name="validation" min="0" max="1" class="form-control" id="validation" value="', $data['validation'] ,'" ', ($_SESSION['role']>=3) ? ('') : ('disabled'), '>
              <small id="emailHelp" class="form-text text-muted">
                ', ($_SESSION['role']>=3) ? ('En tant que super-modérateur, vous pouvez modifier le statut de validation Efrei. ') : ('En cas de problème, contactez un modérateur. '), 'Le <a href="validation.php">statut de vérification Efrei</a> est actuellement à ', $data['validation'] ,'
              </small>
            </div>
            <div class="form-group">
              <label for="validation">Statut de ban (date de fin)</label>
              <input type="text" name="ban" class="form-control" id="ban" placeholder="Aucun ban en cours" value="', $data['ban'] ,'" ', ($_SESSION['role']>=50) ? ('') : ('disabled'), '>
              <small id="emailHelp" class="form-text text-muted">
                ', ($_SESSION['role']>=50) ? ('En tant qu\'administrateur, vous pouvez modifier le statut de ban. ') : ('En cas de problème, contactez un modérateur. ');

                $date = date('Y-m-d H:i:s');
                if ($_SESSION['role'] >= 1 && ($_SESSION['ban'] < $date || $_SESSION['ban'] == NULL) && ($data['ban'] >= $date && $data['ban'] != NULL)){
                  echo '<a href="irondome.php?type=u&action=unban&id=', $data['id'] ,'&user=', $data['id'] ,'">Débannir le compte</a>.';
                } else if ($_SESSION['role'] >= 1 && ($_SESSION['ban'] < $date || $_SESSION['ban'] == NULL) && ($data['ban'] < $date || $data['ban'] == NULL)) {
                  echo '<a href="irondome.php?type=u&action=ban&id=', $data['id'] ,'&user=', $data['id'] ,'">Bannir le compte</a>.';
                } else if (($_SESSION['ban'] < $date || $_SESSION['ban'] == NULL) && ($data['ban'] < $date || $data['ban'] == NULL)){
                  echo '<a href="irondome.php?type=u&action=report&id=', $data['id'] ,'&user=', $data['id'] ,'">Signaler le compte</a>.';
                }

                echo '
              </small>
            </div>

            <div class="form-group">
              <label for="titre">Pseudonyme</label>
              <input type="text" name="pseudo" class="form-control';
              if (isset($_GET['pseudoexists'])){
                echo ' is-invalid';
              }
              echo '" id="pseudo"  value="', $data['pseudo'] ,'" ', ($_SESSION['role']>=10 || $compte == $_SESSION['id']) ? ('') : ('disabled'), '>';
              if (isset($_GET['pseudoexists'])){
                echo '<div class="invalid-feedback">
                  Echec de la validation du pseudonyme. Un compte existe déjà avec ce pseudo.
                </div>';
              }
              echo '
              <small id="emailHelp" class="form-text text-muted">
                Le pseudo est actuellement ', $data['pseudo'] ,'
              </small>
            </div>
              <div class="form-group">
                <label for="titre">Adresse email</label>
                <input type="text" name="email" class="form-control';
                if (isset($_GET['emailexists'])){
                  echo ' is-invalid';
                }
                echo '" id="email"  value="', $data['email'] ,'" ', ($_SESSION['role']>=10 || $compte == $_SESSION['id']) ? ('') : ('disabled'), '>';
                if (isset($_GET['emailexists'])){
                  echo '<div class="invalid-feedback">
                    Echec de la validation du mail. Un compte existe déjà avec cette adresse.
                  </div>';
                }
                echo '
                <small id="emailHelp" class="form-text text-muted">
                  L\'adresse éléctronique est actuellement ', $data['email'] ,'
                </small>
              </div>

              <div class="form-group">
                <label for="annee">Niveau d\'études</label>
                <select name="annee" class="form-control" id="annee" ', ($_SESSION['role']>=3 || $compte == $_SESSION['id']) ? ('') : ('disabled'), '>
                  <option value="1" ', ($data['annee'] == 1) ? ('selected') : ('') ,'>Cycle préparatoire - L1</option>
                  <option value="2" ', ($data['annee'] == 2) ? ('selected') : ('') ,'>Cycle préparatoire - L2</option>
                  <option value="3" ', ($data['annee'] == 3) ? ('selected') : ('') ,'>Cycle ingénieur - L3</option>
                  <option value="4" ', ($data['annee'] == 4) ? ('selected') : ('') ,'>Cycle ingénieur - M1</option>
                  <option value="5" ', ($data['annee'] == 5) ? ('selected') : ('') ,'>Cycle ingénieur - M2</option>
                  <option value="6" ', ($data['annee'] == 6) ? ('selected') : ('') ,'>Ancien élève diplomé</option>
                  <option value="7" ', ($data['annee'] == 7) ? ('selected') : ('') ,'>Intervenant (tous niveaux)</option>
                </select>
                <small id="emailHelp" class="form-text text-muted">
                  Le niveau d\'études est actuellement ', $data['annee'] ,'
                </small>
              </div>

              <div class="form-group">
                <label for="majeure">Choisissez votre majeure</label>
                <select name="majeure" class="form-control" id="majeure" ', ($_SESSION['role']>=3 || $compte == $_SESSION['id']) ? ('') : ('disabled'), '>';

                $majeure_fetch = $bdd->prepare('SELECT * FROM majeures;');
                $majeure_fetch->execute();

                while ($majeure = $majeure_fetch->fetch()) {
                  echo '<option value="', $majeure['id'] ,'" ', ($data['majeure'] == $majeure['id']) ? ('selected') : ('') ,'>', $majeure['nom'] ,'</option>';
                }

                echo '
                </select>
                <small id="emailHelp" class="form-text text-muted">
                  La majeure actuelle est ', $data['majeure'] ,'
                </small>
              </div>

              <div class="form-group">
                <label for="photo">Photo de profil</label>
                <input type="text" name="photo" class="form-control" id="photo" placeholder="URL de la photo de profil (facultatif)" value="', $data['photo'] ,'" ', ($_SESSION['role']>=10 || $compte == $_SESSION['id']) ? ('') : ('disabled'), '>
                <small id="emailHelp" class="form-text text-muted">
                  L\'URL de la photo de profil actuelle est ', $data['photo'] ,'
                </small>
              </div>';

              if ($_SESSION['role']>=10 || $compte == $_SESSION['id']) {
                echo '<div class="form-group">
                  <input type="checkbox" name="dphoto" class="form-check-input" id="dphoto">
                  <label class="form-check-label" for="dphoto">Supprimer la photo actuelle</label>
                </div>';
              }

              echo '
              <div class="form-group">
                <label for="titre">Profil LinkedIn</label>
                <input type="text" name="linkedin" class="form-control" id="linkedin" placeholder="URL du profil LinkedIn (facultatif)" value="', $data['linkedin'] ,'" ', ($_SESSION['role']>=10 || $compte == $_SESSION['id']) ? ('') : ('disabled'), '>
                <small id="emailHelp" class="form-text text-muted">
                  L\'adresse du profil LinkedIn actuelle est ', $data['linkedin'] ,'
                </small>
              </div>';

              if ($_SESSION['role']>=10 || $compte == $_SESSION['id']) {
                echo '<div class="form-group">
                  <input type="checkbox" name="dlinkedin" class="form-check-input" id="dlinkedin">
                  <label class="form-check-label" for="dlinkedin">Supprimer le profil LinkedIn actuel</label>
                </div>';
              }

              if ($_SESSION['role']>=50 || $compte == $_SESSION['id']){
                echo '<h3>Modification du mot de passe</h3>';
                if ($compte == $_SESSION['id'] && $_SESSION['role']<50) {
                  echo '
                  <div class="form-group">
                    <label for="titre">Mot de passe actuel</label>
                    <input type="password" name="cmdp" class="form-control';
                    if (isset($_GET['authfailure'])){
                      echo ' is-invalid';
                    }
                    echo '" id="cmdp" placeholder="Votre mot de passe actuel">';
                    if (isset($_GET['authfailure'])){
                      echo '<div class="invalid-feedback">
                        Le mot de passe actuel que vous avez saisi est incorrect ! Besoin d\'aide ? Contactez un administrateur.
                      </div>';
                    }
                    echo '</div>';
                  }

                  echo '
                <div class="form-group">
                  <label for="titre">Nouveau mot de passe</label>
                  <input type="password" name="nmdp" class="form-control';
                  if (isset($_GET['passfailure'])){
                    echo ' is-invalid';
                  }
                  echo '" id="mdp" placeholder="Prenez un mot de passe sûr">';
                  if (isset($_GET['passfailure'])){
                    echo '<div class="invalid-feedback">
                      Votre nouveau mot de passe ne correspond pas à la confirmation.
                    </div>';
                  }
                  echo '
                </div>
                <div class="form-group">
                  <label for="titre">Confirmez le mot de passe</label>
                  <input type="password" name="vmdp" class="form-control';
                  if (isset($_GET['passfailure'])){
                    echo ' is-invalid';
                  }
                  echo '" id="vmdp" placeholder="Confirmation">';
                  if (isset($_GET['passfailure'])){
                    echo '<div class="invalid-feedback">
                      La confirmation ne correspond pas à votre nouveau mot de passe.
                    </div>';
                  }
                  echo '
                </div>';
              }

              if ($_SESSION['role']>=3 || $compte == $_SESSION['id']) {
                echo '
                <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                <button type="reset" class="btn btn-secondary">Annuler les modifications</button>';
              }

              echo '
              </form><br><br>';

          }

        }

        echo '</div>';

        // Boutons GDPR
        if ($_SESSION['role']>=50 || $compte == $_SESSION['id']){
          echo '<!-- Sidebar Widgets Column -->
          <div class="col-md-4"><br>
          <h2>Espace RGPD</h2>
          <!-- Side Widget -->
          <div class="card my-4">
            <h5 class="card-header">Téléchargez une copie des données</h5>
            <div class="card-body">
              Notre service de portabilité des données n\'est pas encore disponible, mais le sera bientôt. En attendant, vous pouvez <a href="mailto:plugn@groupe-minaste.org">nous contacter ici</a> pour obtenir une copie des données.<br><br>
              <button class="btn btn-primary" disabled>Demander une copie des données</button>
            </div>
          </div>

          <!-- Search Widget -->
          <div class="card my-4">
            <h5 class="card-header">Supprimer le compte</h5>
            <div class="card-body">
              <div class="input-group">
                <form action="account.php?pdelete=true&id=', $compte,'" method="post">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="confirmersup" name="confirmersup" class="form-control" required>
                  <label class="form-check-label" for="confirmersup">Je confirme vouloir supprimer ce compte à vie</label>
                </div><br>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="supcontenu" name="supcontenu" class="form-control">
                  <label class="form-check-label" for="supcontenu">Facultatif : je souhaite également supprimer le contenu produit par ce compte (questions, réponses...)</label>
                </div><br>
                  <button class="btn btn-danger" type="submit">Supprimer ce compte</button>
                </form>
              </div>
            </div>
          </div>
          </div>';
        }

        echo '

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

  if (isset($_SESSION['id'])) {

    if (isset($_GET['pdelete']) && $_GET['pdelete'] == 'true' && isset($_GET['id'])) {
      if ($_SESSION['role']>=50 || $_GET['id'] == $_SESSION['id']){
        // Suppression du compte
        if (isset($_POST['confirmersup']) && $_POST['confirmersup'] == 'on'){
          if (isset($_POST['supcontenu']) && $_POST['supcontenu'] == 'on') {
            // Suppression du contenu
            $sup_questions = $bdd->prepare('DELETE FROM questions WHERE auteur = ?;');
            $sup_questions->execute(array($_GET['id']));

            $sup_reponses = $bdd->prepare('DELETE FROM reponses WHERE auteur = ?;');
            $sup_reponses->execute(array($_GET['id']));

            $sup_sanctions = $bdd->prepare('DELETE FROM sanctions WHERE utilisateur = ? OR delateur = ?;');
            $sup_sanctions->execute(array($_GET['id'], $_GET['id']));
          }
          // Suppression du compte
          $sup_validation = $bdd->prepare('DELETE FROM validations WHERE user = ?;');
          $sup_validation->execute(array($_GET['id']));

          $sup_utilisateur = $bdd->prepare('DELETE FROM utilisateurs WHERE id = ?;');
          $sup_utilisateur->execute(array($_GET['id']));
          if ($_GET['id'] == $_SESSION['id']) {
            header( "refresh:0;url=logout.php?deleted=true" );
          } else {
            header( "refresh:0;url=account.php?deleted=true&id=" . $_GET['id'] );
          }
        } else {
          header( "refresh:0;url=account.php?derror=true&id=" . $_GET['id'] );
        }
      } else {
        header( "refresh:0;url=account.php?dperror=true&id=" . $_GET['id'] );
      }

    } else if (isset($_GET['id'])){
      // Modification de paramètres

      // Modifications de super-modérateurs ou par l'utilisateur
      if ($_SESSION['role']>=3 || $_GET['id'] == $_SESSION['id']) {
        // Changement d'année
        if (isset($_POST['annee'])){
          $newyear = $bdd->prepare('UPDATE utilisateurs SET annee = ? WHERE id = ?;');
          $newyear->execute(array($_POST['annee'], $_GET['id']));
        }
        // Changement de majeure
        if (isset($_POST['majeure'])){
          $newmaj = $bdd->prepare('UPDATE utilisateurs SET majeure = ? WHERE id = ?;');
          $newmaj->execute(array($_POST['majeure'], $_GET['id']));
        }
      }

      // Modifications d'ultra-modérateurs ou par l'utilisateur
      if ($_SESSION['role']>=10 || $_GET['id'] == $_SESSION['id']) {
        // Changement de la photo de profil
        if (isset($_POST['photo'])){
          $newpic = $bdd->prepare('UPDATE utilisateurs SET photo = ? WHERE id = ?;');
          $newpic->execute(array($_POST['photo'], $_GET['id']));
        }
        // Suppression de la photo de profil
        if (isset($_POST['dphoto'])){
          $delpic = $bdd->prepare('UPDATE utilisateurs SET photo = NULL WHERE id = ?;');
          $delpic->execute(array($_GET['id']));
        }
        // Changement du profil LinkedIn
        if (isset($_POST['linkedin'])){
          $newlink = $bdd->prepare('UPDATE utilisateurs SET linkedin = ? WHERE id = ?;');
          $newlink->execute(array($_POST['linkedin'], $_GET['id']));
        }
        // Suppression du profil LinkedIn
        if (isset($_POST['dlinkedin'])){
          $dellink = $bdd->prepare('UPDATE utilisateurs SET linkedin = NULL WHERE id = ?;');
          $dellink->execute(array($_GET['id']));
        }

        // Modification de pseudo
        if (isset($_POST['pseudo'])){
          $pseudo_fetch = $bdd->prepare('SELECT * FROM utilisateurs WHERE pseudo = ?;');
          $pseudo_fetch->execute(array($_POST['pseudo']));
          $pseudo = $pseudo_fetch->fetch();

          if ($pseudo['pseudo'] != $_SESSION['pseudo']) {
            $raiseissue = true;
            header( "refresh:0;url=account.php?pseudoexists=true" );
          } else {
            $newname = $bdd->prepare('UPDATE utilisateurs SET pseudo = ? WHERE id = ?;');
            $newname->execute(array($_POST['pseudo'], $_GET['id']));
          }
        }
        // Modification de mail
        if (isset($_POST['email'])){
          $mail_fetch = $bdd->prepare('SELECT * FROM utilisateurs WHERE email = ?;');
          $mail_fetch->execute(array($_POST['email']));
          $mail = $mail_fetch->fetch();

          if ($mail['email'] != $_SESSION['email']) {
            $raiseissue = true;
            header( "refresh:0;url=account.php?emailexists=true" );
          } else {
            $newmail = $bdd->prepare('UPDATE utilisateurs SET email = ? WHERE id = ?;');
            $newmail->execute(array($_POST['email'], $_GET['id']));
          }

        }

      }

      // Modifications d'administrateurs ou par l'utilisateur
      if ($_SESSION['role']>=50 || $_GET['id'] == $_SESSION['id']){
        // Changement de mot de passe
        if (($_SESSION['role']>=50 || isset($_POST['cmdp'])) && isset($_POST['nmdp']) && !empty($_POST['nmdp']) && isset($_POST['vmdp']) && !empty($_POST['vmdp'])) {
          if ($_SESSION['role']>=50) {
           $verify = true;
         } else if ($_GET['id'] == $_SESSION['id']) {
            $pass_hache = password_hash($_POST['cmdp'], PASSWORD_DEFAULT);

            $auth = $bdd->prepare('SELECT * FROM utilisateurs WHERE id = ?;');
            $auth->execute(array($_GET['id']));
            $authdata = $auth->fetch();

            $verify = password_verify($_POST['cmdp'], $authdata['mdp']);
          } else {
            $verify = false;
          }

          if ($verify){
            if (!empty($_POST['nmdp']) AND !empty($_POST['vmdp']) AND $_POST['nmdp'] == $_POST['vmdp']) {
              $hash=password_hash($_POST['nmdp'], PASSWORD_DEFAULT);

              $newauth = $bdd->prepare('UPDATE utilisateurs SET mdp = ? WHERE id = ?;');
              $newauth->execute(array($hash, $_GET['id']));
            } else {
              $passfailure = true;
            }
          } else {
            $authfailure = true;
          }

        }
      }

      // Modifications de super-modérateurs uniquement
      if ($_SESSION['role']>=3) {
        // Changement du solde karma
        if (isset($_POST['karma'])) {
          $newkarma = $bdd->prepare('UPDATE utilisateurs SET karma = ? WHERE id = ?;');
          $newkarma->execute(array($_POST['karma'], $_GET['id']));
        }
        // Changement du statut de la validation Efrei
        if (isset($_POST['validation'])) {
          $newval = $bdd->prepare('UPDATE utilisateurs SET validation = ? WHERE id = ?;');
          $newval->execute(array($_POST['validation'], $_GET['id']));

          if ($_POST['validation'] != 1) {
            $deletetoken = $bdd->prepare('DELETE FROM validations WHERE user = ?');
            $deletetoken->execute(array($_GET['id']));
          }
        }
      }

      // Modifications d'ultra-modérateurs uniquement
      if ($_SESSION['role']>=10) {
        // Changement de role
        if (isset($_POST['role'])) {
          if ($_POST['role'] <= $_SESSION['role']) {
            $newrole = $bdd->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?;');
            $newrole->execute(array($_POST['role'], $_GET['id']));
          } else {
            $raiseissue = true;
            header( "refresh:0;url=account.php?invalidrole=true" );
          }

        }
      }

      // Modifications d'administrateurs uniquement
      if ($_SESSION['role']>=50) {
        // Changement de la date d'inscription
        if (isset($_POST['inscription'])) {
          $newreg = $bdd->prepare('UPDATE utilisateurs SET inscription = ? WHERE id = ?;');
          $newreg->execute(array($_POST['inscription'], $_GET['id']));
        }
        // Changement d'ID
        if (isset($_POST['id'])) {
          $newid = $bdd->prepare('UPDATE utilisateurs SET id = ? WHERE id = ?;');
          $newid->execute(array($_POST['id'], $_GET['id']));
        }
        if (isset($_POST['ban'])) {
          $newid = $bdd->prepare('UPDATE utilisateurs SET ban = ? WHERE id = ?;');
          $newid->execute(array($_POST['ban'], $_GET['id']));
        }

      }

      if (isset($passfailure)) {
        header( "refresh:0;url=account.php?passfailure=true&id=" . $_GET['id'] );
      } else if (isset($authfailure)) {
        header( "refresh:0;url=account.php?authfailure=true&id=" . $_GET['id'] );
      } else if (!isset($raiseissue)) {
        header( "refresh:0;url=account.php?everythingworked=true&id=" . $_GET['id'] );
      }

    } else {
      header( "refresh:0;url=account.php?ierror=true&id=" . $_GET['id'] );
    }

  } else {
    header( "refresh:0;url=login.php?expired=true" );
  }
}
?>
