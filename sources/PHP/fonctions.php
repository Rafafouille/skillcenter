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
		$_SESSION['classe']="";
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




function getBilanDomaines()
{
	$bilan=array();
	global $bdd,$BDD_PREFIXE;

	//Mise en place du tableau (sans les notes)
	$requeteIndicateurClasse="SELECT niveaux, competence FROM ".$BDD_PREFIXE."indicateurs as ind JOIN ".$BDD_PREFIXE."liensClassesIndicateurs as lie ON ind.id=lie.indicateur WHERE lie.classe='".$_SESSION['classe']."'";
	$requeteIndicateurClasseCompetences="SELECT i.niveaux AS niveaux, c.groupe AS idDomaine
		FROM (".$requeteIndicateurClasse.") AS i JOIN ".$BDD_PREFIXE."competences AS c ON i.competence=c.id";
	$requeteSommeIndicateurClasseCompetencesDomaine="SELECT g.id AS idDomaine, g.nom AS nom, SUM(ic.niveaux) AS sommeNiveaux
		FROM (".$requeteIndicateurClasseCompetences.") AS ic JOIN ".$BDD_PREFIXE."groupes_competences AS g ON ic.idDomaine=g.id GROUP BY g.id";
	$req = $bdd->query($requeteSommeIndicateurClasseCompetencesDomaine);

	while($donnees=$req->fetch())
	{
		$domaine=array(		"nom"=>$donnees["nom"],
							"id"=>$donnees["idDomaine"],
							"sommeNiveaux"=>intval($donnees["sommeNiveaux"]),
							"sommeEleve"=>0
				);
		$bilan[$donnees["idDomaine"]]=$domaine;
	}


	//Bilan des notes (à rajouter dans le tableau vierge)
	$requeteNotes ="SELECT MAX(note) as note, indicateur AS idInd FROM ".$BDD_PREFIXE."notation WHERE eleve=".$_SESSION['id']." GROUP BY indicateur";
	$requeteNotesInd="SELECT n.note AS note,i.competence AS idComp FROM (".$requeteNotes.") AS n JOIN ".$BDD_PREFIXE."indicateurs AS i ON n.idInd=i.id";
	$requeteNotesIndCom="SELECT ni.note AS note,c.groupe as idDomaine FROM (".$requeteNotesInd.") AS ni JOIN ".$BDD_PREFIXE."competences AS c ON ni.idComp=c.id";
	$requeteNotesIndComDom="SELECT sum(note) as sommeNote, g.nom AS nom, g.id AS idDomaine FROM (".$requeteNotesIndCom.") AS nic JOIN ".$BDD_PREFIXE."groupes_competences as g ON nic.idDomaine=g.id GROUP BY g.id";
	$req = $bdd->query($requeteNotesIndComDom);

	while($donnees=$req->fetch())
	{
		$bilan[$donnees["idDomaine"]]['sommeEleve']=intval($donnees["sommeNote"]);
	}
	return $bilan;
}





function getBilanCompetence($idDomaine)
{
	global $bdd,$BDD_PREFIXE;
	$bilan=array();
	
	$requeteIndicateurClasse="SELECT SUM(niveaux) AS somme, competence FROM ".$BDD_PREFIXE."indicateurs as ind JOIN ".$BDD_PREFIXE."liensClassesIndicateurs as lie ON ind.id=lie.indicateur WHERE lie.classe='".$_SESSION['classe']."' GROUP BY ind.competence";
	$requeteIndicateurClasseCompetences="SELECT i.somme AS sommeNiveaux, c.id AS idComp, c.nomAbrege AS nomAbrege,c.nom AS nom FROM (".$requeteIndicateurClasse.") AS i JOIN ".$BDD_PREFIXE."competences AS c ON i.competence=c.id WHERE c.groupe=".$idDomaine;
	
	$req = $bdd->query($requeteIndicateurClasseCompetences);
	while($donnees=$req->fetch())
	{
		$competence=array(	"nom"=>addslashes($donnees["nom"]),
							"nomAbrege"=>addslashes($donnees["nomAbrege"]),
							"sommeNiveaux"=>$donnees['sommeNiveaux'],
							"sommeEleve"=>0
				);
		$bilan[$donnees['idComp']]=$competence;
	}

	$requeteNote="SELECT MAX(note) AS maxi, indicateur FROM ".$BDD_PREFIXE."notation WHERE eleve=".$_SESSION['id']." GROUP BY indicateur";
	$requeteNoteInd="SELECT n.maxi,i.competence FROM (".$requeteNote.") AS n JOIN ".$BDD_PREFIXE."indicateurs AS i ON n.indicateur=i.id";
	$requeteNoteIndComp="SELECT SUM(ni.maxi) AS sommeEleve,c.id AS idComp, c.nom,c.groupe FROM (".$requeteNoteInd.") AS ni JOIN ".$BDD_PREFIXE."competences AS c ON ni.competence=c.id WHERE c.groupe=".$idDomaine." GROUP BY c.id";
	
	$req = $bdd->query($requeteNoteIndComp);
	while($donnees=$req->fetch())
	{
		$bilan[$donnees['idComp']]["sommeEleve"]=$donnees['sommeEleve'];
	}


		return $bilan;
}



