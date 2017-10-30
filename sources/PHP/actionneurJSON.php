<?php
header('Content-type: application/json');
include_once('options.php');
include_once('fonctions.php');
require_once './biblio_php/PHPMailer-5.2.18/PHPMailerAutoload.php';	//Bibliotheque d'envoi de mail
initSession();


$SALT="$232#;E";//Grain de sel pour le hachage du mot de passe

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
	$loginLower="";
	if(isset($_POST['login']))
			{
				$loginLower=strtolower($_POST['login']);
				$login=$_POST['login'];
			}
	$mdp="";
	if(isset($_POST['mdp'])) $mdp=$_POST['mdp'];
	
	if($login!="" && $mdp!="")	//Si les paramètres ne sont pas vides
	{
		usleep(300000);//Pause pour ne pas tester 500000 mot de passe par seconde (3/seconde, max)
		$req = $bdd->prepare('SELECT * FROM '.$BDD_PREFIXE.'utilisateurs WHERE (login=:login OR login=:loginlower) AND (mdp = :mdp OR mdp=:mdpCrypt)');
		$req->execute(array('login' => $login, 'loginlower' => $loginLower, 'mdp' => $mdp, 'mdpCrypt' => crypt($mdp,$SALT)));
		if($donnees = $req->fetch())	//Si l'utilisateur est dans la BDD, avec le bon mot de passe
		{
			$_SESSION['nom']=$donnees['nom'];
			$_SESSION['prenom']=$donnees['prenom'];
			$_SESSION['statut']=$donnees['statut'];
			$_SESSION['id']=$donnees['id'];
			$_SESSION['classe']=$donnees['classe'];
			$reponseJSON["messageRetour"]=":)Vous êtes connecté. Bonjour ".$_SESSION['prenom']." ".$_SESSION['nom']." !";

			//MAJ date de connexion
			$req2=$bdd->prepare("UPDATE ".$BDD_PREFIXE."utilisateurs SET derniere_connexion=NOW() WHERE id=:id");
			$req2->execute(array('id'=>$_SESSION['id']));

			//Badges
			if($AUTORISE_BADGES)
				updateBadges_aLaConnexion($_SESSION['id']);
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
	$_SESSION['classe']="";
	$reponseJSON["messageRetour"]=":)Vous êtes déconnecté. Au revoir !";
}


// TESTE LA CONNECTION ==============================
// Action qui teste régulièrement si on est connecte ou pas.
if($action=="testeConnecte")
{
	if($_SESSION['id'])
		$reponseJSON["messageRetour"]=":X";
	else
		$reponseJSON["messageRetour"]=":|Vous êtes déconnectés du serveur";

	$reponseJSON["connecte"]=$_SESSION['id']!=0;
}


// =====================================================
// FONCTIONS GENERALES
// =====================================================

//Renvoie la liste des classes*************************
if($action=="getListeClasses")
{
	if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur")
	{
		connectToBDD();
		$reponseJSON['listeClasses']=array();
		$reponse = $bdd->query('SELECT DISTINCT(classe) FROM '.$BDD_PREFIXE.'utilisateurs WHERE classe<>""');
		while ($donnees = $reponse->fetch())
			array_push($reponseJSON["listeClasses"],$donnees["classe"]);
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de récupérer la liste des classes.";
	
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
		$reponse = $bdd->query('SELECT * FROM '.$BDD_PREFIXE.'utilisateurs'.$critere.' ORDER BY classe,nom');
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
				'nom' => strtoupper($_POST["newUser_nom"]),
				'prenom' => ucwords($_POST['newUser_prenom']),
				'login' => $_POST['newUser_login'],
				'mdp' => crypt($_POST['newUser_psw'],$SALT),//Le 2ème paramètre : voir la fonction crypt (salt (pas très efficace si constant... mais tant pis)
				'classe' => strtoupper($_POST['newUser_classe']),
				'mail' => $_POST['newUser_mail'],
				'notifieMail' => $_POST['newUser_notifieMail']
			);
		$req = $bdd->prepare('SELECT id FROM '.$BDD_PREFIXE.'utilisateurs WHERE login=:login');
		$req->execute(array('login'=>$_POST['newUser_login']));
		if($donnees=$req->fetch())//Si le login existe déjà
			$reponseJSON["messageRetour"]=":(Le login \"".$_POST["newUser_login"]."\" existe déjà !";
		else
		{
			$req2 = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'utilisateurs(nom, prenom, login, mdp, classe,mail,notifieMail) VALUES(:nom, :prenom, :login, :mdp, :classe, :mail, :notifieMail)');
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
		
		//Vérification que le login n'existe pas déja en cas de changement
		$reponseJSON["debug"]=$_POST['newUser_login'];
		$req = $bdd->prepare('SELECT * FROM '.$BDD_PREFIXE.'utilisateurs WHERE login=":login"');// AND id<>:id');
		$req->execute(array('login' => $_POST['newUser_login']));
		if($donnees=$req->fetch())
			$reponseJSON["messageRetour"]=":(Le nom d'utilisateur existe déjà";
		else
		{
			//Modifications
			if($_POST['newUser_psw']!="")//Si un nouveau mot de passe est proposé
			{
				$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'utilisateurs SET nom=:nom, prenom=:prenom, mdp=:mdp, login=:login, classe=:classe, mail=:mail, notifieMail=:notifieMail WHERE id=:id');
				$req->execute(array(
						'nom' => $_POST["newUser_nom"],
						'prenom' => $_POST['newUser_prenom'],
						'login' => $_POST['newUser_login'],
						'mdp' => crypt($_POST['newUser_psw'],$SALT),
						'classe' => $_POST['newUser_classe'],
						'mail' => $_POST['newUser_mail'],
						'notifieMail' => $_POST['newUser_notifieMail'],
						'id'	=>	$_POST['id']
					));
			}
			else
			{
				$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'utilisateurs SET nom=:nom, prenom=:prenom, login=:login, classe=:classe, mail=:mail, notifieMail=:notifieMail WHERE id=:id');
				$req->execute(array(
						'nom' => $_POST["newUser_nom"],
						'prenom' => $_POST['newUser_prenom'],
						'login' => $_POST['newUser_login'],
						'classe' => $_POST['newUser_classe'],
						'mail' => $_POST['newUser_mail'],
						'notifieMail' => $_POST['newUser_notifieMail'],
						'id'	=>	$_POST['id']
					));
			}
			$reponseJSON["messageRetour"]=":XL'utilisateur << ".$_POST["newUser_prenom"]." ".$_POST['newUser_nom']." >> a bien été mis à jour !";
		}
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de modifier un utilisateur !";
}



