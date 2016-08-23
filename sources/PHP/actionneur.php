<?php
include_once('options.php');
include_once('fonctions.php');

initSession();



/* ******************************
	Programme pour ajax
****************************** */

$action="";
if(isset($_POST['action'])) $action=$_POST['action'];


/* ====================================================
			LOG / UNLOG
=======================================================*/

//CONNECTION =================
// Action qui connecte (i.e. met à jour les variables de session)
// si le couple (utilisateur/mot de passe) est dans la BDD
if($action=="login")
{
	connectToBDD();
	
	//On récupère les variables envoyées
	$login="";
	if(isset($_POST['login'])) $login=$_POST['login'];
	$mdp="";
	if(isset($_POST['mdp'])) $mdp=$_POST['mdp'];
	
	if($login!="" && $mdp!="")	//Si les paramètres ne sont pas vides
	{
		$req = $bdd->prepare('SELECT * FROM '.$BDD_PREFIXE.'utilisateurs WHERE login=:login  AND mdp = :mdp');
		$req->execute(array('login' => $login, 'mdp' => $mdp));
		if($donnees = $req->fetch())	//Si l'utilisateur est dans la BDD, avec le bon mot de passe
		{
			$_SESSION['nom']=$donnees['nom'];
			$_SESSION['prenom']=$donnees['prenom'];
			$_SESSION['statut']=$donnees['statut'];
			$_SESSION['id']=$donnees['id'];
			echo ":)Vous êtes connecté. Bonjour !";
		}
		else	//Si le couple (utilisateur<->mot de passe) n'est pas trouvé...
			echo ":(L'identifiant ou le mot de passe est incorrect.";

		$req->closeCursor();//Fin des requêtes
	}
	else	//Si le mot de passe ou l'identifiant est vide
		echo ":(L'identifiant ou le mot de passe est vide.";
}

// LOGOUT ============================================
// Action qui délogue (met à jour les variables de session)
// l'utilisateur
if($action=="logout")
{
	$_SESSION['nom']="";
	$_SESSION['prenom']="";
	$_SESSION['statut']="";
	$_SESSION['id']=0;
	echo ":)Vous êtes déconnecté. Au revoir !";
}


/* ===================================================
			GESTION DES UTILISATEURS
=======================================================*/

//AJOUT D'UN UTILISATEUR=================
/*if($action=="addUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$tableau=array(
				'nom' => $_POST["newUser_nom"],
				'prenom' => $_POST['newUser_prenom'],
				'login' => $_POST['newUser_login'],
				'mdp' => $_POST['newUser_psw'],
				'classe' => $_POST['newUser_classe']
			);
		$req = $bdd->prepare('SELECT id FROM '.$BDD_PREFIXE.'utilisateurs WHERE login=:login');
		$req->execute(array('login'=>$_POST['newUser_login']));
		if($donnees=$req->fetch())//Si le login existe déjà
			echo ":(Le login \"".$_POST["newUser_login"]."\" existe déjà !";
		else
		{
			$req2 = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'utilisateurs(nom, prenom, login, mdp, classe) VALUES(:nom, :prenom, :login, :mdp, :classe)');
			$req2->execute($tableau);
			echo ":)L'utilisateur << ".$_POST["newUser_prenom"]." ".$_POST['newUser_nom']." >> a bien été ajouté !";
		}
	}
	else
	{
		echo ":(Vous n'avez pas le droit d'ajouter un utilisateur !";
	}
}*/

//SUPPRESSION D'UN UTILISATEUR=================
/*if($action=="delUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$id=0;
		if(isset($_POST['id'])) $id=$_POST['id'];
		if($id)
		{
			if($id!=$_SESSION['id'])
			{
				$req = $bdd->prepare('DELETE FROM '.$BDD_PREFIXE.'utilisateurs WHERE id = :id');
				$req->execute(array(
							'id' => $id
						));
				echo ":)L'utilisateur n°".$id." a bien été supprimé.";
			}
			else
				echo ":(Vous ne pouvez pas modifier vous supprimer vous même.";
		}
		else
			echo ":(Aucune utilisateur à supprimer.";
	}
	else
		echo ":(Vous n'avez pas le droit de supprimer un utilisateur.";
}*/


//UPGRADE UN UTILISATEUR=================
/*if($action=="upgradeUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$id=0;
		if(isset($_POST['id'])) $id=$_POST['id'];
		if($id)
		{
			if($id!=$_SESSION['id'])
			{
				$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'utilisateurs SET statut="admin" WHERE id = :id');
				$req->execute(array(
							'id' => $id
						));
				echo ":)Le statut de l'utilisateur n°".$id." a bien été augmenté.";
			}
			else
				echo ":(Vous ne pouvez pas modifier votre propre statut.";
		}
		else
			echo ":(Aucune utilisateur à modifier.";
	}
	else
		echo ":(Vous n'avez pas le droit de modifier le statut d'un utilisateur.";
}*/