// ===============================================
// HISTORIQUE
// ===========================================


function autoriseModifNoteSelonStatut($idNote)
{
		global $bdd,$BDD_PREFIXE;

		//Récupère l'id du proprio de la note (si c'est un auto évluateur)
		$req=$bdd->prepare("SELECT eleve FROM ".$BDD_PREFIXE."notation WHERE id=:id");
		$req->execute(array('id'=>$idNote));
		$idEleve=0;
		if($donnees=$req->fetch());
			$idEleve=intval($donnees['eleve']);

		return $_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['statut']=="autoeval" && $_SESSION['id']==$idEleve;
}




// ==============================================
// BADGES
// ================================================



//Fonction qui recupere la liste des badges (affichés ou pas encore...)
function getBadges($idEleve)
{
	global $bdd,$BDD_PREFIXE;

	//Récupère les badges déjà données
	$req = $bdd->prepare('SELECT badges,nouveaux_badges FROM '.$BDD_PREFIXE.'utilisateurs WHERE id=:id');
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
	global $bdd,$reponseJSON,$BDD_PREFIXE;

	list ($badges,$BDDnouveaux_badgesTXT)=getBadges($idEleve);//On récupere tous les badges (et les nouveaux)

	//1ere connexion
	if(eligibleBadge_1ere_connexion($idEleve,$badges))
		$BDDnouveaux_badgesTXT.=",badge1ereConnexion,";

	//Update BDD
	str_replace(",,",",",$BDDnouveaux_badgesTXT);
	$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'utilisateurs SET nouveaux_badges=:nouveaux_badges WHERE id=:id');
	$req->execute(array('nouveaux_badges'=>$BDDnouveaux_badgesTXT,'id'=>$idEleve));
}



//Fonction qui met à jour les badges AU MOMENT DE LA NOTATION
function updateBadges_aLaNotation($idEleve)
{
	global $bdd,$reponseJSON,$BDD_PREFIXE;

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

	//Tache d'huile
	if(eligibleBadge_tache_dhuile($idEleve,$badges))
		$BDDnouveaux_badgesTXT.=",badgeTacheDHuile,";

	//Update BDD
	str_replace(",,",",",$BDDnouveaux_badgesTXT);
	$req = $bdd->prepare('UPDATE '.$BDD_PREFIXE.'utilisateurs SET nouveaux_badges=:nouveaux_badges WHERE id=:id');
	$req->execute(array('nouveaux_badges'=>$BDDnouveaux_badgesTXT,'id'=>$idEleve));
}



