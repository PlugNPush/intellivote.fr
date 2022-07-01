<?php
require_once dirname(__FILE__).'/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\POP3;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\Exception;

if (!empty($_POST['email']) AND !empty($_GET['token']) AND !empty($_POST['mdp']) AND !isset($_GET['passworderror'])){ //étape 5
  // Hachage du mot de passe
  $pass_hache = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

  // Vérification des identifiants
  $req = $bdd->prepare('SELECT * FROM individual WHERE email = ?;');
  $req->execute(array($_POST['email']));
  $test = $req->fetch();


  $verify = password_verify($_POST['mdp'], $test['password']);
  if ($verify)
  {  // connexion
      session_start();
      $_SESSION['id'] = $test['id'];
      $_SESSION['registered'] = $test['registered'];
      $_SESSION['email'] = $test['email'];
      $_SESSION['verified'] = $test['verified'];


      header( "refresh:0;url=index.php" );
  } else {
      header( "refresh:0;url=login.php?passworderror=true" );
  }

} else if (isset($_GET['cancel'])){ // étape 4 bonus

  $indv_fetch = $bdd->prepare('SELECT * FROM individual WHERE email = ?;');
  $indv_fetch->execute(array($_SESSION['verifmail']));
  $indv = $indv_fetch->fetch();
  $deletetoken = $bdd->prepare('DELETE FROM validations WHERE individual = ?;');
  $deletetoken->execute(array($indv['id']));

  header( "refresh:0;url=login.php" );

} else if ((isset($_GET['resend'])) OR (!empty($_POST['email']) AND !isset($_GET['token']) AND !isset($_GET['emailexists']) AND !isset($_GET['serror']))){ // étape 2 et étape 4 bonus

  if (isset($_GET['resend'])){
    $_POST['email']=$_SESSION['verifmail'];
  } else {
    $_SESSION['verifmail']=$_POST['email'];
  }

  // vérification de l'existence du mail
  $mailcheck_fetch = $bdd->prepare('SELECT * FROM individual WHERE email = ? AND verified = 1;');
  $mailcheck_fetch->execute(array($_POST['email']));
  $mailcheck = $mailcheck_fetch->fetch();

  // vérification de la validation admin
  $mail_fetch = $bdd->prepare('SELECT *, individual.id as indv FROM individual JOIN governor ON individual.id = governor.individual HAVING email = ? AND governor.verified = 1;');
  $mail_fetch->execute(array($_POST['email']));
  $mail = $mail_fetch->fetch();

  if (!$mailcheck) { // non présent dans la bdd
    header( "refresh:0;url=login.php?emailexists=false" );
  } elseif (!$mail) { // non valide comme admin
    header( "refresh:0;url=login.php?invalidmail=false" );
  } else { // ok, envoie la demande de code
    
    
    if (!isset($_GET['resend'])){
      $token = generateRandomString(256);
        $date = date('Y-m-d H:i:s');
        $newtoken = $bdd->prepare('INSERT INTO validations(type, individual, token, date) VALUES(:type, :individual, :token, :date);');
        $newtoken->execute(array(
          'type' => 10,
          'individual' => $mail['indv'],
          'token' => $token,
          'date' => $date
        ));
      } else {
        $resend_fetch = $bdd->prepare('SELECT validations.token,validations.date FROM individual JOIN validations ON individual.id = validations.individual HAVING email = ?;');
        $resend_fetch->execute(array($_POST['email']));
        $resend = $resend_fetch->fetch();
        $token = $resend['validations.token'];
        $date = $resend['validations.date'];
      }



      $to = $_POST['email'];
      $subject = 'Verification automatique Intellivote';
      $message = '
          <html>
           <body>
            <h1>Vérification automatique de votre compte.</h1>
            <p>Bonjour ' . $_SESSION['surname'] . ' ' . $_SESSION['name'] . ', et bienvenue sur Intellivote. Pour confirmer votre inscription, vous devez confirmer votre identité numérique. Grâce à votre adresse email, vous êtes éligible à notre solution de validation automatique. Cliquez simplement sur le lien ci-dessous pour terminer l\'activation de votre compte.
            <p>Adresse email utilisée</p>
            <h4>' . $_POST['email'] . '</h4>
            <p>Certification demandée le</p>
            <h4>' . $date . '</h4>
            <br>
            <h3><a href="https://gouv.intellivote.fr/login.php?token=' . $token . '">Cliquez ici pour activer automatiquement votre compte</a>.</h3>
            <br>
            <p>À très vite !</p>
            <p>- L\'équipe Intellivote.</p><br><br>
            <p>P.S.: Ce courriel est automatique, veuillez ne pas y répondre.</p>
         </body>
        </html>
        ';


        // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        // En-têtes additionnels
        $headers[] = 'To: <' . $_SESSION['email'] . '>';
        $headers[] = 'From: Validation Intellivote <noreply@intellivote.fr>';

        $mail = new PHPmailer();
        $mail->IsSMTP();
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'mail.groupe-minaste.org';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Username = 'no-reply@efrei-dynamo.fr';
        $mail->Password = getSMTPPassword();
        $mail->SMTPOptions = array(
            'ssl' => array(
               'verify_peer' => false,
               'verify_peer_name' => false,
               'allow_self_signed' => true
            )
        );
        $mail->From = 'no-reply@intellivote.fr';
        $mail->FromName = 'Validation Intellivote';
        $mail->AddAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;

          // Send the mail
          $sent = $mail->send();
      // Envoi
      //$sent = mail($to, $subject, $message, implode("\r\n", $headers));

      if ($sent) {
        header( "refresh:0;url=login.php?pending=true" );
      } else {
        header( "refresh:0;url=login.php?serror=true" );
      }
    }

} elseif (!empty($_POST['email']) AND !empty($_GET['token']) AND !isset($_POST['mdp'])) {
  echo 3;
  $vtoken = $bdd->prepare('SELECT * FROM validations WHERE token = ?;');
  $vtoken->execute(array($_GET['token']));
  $token = $vtoken->fetch();

  if ($token) {
    $validation = $bdd->prepare('UPDATE individual SET verified = 1 WHERE id = ?;');
    $validation->execute(array($_SESSION['id']));

    $validation = $bdd->prepare('UPDATE validations SET validated = 1 WHERE token = ?;');
    $validation->execute(array($_GET['token']));

    header( "refresh:0;url=login.php" );
  } else {
    header( "refresh:0;url=login.php?invalidtoken=true" );
  }


} else {
  echo '<!DOCTYPE html>
  <html lang="fr">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; child-src \'none\';">

    <title>Intellivote - Espace Gouvernement</title>

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
      <a class="navbar-brand" href="index.php">gouv.intellivote.fr</a>
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

        if (isset($_GET['pending']) OR isset($_GET['resent'])){ //étape 3
            echo'<h1 class="my-4">Validation du compte Gouvernement</h1>';

            if (isset($_GET['pending'])) {
              echo '<div class="alert alert-success fade show" role="alert">
                <strong>Validation en attente.</strong><br> Votre lien d\'authentification vous a été envoyé sur votre adresse mail. Le mail de validation se trouve dans votre dossier de spams, aussi appelé courrier indésirable.
              </div>';
            }

            if (isset($_GET['resent'])) {
              echo '<div class="alert alert-success fade show" role="alert">
                <strong>Email renvoyé !</strong><br> Votre lien d\'authentification vous a été envoyé une nouvelle fois sur votre adresse mail. Le mail de validation se trouve dans votre dossier de spams, aussi appelé courrier indésirable.
              </div>';
            }
            
            if (isset($_SESSION['verified']) && $_SESSION['verified'] == 1 && $data) {
              echo '<div class="alert alert-success fade show" role="alert">
                <strong>Félicitations, votre compte Intellivote est validé !</strong><br>Votre identité numérique a été certifiée avec une signature numérique le ', $data['date'], ' via l\'adresse email suivante : <a href="mailto:', $_SESSION['email'] ,'">', $_SESSION['email'] ,'</a>.
              </div>
              <a href="index.php" class="btn btn-success btn-lg btn-block">Continuer sur Intellivote</a><br><br>';
            } else if (isset($_SESSION['verified']) && $_SESSION['verified'] == 1) {
              echo '<div class="alert alert-success fade show" role="alert">
                <strong>Votre compte est validé manuellement par un représentant du Gouvernement !</strong><br>Vous n\'avez rien d\'autre à faire.
              </div>
              <a href="index.php" class="btn btn-success btn-lg btn-block">Continuer sur Intellivote</a><br><br>';
            } else {
              echo '<div class="alert alert-info fade show" role="alert">
                <strong>Un processus de vérification est en cours...</strong><br> Votre lien d\'authentification vous a été envoyé sur votre adresse mail. Le mail de validation se trouve dans votre dossier de spams, aussi appelé courrier indésirable. En cas de problème, contactez un modérateur.
              </div>
              <form action="login.php" method="get">
                <a href="login.php?resend=true" class="btn btn-secondary">Renvoyer le mail</a>
                <a href="login.php?cancel=true" class="btn btn-danger">Annuler la validation</a>
                </form><br><br>';
            }

        } else { // étape 1 (mail)/ étape 4 (mdp)
          echo '<h1 class="my-4">Connexion Espace Gouvernement</h1>';
          if (isset($_GET['deleted'])) {
            echo '
            <div class="alert alert-success fade show" role="alert">
              <strong>Votre compte a bien été supprimé</strong>. Cette suppression se repercute sur tous les espaces Intellivote.
            </div>';
          }
          if (isset($_GET['expired'])) {
            echo '
            <div class="alert alert-info fade show" role="alert">
              <strong>Votre session a expiré</strong>. Pour votre sécurité, votre session a expiré. Veuillez vous reconnecter pour continuer.
            </div>';
          }
          if (isset($_GET['invalidmail'])) { //invalidmail=false
            echo '
            <div class="alert alert-info fade show" role="alert">
              <strong>Adresse e-mail invalide !</strong><br> Il semblerait que l\'adresse email fournie ne soit pas correcte.
            </div>';
          }
          if (isset($_GET['emailexists'])) { //emailexists=false
            echo '
            <div class="alert alert-info fade show" role="alert">
              <strong>Echec de la validation du mail</strong>. Ce mail n\'est pas éligible à l\'espace Gouvernement.
            </div>';
          }
          if (isset($_GET['invalidtoken'])) {
            echo '<div class="alert alert-danger fade show" role="alert">
              <strong>Erreur lors de la validation !</strong><br> Il semblerait que la clé d\'authentification unique envoyée sur votre adresse email soit erronée. Veuillez réessayer.
            </div>';
          }
          if (isset($_GET['serror'])) {
            echo '<div class="alert alert-danger fade show" role="alert">
              <strong>Erreur lors de la validation !</strong><br> Le courrier éléctronique contenant votre code de validation n\'a pas pu s\'envoyer. Veuillez contacter un modérateur.
            </div>';
          }
          if (isset($_GET['ierror'])) {
            echo '
            <div class="alert alert-danger fade show" role="alert">
              <strong>Une erreur interne inattendue s\'est produite</strong>. Un paramètre attendu n\'est pas parvenu à sa destination. Veuillez réesayer puis contacter un modérateur si l\'erreur se reproduit.
            </div>';
          }

          if (empty($_POST['email']) AND isset($_GET['token'])){
            echo '
            <form action="login.php" method="post">
            <div class="form-group">
              <label for="email">Saisissez votre adresse e-mail</label>
              <input type="text" name="email" class="form-control" id="email" placeholder="Courriel" required>
            </div>';
          }
          else {
            $_SESSION['verifmail']="";
            echo '
            <form action="login.php?token=' . $_GET['token'] . '" method="post">
            <div class="form-group">
              <label for="mdp">Saisissez votre mot de passe</label>
              <input type="password" name="mdp" class="form-control';

              if (isset($_GET['passworderror'])){
                echo ' is-invalid';
              }

              echo '" id="mdp" placeholder="Mot de passe" required>';

              if (isset($_GET['passworderror'])){
                echo '<div class="invalid-feedback">
                  Mot de passe incorrect ! Besoin d\'aide ? Contactez un administrateur.
                </div>';
              }

              echo '
            </div>';
          }
          echo '
            <button type="submit" class="btn btn-primary">Se connecter</button>
            </form><br><br>';
        }
        echo '</div>

      </div>
      <!-- /.row -->

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <footer class="py-5 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">&copy; 2022 Intellivote. Tous droits reservés. <a href="https://www.intellivote.fr/legal.php">Mentions légales</a>.</p>
      </div>
      <!-- /.container -->
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  </body>

  </html>';

}

?>
