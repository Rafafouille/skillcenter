<?php
header('Content-type: application/json');
include_once('options.php');
include_once('fonctions.php');

initSession();


/* ******************************
	Programme pour ajax (avec réponse JSON)
****************************** */




//Format de la réponse
/* ***************************** */
$reponseJSON=initReponseJSON();

$action="";
if(isset($_POST['action'])) $action=$_POST['action'];


// =====================================================
// CONNECTION / SESSION
// =====================================================

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
		$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE login=:login  AND mdp = :mdp');
		$req->execute(array('login' => $login, 'mdp' => $mdp));
		if($donnees = $req->fetch())	//Si l'utilisateur est dans la BDD, avec le bon mot de passe
		{
			$_SESSION['nom']=$donnees['nom'];
			$_SESSION['prenom']=$donnees['prenom'];
			$_SESSION['statut']=$donnees['statut'];
			$_SESSION['id']=$donnees['id'];
			$reponseJSON["messageRetour"]=":)Vous êtes connecté. Bonjour ".$_SESSION['prenom']." ".$_SESSION['nom']." !";
		}
		else	//Si le couple (utilisateur<->mot de passe) n'est pas trouvé...
			$reponseJSON["messageRetour"]=":(L'identifiant ou le mot de passe est incorrect.";

		$req->closeCursor();//Fin des requêtes
	}
	else	//Si le mot de passe ou l'identifiant est vide
		$reponseJSON["messageRetour"]=":(L'identifiant ou le mot de passe est vide.";
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
	$reponseJSON["messageRetour"]=":)Vous êtes déconnecté. Au revoir !";
}


// =====================================================
// FONCTIONS GENERALES
// =====================================================

//Renvoie la liste des classes*************************
if($action=="getListeClasses")
{
	connectToBDD();
	$reponseJSON['listeClasses']=array();
	$reponse = $bdd->query('SELECT DISTINCT(classe) FROM utilisateurs WHERE classe<>""');
	while ($donnees = $reponse->fetch())
		array_push($reponseJSON["listeClasses"],$donnees["classe"]);
}

// =====================================================
// ADMINISTRATION UTILISATEURS
// =====================================================


//LISTE DES UTILISATEURS *****************************
if($action=="getUsersList")
{
	if($_SESSION['statut']=="admin")
	{
		//Critères de sélection
		$classe="[ALL]";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];

		$critere="";
		if($classe!="[ALL]")
			$critere=' WHERE classe="'.$classe.'"';

		//Requete SQL
		connectToBDD();
		$reponse = $bdd->query('SELECT * FROM utilisateurs'.$critere);
		$reponseJSON["listeUsers"]=array();
		while ($donnees = $reponse->fetch())
		{
			$tabUser=array();
			$tabUser['id']=$donnees['id'];
			$tabUser['nom']=$donnees['nom'];
			$tabUser['prenom']=$donnees['prenom'];
			$tabUser['login']=$donnees['login'];
			$tabUser['classe']=$donnees['classe'];
			$tabUser['statut']=$donnees['statut'];
			$tabUser['mail']=$donnees['mail'];
			$tabUser['notifieMail']=$donnees['notifieMail'];
			array_push($reponseJSON["listeUsers"],$tabUser);
			$reponseJSON["messageRetour"]=":XListe des utilisateurs récupérée";
		}
	}
	else//Si pas admin
		$reponseJSON["messageRetour"]=":(Vous ne pouvez pas récupérer la liste des utilisateurs";
}