//VERIFIVATION BADGE : 1ere Connexion
function eligibleBadge_1ere_connexion($idEleve,$badges)
{
	if(!in_array("badge1ereConnexion",$badges))
	{
		global $bdd,$BDD_PREFIXE;
		$req = $bdd->prepare('SELECT derniere_connexion as dc FROM '.$BDD_PREFIXE.'utilisateurs WHERE id=:id');
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
		global $bdd,$BDD_PREFIXE;
		$req = $bdd->prepare('SELECT COUNT(DISTINCT indicateur) as c FROM '.$BDD_PREFIXE.'notation WHERE eleve=:id');
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
		global $bdd,$BDD_PREFIXE;
		$req = $bdd->prepare("SELECT count(*) AS c FROM (SELECT note,indicateur FROM ".$BDD_PREFIXE."notation WHERE eleve=:id) as n JOIN ".$BDD_PREFIXE."indicateurs as i on n.indicateur=i.id WHERE n.note=i.niveaux");
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
		global $bdd,$BDD_PREFIXE;
		$req = $bdd->prepare('SELECT COUNT(DISTINCT indicateur) as c FROM '.$BDD_PREFIXE.'notation WHERE eleve=:id');
		$req->execute(array('id'=>$idEleve));
		$donnees=$req->fetch();
		if($donnees['c']>=5)
			return true;
	}
	return false;
}



//VERIFIVATION BADGE : Tache d'huile (1 critère noté à "0")
function eligibleBadge_tache_dhuile($idEleve,$badges)
{
	if(!in_array("badgeTacheDHuile",$badges))
	{
		global $bdd,$BDD_PREFIXE;
		$req = $bdd->prepare("SELECT count(*) as c FROM ".$BDD_PREFIXE."notation WHERE eleve=:id AND note=0");
		$req->execute(array('id'=>$idEleve));
		$donnees=$req->fetch();
		if($donnees['c']>0)
			return true;
	}
	return false;
}

//Fonction qui fait passer un badge obtenu, mais pas encore annoncé, vers la liste des badges obtenus ET annoncés
function valideBadges($idEleve)
{
	global $bdd,$AUTORISE_BADGES,$BDD_PREFIXE;
	if($AUTORISE_BADGES)
	{
		$req = $bdd->prepare('SELECT badges as b,	nouveaux_badges as nb FROM '.$BDD_PREFIXE.'utilisateurs WHERE id=:id');
		$req->execute(array('id'=>$idEleve));
		$donnees=$req->fetch();
		$total=
	str_replace(",,",",",$donnees["b"].",".$donnees['nb']);

		$req=$bdd->prepare('UPDATE '.$BDD_PREFIXE.'utilisateurs SET badges="'.$total.'", nouveaux_badges="" WHERE id=:id');
		$req->execute(array('id'=>$idEleve));
	}
}



// ======================================
// COMPETENCES
// ====================================

//Fonction qui supprime un indicateur
function supprimeIndicateur($idIndicateur)
{
	global $bdd,$BDD_PREFIXE;
	$req = $bdd->prepare('DELETE FROM '.$BDD_PREFIXE.'indicateurs WHERE id=:idIndicateur');
	$req->execute(array('idIndicateur' => $idIndicateur));
}

//Fonction qui supprime une compétence
function supprimeCompetence($idCompetence,$supprInd=true)
{
	global $bdd,$BDD_PREFIXE;
	if($supprInd)
	{
		$req = $bdd->prepare('DELETE FROM '.$BDD_PREFIXE.'indicateurs WHERE competence=:idComp');
		$req->execute(array('idComp' => $idCompetence));
	}
	$req = $bdd->prepare('DELETE FROM '.$BDD_PREFIXE.'competences WHERE id=:idCompetence');
	$req->execute(array('idCompetence' => $idCompetence));
}


//Fonction qui supprime une compétence
function supprimeDomaine($idDomaine,$supprComp=true,$supprInd=true)
{
	global $bdd,$BDD_PREFIXE,$reponseJSON;

	if($supprComp)
	{
		$req = $bdd->prepare('SELECT id FROM '.$BDD_PREFIXE.'competences WHERE groupe=:idDomaine');
		$req->execute(array('idDomaine' => $idDomaine));
		while($donnees=$req->fetch())
		{
			supprimeCompetence($donnees['id'],$supprInd);
		}
	}
	$req = $bdd->prepare('DELETE FROM '.$BDD_PREFIXE.'groupes_competences WHERE id=:idDomaine');
	$req->execute(array('idDomaine' => $idDomaine));
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




//Fonction qui initialise la réponse XML en Ajax
function initReponseJSON()
{
	$initJSON= array();
	$initJSON['messageRetour']="Message Retour";	//Message à afficher (ou pas) en retour de réponse
	$initJSON['debug']="(no comment)";	//Variable de debug

	return $initJSON;
}

?>