//DOWNGRADE UN UTILISATEUR=================
/*if($action=="downgradeUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$id=0;
		if(isset($_POST['id'])) $id=$_POST['id'];
		if($id)
		{
			if($id!=$_SESSION['id'])
			{
				$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'utilisateurs SET statut="" WHERE id = :id');
				$req->execute(array(
							'id' => $id
						));
				echo ":)Le statut de l'utilisateur n°".$id." a bien été diminué.";
			}
			else
				echo ":(Vous ne pouvez pas modifier votre propre statut.";
		}
		else
			echo ":(Aucune utilisateur à modifier.";
	}
	else
		echo ":(Vous n'avez pas le droit de modifier le statut d'un utilisateur.";
}*/



//AJOUT D'UN INDICATEUR=================
/*if($action=="addIndicateur")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$nom="";
		if(isset($_POST['nom'])) $nom=$_POST['nom'];
		$details="";
		if(isset($_POST['details'])) $details=$_POST['details'];
		$niveaux=1;
		if(isset($_POST['niveaux'])) $niveaux=intval($_POST['niveaux']);
		$idCompetence=0;
		if(isset($_POST['idCompetence'])) $idCompetence=intval($_POST['idCompetence']);
		if($nom!="")
		{
			$req = $bdd->prepare('INSERT INTO indicateurs (nom,details,niveaux,competence) VALUES(:nom,:details,:niveaux,:idCompetence)');
			$req->execute(array(
						'nom' => $nom,
						'details' => $details,
						'niveaux' => $niveaux,
						'idCompetence' => $idCompetence
					));
			echo ":)L'indicateur ".$nom." a bien été créé.";
		}
		else
			echo ":(Le nom de l'indicateur est vide.";
	}
	else
		echo ":(Vous n'avez pas le droit de créer un indicateur.";
}*/




//OBTIENT LA NOTATION DES ELEVES=================
if($action=="getNotationEleves")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$eleve=0;
		if(isset($_POST['eleve'])) $eleve=intval($_POST['eleve']);
		
		if($eleve)//Si pas d'eleve selectionné
		{		
			//Recupere la liste des groupes
			$numCompetence=1;
			$reponseGr = $bdd->query('SELECT * FROM '.$BDD_PREFIXE.'groupes_competences ORDER BY position');
			while ($donneesGr = $reponseGr->fetch())
			{
			echo '
							<div class="groupe_competences">
								<div class="entete_groupe_competences">
									<h3 onclick="$(this).parent().parent().find(\'.groupe_contenu\').toggle(\'easings\');">
										'.$donneesGr['nom'].'
									</h3>
								</div>
								<div class="groupe_contenu">';
				$numIndicateur=1;
				$reponseComp = $bdd->query('SELECT * FROM '.$BDD_PREFIXE.'competences WHERE groupe='.$donneesGr['id'].' ORDER BY position');
				while ($donneesComp = $reponseComp->fetch())
					{
						echo '
									<div class="competence">
										<h3>'.$numCompetence++." - ".$donneesComp["nom"].'</h3>
										<div class="listeIndicateurs">
											<table class="indicateurs">';
											$reponseInd = $bdd->query('SELECT * FROM '.$BDD_PREFIXE.'indicateurs WHERE competence='.$donneesComp['id'].' ORDER BY position');
											while ($donneesInd = $reponseInd->fetch())
											{
												echo '
											<tr>
												<td class="intituleIndicateur">
													'.($numCompetence-1).".".$numIndicateur." - ".$donneesInd['nom'].'
												</td>
												<td class="detailIndicateur">
													<img src="./sources/images/icone-info.png" alt="[i]"  style="cursor:help;" title="'.$donneesInd['details'].'"/>
												</td>
												<td class="niveauxIndicateur">';
													$note=getNoteMax($eleve,$donneesInd['id']);
													printEchelleCouleur($note,$donneesInd["niveaux"],true,$donneesInd['id']);
												echo '
												</td>
											</tr>';
											}
						echo '
											</table>
										</div>
									</div>';
					}
			echo '
								</div>
							</div>';
			}
		}	//Fin 'si pas d'eleve'
		else
			echo ":(Pas d'élève trouvé-.";
	}
	else
	{
		echo ":(Vous ne pouvez pas récupérer cette liste de notation.";
	}
}



//NOUVELLE NOTE =================
/*if($action=="newNote")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$eleve=0;
		if(isset($_POST['eleve'])) $eleve=intval($_POST['eleve']);
		$indicateur=0;
		if(isset($_POST['indicateur'])) $indicateur=intval($_POST['indicateur']);
		$note=0;
		if(isset($_POST['note'])) $note=intval($_POST['note']);
		
		$req = $bdd->prepare('INSERT INTO notation (note,date,eleve,indicateur,examinateur) VALUES(:note,NOW(),:eleve,:indicateur,'.$_SESSION['id'].')');
		$req->execute(array(
						'note' => $note,
						'eleve' => $eleve,
						'indicateur' => $indicateur
					));
		echo ":)La note a été ajoutée.";
	}
	else
	{
		echo ":(Vous ne pouvez pas ajouter une note.";
	}
}*/
?>