//AJOUT D'UN UTILISATEUR*************************
if($action=="addUser")
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
		$req = $bdd->prepare('SELECT id FROM utilisateurs WHERE login=:login');
		$req->execute(array('login'=>$_POST['newUser_login']));
		if($donnees=$req->fetch())//Si le login existe déjà
			$reponseJSON["messageRetour"]=":(Le login \"".$_POST["newUser_login"]."\" existe déjà !";
		else
		{
			$req2 = $bdd->prepare('INSERT INTO utilisateurs(nom, prenom, login, mdp, classe) VALUES(:nom, :prenom, :login, :mdp, :classe)');
			$req2->execute($tableau);
			$reponseJSON["messageRetour"]=":)L'utilisateur << ".$_POST["newUser_prenom"]." ".$_POST['newUser_nom']." >> a bien été ajouté !";
		}
	}
	else
	{
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit d'ajouter un utilisateur !";
	}
}



//UPDATE UN UTILISATEUR*************************
if($action=="updateUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		//Tableau de valeurs
		$tableau=array(
				'nom' => $_POST["newUser_nom"],
				'prenom' => $_POST['newUser_prenom'],
				'login' => $_POST['newUser_login'],
				'mdp' => $_POST['newUser_psw'],
				'classe' => $_POST['newUser_classe'],
				'id'	=>	$_POST['id']
			);
		
		//Vérification que le login n'existe pas déja en cas de changement
		$reponseJSON["debug"]=$_POST['newUser_login'];
		$req = $bdd->prepare('SELECT * FROM utilisateurs WHERE login=":login"');// AND id<>:id');
		$req->execute($tableau);
		if($donnees=$req->fetch())
			$reponseJSON["messageRetour"]=":(Le nom d'utilisateur existe déjà";
		else
		{
			//Modifications
			if($_POST['newUser_psw']!="")//Si un nouveau mot de passe est proposé
				$req = $bdd->prepare('UPDATE utilisateurs SET nom=:nom, prenom=:prenom, mdp=:mdp login=:login, classe=:classe WHERE id=:id');
			else
				$req = $bdd->prepare('UPDATE utilisateurs SET nom=:nom, prenom=:prenom, login=:login, classe=:classe WHERE id=:id');
			$req->execute($tableau);
			$reponseJSON["messageRetour"]=":)L'utilisateur << ".$_POST["newUser_prenom"]." ".$_POST['newUser_nom']." >> a bien été mis à jour !";
		}
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de modifier un utilisateur !";
}




// =====================================================
// NOTATION
// =====================================================
if($action=="getListeEleves")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$classe="";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];
		if($classe!="")
		{
			$reponseJSON["classe"]=$classe;//On renvoie la classe (pour info)

			$req = $bdd->prepare('SELECT nom,prenom,id FROM utilisateurs WHERE classe=:classe');
			$req->execute(array('classe'=>$classe));
			while ($donnees = $req->fetch())
				{
					//echo "
					//	<option value='".$donnees['id']."'>".$donnees['nom']." ".$donnees['prenom']."</option>";
					$id=$donnees['id'];
					$nom=$donnees['nom'];
					$prenom=$donnees['prenom'];
					$reponseJSON["listeEleves"][$id]["id"]=$id;
					$reponseJSON["listeEleves"][$id]["nom"]=$nom;
					$reponseJSON["listeEleves"][$id]["prenom"]=$prenom;
				}
		}
		else
		{
			$reponseJSON["messageRetour"]=":(Classe vide";
		}
	}
	else
		$reponseJSON["messageRetour"]=":(Vous ne pouvez pas faire cette action !";
}