//CHANGE LE STATUT D'UN UTILISATEUR*************************
if($action=="changeStatutUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();

		$id=-1;
		if(isset($_POST['id'])) $id=$_POST['id'];

		if($id>-1)
		{
			$reqSt = $bdd->prepare('SELECT statut FROM '.$BDD_PREFIXE.'utilisateurs WHERE id=:id');
			$reqSt->execute(array('id'=>$id));
			if($donneesSt=$reqSt->fetch())
			{
				$statut=$donneesSt["statut"]; //On récupère le statut précédent

				$tabl_rotations_statuts=array(	""=>"autoeval",
							"autoeval"=>"evaluateur",
							"evaluateur"=>"admin",
							"admin"=>""
						);

				$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'utilisateurs SET statut=:statut WHERE id=:id');
				$req->execute(array(
					"id"=>$id,
					"statut"=>$tabl_rotations_statuts[$statut]
					));
				$reponseJSON["messageRetour"]=":XLe statut a bien été mis à jour";
				$reponseJSON["user"]["statut"]=$tabl_rotations_statuts[$statut];
				$reponseJSON["user"]["id"]=$id;
			}
			else
				$reponseJSON["messageRetour"]=":(L'utilisateur ".$id." (dont on veut changer le statut) n'a pas été trouvé !";
		}
		else
			$reponseJSON["messageRetour"]=":(Aucun utilisateur transmis pour la modification de statut !";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de modifier le statut d'un utilisateur !";
}



//SUPPRIME UN UTILISATEUR*************************
if($action=="delUser")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$id=isset($_POST['id'])?$_POST['id']:0;
		$suppEval=isset($_POST['suppEval'])?$_POST['suppEval']=="true":false;

		
		$reponseJSON["debug"]=$suppEval;

		if($id)
		{
			if($id!=$_SESSION['id'])
			{
				$req = $bdd->prepare('DELETE FROM '.$BDD_PREFIXE.'utilisateurs WHERE id = :id');
				$req->execute(array(
							'id' => $id
						));

				if($suppEval)//Si on supprime AUSSI les evaluations
				{
						$req = $bdd->prepare('SELECT COUNT(*) AS nb FROM '.$BDD_PREFIXE.'notation WHERE eleve = :id');
						$req->execute(array('id' => $id));
						$data=$req->fetch();
						$reponseJSON["nbEvalSupprime"]=intval($data['nb']);
						$req = $bdd->prepare('DELETE FROM '.$BDD_PREFIXE.'notation WHERE eleve = :id');
						$req->execute(array('id' => $id));
				}

				$reponseJSON["idSupprime"]=$id;
				$reponseJSON["messageRetour"]=":XL'utilisateur n°".$id." a bien été supprimé.";
			}
			else
				$reponseJSON["messageRetour"]=":(Vous ne pouvez pas vous supprimer vous même.";
		}
		else
			$reponseJSON["messageRetour"]=":(Aucune utilisateur à supprimer.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de supprimer un utilisateur.";
}


// Envoie un bilan à UN utilisateur ************************
if($action=="envoieBilan")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$id=0;
		if(isset($_POST['id'])) $id=intval($_POST['id']);
		if($id)
			$reponseJSON["messageRetour"]=envoieBilan($id);//Envoi du bilan
		else //Si pas d'id
			$reponseJSON["messageRetour"]=":(Aucune utilisateur à qui envoyer un bilan.";
	}
	else //Si pas admin
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit d'envoyer le bilan à un utilisateur.";
}


// Envoie un bilan à PLUSIEURSD utilisateurs ************************
if($action=="envoiePlusieursBilans")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$classe=isset($_POST['classe'])?$_POST['classe']:"";
		if($classe!="[ALL]")//S'il y a une classe spécifique...
		{
			$req=$bdd->prepare("SELECT id,nom,prenom  FROM ".$BDD_PREFIXE."utilisateurs WHERE classe=:classe");
			$req->execute(array('classe'=>$classe));
		}
		else
			$req=$bdd->query("SELECT id,nom,prenom FROM ".$BDD_PREFIXE."utilisateurs");

		$reponseJSON["retoursDeChaqueEnvoi"]=array();
		$compteurEnvois=0;
		while($data=$req->fetch())//Pour chaque utilisateur...
		{
			$id=intval($data['id']);
			$nom=$data['prenom']." ".$data['nom'];
			$retour=envoieBilan($id);
			if($retour)
			{
				array_push($reponseJSON["retoursDeChaqueEnvoi"],array("id"=>$id,"nom"=>$nom,"retour"=>$retour));
				if(substr($retour,0,2)==":)")
					$compteurEnvois+=1;
			}
		}
		if($compteurEnvois>1)
			$reponseJSON["messageRetour"]=":)".strval($compteurEnvois)." mails ont été envoyés";
		elseif($compteurEnvois==1)
			$reponseJSON["messageRetour"]=":)1 mail a été envoyé";
		else
			$reponseJSON["messageRetour"]=":|Aucun mail n'a été envoyé";
	}
	else //Si pas admin
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit d'envoyer plusieurs bilans.";
}



