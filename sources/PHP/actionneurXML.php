<?php
include_once('options.php');
include_once('fonctions.php');

initSession();


/* ******************************
	Programme pour ajax (avec réponse XML)
****************************** */




//Format de la réponse
/* ***************************** */



$reponseXML=initReponseXML();

$action="";
if(isset($_POST['action'])) $action=$_POST['action'];


if($action=="getUsersList")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$reponse = $bdd->query('SELECT * FROM utilisateurs');
		while ($donnees = $reponse->fetch())
		{
			$user=$reponseXML->addChild("utilisateur");
			$user->addChild("id",$donnees['id']);
			$user->addChild("nom",$donnees['nom']);
			$user->addChild("prenom",$donnees['prenom']);
			$user->addChild("login",$donnees['login']);
			$user->addChild("classe",$donnees['classe']);
			$user->addChild("statut",$donnees['statut']);
			$user->addChild("mail",$donnees['mail']);
			$user->addChild("notifieMail",$donnees['notifieMail']);
			$reponseXML->messageRetour=":)Liste des utilisateurs récupérée";
		}
	}
	else//Si pas admin
		$reponseXML->messageRetour=":(Vous ne pouvez pas récupérer la liste des utilisateurs";
}

echo $reponseXML->asXML();;

?>