//OBTIENT LA NOTATION DES ELEVES=================
if($action=="getNotationEleves")
{
	$eleve=0;
	if(isset($_POST['eleve'])) $eleve=intval($_POST['eleve']);

	if($_SESSION['statut']=="admin" || $eleve==$_SESSION['id'] && $eleve>0)	//Si admin, ou utilisateur connecté qui demande sa propre notation
	{
		connectToBDD();


		//Recupere la classe de l'élève
		//$reqClasse = $bdd->query('SELECT classe FROM utilisateurs WHERE id='.$eleve);
		$reqClasse = $bdd->prepare('SELECT classe FROM utilisateurs WHERE id=:eleve');
		$reqClasse->execute(array('eleve'=>$eleve));
		
		if($donneesClasse=$reqClasse->fetch())//Si pas d'eleve selectionné
		{		

			$classe=$donneesClasse['classe'];
			
			$req_ind="(SELECT * FROM indicateurs AS i JOIN liensClassesIndicateurs AS l ON i.id=l.indicateur WHERE classe='".$classe."')";
			$req_comp_gr="(SELECT comp.id AS idComp, comp.nom AS nomComp, gr.id AS idGroup, gr.nom AS nomGroup FROM competences AS comp JOIN groupes_competences AS gr ON  comp.groupe=gr.id)";

			$requete="SELECT  E1.idComp,  E1.nomComp, E1.idGroup, E1.nomGroup, ind.id AS idInd, ind.nom AS nomInd, ind.details AS detailsInd, ind.niveaux AS niveauxInd FROM ".$req_ind." as ind JOIN ".$req_comp_gr." AS E1 ON ind.competence = E1.idComp";
			$req = $bdd->query($requete);

			while($reponse=$req->fetch())
			{
				$idGroup=intval($reponse['idGroup']);
				$nomGroup=$reponse['nomGroup'];

				$idComp=intval($reponse['idComp']);
				$nomComp=$reponse['nomComp'];

				$idInd=intval($reponse['idInd']);
				$nomInd=$reponse['nomInd'];
				$detailsInd=$reponse['detailsInd'];
				$niveauxInd=intval($reponse['niveauxInd']);

				//Si le groupe n'existe pas, on le crée
				if(!isset($reponseJSON['listeGroupes'][$idGroup]))
				{
					$reponseJSON['listeGroupes'][$idGroup]["id"]=$idGroup;
					$reponseJSON['listeGroupes'][$idGroup]["nom"]=$nomGroup;
					$reponseJSON['listeGroupes'][$idGroup]["selected"]=true;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"]=array();
				}
				//Si la compétence n'existe pas...
				if(!isset($reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]))
				{
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["id"]=$idComp;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["nom"]=$nomComp;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["selected"]=true;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"]=array();
				}

				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["id"]=$idInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["nom"]=$nomInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["details"]=$detailsInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauMax"]=$niveauxInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMax"]=-1;//Par defaut
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMoy"]=-1;//Par defaut
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveLast"]=-1;//Par defaut

				$reqNote = $bdd->prepare("SELECT MAX(note) as max FROM notation WHERE eleve=:eleve AND indicateur=".$idInd);
				$reqNote->execute(array('eleve'=>$eleve));
				if($donneesNote=$reqNote->fetch())
					{if($donneesNote["max"]==null) $donneesNote["max"]=-1;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMax"]=$donneesNote["max"];
					}

				$reqNote = $bdd->prepare("SELECT AVG(note) as moy FROM notation WHERE eleve=:eleve AND indicateur=".$idInd);
				$reqNote->execute(array('eleve'=>$eleve));
				if($donneesNote=$reqNote->fetch())
					{if($donneesNote["moy"]==null) $donneesNote["moy"]=-1;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMoy"]=$donneesNote["moy"];
					}

				$reqNote = $bdd->prepare("SELECT note as last FROM notation WHERE eleve=:eleve AND indicateur=".$idInd." ORDER BY date DESC LIMIT 1");
				$reqNote->execute(array('eleve'=>$eleve));
				if($donneesNote=$reqNote->fetch())
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveLast"]=$donneesNote["last"];


			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["selected"]=true;
			}	

			$reponseJSON["messageRetour"]=":)Evaluation récupérées.";
		}	//Fin 'si pas d'eleve'
		else
			$reponseJSON["messageRetour"]=":(Pas d'élève trouvé.";
	}
	else
	{
		$reponseJSON["messageRetour"]=":(Vous ne pouvez pas récupérer cette liste de notation.";
	}
}



