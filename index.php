<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
$myIncludePath = '/var/www/html/dictacloud';
set_include_path(get_include_path() . PATH_SEPARATOR . $myIncludePath); 

//error_log( "debut index.php");

//Make sure that it is a POST request.
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    throw new Exception('Request method must be POST!');
    error_log("Request method must be POST!");
}

$vide=" ";

//error_log("etape 1 index.php");

//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
$contentTypeJsonAttendu = "application/json; charset=utf-8";
$contentTypeImageAttendu = "application/x-www-form-urlencoded; charset=UTF-8";

//error_log("content-type = " . $contentType);

if(strcasecmp($contentType, $contentTypeJsonAttendu) != 0){
    //check if the content type of the POST request is set to image/jpeg
    if(strcasecmp($contentType, $contentTypeImageAttendu) != 0){
        throw new Exception('if not POST ; Content type must be: ' . $contentTypeImageAttendu);
        error_log("if not POST ; Content type must be: " . $contentTypeImageAttendu);
    }else{
        // c'est une image on la stocke
        //$content = trim(file_get_contents("php://input"));
        //error_log("c'est une image qui a ete envoyer, il faut la traiter");
        // TODO enregistrer sur disque
        if (isset($_POST['PSEUDO'])){
            $Pseudo = $_POST{"PSEUDO"};
            $_SESSION['PSEUDO'] = $Pseudo;
            //error_log("index.php : Pseudo = " . $Pseudo);
        }

        if (isset($_POST['REQUETE'])){
            $Requete = $_POST{"REQUETE"};
            $_SESSION['REQUETE'] = $Requete;
            //error_log("index.php : Requete = " . $Requete);
        }else{
            $Requete = $vide;
        }
        if (isset($_POST["TREATMENT"])){
            $Treatment = $_POST["TREATMENT"];
            $_SESSION['TREATMENT'] = $Treatment;
            //error_log("index.php : traitement => " . $Treatment);
        }else{
            $Treatment = $vide;
        }

        if (isset($_POST['FILENAME'])){
            $Filename = $_POST['FILENAME'];
            $_SESSION['FILENAME'] = $Filename; 
            //error_log("index.php : Filename = " . $Filename);
        }else{
            $Filename = $vide;
        }

        if (isset($_POST['IMAGE'])) {
            if ($_POST['IMAGE'] != "" && $Filename != $vide) {
                $imageData = $_POST['IMAGE'];
                $ficHandle = fopen("downloads/". $Filename,"w");
                fwrite($ficHandle,base64_decode($imageData));
                //error_log("index.php ; image sauvegardee ; taille = " . strlen($imageData));
                if (strlen($imageData) > 0){
                    include_once 'controleurs/storePhoto2.php';
                }
                exit;
            } 
        } else {
            $Image = $vide;
            unset($_SESSION['IMAGE']);
        }

        if ($Requete != $vide){
            //error_log("index.php : analyse de la requete " . $Requete);
            switch ($Requete){
                //*********************    
                //**    liste
                //*********************    
                case 'liste':
                    //error_log("index.php : liste des fichiers");
                    include_once 'controleurs/liste.php';
                    break;
                //*********************    
                //**    SendFileByEmail
                //*********************    
                case 'SendFileByEmail':
                    //error_log("index.php : SendFileByEmail");
                    include_once 'controleurs/sendFileByEmail.php';
                    break;
                //*********************    
                //**    RemoveFileOnServer
                //*********************    
                case 'RemoveFileOnServer':
                    //error_log("index.php : RemoveFileOnServer");
                    include_once 'controleurs/RemoveFileOnServer.php';
                    break;
                //*********************
                //**    send Audio
                //*********************
                case 'sendAudio':
                    //error_log("index.php lancement de storeAudio avec " . $Requete);
                    include_once 'controleurs/storeAudio.php';
                    break;
                //*********************
                //**    default
                //*********************
                default:
                    error_log("index.php requete inconnue : " . $Requete);
                    break;
                }
            }
        exit;
    }
    //throw new Exception('Content type must be: application/json');
    error_log("Content type must be: " . $contentTypeJsonAttendu);    
}
 
//error_log("etape 2 index.php");

//Receive the RAW post data.
$content = trim(file_get_contents("php://input"));
 
//Attempt to decode the incoming RAW post data from JSON.
$decoded = json_decode($content, true);
 
//If json_decode failed, the JSON is invalid.
if(!is_array($decoded)){
    throw new Exception('Received content contained invalid JSON!');
    //echo "Received content contained invalid JSON!\n";
}

$decoded = json_decode($content);

if (array_key_exists("REQUETE",$decoded)){
    $Requete = $decoded->{"REQUETE"};
    $_SESSION['REQUETE'] = $Requete;
}else{
    $Requete = $vide;
}

if (array_key_exists("TREATMENT",$decoded)){
    $Treatment = $decoded->{"TREATMENT"};
    $_SESSION['TREATMENT'] = $Treatment;

}else{
    $Treatment = $vide;
}
//error_log("index.php : traitement => " . $Treatment);

