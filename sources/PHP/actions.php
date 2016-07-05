<?php
include_once('options.php');

// ==============================================
// LOG
/*if($action=="loggin")
{
	if($loginBoxLogin!="" && $loginBoxPwd!="")
	{
		$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE login=:login  AND mdp = :mdp');
		$req->execute(array('login' => $loginBoxLogin, 'mdp' => $loginBoxPwd));
		if($donnees = $req->fetch())
		{
			$_SESSION['nom']=$donnees['nom'];
			$_SESSION['prenom']=$donnees['prenom'];
			$_SESSION['statut']=$donnees['statut'];
			$_SESSION['id']=$donnees['id'];
			$messageRetour=":)Vous êtes connecté. Bonjour !";
		}
		else
		{$messageRetour=":(L'identifiant ou le mot de passe est incorrect.";}

		$req->closeCursor();
	}
	else
	{$messageRetour=":(L'identifiant ou le mot de passe est vide.";}
}


// ==============================================
// UNLOG
if($action=="unlog")
{
	$_SESSION['nom']="";
	$_SESSION['prenom']="";
	$_SESSION['statut']="";
	$messageRetour=":)Vous êtes déconnecté. Au revoir !";
}
*/

// ==============================================
// Ajoute nouvel utilisateurs
if($action=="newUser")
{
	if($_SESSION['statut']=="admin")
	{
		$req = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'utilisateurs(nom, prenom, login, mdp, classe) VALUES(:nom, :prenom, :login, :mdp, :classe)');
		$req->execute(array(
				'nom' => $_POST["newUser_nom"],
				'prenom' => $_POST['newUser_prenom'],
				'login' => $_POST['newUser_login'],
				'mdp' => $_POST['newUser_psw'],
				'classe' => $_POST['newUser_classe']
			));

		$messageRetour=":)L'utilisateur \"".$_POST["newUser_prenom"]." ".$_POST['newUser_nom']."\" a bien été ajouté !";
	}
	else
	{
		$messageRetour=":(Vous n'avez pas le droit d'ajouter un utilisateur !";
	}
}


?>