// Action qui ajoute une nouvelle note **************************************
if($action=="newNote")
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
		
		//Ajoute de la note
		$req = $bdd->prepare('INSERT INTO notation (note,date,eleve,indicateur,examinateur) VALUES(:note,NOW(),:eleve,:indicateur,'.$_SESSION['id'].')');
		$req->execute(array(
						'note' => $note,
						'eleve' => $eleve,
						'indicateur' => $indicateur
					));
					
		//On récupère la note pour MAJ coté client
		$reponseJSON["note"]=getNotationPourJSON($eleve,$indicateur);
				
		$reponseJSON["messageRetour"]=":)La note a été ajoutée.";
	}
	else
	{
		$reponseJSON["messageRetour"]=":(Vous ne pouvez pas ajouter une note.";
	}
}





// =====================================================
// ADMINISTRATION COMPETENCES
// =====================================================


//Update liste des compétences
if($action=="updateCompetencesSelonClasse")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		
		$classe="";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];

		$reponseJSON['listeGroupes']=array();
		
		//if($classe!="[ALL]")
		//	$requete="SELECT  E1.idComp,  E1.nomComp, E1.idGroup, E1.nomGroup, ind.id AS idInd, ind.nom AS nomInd, ind.details AS detailsInd, ind.niveaux AS niveauxInd FROM (SELECT * FROM indicateurs AS i JOIN liensClassesIndicateurs AS l ON i.id=l.indicateur WHERE classe='".$classe."') as ind JOIN (SELECT co.id AS idComp, co.nom AS nomComp, gr.id AS idGroup, gr.nom AS nomGroup FROM competences AS co JOIN groupes_competences AS gr ON  co.groupe=gr.id) AS E1 ON ind.competence = E1.idComp";
		//else

		//NOUVEAU
		
		//Liste des groupes ******
		$reqGr = $bdd->query("SELECT * FROM groupes_competences ORDER BY position");
		while($reponseGr=$reqGr->fetch())
		{
			$idGroup=intval($reponseGr['id']);
			$nomGroup=$reponseGr['nom'];
			$positionGroup=intval($reponseGr['position']);
			
			$reponseJSON['listeGroupes'][$idGroup]["id"]=$idGroup;
			$reponseJSON['listeGroupes'][$idGroup]["nom"]=$nomGroup;
			$reponseJSON['listeGroupes'][$idGroup]["position"]=$positionGroup;
			$reponseJSON['listeGroupes'][$idGroup]["selected"]=false;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"]=array();
			
			//Liste des compétences ******
			$reqComp=$bdd->query("SELECT * FROM competences WHERE groupe=".$idGroup." ORDER BY position");
			while($reponseComp=$reqComp->fetch())
			{
				$idComp=intval($reponseComp['id']);
				$nomComp=$reponseComp['nom'];
				$positionComp=intval($reponseComp['position']);
				
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["id"]=$idComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["nom"]=$nomComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["position"]=$positionComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["selected"]=false;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"]=array();
				
				//Liste des indicateurs ******
				$reqInd=$bdd->query("SELECT * FROM indicateurs WHERE competence=".$idComp." ORDER BY position");
				while($reponseInd=$reqInd->fetch())
				{
					$idInd=intval($reponseInd['id']);
					$nomInd=$reponseInd['nom'];
					$detailsInd=$reponseInd['details'];
					$niveauxInd=intval($reponseInd['niveaux']);
					$positionInd=intval($reponseInd['position']);
					
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["id"]=$idInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["nom"]=$nomInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["details"]=$detailsInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveaux"]=$niveauxInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["position"]=$positionInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["selected"]=false;
				}
				
			}
		}


		//On tag celles qui sont dans la classe souhaitée

		$req_ind="(SELECT * FROM indicateurs AS i JOIN liensClassesIndicateurs AS l ON i.id=l.indicateur WHERE classe='".$classe."')";
		$req_comp_gr="(SELECT comp.id AS idComp, comp.nom AS nomComp, gr.id AS idGroup, gr.nom AS nomGroup FROM competences AS comp JOIN groupes_competences AS gr ON  comp.groupe=gr.id)";

		$requete="SELECT  E1.idComp,  E1.nomComp, E1.idGroup, E1.nomGroup, ind.id AS idInd, ind.nom AS nomInd, ind.details AS detailsInd, ind.niveaux AS niveauxInd FROM ".$req_ind." as ind JOIN ".$req_comp_gr." AS E1 ON ind.competence = E1.idComp";
		$req = $bdd->query($requete);
		while($reponse=$req->fetch())
		{
			$idGroup=intval($reponse['idGroup']);
			$idComp=intval($reponse['idComp']);
			$idInd=intval($reponse['idInd']);

			$reponseJSON['listeGroupes'][$idGroup]["selected"]=true;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["selected"]=true;
			$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["selected"]=true;
		}
		$reponseJSON["messageRetour"]=":)Liste des compétences récupérées";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit d'obtenir la liste des compétences !";

}




