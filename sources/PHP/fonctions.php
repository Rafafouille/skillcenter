<?php
include_once('options.php');

//Lance la session et initialise les variables associées
function initSession()
{
	session_start();
	if(!isset($_SESSION['statut']))
	{
		$_SESSION['statut']="";
		$_SESSION['nom']="";
		$_SESSION['prenom']="";
		$_SESSION['id']=0;	//ATTENTION : 0 = pas connecté.
	}
}

//Connection à la BDD
function connectToBDD()
{
	global $bdd,$BDD_SERVER,$BDD_NOM_BDD,$BDD_LOGIN,$BDD_MOT_DE_PASSE;
	try
	{
	$bdd = new PDO('mysql:host='.$BDD_SERVER.';dbname='.$BDD_NOM_BDD.';charset=utf8', $BDD_LOGIN, $BDD_MOT_DE_PASSE, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	//$bdd->query("SET NAMES 'utf8'");	//Pour spécifier qu'on travail en UTF8 - Inutile avec PDO

	}
	catch (Exception $e)
	{
			die('Erreur : '.$e->getMessage());
	}
}



// ==============================================
// NOTATION 
// ================================================





//Fonction renvoie la note max associéé à un indicateur et un éleve
//$eleve = id de l'eleve
//$indicateur = id de l'indicateur
function getNoteMax($eleve,$indicateur)
{
	global $bdd,$BDD_PREFIXE;
	$reqNote = $bdd->prepare('SELECT MAX(note) AS maxi FROM '.$BDD_PREFIXE.'notation WHERE indicateur=:indicateur AND eleve=:eleve');
	$reqNote->execute(array('eleve'=>$eleve,'indicateur'=>$indicateur));
	if($donneesNote = $reqNote->fetch())
		return $donneesNote["maxi"];
	else
		return -1;//En cas de "pas de note"
}

//Fonction renvoie la note moyenne (arrondi à l'entier inférieur) associée à un indicateur et un éleve
//$eleve = id de l'eleve
//$indicateur = id de l'indicateur
function getNoteMoy($eleve,$indicateur)
{
	global $bdd,$BDD_PREFIXE;
	$reqNote = $bdd->prepare('SELECT AVG(note) AS moy FROM '.$BDD_PREFIXE.'notation WHERE indicateur=:indicateur AND eleve=:eleve');
	$reqNote->execute(array('eleve'=>$eleve,'indicateur'=>$indicateur));
	if($donneesNote = $reqNote->fetch())
		return $donneesNote["moy"];
	else
		return -1;//En cas de "pas de note"
}

//Fonction renvoie la dernière note associée à un indicateur et un éleve
//$eleve = id de l'eleve
//$indicateur = id de l'indicateur
function getNoteLast($eleve,$indicateur)
{
	global $bdd,$BDD_PREFIXE;
	$reqNote = $bdd->prepare('SELECT note as last FROM '.$BDD_PREFIXE.'notation WHERE eleve=:eleve AND indicateur=:indicateur ORDER BY date DESC LIMIT 1');
	$reqNote->execute(array('eleve'=>$eleve,'indicateur'=>$indicateur));
	if($donneesNote = $reqNote->fetch())
		return $donneesNote["last"];
	else
		return -1;//En cas de "pas de note"
}


//Fonction qui renvoie un tableau avec la note max, moyenne et dernière d'un élève pour un indicateur donnée (renvoie également le n°id de l'élève et de l'indicateur)
//($eleve et $indicateur sont les numéro id, entiers)
function getNotationPourJSON($eleve,$indicateur)
{
	//global $bdd;
	$note=array(	"max"=>getNoteMax($eleve,$indicateur),
					"moy"=>getNoteMoy($eleve,$indicateur),
					"last"=>getNoteLast($eleve,$indicateur),
					"niveauMax"=>getNiveauMaxIndicateur($indicateur),
					"idEleve"=>$eleve,
					"idIndicateur"=>$indicateur);//Tableau à renvoyé, initialisé à -1

	return $note;
}




// ==============================================
// BADGES
// ================================================



//Fonction qui recupere la liste des badges (affichés ou pas encore...)
function getBadges($idEleve)
{
	global $bdd;

	//Récupère les badges déjà données
	$req = $bdd->prepare('SELECT badges,nouveaux_badges FROM utilisateurs WHERE id=:id');
	$req->execute(array('id'=>$idEleve));
	$donnees=$req->fetch();

	$BDDbadgesTXT=$donnees['badges'];//Badges deja donnes
	$BDDnouveaux_badgesTXT=$donnees['nouveaux_badges'];//Nouveau badges acquis

	$badgesTXT=$BDDbadgesTXT.",".$BDDnouveaux_badgesTXT;
	$badges=explode(",",$badgesTXT);
	return array($badges,$BDDnouveaux_badgesTXT);
}



//Fonction qui met à jour TOUS les badges pour un utilisateur
function updateBadges($idEleve)
{
	updateBadges_aLaNotation($idEleve);
}


//Fonction qui met à jour les badges AU MOMENT DE LA CONNEXION DE L'ELEVE
function updateBadges_aLaConnexion($idEleve)
{
	global $bdd,$reponseJSON;

	list ($badges,$BDDnouveaux_badgesTXT)=getBadges($idEleve);//On récupere tous les badges (et les nouveaux)

	//1ere connexion
	if(eligibleBadge_1ere_connexion($idEleve,$badges))
		$BDDnouveaux_badgesTXT.=",badge1ereConnexion,";

	//Update BDD
	str_replace(",,",",",$BDDnouveaux_badgesTXT);
	$req = $bdd->prepare('UPDATE utilisateurs SET nouveaux_badges=:nouveaux_badges WHERE id=:id');
	$req->execute(array('nouveaux_badges'=>$BDDnouveaux_badgesTXT,'id'=>$idEleve));
}



//Fonction qui met à jour les badges AU MOMENT DE LA NOTATION
function updateBadges_aLaNotation($idEleve)
{
	global $bdd,$reponseJSON;

	list ($badges,$BDDnouveaux_badgesTXT)=getBadges($idEleve);//On récupere tous les badges (et les nouveaux)

	//1ere brique
	if(eligibleBadge_1ere_brique($idEleve,$badges))
		$BDDnouveaux_badgesTXT.=",badge1ereBrique,";

	//Décollage
	if(eligibleBadge_decollage($idEleve,$badges))
		$BDDnouveaux_badgesTXT.=",badgeDecollage,";


	//Choses serieuses
	if(eligibleBadge_choses_serieuses_commencent($idEleve,$badges))
		$BDDnouveaux_badgesTXT.=",badgeChosesSerieusesCommencent,";

	//Update BDD
	str_replace(",,",",",$BDDnouveaux_badgesTXT);
	$req = $bdd->prepare('UPDATE utilisateurs SET nouveaux_badges=:nouveaux_badges WHERE id=:id');
	$req->execute(array('nouveaux_badges'=>$BDDnouveaux_badgesTXT,'id'=>$idEleve));
}



//VERIFIVATION BADGE : 1ere Connexion
function eligibleBadge_1ere_connexion($idEleve,$badges)
{
	if(!in_array("badge1ereConnexion",$badges))
	{
		global $bdd;
		$req = $bdd->prepare('SELECT derniere_connexion as dc FROM utilisateurs WHERE id=:id');
		$req->execute(array('id'=>$idEleve));
		$donnees=$req->fetch();
		if($donnees['dc']!=0)
			return true;
	}
	return false;
}



//VERIFIVATION BADGE : 1ere Brique (1er critere noté)
function eligibleBadge_1ere_brique($idEleve,$badges)
{
	if(!in_array("badge1ereBrique",$badges))
	{
		global $bdd;
		$req = $bdd->prepare('SELECT COUNT(DISTINCT indicateur) as c FROM notation WHERE eleve=:id');
		$req->execute(array('id'=>$idEleve));
		$donnees=$req->fetch();
		if($donnees['c']>0)
			return true;
	}
	return false;
}

//VERIFIVATION BADGE : Decollage (1er critere validé)
function eligibleBadge_decollage($idEleve,$badges)
{
	if(!in_array("badgeDecollage",$badges))
	{
		global $bdd;
		$req = $bdd->prepare("SELECT count(*) AS c FROM (SELECT note,indicateur FROM notation WHERE eleve=:id) as n JOIN indicateurs as i on n.indicateur=i.id WHERE n.note=i.niveaux");
		$req->execute(array('id'=>$idEleve));
		$donnees=$req->fetch();
		if($donnees['c']>0)
			return true;
	}
	return false;
}


//VERIFIVATION BADGE : choses_serieuses (5 criteres notés)
function eligibleBadge_choses_serieuses_commencent($idEleve,$badges)
{
	if(!in_array("badgeChosesSerieusesCommencent",$badges))
	{
		global $bdd;
		$req = $bdd->prepare('SELECT COUNT(DISTINCT indicateur) as c FROM notation WHERE eleve=:id');
		$req->execute(array('id'=>$idEleve));
		$donnees=$req->fetch();
		if($donnees['c']>=5)
			return true;
	}
	return false;
}


//Fonction qui fait passer un badge obtenu, mais pas encore annoncé, vers la liste des badges obtenus ET annoncés
function valideBadges($idEleve)
{
	global $bdd,$AUTORISE_BADGES;
	if($AUTORISE_BADGES)
	{
		$req = $bdd->prepare('SELECT badges as b,	nouveaux_badges as nb FROM utilisateurs WHERE id=:id');
		$req->execute(array('id'=>$idEleve));
		$donnees=$req->fetch();
		$total=
	str_replace(",,",",",$donnees["b"].",".$donnees['nb']);

		$req=$bdd->prepare('UPDATE utilisateurs SET badges="'.$total.'", nouveaux_badges="" WHERE id=:id');
		$req->execute(array('id'=>$idEleve));
	}
}




// =================================
// A SUPPRIMER ?????
//Renvoie une couleur de l'arc en ciel entre rouge et vert (pour les compétences)
function setArcEnCiel($val,$maxi)
{
	if($val<0)
		return "#FF0000";
	if($val>$maxi)
		return "#00FF00";
	$n=$val/$maxi;
	if($n<0.5)
		return "#FF".substr("00".dechex(2*$n*255),-2,2)."00";
	else
		return "#".substr("00".dechex((2-2*$n)*255),-2,2)."FF00";
}



// =================================
// A SUPPRIMER ?????

//Ecrit le code html pour afficher
//l'échelle de couleur
// Note : entier représentant la note courante
// $modifiable : true si c'est le prof (qui peut cliquer et modifier), false sinon
// $maxi : valeur maxi de l'échelle
// $indicateur donne le numéro ID de l'indicateur (pour pouvoir cliquer dessus et enregistrer la note)
function printEchelleCouleur($note,$maxi,$modifiable=false,$indicateur=0)
{
	for($i=0;$i<=$maxi;$i++)
	{
		
		$classCSS="indicateurEteint";
		$styleBackground="";
		$actionJS="";
		if($modifiable)
		{
			if($i<=$note)
				{$classCSS="indicateurAllumeModifiable";
				$styleBackground='background-color:'.setArcEnCiel($i,$maxi).';';}
			else
				$classCSS="indicateurEteintModifiable";
			$actionJS="donneNote(".$i.",$('#notationListeEleves').val(),".$indicateur.")";
		}
		elseif($i<=$note)
			{$classCSS="indicateurAllume";
			$styleBackground='background-color:'.setArcEnCiel($i,$maxi).';';}
		
		
		
		echo '
			<div class="'.$classCSS.'" style="'.$styleBackground.'" onclick="'.$actionJS.'">'.$i.'</div>';
		
	}
}





// ==============================================
// COMPETENCES
// ================================================



//Fonction renvoie la note maximale qu'on peut obtenir pour l'indicateur voulu*
//$indicateur = id de l'indicateur
function getNiveauMaxIndicateur($idIndicateur)
{
	global $bdd,$BDD_PREFIXE;
	$req = $bdd->prepare('SELECT niveaux FROM '.$BDD_PREFIXE.'indicateurs WHERE id=:id');
	$req->execute(array('id'=>$idIndicateur));
	if($donnees = $req->fetch())
		return $donnees["niveaux"];
	else
		return -1;//En cas de "pas d'indicateur"
}





//==================================================
//A SUPPRIMER !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//Fonction qui initialise la réponse XML en Ajax
function initReponseXML()
{
$initXML= <<<XML
<?xml version='1.0' standalone='yes'?>
<reponse>
	<messageRetour>Message Retour</messageRetour>
</reponse>
XML;
	return new SimpleXMLElement($initXML);
}

//Fonction qui initialise la réponse XML en Ajax
function initReponseJSON()
{
	$initJSON= array();
	$initJSON['messageRetour']="Message Retour";	//Message à afficher (ou pas) en retour de réponse
	$initJSON['debug']="(no comment)";	//Variable de debug

	return $initJSON;
}

?>
