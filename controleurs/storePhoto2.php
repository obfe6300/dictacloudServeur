<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$myIncludePath = '/var/www/html/dictacloud';
set_include_path(get_include_path() . PATH_SEPARATOR . $myIncludePath); 

include_once ('modeles/Users/ClassUsers.php');

error_log("storePhoto2 : debut");


$content = trim(file_get_contents("php://input"));


header( 'content-type: text/html; charset=utf-8' );
if (isset($_SESSION['REQUETE'])) {
     $Requete = $_SESSION['REQUETE'];
     error_log("storePhoto2.php : recupere REQUETE " . $Requete);
} else {
	$Requete = " ";
}
if (isset($_SESSION['PSEUDO'])) {
     $Pseudo = $_SESSION['PSEUDO'];
     error_log("storePhoto2.php : recupere PSEUDO " . $Pseudo);
} else {
	$Pseudo = " ";
}
if (isset($_SESSION['FILENAME'])) {
     $Filename = $_SESSION['FILENAME'];
     error_log("storePhoto2.php : recupere FILENAME " . $Filename);
} else {
	$Filename = " ";
}



// TODO envoyer par mail si flag envoi positionné
// recuperation de l'adresse mail du user
$user = new User($Pseudo, "", "");
$user->checkPseudo($Pseudo);

$Email = $user->getEmail();
error_log("email = " . $Email);


$subject = "[Dictacloud] Votre photo : " . $Filename;
$message = "Bonjour $Pseudo<br><br>"
        . "Voici, en pièce jointe, la photo que vous venez de prendre<br>"
        . "Cordialement<br>"
        . "Le serveur Dictacloud";
file_put_contents("/tmp/mail.txt", "to: $Email\n");
file_put_contents("/tmp/mail.txt", "from: Dictacloud <froger.popote@wanadoo.fr>\n", FILE_APPEND);
file_put_contents("/tmp/mail.txt", "subject: $subject\n", FILE_APPEND);
file_put_contents("/tmp/mail.txt", "$message\n", FILE_APPEND);

//. " -e 'set smtp_url=\"smtp.orange.fr::465\"'"
//. " -e 'set smtp_url=\"199.59.243.120::465\"'"
        //. " -e 'set ssl_starttls=yes'"
        //. " -e 'set smtp_url=\"smtp.orange.fr::465\"'"
        //. " -e 'set smtp_user=\"bruno.froger2\"'"
        //. " -e 'set smtp_pass=\"3paul2fan\"'"
        //. " -e 'set hostname=\"wanadoo.fr\"'"
$cmd = "mutt -H - -n "
        . " -e 'set content_type=\"text/html\"'"
        . " -e 'set copy=no'"
        . " -a downloads/$Filename"
        . " < /tmp/mail.txt";
//error_log("commande executee : $cmd");
$ficMail = shell_exec("more /tmp/mail.txt");
error_log("fichier mail : " . $ficMail);
shell_exec($cmd);



// reponse OK vers application android 

$result="OK";

$message = "photo traitée dans storePhoto2\n";

error_log("storePhoto2.php : fin OK");

echo $Requete . ":";
echo $result . ":";
echo $Pseudo . ":";
echo $Filename . ":";
echo $message;
exit;