// =====================================================
// BILAN
// =====================================================
if($action=="getListeEleves")
{
	if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur")
	{
		connectToBDD();
		$classe="";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];
		if($classe!="")
		{
			$reponseJSON["classe"]=$classe;//On renvoie la classe (pour info)

			$req = $bdd->prepare('SELECT nom,prenom,id FROM '.$BDD_PREFIXE.'utilisateurs WHERE classe=:classe ORDER BY nom');
			$req->execute(array('classe'=>$classe));
			$numeroEleve=0;
			while ($donnees = $req->fetch())
				{
					$id=$donnees['id'];
					$nom=$donnees['nom'];
					$prenom=$donnees['prenom'];
					$reponseJSON["listeEleves"][$numeroEleve]["id"]=$id;
					$reponseJSON["listeEleves"][$numeroEleve]["nom"]=$nom;
					$reponseJSON["listeEleves"][$numeroEleve]["prenom"]=$prenom;
					$numeroEleve++;
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
	
	$contexte="ALL_CONTEXTE";
	if(isset($_POST['contexte'])) $contexte=$_POST['contexte'];
	$conditionSurContexte="";
	if($contexte!="ALL_CONTEXTE") $conditionSurContexte=" AND contexte='".$contexte."'";

	if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $eleve==$_SESSION['id'] && $eleve>0)	//Si admin, ou utilisateur connecté qui demande sa propre notation
	{
		connectToBDD();

		//Recupere la classe de l'élève
		$reqClasse = $bdd->prepare('SELECT classe FROM '.$BDD_PREFIXE.'utilisateurs WHERE id=:eleve');
		$reqClasse->execute(array('eleve'=>$eleve));
		
		if($donneesClasse=$reqClasse->fetch())
		{		
			$classe=$donneesClasse['classe'];
			
			$req_ind="(SELECT * FROM ".$BDD_PREFIXE."indicateurs AS i JOIN ".$BDD_PREFIXE."liensClassesIndicateurs AS l ON i.id=l.indicateur WHERE classe='".$classe."')";
			$req_comp_gr="(SELECT comp.id AS idComp, comp.nom AS nomComp, comp.nomAbrege AS nomCompAbrege, gr.id AS idGroup, gr.nom AS nomGroup FROM ".$BDD_PREFIXE."competences AS comp JOIN ".$BDD_PREFIXE."groupes_competences AS gr ON  comp.groupe=gr.id)";

			$requete="SELECT  E1.idComp,  E1.nomComp, E1.nomCompAbrege AS nomCompAbrege, E1.idGroup, E1.nomGroup, ind.id AS idInd, ind.nom AS nomInd, ind.details AS detailsInd, ind.niveaux AS niveauxInd, ind.lien AS lienInd, ind.position AS positionInd FROM ".$req_ind." as ind JOIN ".$req_comp_gr." AS E1 ON ind.competence = E1.idComp";
			$req = $bdd->query($requete);

			while($reponse=$req->fetch())
			{
				$idGroup=intval($reponse['idGroup']);
				$nomGroup=$reponse['nomGroup'];

				$idComp=intval($reponse['idComp']);
				$nomComp=$reponse['nomComp'];
				$nomCompAbrege=($reponse['nomCompAbrege']!="")?$reponse['nomCompAbrege']:substr($reponse['nomComp'],0,20);;

				$idInd=intval($reponse['idInd']);
				$nomInd=$reponse['nomInd'];
				$detailsInd=$reponse['detailsInd'];
				$niveauxInd=intval($reponse['niveauxInd']);
				$lienInd=$reponse['lienInd'];
				$positionInd=intval($reponse['positionInd']);

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
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["nomAbrege"]=$nomCompAbrege;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["selected"]=true;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"]=array();
				}

				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["id"]=$idInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["nom"]=$nomInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["details"]=$detailsInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauMax"]=$niveauxInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["lien"]=$lienInd;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMax"]=-1;//Par defaut
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMoy"]=-1;//Par defaut
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveLast"]=-1;//Par defaut
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["commentaires"]=false;//Dit si il y a des commentaires ou non
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["selected"]=true;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["position"]=$positionInd;
				
				//Note max
				$reqNote = $bdd->prepare("SELECT MAX(note) as max FROM ".$BDD_PREFIXE."notation WHERE eleve=:eleve AND indicateur=".$idInd.$conditionSurContexte);
				$reqNote->execute(array('eleve'=>$eleve));
				if($donneesNote=$reqNote->fetch())
					{if($donneesNote["max"]==null) $donneesNote["max"]=-1;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMax"]=$donneesNote["max"];
					}

				//Note moyenne
				$reqNote = $bdd->prepare("SELECT AVG(note) as moy FROM ".$BDD_PREFIXE."notation WHERE eleve=:eleve AND indicateur=".$idInd.$conditionSurContexte);
				$reqNote->execute(array('eleve'=>$eleve));
				if($donneesNote=$reqNote->fetch())
					{if($donneesNote["moy"]==null) $donneesNote["moy"]=-1;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMoy"]=$donneesNote["moy"];
					}

				//Derniere note
				$reqNote = $bdd->prepare("SELECT note as last FROM ".$BDD_PREFIXE."notation WHERE eleve=:eleve AND indicateur=".$idInd.$conditionSurContexte." ORDER BY date DESC LIMIT 1");
				$reqNote->execute(array('eleve'=>$eleve));
				if($donneesNote=$reqNote->fetch())
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveLast"]=$donneesNote["last"];

				//Commentaires ou non ?
				if($AUTORISE_COMMENTAIRES)
				{
					$reqCom = $bdd->prepare("SELECT id FROM ".$BDD_PREFIXE."notation WHERE eleve=:eleve AND indicateur=".$idInd.$conditionSurContexte." AND commentaire<>\"\" LIMIT 1");
					$reqCom->execute(array('eleve'=>$eleve));
					if($donneesCom=$reqCom->fetch())
						$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["commentaires"]=true;
				}
			}	

			$reponseJSON["messageRetour"]=":XEvaluation récupérées.";
		}	//Fin 'si pas d'eleve'
		else
			$reponseJSON["messageRetour"]=":(Pas d'élève trouvé.";
	}
	else
	{
		$reponseJSON["messageRetour"]=":(Vous ne pouvez pas récupérer cette liste de notation.";
	}
}


