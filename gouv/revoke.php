<?php
require_once dirname(__FILE__).'/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\POP3;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION['id'])){

  if (isset($_GET['revoke']) || isset($_GET['crevoke'])) {
    if (isset($_POST['electorindv'])) {

      $req = $bdd->prepare('SELECT * FROM individual WHERE id = ? ;');
      $req->execute(array($_POST['electorindv']));
      $indiv = $req->fetch();

      if (empty($indiv['id'])) {
        header( "refresh:0;url=revoke.php?electorindverror=true");
      }

    } else {

      if ($_POST['idmairie'] != -1) {
        $req = $bdd->prepare('SELECT * FROM mairies WHERE id = ? ;');
        $req->execute(array($_POST['idmairie']));
        $mairie = $req->fetch();

        $req = $bdd->prepare('SELECT * FROM mayor WHERE mairie = ? AND individual = ? ;');
        $req->execute(array($_POST['idmairie'], $_POST['mayorindv']));
        $mairiecheck = $req->fetch();
      }

      $req = $bdd->prepare('SELECT * FROM individual WHERE id = ? ;');
      $req->execute(array($_POST['mayorindv']));
      $indiv = $req->fetch();

      if (empty($indiv['id'])) {
        header( "refresh:0;url=revoke.php?mayorindverror=true");
      } else if ($_POST['idmairie'] != -1 && empty($mairie['id'])) {
        header( "refresh:0;url=revoke.php?idmairieerror=true");
      } else if ($_POST['idmairie'] != -1 && empty($mairiecheck['id'])) {
        header( "refresh:0;url=revoke.php?mairiecheckerror=true&idmairieerror=true");
      }

    }
  }

  if (!isset($_GET['crevoke'])) {

        echo '<!DOCTYPE html>
        <html lang="fr">

        <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; style-src https://* \'self\' \'unsafe-inline\' child-src \'none\';">

        <title>Intellivote - Espace Gouvernement - R??voquer un acc??s</title>

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
                    <a class="nav-link" href="https://www.intellivote.fr">Espace ??lecteur</a>
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

                echo '<h1 class="my-4">Espace Gouvernement - R??voquer un acc??s</h1>';

                $gouv_fetch = $bdd->prepare('SELECT * FROM governor WHERE individual = ? AND verified = 1;');
                $gouv_fetch->execute(array($_SESSION['id']));
                $gouv = $gouv_fetch->fetch();

                if (!$gouv) {
                  echo '<div class="alert alert-danger fade show" role="alert">
                    <strong>Vous n\'avez pas acc??s ?? l\'espace Gouvernement.</strong> Assurez-vous d\'avoir bien valid?? votre compte <a href="https://www.intellivote.fr/validation.php">en cliquant ici</a>. D\'ici l??, par s??curit??, vous devez utiliser l\'interface de gestion interne d\'Intellivote pour pouvoir administrer le service, la connexion ?? distance n\'est pas possible. Intellivote ne vous demandera jamais vos identifiants ni codes de v??rifications, ne les communiquez jamais.
                  </div><br><br>';
                } else {

                  if (isset($_GET['revoke'])) {

                    echo '
                      <h2><a>V??rification :</a></h2>
                      <form action="revoke.php?crevoke=true" method="post">';

                      if (isset($_POST['electorindv'])) {

                        $req = $bdd->prepare('SELECT * FROM individual WHERE id = ? ;');
                        $req->execute(array($_POST['electorindv']));
                        $indiv = $req->fetch();

                        echo '<div class="alert alert-danger fade show" role="alert">
                          <strong>ATTENTION : VOTRE R??SPONSABILIT?? EST ENGAG??E.</strong><br> Cet espace permet de r??voquer l\'acc??s d\'un ??lecteur ?? la plateforme Intellivote, suite ?? un retrait de citoyennet?? ou interdiction d\'entr??e sur le territoire par exemple. Votre r??sponsabilt?? est pleinement engag??e sur cette op??ration. N\'utilisez ce formulaire uniquement si vous en avez re??u l\'instruction d\'une haute juridiction administrative, ou si l\'??lecteur vous a donn?? l\'autorisation de proc??der ?? cette op??ration afin de retrouver un mot de passe perdu. Un ??lecteur peut toujours se r??voquer son acc??s lui-m??me, et en cas de d??c??s ou d\'impossibilit?? de voter en ligne, c\'est ?? la mairie de prendre en charge cette op??ration.<br><strong>ATTENTION : cette proc??dure va modifier le mot de passe associ?? au compte de l\'??lecteur. L\'utilisateur sera inform?? de son nouveau mot de passe par e-mail.</strong>
                        </div>';

                        echo '<div class="form-group">
                          <label for="individual">Confirmez-vous les donn??es ?<br>
                          <div class="alert alert-info fade show" role="alert">
                          - <strong>ID de l\'??lecteur :</strong> ' . $_POST['electorindv'] . ' | Nom : '. $indiv['name'] . ' | Pr??nom : ' . $indiv['surname'] . ' | E-mail : ' . $indiv['email'];
                            echo '<br> - <strong>ATTENTION : sera revoqu?? en tant qu\'??lecteur sur Intellivote.</strong>';
                          echo '</div>
                          </label>
                          <input type="hidden" name="electorindv" class="form-control" id="electorindv" value="' . $_POST['electorindv'] . '" required>';

                        echo '</div>

                        <button type="submit" class="btn btn-danger">Confirmer les donn??es, r??voquer l\'acc??s et envoyer le nouveau mot de passe par e-mail</button>

                      </form><br>

                      <br><br>

                      <a class="btn btn-primary" href="revoke.php">Retour en arri??re</a>';

                      } else {

                        if ($_POST['idmairie'] != -1) {
                          $req = $bdd->prepare('SELECT * FROM mairies WHERE id = ? ;');
                          $req->execute(array($_POST['idmairie']));
                          $mairie = $req->fetch();
                        }

                        $req = $bdd->prepare('SELECT * FROM individual WHERE id = ? ;');
                        $req->execute(array($_POST['mayorindv']));
                        $indiv = $req->fetch();

                        echo '<div class="form-group">
                          <label for="individual">Confirmez-vous les donn??es ?<br>
                          <div class="alert alert-info fade show" role="alert">
                          - <strong>ID de l\'employ?? de mairie :</strong> ' . $_POST['mayorindv'] . ' | Nom : '. $indiv['name'] . ' | Pr??nom : ' . $indiv['surname'];
                          if ($_POST['idmairie'] != -1) {
                            echo '<br> - <strong>ID de la mairie :</strong> ' . $_POST['idmairie'] . ' | Nom : ' . $mairie['nom'] . ' | INSEE : ' . $mairie['insee'];
                          } else {
                            echo '<br> - <strong>Sera revoqu?? de toutes les mairies du pays</strong>';
                          }
                          echo '</div>
                          </label>
                          <input type="hidden" name="mayorindv" class="form-control" id="mayorindv" value="' . $_POST['mayorindv'] . '" required>';

                          echo '<input type="hidden" name="idmairie" class="form-control" id="idmairie" value="' . $_POST['idmairie'] . '" required>';

                        echo '</div>

                        <button type="submit" class="btn btn-danger">Confirmer les donn??es et r??voquer l\'acc??s</button>

                      </form><br>

                      <br><br>

                      <a class="btn btn-primary" href="revoke.php">Retour en arri??re</a>';
                      }

                  } else {
                    echo '
                    <h2><a>R??voquer un employ?? de mairie :</a></h2>
                    <form action="revoke.php?revoke=true" method="post">';

                    if (isset($_GET['mairiecheckerror'])) {
                      echo '<div class="alert alert-danger fade show" role="alert">
                        <strong>Une erreur s\'est produite.</strong> Aucune correspondance n\'a pu ??tre trouv??e pour cet ??lecteur et cette mairie.
                      </div>';
                    }

                    echo '

                        <div class="form-group">
                            <label for="mayorindv">Saisissez l\'ID de l\'employ?? de la mairie</label>
                            <input type="text" name="mayorindv" class="form-control';

                            if (isset($_GET['mayorindverror'])){
                                echo ' is-invalid';
                            }

                            echo ' "id="mayorindv" placeholder="Saisissez l\'ID de l\'employ?? de mairie" required> ';

                            if (isset($_GET['mayorindverror'])){
                                echo '<div class="invalid-feedback">
                                ID de l\'employ?? de la mairie incorrect ! V??rifiez votre saisie.
                                </div>';
                            }

                            echo ' <small id="NameHelp" class="form-text text-muted">
                                Veuillez saisir l\'identifiant de l\'employ?? de la mairie. Celui-ci vous avait ??t?? communiqu?? lors de son enregistrement. En cas de probl??me, contactez un administrateur.
                            </small>

                            <label for="idmairie">Saisissez l\'ID de la mairie </label>
                            <div>
                                <input type="text" name="idmairie" class="form-control';
                                if (isset($_GET['idmairieerror'])){
                                    echo ' is-invalid';
                                }
                                echo '" id="idmairie" placeholder="Saisissez -1 pour r??voquer tous les acc??s" required>';
                                if (isset($_GET['idmairieerror'])){
                                    echo '<div class="invalid-feedback">
                                    ID de la mairie incorrect ! V??rifiez votre saisie. L\'employ?? ne travavaille peut-??tre pas dans cette mairie.
                                    </div>';
                                }
                                echo '
                            </div>';

                            echo '
                            <small id="DateHelp" class="form-text text-muted">
                                Vous pouvez indiquer -1 pour r??voquer l\'acc??s de l\'employ?? ?? toutes les mairies du pays.
                            </small>


                        </div>

                        <button type="submit" class="btn btn-danger">R??voquer les acc??s de l\'employ?? de la mairie</button>

                    </form><br><br>';


                    echo '
                    <h2><a>R??voquer un ??lecteur :</a></h2>
                    <form action="revoke.php?revoke=true" method="post">';

                    echo '<div class="alert alert-danger fade show" role="alert">
                      <strong>ATTENTION : VOTRE R??SPONSABILIT?? EST ENGAG??E.</strong><br> Cet espace permet de r??voquer l\'acc??s d\'un ??lecteur ?? la plateforme Intellivote, suite ?? un retrait de citoyennet?? ou interdiction d\'entr??e sur le territoire par exemple. Votre r??sponsabilt?? est pleinement engag??e sur cette op??ration. N\'utilisez ce formulaire uniquement si vous en avez re??u l\'instruction d\'une haute juridiction administrative, ou si l\'??lecteur vous a donn?? l\'autorisation de proc??der ?? cette op??ration afin de retrouver un mot de passe perdu. Un ??lecteur peut toujours se r??voquer son acc??s lui-m??me, et en cas de d??c??s ou d\'impossibilit?? de voter en ligne, c\'est ?? la mairie de prendre en charge cette op??ration.<br><strong>ATTENTION : cette proc??dure va modifier le mot de passe associ?? au compte de l\'??lecteur. L\'utilisateur sera inform?? de son nouveau mot de passe par e-mail.</strong>
                    </div>';

                    echo '

                        <div class="form-group">
                            <label for="electorindv">Saisissez l\'ID de l\'??lecteur</label>
                            <input type="text" name="electorindv" class="form-control';

                            if (isset($_GET['electorindverror'])){
                                echo ' is-invalid';
                            }

                            echo ' "id="electorindv" placeholder="Saisissez l\'ID de l\'??lecteur" required> ';

                            if (isset($_GET['electorindverror'])){
                                echo '<div class="invalid-feedback">
                                ID de l\'??lecteur incorrect ! V??rifiez votre saisie.
                                </div>';
                            }

                            echo ' <small id="NameHelp" class="form-text text-muted">
                                Veuillez saisir l\'identifiant de l\'??lecteur. En cas de probl??me, contactez un administrateur.
                            </small>


                        </div>

                        <button type="submit" class="btn btn-danger">R??voquer le statut d\'??lecteur et r??initialiser son mot de passe</button>

                    </form><br><br>';
                  }

                      echo '
                      <a class = "btn btn-primary" href = "index.php">Annuler</a>
                      <br><br>';


                }

                echo '
                <a class = "btn btn-secondary" href = "logout.php">Se d??connecter</a>
                <br><br>';

            echo '</div>

            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

        <!-- Footer -->
        <footer class="py-5" style="background-color: #e04a51;">
            <div class="container">
            <p class="m-0 text-center text-white">&copy; 2022 Intellivote. Tous droits reserv??s. <a href="https://www.intellivote.fr/legal.php" style="color: lightcyan;">Mentions l??gales</a>.</p>
            </div>
            <!-- /.container -->
        </footer>

        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        </body>

        </html>';

    } else {

      $gouv_fetch = $bdd->prepare('SELECT * FROM governor WHERE individual = ? AND verified = 1;');
      $gouv_fetch->execute(array($_SESSION['id']));
      $gouv = $gouv_fetch->fetch();

      if (!$gouv) {
        header( "refresh:0;url=index.php" );
      } else {
        if (isset($_POST['electorindv'])) {
          $req = $bdd->prepare('DELETE FROM elector WHERE individual = ?;');
          $req->execute(array($_POST['electorindv']));

          $token = generateRandomString(55);
          $date = date('Y-m-d H:i:s');
          $pass_hache = password_hash($token, PASSWORD_DEFAULT);

          $upd1 = $bdd->prepare('UPDATE individual SET password = ? WHERE id = ?;');
          $upd1->execute(array($pass_hache, $_POST['electorindv']));

          $datafetch = $bdd->prepare('SELECT * FROM individual WHERE id = ?;');
          $datafetch->execute(array($_POST['electorindv']));
          $data = $datafetch->fetch();

          $to = $data['email']; // $_POST['email']
          $subject = 'IMPORTANT : Nouveau mot de passe Intellivote';
          $message = '
              <html>
               <body>
                <h1>Compte ??lecteur r??voqu?? par une haute instance.</h1>
                <p>Bonjour ' . $data['surname'] . ' ' . $data['name'] . ', l\'acc??s ?? votre compte a ??t?? r??voqu?? par une haute instance. Si vous pensez que cette suspension est une erreur, ou si vous aviez demand?? une r??initialisation de mot de passe aupr??s d\'une instance de l\'??tat, vous devrez vous r??inscrire en mairie en suivant les instructions sur votre espace ??lecteur.
                <p>Adresse email utilis??e</p>
                <h4>' . $data['email'] . '</h4>
                <p>Date effective de la suspension</p>
                <h4>' . $date . '</h4>
                <br>
                <h3>Votre nouveau mot de passe est :<br><strong> ' . $token . '</strong></h3>
                <br>
                <p>Nous vous invitons ?? changer ce mot de passe lors de votre prochaine connexion.</p>
                <br>
                <p>Bien cordialement,</p>
                <p>- L\'??quipe Intellivote.</p><br><br>
                <p>P.S.: Ce courriel est automatique, veuillez ne pas y r??pondre.</p>
             </body>
            </html>
            ';


          // Pour envoyer un mail HTML, l'en-t??te Content-type doit ??tre d??fini
          $headers[] = 'MIME-Version: 1.0';
          $headers[] = 'Content-type: text/html; charset=iso-8859-1';

          // En-t??tes additionnels
          $headers[] = 'To: <' . $data['email'] . '>';
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

          if ($sent) {
            header( "refresh:0;url=index.php?electorrevokesuccess=true" );
          } else {
            header( "refresh:0;url=index.php?electorrevokesuccess=true&tmpemail=" . $data['email'] . "&tmppass=" . $token );
          }


        } else {
          if ($_POST['idmairie'] != -1) {
            $req = $bdd->prepare('DELETE FROM mayor WHERE mairie = ? AND individual = ?;');
            $req->execute(array($_POST['idmairie'], $_POST['mayorindv']));
            header( "refresh:0;url=index.php?mayor1revokesuccess=true" );
          } else {
            $req = $bdd->prepare('DELETE FROM mayor WHERE individual = ?;');
            $req->execute(array($_POST['mayorindv']));
            header( "refresh:0;url=index.php?mayor2revokesuccess=true" );
          }
        }
      }

    }

} else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
