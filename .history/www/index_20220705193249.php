<?php
require_once dirname(__FILE__).'/../config.php';


if (isset($_SESSION['id'])){

    echo '<!DOCTYPE html>
    <html lang="fr">

    <head>

      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="">
      <meta name="author" content="">

      <meta http-equiv="Content-Security-Policy" content="default-src \'self\'; img-src https://* \'self\' data:; style-src https://* \'self\' \'unsafe-inline\' child-src \'none\';">

      <title>Intellivote - Espace électeur</title>

      <link href="css/custom.css" rel="stylesheet">

<!-- Bootstrap core CSS -->
      <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

      <!-- Custom styles for this template -->
      <link href="css/blog-home.css" rel="stylesheet">

    </head>

    <body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
      <div class="container">
        <a class="navbar-brand" href="index.php"><img src="image/logo.png" width="160" height="30"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span id="new-dark-navbar-toggler-icon" class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="https://www.intellivote.fr">Espace électeur<span class="sr-only">(current)</span></a>
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

              echo '<h1 class="my-4">Bienvenue sur Intellivote,
                <small>', $_SESSION['surname'], ' ', $_SESSION['name'], '</small>
              </h1>';


            if (isset($_GET['ierror'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Une erreur interne inattendue s\'est produite</strong>. Un paramètre attendu n\'est pas parvenu à sa destination. Veuillez réesayer puis contacter un modérateur si l\'erreur se reproduit.
              </div>';
            }
            if (isset($_GET['dperror'])) {
              echo '
              <div class="alert alert-danger fade show" role="alert">
                <strong>Une erreur s\'est produite</strong>. Vous ne disposez pas des autorisations nécéssaires pour réaliser cette opération.
              </div>';
            }

              if ($_SESSION['verified'] != 1) {
                echo '
                <div class="alert alert-danger fade show" role="alert">
                  <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Vous devez confirmer votre compte pour accéder au site. Celui-ci n\'a pas encore pu être vérifié.<br><a class = "btn btn-primary" href = "validation.php">Lancer ou vérifier la procédure de validation</a>
                </div>';
              } else {
                echo '
                <div class="alert alert-info fade show" role="alert">
                  <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Votre compte est prêt.<br>
                </div>';

                $gatherdata = $bdd->prepare('SELECT * FROM elector WHERE individual = ? AND verified = 1;');
                $gatherdata->execute(array($_SESSION['id']));
                $data = $gatherdata->fetch();

                // Partie Pablo -----------------------------------------------------------------------------------------------
                if ($data) {
                  echo '
                  <div class="alert alert-info fade show" role="alert"';

                    //get actual time in paris 
                    $curdate = date('Y-m-d h:i:s');

                    //display current date 
                    /*
                     echo '
                      <div class="alert alert-info fade show" role="alert">
                        <strong>Heure locale  ', $curdate, '.<br>
                      </div> 
                    </div>';
                    */

                    //check if any election is ongoing in the database 
                    $getdates = $bdd->prepare('SELECT * FROM election WHERE begindate < ? AND ? < enddate ;');
                    $getdates->execute(array($curdate,$curdate));

                    $i = 0;
                    while ($election=$getdates->fetch()){ //case 1 or many ongoing elections 
                      echo '
                      <div class="alert alert-info fade show" role="alert">
                        <strong> ', $election['description'], ' : </strong><br>
                      ';
                      $i++;

                      //display all candidates
                      $getcandidates = $bdd->prepare('SELECT * FROM election JOIN candidate ON candidate.election= ? GROUP BY candidate.surname , candidate.name ');
                      $getcandidates->execute(array($election['id']));
                    
                      $j = 0;
                      while ($candidates = $getcandidates->fetch()){ //case 1 or many candidates
                        echo '
                        <div class="alert alert-info fade show" role="alert">
                          <strong> ', $candidates['surname'],' ',$candidates['name'], ' : <a href="',$candidates['programme'],'"> Cliquez ici pour lire le programme.</a></strong><br>
                          <p> Parti : ',$candidates['party'],'</p>
                          </div>';
                        $j++;
                      };
                      if ($j==0) { //case no candidates
                        echo '
                        <div>
                        <p>Pas de candidats.</p>
                        </div>';
                      }   
                    
                      
                      // check if elector did vote
                      $getvoted = $bdd->prepare('SELECT * FROM voted JOIN elector ON voted.elector=elector.id');
                      $getvoted->execute();

                      $k = 0;
                      while ($voted = $getvoted->fetch()){ // if elector already voted
                        if ($voted['election']==$election['id']){
                          echo '
                          <div>
                          <p>Vous avez déjà voté.</p>
                          </div>';
                          $k++;
                        }
                        
                      };
                      if ($k==0) {  //if elector didnt vote : display button vote and all existing candidates
                        $getcandidates2 = $bdd->prepare('SELECT * FROM election JOIN candidate ON candidate.election= ? GROUP BY candidate.surname , candidate.name ');
                        $getcandidates2->execute(array($election['id']));
                   
                        // candidate choice select
                        if (!isset($_POST["monVote".$election['id']])){ // if elector hasnt voted yet and hasn't selected a candidate
                            echo '
                            <div>
                              <form action="index.php" method="post">
                              <div class="form-group">
                                <label for="token"><strong>Sélectionnez un candidat pour procéder au vote en ligne :</strong></label>
                                  <select id="monVote" name="monVote'.$election['id'].'"> 
                                    <option disabled selected value> </option>';
                            while ($candidates2 = $getcandidates2->fetch()){ //case 1 or many candidates
                              echo '
                                    <option value="',$candidates2['id'],'">', $candidates2['surname'],' ',$candidates2['name'],' - ',$candidates2['party'],'</option>
                              ';
                            };
                            echo '
                                    <option value ="blanc">Vote Blanc</option>
                                  </select><br>
                              </div>
                              <button type="submit" class="btn btn-primary">Voter</button>
                              </form>
                              <br>
                              <p>L\'élection prend fin le : ',date_format($election["enddate"], 'Y-m-d H:i:s'),' heures.</p>
                            </div>';
                          }   
                          else { // if elector hasnt voted yet and has selected a candidate

                            //create token
                            $token = generateRandomString(512);

                            //insert new "voted" in db 
                            $newvoted = $bdd->prepare('INSERT INTO voted (election,elector) VALUES (:election,:elector);');
                            $newvoted->execute(array(
                              'election' => $election['id'],
                              'elector' =>  $data['id'] 
                            ));
                            
                            // insert new "votes" in db 
                            if ($_POST["monVote".$election['id']]=="blanc"){ //case "vote blanc" 
                              $newvotes = $bdd->prepare('INSERT INTO votes (token, date,election,mairie) VALUES (:token, :date, :election, :mairie);');
                              $newvotes->execute(array(
                                'token' => $token,
                                'date' => $curdate,
                                'election' => $election['id'],
                                'mairie' => $data['mairie']
                              ));
                            }
                            else { //case any  other candidate is selected
                              $newvotes = $bdd->prepare('INSERT INTO votes (token, date,candidate,election,mairie) VALUES (:token, :date, :candidate, :election, :mairie);');
                              $newvotes->execute(array(
                                'token' => $token,
                                'date' => $curdate,
                                'candidate' => $_POST["monVote".$election['id']], 
                                'election' => $election['id'],
                                'mairie' => $data['mairie']
                              ));
                            };

                            //check existing token 
                            $gettoken = $bdd->prepare('SELECT votes.token FROM votes WHERE votes.token = ?');
                            $gettoken->execute(array($token));

                            $tokencpt=0;
                            while ($fetchtoken = $gettoken->fetch()){ 
                              $tokencpt++;
                            };
                                
                            if ($tokencpt==1){
                              echo '
                              <div>
                              <p>Vous avez voté.</p>
                              </div>';
                            }
                            else {
                              /* 
                              case where token isnt in the database 

                              FILL HERE 

                              */
                            }

                          };



                        }
                      
                    echo "
                      </div> \n";
                        
                    // end of candidates display   

                    };
                    if ($i==0) { //case no ongoing election
                      echo '
                      <strong>Pas d\'élections à venir.<br>';
                    }               


                // Fin Partie Pablo ------------------------------------------------------------------------------------------    

                } else {
                  $gatherdataverif = $bdd->prepare('SELECT * FROM validations WHERE type = 1 AND individual = ?');
                  $gatherdataverif->execute(array($_SESSION['id']));
                  $dataverif = $gatherdataverif->fetch();
                  if(isset($_GET['verifmairie']) || $dataverif) {


                    if(!$dataverif){
                        $token = generateRandomString(20);
                          $date = date('Y-m-d H:i:s');

                          $newtoken = $bdd->prepare('INSERT INTO validations(type, individual, token, date) VALUES(:type, :individual, :token, :date);');
                          $newtoken->execute(array(
                            'type' => 1,
                            'individual' => $_SESSION['id'],
                            'token' => $token,
                            'date' => $date
                          ));
                        }else {
                          $token=$dataverif['token'];
                        }

                        echo '
                        <div class="alert alert-info fade show" role="alert">
                          <strong>Voici votre token: ', $token ,' </strong><br>Ce dernier devra être présenté dans votre mairie, ou par téléphone.<br>Pensez aux justificatifs habituels: pièce d\'identité, justificatif de domicile et carte d\'électeur.<br>Si vous voulez voter aux prochaines élections, pensez à valider votre compte au moins 7 jours avant le vote.
                        </div>';


                  } else {
                    echo '
                  <div class="alert alert-warning fade show" role="alert">
                    <strong>Bonjour ', $_SESSION['surname'], ' !</strong><br> Vous devez maintenant vous authentifier en tant qu\'électeur, donc relier votre identité numérique à votre identité physique. Lancez une pré-demande en ligne ou rendez-vous en mairie.<br><a class = "btn btn-primary" href = "index.php?verifmairie=true">Relier mon identité physique</a><br>
                    <br>Vous representez une mairie ? Votre demande devra être traitée par <a href="https://gouv.intellivote.fr">un représentant de l\'État</a>.
                  </div>';
                  }

                }
              }

            echo '

            <a class = "btn btn-secondary" href = "logout.php">Se déconnecter</a><br><br>

          </div>

        </div>
        <!-- /.row -->

      </div>
      <!-- /.container -->

      <!-- Footer -->
      <footer class="py-5" style="background-color: #336db5;">
        <div class="container">
          <p class="m-0 text-center text-white">&copy; 2022 Intellivote. Tous droits reservés. <a href="/legal.php" style="color: lightcyan;">Mentions légales</a>.</p>
        </div>
        <!-- /.container -->
      </footer>

      <!-- Bootstrap core JavaScript -->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    </body>

    </html>
';

} else {
  header( "refresh:0;url=login.php?expired=true" );
}

?>