//OBTIENT LA NOTATION DES ELEVES=================
if($action=="getComments")
{
	if($AUTORISE_COMMENTAIRES)
	{
		$idInd=0;
			if(isset($_POST['idInd'])) $idInd=intval($_POST['idInd']);
		$idEleve=$_SESSION['id'];//Par defaut, on prendra les comments de l'utilisateur
			if(isset($_POST['idEleve']) && ($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur"))
				$idEleve=intval($_POST['idEleve']);//...mais si on envoit un élève et qu'on est examinateur
		if($idInd)
		{
			if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['id']==$idEleve)
			{
				connectToBDD();
				$req= $bdd->prepare('SELECT id,date,contexte,commentaire FROM '.$BDD_PREFIXE.'notation WHERE eleve=:idEleve AND indicateur=:idInd AND commentaire<>"" ORDER BY contexte');
				$req->execute(array(	'idInd'=>$idInd,
							'idEleve'=>$idEleve
							));
				while($donnees=$req->fetch())
				{
					$contexte=$donnees['contexte'];
					$idEval=intval($donnees['id']);
					$commentaire=$donnees['commentaire'];
					$date=date_format(date_create($donnees['date']),'d/m/y H:i');
					$reponseJSON['commentaires'][$contexte][$idEval]["texte"]=$commentaire;
					$reponseJSON['commentaires'][$contexte][$idEval]["date"]=$date;
				}
				$reponseJSON['debug']=$idEleve;
				$reponseJSON["messageRetour"]=":XLes commentaires ont bien été récupérés.";
			}
			else		
				$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de voir ces commentaires.";
		}
		else
			$reponseJSON["messageRetour"]=":(Aucune évaluation demandée pour le commentaire.";
	}
	else
	$reponseJSON["messageRetour"]=":(La fonction 'Commentaires' est désactivée.";
	
}

// Action qui ajoute une nouvelle note **************************************
if($action=="newNote")
{
	$eleve=isset($_POST['eleve'])?intval($_POST['eleve']):0;

	if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['statut']=="autoeval" && $_SESSION['id']==$eleve)
	{
		//RECUPERE LES DONNEES ------------
		$indicateur=isset($_POST['indicateur'])?intval($_POST['indicateur']):0;
		$note=isset($_POST['note'])?intval($_POST['note']):0;
		
		//ECRITURE DE LA NOTE ------------
		connectToBDD();
		$nextId=getNextFreeIdOfTable($bdd,$BDD_PREFIXE.'notation');
		$req = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'notation (id,note,date,eleve,indicateur,examinateur,contexte,commentaire) VALUES(:id,:note,NOW(),:eleve,:indicateur,'.$_SESSION['id'].',"","")');
		$req->execute(array(
				'id' => $nextId,
				'note' => $note,
				'eleve' => $eleve,
				'indicateur' => $indicateur
				));
		

		
		//RETOUR ------------
		$reponseJSON["note"]=getNotationPourJSON($eleve,$indicateur);

		$repNote=$bdd->query("SELECT * FROM ".$BDD_PREFIXE."notation WHERE id=".strval($nextId));
		$dataNote=$repNote->fetch();

		$repEleve=$bdd->query("SELECT nom,prenom FROM ".$BDD_PREFIXE."utilisateurs WHERE id=".$dataNote["eleve"]);
		$dataEleve=$repEleve->fetch();

		$repProf=$bdd->query("SELECT nom,prenom FROM ".$BDD_PREFIXE."utilisateurs WHERE id=".$dataNote["examinateur"]);
		$dataProf=$repProf->fetch();

		$repInd=$bdd->query("SELECT nom,niveaux FROM ".$BDD_PREFIXE."indicateurs WHERE id=".$dataNote["indicateur"]);
		$dataInd=$repInd->fetch();

		$reponseJSON["notation"]["debug"]="SELECT nom,prenom FROM ".$BDD_PREFIXE."utilisateurs WHERE id=".$dataNote["eleve"];

		$reponseJSON["notation"]["id"]=$dataNote["id"];
		$reponseJSON["notation"]["prenomEleve"]=$dataEleve["prenom"];
		$reponseJSON["notation"]["nomEleve"]=$dataEleve["nom"];
		$reponseJSON["notation"]["prenomProf"]=$dataProf["prenom"];
		$reponseJSON["notation"]["nomProf"]=$dataProf["nom"];
		$reponseJSON["notation"]["date"]=$dataNote['date'];
		$reponseJSON["notation"]["note"]=$dataNote['note'];
		$reponseJSON["notation"]["niveaux"]=$dataInd['niveaux'];
		$reponseJSON["notation"]["nomIndicateur"]=$dataInd['nom'];
				
		$reponseJSON["messageRetour"]=":)La note a été ajoutée.<br/><div style=\"cursor:pointer;color:red;\" onclick=\"supprimeNotation(".$dataNote["id"].");\">ANNULER LA NOTE</div>";


		//BADGES ---------------------------
		if($AUTORISE_BADGES)
			updateBadges_aLaNotation($eleve);
	}
	else
	{
		$reponseJSON["messageRetour"]=":(Vous ne pouvez pas ajouter une note.";
	}
}



// Action qui ajoute un commentaire à une note **************************************
if($action=="addCommentaireEval")
{
	$idEval=0;
		if(isset($_POST['idEval'])) $idEval=intval($_POST['idEval']);

	if($idEval)//Si un numero d'evaluation a été envoyé
	{
		//Verifiction de l'éleve qui doit obtenir ce commnetaire (nécessaire de la vérifier s'il est en autoeval, ^par sécurité)
		connectToBDD();
		$req = $bdd->prepare('SELECT eleve FROM '.$BDD_PREFIXE.'notation WHERE id=:idEval');
		$req->execute(array(
					'idEval' => $idEval
					));
		$idEleve=0;
		if($donnees=$req->fetch())
			$idEleve=$donnees['eleve'];//On recupere le n° de l'eleve concerne par l'evaluation
		
		
		if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['statut']=="autoeval" && $_SESSION['id']==$idEleve)
		{
			$idEval=0;
			if(isset($_POST['idEval'])) $idEval=intval($_POST['idEval']);


				$contexte="";
				if(isset($_POST['contexte'])) $contexte=$_POST['contexte'];
				$commentaire="";
				if(isset($_POST['commentaire'])) $commentaire=$_POST['commentaire'];

				connectToBDD();
				$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'notation SET contexte=:contexte, commentaire=:commentaire WHERE id=:idEval');
				$req->execute(array(
						'idEval' => $idEval,
						'contexte' => $contexte,
						'commentaire' => $commentaire
						));
						
				
				$reponseJSON["commentaire"]["contexte"]=$contexte;
				$reponseJSON["commentaire"]["commentaire"]=$commentaire;
						

				$req = $bdd->prepare('SELECT * FROM '.$BDD_PREFIXE.'notation WHERE id=:idEval');
				$req->execute(array(
						'idEval' => $idEval
						));
				if($donnees=$req->fetch())
				{
					$reponseJSON["evaluation"]["id"]=$idEval;
					$reponseJSON["evaluation"]["eval"]=$donnees['note'];
					$reponseJSON["evaluation"]["indicateur"]=$donnees['indicateur'];
					$reponseJSON["messageRetour"]=":XCommentaire ajouté.";
				}
				else
					$reponseJSON["messageRetour"]=":(Aucune évaluation associée au commentaire.";
		}
		else
			$reponseJSON["messageRetour"]=":(Vous ne pouvez pas ajouter de commentaires à une évaluation.";
	}
	else
		$reponseJSON["messageRetour"]=":(Aucune évaluation choisie pour le commentaire.";
}