//AJOUT D'UN GROUPE=================
if($action=="addGroupeCompetences")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$nom="";
		if(isset($_POST['nom'])) $nom=$_POST['nom'];
		if($nom!="")
		{
			//Écriture
			$req = $bdd->prepare('INSERT INTO groupes_competences (nom) VALUES(:nom)');
			$req->execute(array('nom' => $nom));

			//Vérification
			$req2 =  $bdd->prepare('SELECT id FROM groupes_competences WHERE nom=:nom ORDER BY id DESC LIMIT 1');
			$req2->execute(array('nom' => $nom));

			if($donnees=$req2->fetch())
			{
				$reponseJSON["messageRetour"]=":)Le domaine ".$nom." a bien été créé.";
				$reponseJSON["groupe"]["nom"]=$nom;
				$reponseJSON["groupe"]["id"]=intval($donnees['id']);
			}
			else
				$reponseJSON["messageRetour"]=":(Le domaine n'a pas été enregistré pour une raison inconnue";
		}
		else
			$reponseJSON["messageRetour"]=":(Le nom du domaine est vide.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de créer un domaine.";
}



//AJOUT D'UNE COMPETENCE=================
if($action=="addCompetence")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$nom="";
		if(isset($_POST['nom'])) $nom=$_POST['nom'];
		$idGroupe=0;
		if(isset($_POST['idGroupe'])) $idGroupe=intval($_POST['idGroupe']);
		if($nom!="")
		{
			$req = $bdd->prepare('INSERT INTO competences (nom,groupe) VALUES(:nom,:idGroupe)');
			$req->execute(array(
						'nom' => $nom,
						'idGroupe' => $idGroupe
					));

			//Vérification
			$req2 =  $bdd->prepare('SELECT id FROM competences WHERE nom=:nom ORDER BY id DESC LIMIT 1');
			$req2->execute(array('nom' => $nom));

			if($donnees=$req2->fetch())
			{
				$reponseJSON["messageRetour"]=":)La compétence ".$nom." a bien été créé.";
				$reponseJSON["competence"]["nom"]=$nom;
				$reponseJSON["competence"]["id"]=intval($donnees['id']);
				$reponseJSON["competence"]["groupe"]=$idGroupe;
			}
			else
				$reponseJSON["messageRetour"]=":(La compétence n'a pas été enregistrée pour une raison inconnue";
		}
		else
			$reponseJSON["messageRetour"]=":(Le nom de la compétence est vide.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de créer une compétence.";
}