if (array_key_exists("PSEUDO",$decoded)){
    $Pseudo = $decoded->{"PSEUDO"};
    $_SESSION['PSEUDO'] = $Pseudo;
}else{
    $Pseudo = $vide;
}
if (array_key_exists("EMAIL",$decoded)){
    $Email = $decoded->{"EMAIL"};
    $_SESSION['EMAIL'] = $Email;
}else{
    $Email = $vide;
}
if (array_key_exists("PASSWD",$decoded)){
    $Passwd = $decoded->{"PASSWD"};
    $_SESSION['PASSWD'] = $Passwd;
}else{
    $Passwd = $vide;
}
if (array_key_exists("FILENAME",$decoded)){
    $Filename = $decoded->{"FILENAME"};
    $_SESSION['FILENAME'] = $Filename;
}else{
    $Filename = $vide;
}


error_log("requete  = ". $Requete . "\n");
error_log("pseudo   = ". $Pseudo . "\n");
//error_log("email    = ". $Email . "\n");
//error_log("passwd   = ". $Passwd . "\n");
//error_log("filename = ". $Filename . "\n");

//echo "====================\n";
//echo "serveur Dictacloud\n";
//echo "--------------------\n";
//echo "Analyse parametres :\n";

include_once ('modeles/Users/ClassUsers.php');

//error_log("[" . $Requete . ":" . $Pseudo . ":" . $Email . ":" . $Passwd . "]\n");

//echo "\n--------------------\n";

// contruction de la reponse

//echo "traitement des commandes\n";

$user =  new User($Pseudo, $Email, $Passwd);

if ($Requete != ""){
    // analyse de la requete
    switch ($Requete){
        //*********************    
        //**    register
        //*********************    
        case "register":
            if ($Pseudo == ""  || $Passwd == ""){
                $result="Erreur => register => manque parametre"; 
            }else{
                //error_log("register => " . $Pseudo . "\n");
                if ( ! $user->checkPseudo($Pseudo)){
                    $result="Erreur => register => pseudo inconnu";
                } else if ( ! $user->checkPasswd($Pseudo,$Passwd)){
                    $result="Erreur => register => mot de passe invalide";
                } else {
                    //error_log("submit register");
                    $result="OK";
                }            
            }
            $user->result($Requete,$result);
            break; 
        //*********************    
        //**    unregister
        //*********************    
        case "unregister":
            if ($Pseudo == ""){
                $result="Erreur => register => manque parametre"; 
            }else{
                //error_log("unregister => " . $Pseudo . "\n");
                if ( ! $user->checkPseudo($Pseudo)){
                    $result="Erreur => unregister => pseudo inconnu";
                } else {
                    //error_log("exec unregister\n");
                    $result="OK";
                }            
            }
            $user->result($Requete,$result);
            break; 
        //*********************    
        //**    subscribe
        //*********************    
        case 'subsribe':
            if ($Pseudo == "" || $Email == "" || $Passwd == ""){
                $result="Erreur => subscribe => manque parametre";
            }else{
                //error_log("subscribe => " . $Pseudo . "\n");
                //$user->display();
                if ($user->checkEmail($Email)){
                    $result="Erreur => subscribe => l'email " . $Email . " existe deja";
                } else if ($user->checkPseudo($Pseudo)){
                    $result="Erreur => subscribe => le pseudo " . $Pseudo . " existe deja";
                }  else if ($Pseudo == ""){
                    $result="Erreur => subscribe => le passwd  n'est pas valide";
                } else {
                    //error_log("subsribe new user\n");
                    //$user->display();
                    $user->create();
                    $result="OK";
                }            
            }
            $user->result($Requete,$result);
            break; 
        //*********************    
        //**    unsubscribe
        //*********************    
        case 'unsubsribe':
            if ($Pseudo == ""){
                $result="Erreur => unsubscribe => manque parametre";
            }else if (!$user->checkPseudo($Pseudo)){
                $result="Erreur => unsubscribe => le pseudo " . $Pseudo . " n'existe pas";
            } else if (!$user->checkPasswd($Pseudo,$Passwd)){
                $result="Erreur => unsubscribe => le passwd " . $Passwd . " n'est pas vadlide";
            } else {
                $result="OK";
                $user->delete();
            }
            $user->result($Requete,$result);
            break; 
        //*********************    
        //**    send Photo
        //*********************    
        case 'sendPhoto':
            include_once 'controleurs/storePhoto2.php';
            break;
        //*********************
        //**    liste
        //*********************    
        case 'liste':
            //error_log("liste des fichiers");
            include_once 'controleurs/liste.php';
            break;
        //*********************    
        //**    SendFileByEmail
        //*********************    
        case 'SendFileByEmail':
            error_log("SendFileByEmail");
            //include_once 'controleurs/sendFileByEmail.php';
            break;
        //*********************    
        //**    RemoveFileOnServer
        //*********************    
        case 'RemoveFileOnServer':
            error_log("RemoveFileOnServer");
            //include_once 'controleurs/RemoveFileOnServer.php';
            break;
        //*********************    
        //**    default
        //*********************    
        default:
            $result="Error => requete [" . $Requete . "] inconnue";
            $user->result($Requete,$result);
            break;
    }
}
