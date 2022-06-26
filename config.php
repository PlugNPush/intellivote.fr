<?php
require_once dirname(__FILE__).'/../config/config.php';
require_once dirname(__FILE__).'/../config/efreidynconfig.php';

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/POP3.php';
require 'PHPMailer/OAuth.php';
require 'PHPMailer/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\POP3;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\Exception;


try
{
    $bdd = new PDO('mysql:host='.getDBHost().';dbname=intellivote', getDBUsername(), getDBPassword(), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}

session_start();
if (isset($_SESSION['id'])) {
  $req = $bdd->prepare('SELECT * FROM individual WHERE id = ?;');
  $req->execute(array($_SESSION['id']));
  $test = $req->fetch();
  $_SESSION['id'] = $test['id'];
  $_SESSION['name'] = $test['name'];
  $_SESSION['surname'] = $test['surname'];
  $_SESSION['birthday'] = $test['birthday'];
  $_SESSION['birthplace'] = $test['birthplace'];
  $_SESSION['registered'] = $test['registered'];
  $_SESSION['email'] = $test['email'];
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>