//AJOUT D'UN INDICATEUR =================
if($action=="addIndicateur")
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
		$classe="";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];
		
		if($nom!="")
		{
			$req = $bdd->prepare('INSERT INTO indicateurs (nom,details,niveaux,competence) VALUES(:nom,:details,:niveaux,:idCompetence)');
			$req->execute(array(
						'nom' => $nom,
						'details' => $details,
						'niveaux' => $niveaux,
						'idCompetence' => $idCompetence
					));
					
					
			//Vérification
			$req2 =  $bdd->prepare('SELECT id FROM indicateurs WHERE nom=:nom ORDER BY id DESC LIMIT 1');
			$req2->execute(array('nom' => $nom));

			if($donnees=$req2->fetch())
			{
				$reponseJSON["indicateur"]["nom"]=$nom;
				$reponseJSON["indicateur"]["details"]=$details;
				$reponseJSON["indicateur"]["niveaux"]=$niveaux;
				$reponseJSON["indicateur"]["id"]=intval($donnees['id']);
				$reponseJSON["indicateur"]["competence"]=$idCompetence;
				$reponseJSON["indicateur"]["selected"]=false; //Par défaut
				
				
				//Ajout du lien du nouvel indicateur avec la classe sélectionnée
				if($classe!="")
				{
					$requeteLier = $bdd->prepare('INSERT INTO liensClassesIndicateurs(indicateur, classe) VALUES(:indicateur, :classe)');
					$requeteLier->execute(array('indicateur' => intval($donnees['id']), 'classe' => $classe));
					$reponseJSON["indicateur"]["selected"]=true;
				}
				
				$reponseJSON["messageRetour"]=":)L'indicateur ".$nom." a bien été créé.";
				
			}
			else
				$reponseJSON["messageRetour"]=":(L'indicateur n'a pas été enregistrée pour une raison inconnue";
					
		}
		else
			$reponseJSON["messageRetour"]=":(Le nom de l'indicateur est vide.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de créer un indicateur.";
}



//SUPPRESSION D'UN INDICATEUR =================
if($action=="supprimeIndicateur")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		
		$idIndicateur=0;
		if(isset($_POST['idIndicateur'])) $idIndicateur=intval($_POST['idIndicateur']);

		
		if($idIndicateur!=0)
		{
			$req = $bdd->prepare('DELETE FROM indicateurs WHERE id=:idIndicateur');
			$req->execute(array('idIndicateur' => $idIndicateur));
			$reponseJSON["messageRetour"]=":)L'indicateur a bien été supprimé.";
			$reponseJSON["indicateur"]["id"]=$idIndicateur;
		}
		else
			$reponseJSON["messageRetour"]=":(Aucun indicateur à supprimer n'a été transmis.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de supprimer un indicateur.";
}
	
	




//Update liste des compétences
if($action=="lierDelierIndicateurClasse")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$classe="";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];
		$indicateur=0;
		if(isset($_POST['indicateur'])) $indicateur=intval($_POST['indicateur']);
		$lier="";
		if(isset($_POST['lier'])) $lier=$_POST['lier'];

		$reponseJSON["indicateur"]=$indicateur;
		$reponseJSON["classe"]=$classe;

		if($classe!="" and $indicateur!=0 and $lier!="")
		{
			if($lier=="true")
			{
				$requete = $bdd->prepare('INSERT INTO liensClassesIndicateurs(indicateur, classe) VALUES(:indicateur, :classe)');
				$requete->execute(array('indicateur' => $indicateur, 'classe' => $classe));
				$reponseJSON["messageRetour"]=":)Lier";
				$reponseJSON["lier"]=true;
			}
			else	
			{
				$requete = $bdd->prepare('DELETE FROM liensClassesIndicateurs WHERE indicateur=:indicateur AND classe=:classe');
				$requete->execute(array('indicateur' => $indicateur, 'classe' => $classe));
				$reponseJSON["messageRetour"]=":)Délier";
				$reponseJSON["lier"]=false;
			}
		}
		else
		{
			$reponseJSON["messageRetour"]=":(Il manque des infos dans la liaison indicateur<->classe !";
		}
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de lier/délier une compétence !";

}



echo json_encode($reponseJSON);

?>