// =====================================================
// HISTORIQUE
// =====================================================

//Modifie une notation
if($action=="modifieNotation")
{
		connectToBDD();

		//Récupere les données
		$id=-1;
		if(isset($_POST['idNotation'])) $id=intval($_POST['idNotation']);
		$note="-1";
		if(isset($_POST['note'])) $note=intval($_POST['note']);
		$contexte="";
		if(isset($_POST['contexte'])) $contexte=$_POST['contexte'];
		$commentaire="";
		if(isset($_POST['commentaire'])) $commentaire=$_POST['commentaire'];

	if(autoriseModifNoteSelonStatut($id))
	{
		if($id>0)
		{

			$req=$bdd->prepare("UPDATE ".$BDD_PREFIXE."notation SET note=:note, date=NOW(),examinateur=".$_SESSION['id'].", contexte=:contexte, commentaire=:commentaire WHERE id=:id");
			$req->execute(array('id'=>$id,
								'note'=>$note,
								'contexte'=>$contexte,
								'commentaire'=>$commentaire
								));
			$reponseJSON["idNotation"]=$id;
			$reponseJSON["note"]=$note;
			$reponseJSON["date"]=date("Y-m-d H:i");
			$reponseJSON["evaluateur"]=$_SESSION['prenom']." ".$_SESSION['nom'];
			$reponseJSON["contexte"]=$contexte;
			$reponseJSON["commentaire"]=$commentaire;
			$reponseJSON["messageRetour"]=":XL'évaluation a bien été mise à jour.";

		}
		else
			$reponseJSON["messageRetour"]=":(L'évaluation à supprimer n'a pas/a mal été transmise.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de modifier une évaluation.";
}



//Supprime une notation
if($action=="supprimeNotation")
{
	connectToBDD();
	$id=-1;
	if(isset($_POST['idNotation'])) $id=intval($_POST['idNotation']);

	if(autoriseModifNoteSelonStatut($id))
	{

		if($id>0)
		{

			$req=$bdd->prepare("DELETE FROM ".$BDD_PREFIXE."notation WHERE id=:id");
			$req->execute(array('id'=>$id));
			$reponseJSON["idNotation"]=$id;
			$reponseJSON["messageRetour"]=":|L'évaluation a bien été supprimée.";
		}
		else
			$reponseJSON["messageRetour"]=":(L'évaluation à supprimer n'a pas/a mal été transmise.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de supprimer une évaluation.";
}




// =====================================================
// ADMINISTRATION COMPETENCES
// =====================================================



//Nettoyer la base de données
if($action=="nettoyerLaBase")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$modif_apportee=false; //tag qui vaut true s'il y a une modif
		//Recupere les variables
			$nettoyer_supprimer_notes_fantomes=isset($_POST['nettoyer_supprimer_notes_fantomes'])?$_POST['nettoyer_supprimer_notes_fantomes']=="true":false;
			$nettoyer_supprimer_notes_sans_critere=isset($_POST['nettoyer_supprimer_notes_sans_critere'])?$_POST['nettoyer_supprimer_notes_sans_critere']=="true":false;
			$nettoyer_depasse_critere_max=isset($_POST['nettoyer_depasse_critere_max'])?$_POST['nettoyer_depasse_critere_max']=="true":false;
			$nettoyer_depasse_critere_max_option=isset($_POST['nettoyer_depasse_critere_max_option'])?$_POST['nettoyer_depasse_critere_max_option']:"";
			$nettoyer_supprimer_comp_orphelins=isset($_POST['nettoyer_supprimer_comp_orphelins'])?$_POST['nettoyer_supprimer_comp_orphelins']=="true":false;
			$nettoyer_supprimer_comp_orphelins_et_ses_criteres=isset($_POST['nettoyer_supprimer_comp_orphelins_et_ses_criteres'])?$_POST['nettoyer_supprimer_comp_orphelins_et_ses_criteres']=="true":false;
			$nettoyer_supprimer_notes_criteres_orphelins=isset($_POST['nettoyer_supprimer_notes_criteres_orphelins'])?$_POST['nettoyer_supprimer_notes_criteres_orphelins']=="true":false;
			$nettoyer_reordonner=isset($_POST['nettoyer_reordonner'])?$_POST['nettoyer_reordonner']=="true":false;

			$nb_suppr_fantom=0;
			$nb_suppr_plus_dindicateur=0;
			$nb_critere_depasse_sup=0;
			$nb_critere_depasse_inf=0;
			$nb_comp_orpheline=0;
			$nb_ind_supprime=0;

			//Supprimer les notes des phantomes
			if($nettoyer_supprimer_notes_fantomes)
			{
				$req=$bdd->query("SELECT COUNT(id) as nb FROM ".$BDD_PREFIXE."notation WHERE eleve NOT IN (select id from ".$BDD_PREFIXE."utilisateurs)");
				$data=$req->fetch();
				$nb_suppr_fantom=intval($data['nb']);
				if(intval($data['nb'])) $modif_apportee=true;//Si modif...
				$bdd->query("DELETE FROM ".$BDD_PREFIXE."notation WHERE eleve NOT IN (select id from ".$BDD_PREFIXE."utilisateurs)");
			}

			//Supprimer les notes qui n'ont pas de critère
			if($nettoyer_supprimer_notes_sans_critere)
			{
				$req=$bdd->query("SELECT COUNT(id) as nb FROM ".$BDD_PREFIXE."notation WHERE indicateur NOT IN (select id from ".$BDD_PREFIXE."indicateurs)");
				$data=$req->fetch();
				$nb_suppr_plus_dindicateur=intval($data['nb']);
				if(intval($data['nb'])) $modif_apportee=true;//Si modif...
				$bdd->query("DELETE FROM ".$BDD_PREFIXE."notation WHERE indicateur NOT IN (select id from ".$BDD_PREFIXE."indicateurs)");
			}

			//Gerer les notes qui dépassent les maximum ET le minimum 0
			if($nettoyer_depasse_critere_max)
			{
				$req=$bdd->query("SELECT count(*) as nb FROM ".$BDD_PREFIXE."notation as n join ".$BDD_PREFIXE."indicateurs as i ON n.indicateur = i.id WHERE n.note > i.niveaux");
				$data=$req->fetch();
				$nb_critere_depasse_sup=intval($data['nb']);
				if(intval($data['nb'])) $modif_apportee=true;//Si modif...

				$req=$bdd->query("SELECT count(*) as nb FROM ".$BDD_PREFIXE."notation where note < 0");
				$data=$req->fetch();
				$nb_critere_depasse_inf=intval($data['nb']);
				if(intval($data['nb'])) $modif_apportee=true;//Si modif...

				if($nettoyer_depasse_critere_max_option=="depasse_critere_max_tronque")
				{
					$req=$bdd->query("SELECT n.id as id_note, niveaux FROM ".$BDD_PREFIXE."notation as n join ".$BDD_PREFIXE."indicateurs as i ON n.indicateur = i.id WHERE n.note > i.niveaux");
					while($data=$req->fetch())
					{
						$bdd->query("UPDATE ".$BDD_PREFIXE."notation SET note=".$data["niveaux"]." WHERE id=".$data['id_note']);
					}
					$bdd->query("UPDATE ".$BDD_PREFIXE."notation SET note=0 WHERE note<0");
				}
				elseif($nettoyer_depasse_critere_max_option=="depasse_critere_max_suppr")
				{
					$bdd->query("DELETE FROM ".$BDD_PREFIXE."notation WHERE id IN (SELECT n.id FROM (SELECT * FROM ".$BDD_PREFIXE."notation) as n join ".$BDD_PREFIXE."indicateurs as i ON n.indicateur = i.id WHERE n.note > i.niveaux)");
					$bdd->query("DELETE FROM ".$BDD_PREFIXE."notation WHERE note < 0");	
				}


			}


			//Supprimer les competences orphelines
			if($nettoyer_supprimer_comp_orphelins	)
			{
				$req=$bdd->query("SELECT COUNT(id) as nb FROM ".$BDD_PREFIXE."competences WHERE groupe NOT IN (select id from ".$BDD_PREFIXE."groupes_competences)");
				$data=$req->fetch();
				$nb_comp_orpheline=intval($data['nb']);
				if(intval($data['nb'])) $modif_apportee=true;//Si modif...
				if($nettoyer_supprimer_comp_orphelins_et_ses_criteres)
				{
					$req=$bdd->query("SELECT COUNT(*) as nb FROM ".$BDD_PREFIXE."indicateurs WHERE competence IN (SELECT id FROM ".$BDD_PREFIXE."competences WHERE groupe NOT IN (select id from ".$BDD_PREFIXE."groupes_competences))");
					$data=$req->fetch();
					$nb_ind_supprime+=intval($data['nb']);	
					$req=$bdd->query("DELETE FROM ".$BDD_PREFIXE."indicateurs WHERE competence IN (SELECT id FROM ".$BDD_PREFIXE."competences WHERE groupe NOT IN (select id from ".$BDD_PREFIXE."groupes_competences))");
				}

				$bdd->query("DELETE FROM ".$BDD_PREFIXE."competences WHERE groupe NOT IN (select id from ".$BDD_PREFIXE."groupes_competences)");
			}


			//Supprimer les indicateurs fantomes
			if($nettoyer_supprimer_notes_criteres_orphelins)
			{
				$req=$bdd->query("SELECT COUNT(id) as nb FROM ".$BDD_PREFIXE."indicateurs WHERE competence NOT IN (select id from ".$BDD_PREFIXE."competences)");
				$data=$req->fetch();
				$nb_ind_supprime+=intval($data['nb']);
				if(intval($data['nb'])) $modif_apportee=true;//Si modif...
				$bdd->query("DELETE FROM ".$BDD_PREFIXE."indicateurs WHERE competence NOT IN (select id from ".$BDD_PREFIXE."competences)");
			}


			//Remettre à plat la numérotation des critere/competences/domaines
			if($nettoyer_reordonner)
			{
				$modif_apportee=true;
				$req=$bdd->query("SELECT id FROM ".$BDD_PREFIXE."indicateurs ORDER BY position");
				$cpt=1;
			1+1;
				while($data=$req->fetch())
				{
					$bdd->query("UPDATE ".$BDD_PREFIXE."indicateurs SET position=".strval($cpt)." WHERE id=".strval($data['id']));
					$cpt+=1;
				}

				$req=$bdd->query("SELECT id FROM ".$BDD_PREFIXE."competences ORDER BY position");
				$cpt=1;
				while($data=$req->fetch())
				{
					$bdd->query("UPDATE ".$BDD_PREFIXE."competences SET position=".strval($cpt)." WHERE id=".strval($data['id']));
					$cpt+=1;
				}

				$req=$bdd->query("SELECT id FROM ".$BDD_PREFIXE."groupes_competences ORDER BY position");
				$cpt=1;
				while($data=$req->fetch())
				{
					$bdd->query("UPDATE ".$BDD_PREFIXE."groupes_competences SET position=".strval($cpt)." WHERE id=".strval($data['id']));
					$cpt+=1;
				}
			}

			$reponseJSON['bilan_nettoyage']=array();
			$reponseJSON['bilan_nettoyage']["notes_supprimees"]['plus_user']=$nb_suppr_fantom;
			$reponseJSON['bilan_nettoyage']["notes_supprimees"]['plus_indicateur']=$nb_suppr_plus_dindicateur;
			$reponseJSON['bilan_nettoyage']["criteres_depasse"]['sup']=$nb_critere_depasse_sup;
			$reponseJSON['bilan_nettoyage']["criteres_depasse"]['inf']=$nb_critere_depasse_inf;
			$reponseJSON['bilan_nettoyage']["comp_supprimees"]=$nb_comp_orpheline;
			$reponseJSON['bilan_nettoyage']["ind_supprimees"]=$nb_ind_supprime;
			$reponseJSON['bilan_nettoyage']['modif_apportee']=$modif_apportee;
			$reponseJSON['messageRetour']=":XNettoyage de la BDD effectuée.";


	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de nettoyer la BDD !";
}







//Update liste des compétences ***************************************
if($action=="updateCompetencesSelonClasse")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		
		$classe="";
		if(isset($_POST['classe'])) $classe=$_POST['classe'];

		$reponseJSON['listeGroupes']=array();
		
		//Liste des groupes ******
		$reqGr = $bdd->query("SELECT * FROM ".$BDD_PREFIXE."groupes_competences ORDER BY position");
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
			$reqComp=$bdd->query("SELECT * FROM ".$BDD_PREFIXE."competences WHERE groupe=".$idGroup." ORDER BY position");
			while($reponseComp=$reqComp->fetch())
			{
				$idComp=intval($reponseComp['id']);
				$nomComp=$reponseComp['nom'];
				$nomAbregeComp=$reponseComp['nomAbrege'];
				$positionComp=intval($reponseComp['position']);
				
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["id"]=$idComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["nom"]=$nomComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["nomAbrege"]=$nomAbregeComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["position"]=$positionComp;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["selected"]=false;
				$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"]=array();
				
				//Liste des indicateurs ******
				$reqInd=$bdd->query("SELECT * FROM ".$BDD_PREFIXE."indicateurs WHERE competence=".$idComp." ORDER BY position");
				while($reponseInd=$reqInd->fetch())
				{
					$idInd=intval($reponseInd['id']);
					$nomInd=$reponseInd['nom'];
					$detailsInd=$reponseInd['details'];
					$niveauxInd=intval($reponseInd['niveaux']);
					$positionInd=intval($reponseInd['position']);
					$lien=$reponseInd['lien'];
					
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["id"]=$idInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["nom"]=$nomInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["details"]=$detailsInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveaux"]=$niveauxInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["position"]=$positionInd;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["lien"]=$lien;
					$reponseJSON['listeGroupes'][$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["selected"]=false;
				}
				
			}
		}


		//On tag celles qui sont dans la classe souhaitée

		$req_ind="(SELECT * FROM ".$BDD_PREFIXE."indicateurs AS i JOIN ".$BDD_PREFIXE."liensClassesIndicateurs AS l ON i.id=l.indicateur WHERE classe='".$classe."')";
		$req_comp_gr="(SELECT comp.id AS idComp, comp.nom AS nomComp, gr.id AS idGroup, gr.nom AS nomGroup FROM ".$BDD_PREFIXE."competences AS comp JOIN ".$BDD_PREFIXE."groupes_competences AS gr ON  comp.groupe=gr.id)";

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
		$reponseJSON["messageRetour"]=":XListe des compétences récupérées";
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
			$req = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'groupes_competences (nom) VALUES(:nom)');
			$req->execute(array('nom' => $nom));

			//Vérification
			$req2 =  $bdd->prepare('SELECT id FROM '.$BDD_PREFIXE.'groupes_competences WHERE nom=:nom ORDER BY id DESC LIMIT 1');
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

