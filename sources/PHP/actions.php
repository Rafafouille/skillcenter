<?php
include_once('options.php');



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
