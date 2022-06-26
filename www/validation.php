<?php
require_once dirname(__FILE__).'/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\POP3;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION['id'])){

  if (empty($_POST['email']) && empty($_GET['token']) && !isset($_GET['resend']) && !isset($_GET['cancel'])){
    echo '<!DOCTYPE html>
    <html lang="fr">

    <head>

      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="">
      <meta name="author" content="">

      <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; child-src \'none\';">

      <title>Intellivote - Espace électeur</title>

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
    <a class="navbar-brand" href="index.php">intellivote.fr</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span id="new-dark-navbar-toggler-icon" class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="https://www.intellivote.fr">Espace élécteur<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://mairie.intellivote.fr">Espace mairie</a>
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


            echo'<h1 class="my-4">Validation du compte</h1>';


            $gatherdata = $bdd->prepare('SELECT * FROM validations WHERE individual = ? AND type = 0;');
            $gatherdata->execute(array($_SESSION['id']));
            $data = $gatherdata->fetch();


            if (isset($_GET['invalidmail'])) {
              echo '<div class="alert alert-danger fade show" role="alert">
                <strong>Adresse e-mail invalide !</strong><br> Il semblerait que l\'adresse email fournie ne soit pas fournie par l\'Efrei.
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

            if (isset($_GET['pending'])) {
              echo '<div class="alert alert-success fade show" role="alert">
                <strong>Validation en attente.</strong><br> Votre code d\'authentification vous a été envoyé sur votre adresse mail. Le mail de validation se trouve dans votre dossier de spams, aussi appelé courrier indésirable.
              </div>';
            }

            if (isset($_GET['resent'])) {
              echo '<div class="alert alert-success fade show" role="alert">
                <strong>Email renvoyé !</strong><br> Votre code d\'authentification vous a été envoyé une nouvelle fois sur votre adresse mail. Le mail de validation se trouve dans votre dossier de spams, aussi appelé courrier indésirable.
              </div>';
            }

            if (isset($_GET['emailexists'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Echec de la validation du mail.</strong> Un compte a déjà été vérifié avec cette adresse mail.
              </div>';
            }

            if (isset($_SESSION['validation']) && $_SESSION['validation'] == 1 && $data) {
              echo '<div class="alert alert-success fade show" role="alert">
                <strong>Félicitations, votre compte Intellivote est validé !</strong><br>Votre identité numérique a été certifiée avec une signature numérique le ', $data['date'], ' via l\'adresse email Efrei suivante : <a href="mailto:', $data['email'] ,'">', $data['email'] ,'</a>.
              </div>
              <a href="index.php" class="btn btn-success btn-lg btn-block">Accéder à Efrei Dynamo</a><br><br>';
            } else if (isset($_SESSION['validation']) && $_SESSION['validation'] == 1) {
              echo '<div class="alert alert-success fade show" role="alert">
                <strong>Votre compte est validé manuellement par un représentant du Gouvernement !</strong><br>Vous n\'avez rien d\'autre à faire.
              </div>
              <a href="index.php" class="btn btn-success btn-lg btn-block">Accéder à Efrei Dynamo</a><br><br>';
            } else if ($data) {
              echo '<div class="alert alert-info fade show" role="alert">
                <strong>Un processus de vérification est en cours...</strong><br> Votre code d\'authentification vous a été envoyé sur votre adresse mail. Le mail de validation se trouve dans votre dossier de spams, aussi appelé courrier indésirable. En cas de problème, contactez un modérateur.
              </div>
              <form action="validation.php" method="get">
                <div class="form-group">
                  <label for="token">Saisissez le code à usage unique</label>
                  <input type="text" name="token" class="form-control" id="token" placeholder="Saisissez le code reçu sur votre adresse mail" required>
                  <small id="emailHelp" class="form-text text-muted">
                    Vous pouvez également cliquer sur le lien envoyé dans le mail que vous avez reçu. En cas de problème, contactez un modérateur.
                  </small>
                </div>
                <button type="submit" class="btn btn-primary">Vérifier l\'authenticité du compte</button>
                <a href="validation.php?resend=true" class="btn btn-secondary">Renvoyer le code</a>
                <a href="validation.php?cancel=true" class="btn btn-danger">Annuler la validation</a>
                </form><br><br>';
            } else {

                echo '
                <form action="validation.php" method="post">
                  <div class="form-group">
                    <label for="email">Confirmez votre adresse mail</label>
                    <input type="text" name="email" class="form-control" id="email" placeholder="', $_SESSION['email'] ,'" value="', $_SESSION['email'] ,'" required>
                    <small id="emailHelp" class="form-text text-muted">
                      Vous ne pourrrez plus modifier cette adresse une fois votre compte validé.
                    </small>
                  </div>
                  <button type="submit" class="btn btn-primary">Démarrer le processus de vérification</button>
                  </form><br><br>';
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
          <p class="m-0 text-center text-white">&copy; 2022 Intellivote. Tous droits reservés. <a href="/legal.php">Mentions légales</a>.</p>
        </div>
        <!-- /.container -->
      </footer>

      <!-- Bootstrap core JavaScript -->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    </body>

    </html>
';
} else if (isset($_GET['resend'])){

  $gatherdata = $bdd->prepare('SELECT * FROM validations WHERE individual = ? AND type = 0;');
  $gatherdata->execute(array($_SESSION['id']));
  $data = $gatherdata->fetch();

  if ($data) {
    $to = $_SESSION['email']; // $_POST['email']
    $subject = 'Verification automatique Intellivote';
    $message = '
        <html>
         <body>
          <h1>Vérification automatique de votre compte.</h1>
          <p>Bonjour ' . $_SESSION['name'] . ' ' . $_SESSION['surname'] . ', et bienvenue sur Intellivote. Pour confirmer votre inscription, vous devez confirmer votre identité numérique. Grâce à votre adresse email, vous êtes éligible à notre solution de validation automatique. Cliquez simplement sur le lien ci-dessous pour terminer l\'activation de votre compte.
          <p>Adresse email utilisée</p>
          <h4>' . $_POST['email'] . '</h4>
          <p>Certification demandée le</p>
          <h4>' . $date . '</h4>
          <br>
          <h3><a href="https://www.intellivote.fr/validation.php?token=' . $token . '">Cliquez ici pour activer automatiquement votre compte</a>.</h3>
          <br>
          <p>En cas de problème avec le lien ci-dessus, vous pouvez aussi copier votre code d\'authentification à usage unique :</p>
          <h4>' . $token . '</h4>
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
    $headers[] = 'To: <' . $_POST['email'] . '>';
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
        header( "refresh:0;url=validation.php?resent=true" );
      } else {
        echo 'Mailer Error: ' . $mail->ErrorInfo . '!';
        header( "refresh:0;url=validation.php?serror=true" );
      }

  } else {
    header( "refresh:0;url=validation.php?ierror=true" );
  }

} else if (isset($_GET['cancel'])){

  $deletetoken = $bdd->prepare('DELETE FROM validations WHERE individual = ? AND type = 0');
  $deletetoken->execute(array($_SESSION['id']));

  header( "refresh:0;url=validation.php" );

} else if (!isset($_GET['token'])){

    $mailchange = $bdd->prepare('UPDATE individual SET email = ? WHERE id = ?');
    $mailchange->execute(array($_POST['email'], $_SESSION['id']));


    $mail_fetch = $bdd->prepare('SELECT * FROM validations WHERE individual = ? AND type = 0;');
    $mail_fetch->execute(array($_SESSION['id']));
    $mail = $mail_fetch->fetch();

    if ($mail) {
      header( "refresh:0;url=validation.php?emailexists=true" );
    } else {
      $newmail = $bdd->prepare('UPDATE individual SET email = ? WHERE id = ?;');
      $newmail->execute(array($_POST['email'], $_SESSION['id']));

      $token = generateRandomString(256);
      $date = date('Y-m-d H:i:s', strtotime('+1 day'));

      $newtoken = $bdd->prepare('INSERT INTO validations(type, individual, token, expiration) VALUES(:type, :individual, :token, :expiration);');
      $newtoken->execute(array(
        'type' => 0,
        'individual' => $_SESSION['id'],
        'token' => $token,
        'expiration' => $date
      ));


      $to = $_SESSION['email']; // $_POST['email']
      $subject = 'Verification automatique Intellivote';
      $message = '
          <html>
           <body>
            <h1>Vérification automatique de votre compte.</h1>
            <p>Bonjour ' . $_SESSION['name'] . ' ' . $_SESSION['surname'] . ', et bienvenue sur Intellivote. Pour confirmer votre inscription, vous devez confirmer votre identité numérique. Grâce à votre adresse email, vous êtes éligible à notre solution de validation automatique. Cliquez simplement sur le lien ci-dessous pour terminer l\'activation de votre compte.
            <p>Adresse email utilisée</p>
            <h4>' . $_POST['email'] . '</h4>
            <p>Certification demandée le</p>
            <h4>' . $date . '</h4>
            <br>
            <h3><a href="https://www.intellivote.fr/validation.php?token=' . $token . '">Cliquez ici pour activer automatiquement votre compte</a>.</h3>
            <br>
            <p>En cas de problème avec le lien ci-dessus, vous pouvez aussi copier votre code d\'authentification à usage unique :</p>
            <h4>' . $token . '</h4>
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
      $headers[] = 'To: <' . $_POST['email'] . '>';
      $headers[] = 'From: Validation Intellivote <noreply@intellivote.fr>';

      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->IsHTML(true);
      $mail->CharSet = 'UTF-8';
      $mail->Host = 'smtp.free.fr';
      $mail->Port = 465;
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
      $mail->Username = 'craftsearch';
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
        header( "refresh:0;url=validation.php?pending=true" );
      } else {
        header( "refresh:0;url=validation.php?serror=true" );
      }
    }

} else {
  $vtoken = $bdd->prepare('SELECT * FROM validations WHERE token = ?;');
  $vtoken->execute(array($_GET['token']));
  $token = $vtoken->fetch();

  if ($token) {
    $validation = $bdd->prepare('UPDATE individual SET validation = 1 WHERE id = ?;');
    $validation->execute(array($_SESSION['id']));

    header( "refresh:0;url=validation.php" );
  } else {
    header( "refresh:0;url=validation.php?invalidtoken=true" );
  }

}

}
else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