//MODIF D'UN DOMAINE=================
if($action=="modifDomaine")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$nom="";
		if(isset($_POST['nom'])) $nom=$_POST['nom'];
		$idDomaine="";
		if(isset($_POST['idDomaine'])) $idDomaine=intval($_POST['idDomaine']);
		if($idDomaine!=0)
		{
			//Écriture
			$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'groupes_competences SET nom=:nom WHERE id=:idDomaine');
			$req->execute(array('nom' => $nom,
								"idDomaine"=>$idDomaine));

			
			$reponseJSON["messageRetour"]=":XLe domaine ".$nom." a bien été créé.";
			$reponseJSON["domaine"]["nom"]=$nom;
			$reponseJSON["domaine"]["id"]=$idDomaine;
		}
		else
			$reponseJSON["messageRetour"]=":(Aucun n° de domaine n'a été envoyé.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de modifier un domaine.";
}

//SUPPRESSION D'UN DOMAINE =================
if($action=="supprimeDomaine")
{
	if($_SESSION['statut']=="admin")
	{

		connectToBDD();

		$idDomaine=0;
		if(isset($_POST['idDomaine'])) $idDomaine=intval($_POST['idDomaine']);
		$supprimeCompetences=true;
		if(isset($_POST['supprimeCompetences'])) $supprimeCompetences=intval($_POST['supprimeCompetences']);
		$supprimeCriteres=true;
		if(isset($_POST['supprimeCriteres'])) $supprimeCriteres=intval($_POST['supprimeCriteres']);

		if($idDomaine!=0)
		{
			supprimeDomaine($idDomaine,$supprimeCompetences,$supprimeCriteres);
			$reponseJSON["messageRetour"]=":XLe domaine a bien été supprimé.";
			$reponseJSON["domaine"]["id"]=$idDomaine;
		}
		else
			$reponseJSON["messageRetour"]=":(Aucun domaine à supprimer n'a été transmis.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de supprimer un domaine.";
}

//AJOUT D'UNE COMPETENCE=================
if($action=="addCompetence")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$nom="";
		if(isset($_POST['nom'])) $nom=$_POST['nom'];
		$nomAbrege="";
		if(isset($_POST['nomAbrege'])) $nomAbrege=$_POST['nomAbrege'];
		$idGroupe=0;
		if(isset($_POST['idGroupe'])) $idGroupe=intval($_POST['idGroupe']);
		if($nom!="")
		{
			$req = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'competences (nom,nomAbrege,groupe) VALUES(:nom,:nomAbrege,:idGroupe)');
			$req->execute(array(
						'nom' => $nom,
						'nomAbrege' => $nomAbrege,
						'idGroupe' => $idGroupe
					));

			//Vérification
			$req2 =  $bdd->prepare('SELECT id FROM '.$BDD_PREFIXE.'competences WHERE nom=:nom ORDER BY id DESC LIMIT 1');
			$req2->execute(array('nom' => $nom));

			if($donnees=$req2->fetch())
			{
				$reponseJSON["messageRetour"]=":XLa compétence ".$nom." a bien été créé.";
				$reponseJSON["competence"]["nom"]=$nom;
				$reponseJSON["competence"]["nomAbrege"]=$nomAbrege;
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


//MODIFICATION D'UNE COMPETENCE=================
if($action=="modifCompetence")
{
	if($_SESSION['statut']=="admin")
	{
		connectToBDD();
		$nom="";
		if(isset($_POST['nom'])) $nom=$_POST['nom'];
		$nomAbrege="";
		if(isset($_POST['nomAbrege'])) $nomAbrege=$_POST['nomAbrege'];
		$idDomaine=0;
		if(isset($_POST['idDomaine'])) $idDomaine=intval($_POST['idDomaine']);
		$idCompetence=0;
		if(isset($_POST['idCompetence'])) $idCompetence=intval($_POST['idCompetence']);
		
		if($idCompetence!=0)
		{
			$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'competences SET nom=:nom, nomAbrege=:nomAbrege, groupe=:idDomaine WHERE id=:idCompetence');
			$req->execute(array(
						'nom' => $nom,
						'nomAbrege' => $nomAbrege,
						'idDomaine' => $idDomaine,
						'idCompetence' => $idCompetence
					));

			$reponseJSON["messageRetour"]=":XLa compétence ".$nom." a bien été modifiée.";
			$reponseJSON["competence"]["nom"]=$nom;
			$reponseJSON["competence"]["nomAbrege"]=$nomAbrege;
			$reponseJSON["competence"]["id"]=$idCompetence;
			$reponseJSON["competence"]["groupe"]=$idDomaine;
		}
		else
			$reponseJSON["messageRetour"]=":(Aucun n° de compétence envoyé.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de modifier une compétence.";
}


//SUPPRESSION D'UNE COMPETENCE =================
if($action=="supprimeCompetence")
{
	if($_SESSION['statut']=="admin")
	{

		connectToBDD();
		
		$idCompetence=0;
		if(isset($_POST['idCompetence'])) $idCompetence=intval($_POST['idCompetence']);
		$supprimeIndicateur=true;
		if(isset($_POST['supprimeIndicateur'])) $supprimeIndicateur=intval($_POST['supprimeIndicateur']);

		
		if($idCompetence!=0)
		{
			supprimeCompetence($idCompetence,$supprimeIndicateur);
			$reponseJSON["messageRetour"]=":XLa compétence a bien été supprimée.";
			$reponseJSON["competence"]["id"]=$idCompetence;
		}
		else
			$reponseJSON["messageRetour"]=":(Aucune compétence à supprimer n'a été transmise.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de supprimer une compétence.";
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
		$lien="";
		if(isset($_POST['lien'])) $lien=$_POST['lien'];
		
		if($nom!="")
		{
			$req = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'indicateurs (nom,details,niveaux,competence,lien) VALUES(:nom,:details,:niveaux,:idCompetence,:lien)');
			$req->execute(array(
						'nom' => $nom,
						'details' => $details,
						'niveaux' => $niveaux,
						'idCompetence' => $idCompetence,
						'lien' => $lien
					));
					
					
			//Vérification
			$req2 =  $bdd->prepare('SELECT id FROM '.$BDD_PREFIXE.'indicateurs WHERE nom=:nom ORDER BY id DESC LIMIT 1');
			$req2->execute(array('nom' => $nom));

			if($donnees=$req2->fetch())
			{
				$reponseJSON["indicateur"]["nom"]=$nom;
				$reponseJSON["indicateur"]["details"]=$details;
				$reponseJSON["indicateur"]["niveaux"]=$niveaux;
				$reponseJSON["indicateur"]["id"]=intval($donnees['id']);
				$reponseJSON["indicateur"]["competence"]=$idCompetence;
				$reponseJSON["indicateur"]["lien"]=$lien;
				$reponseJSON["indicateur"]["selected"]=false; //Par défaut
				
				
				//Ajout du lien du nouvel indicateur avec la classe sélectionnée
				if($classe!="")
				{
					$requeteLier = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'liensClassesIndicateurs(indicateur, classe) VALUES(:indicateur, :classe)');
					$requeteLier->execute(array('indicateur' => intval($donnees['id']), 'classe' => $classe));
					$reponseJSON["indicateur"]["selected"]=true;
				}
				
				$reponseJSON["messageRetour"]=":XL'indicateur ".$nom." a bien été créé.";
				
			}
			else
				$reponseJSON["messageRetour"]=":(L'indicateur n'a pas été enregistrée pour une raison inconnue";
					
		}
		else
			$reponseJSON["messageRetour"]=":(L'intitulé de l'indicateur est vide.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de créer un indicateur.";
}

//Modifier un indicateur ==================================
if($action=="modifCritere")
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
		$idCritere=0;
		if(isset($_POST['idCritere'])) $idCritere=intval($_POST['idCritere']);
		$lien="";
		if(isset($_POST['lien'])) $lien=$_POST['lien'];
		
		if($idCritere!=0)
		{
			if($nom!="")
			{
				$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'indicateurs SET nom=:nom, details=:details, niveaux=:niveaux, competence=:idCompetence, lien=:lien WHERE id=:idCritere');
				$req->execute(array(
							'nom' => $nom,
							'details' => $details,
							'niveaux' => $niveaux,
							'idCompetence' => $idCompetence,
							'idCritere' => $idCritere,
							'lien' => $lien
						));

					$reponseJSON["indicateur"]["nom"]=$nom;
					$reponseJSON["indicateur"]["details"]=$details;
					$reponseJSON["indicateur"]["niveaux"]=$niveaux;
					$reponseJSON["indicateur"]["id"]=$idCritere;
					$reponseJSON["indicateur"]["competence"]=$idCompetence;
					$reponseJSON["indicateur"]["lien"]=$lien;

					$reponseJSON["messageRetour"]=":XL'indicateur ".$nom." a bien été modifié.";
					
						
			}
			else
				$reponseJSON["messageRetour"]=":(L'intitulé de l'indicateur est vide.";
		}
		else
			$reponseJSON["messageRetour"]=":(Aucun critère transmis.";
	}
	else
		$reponseJSON["messageRetour"]=":(Vous n'avez pas le droit de modifier un indicateur.";
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
			supprimeIndicateur($idIndicateur);
			$reponseJSON["messageRetour"]=":XL'indicateur a bien été supprimé.";
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
				$requete = $bdd->prepare('INSERT INTO '.$BDD_PREFIXE.'liensClassesIndicateurs(indicateur, classe) VALUES(:indicateur, :classe)');
				$requete->execute(array('indicateur' => $indicateur, 'classe' => $classe));
				$reponseJSON["messageRetour"]=":XLier";
				$reponseJSON["lier"]=true;
			}
			else	
			{
				$requete = $bdd->prepare('DELETE FROM '.$BDD_PREFIXE.'liensClassesIndicateurs WHERE indicateur=:indicateur AND classe=:classe');
				$requete->execute(array('indicateur' => $indicateur, 'classe' => $classe));
				$reponseJSON["messageRetour"]=":XDélier";
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
