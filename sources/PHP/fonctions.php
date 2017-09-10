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
// UTILISATEUR
// ================================================

//Envoie un bilan à l'utilisateur. Renvoie 1 si ca a marché et false sinon.
function envoieBilan($id)
{
	global $bdd,$BDD_PREFIXE;
	$req=$bdd->prepare("SELECT nom, prenom, mail, date_dernier_envoi_bilan, notifieMail FROM ".$BDD_PREFIXE."utilisateurs WHERE id=:id AND mail<>''");
	$req->execute(array('id'=>$id));
	if($donnee=$req->fetch())
	{
		if(intval($donnee["notifieMail"]))
		{
			$sujet="[SkillCenter] Bilan des compétences";
			$mail=$donnee["mail"];

			//Header du mail
			$passage_ligne = "\n";//Choix du retour à la ligne selon serveur
			if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail))
				$passage_ligne = "\r\n";
			$boundary = "-----=".md5(rand());
			/*$header = "From: \"SkillCenter - Ne pas ".mb_encode_mimeheader(utf8_decode("répondre"))." -\" <noreply@allais.eu>".$passage_ligne;*/
			/*$header .= "Reply-to: noreply@allais.eu".$passage_ligne;*/
			$header = "MIME-Version: 1.0".$passage_ligne;
			$header .= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;



			$req2=$bdd->prepare("SELECT MAX(n.note) AS note,i.nom AS nomInd, i.id AS idInd, i.niveaux AS niveaux, c.nom AS nomComp, c.id AS idComp, g.nom AS nomGr, g.id AS idGr FROM ".$BDD_PREFIXE."notation AS n JOIN ".$BDD_PREFIXE."indicateurs AS i ON n.indicateur=i.id JOIN ".$BDD_PREFIXE."competences AS c ON i.competence=c.id JOIN ".$BDD_PREFIXE."groupes_competences AS g ON c.groupe=g.id WHERE n.date>'".$donnee['date_dernier_envoi_bilan']."' AND n.eleve=:id GROUP BY n.indicateur ORDER BY g.id,c.id,i.id");
			$req2->execute(array('id'=>$id));

			//Début message txt
			$messageTXT="Bonjour ".ucfirst($donnee["prenom"])." ".strtoupper($donnee["nom"])." !".$passage_ligne.$passage_ligne."Vous avez récemment obtenu les évaluations suivantes :".$passage_ligne;
			//Début Message HTML
			$messageHTML = "<html><head>
<style type=\"text/css\">

	h2
	{
		background-color:#CCCCFF;
		border-radius:5px;
		padding:5px;
		padding-left:20px;
	}

	.case_critere
	{
		display:inline-block;
		width:20px;
		height:20px;
		text-align:center;
		border-radius:4px;
		font-size:small;
		margin:2px;
	}
	.case_critere span
	{
		vertical-align:middle;
	}
	td
	{
		padding-left:40px;
	}
	.nomCritere::before
	{ 
	  content: \"⊕ \";
  	color: blue;
	}
	#signature
	{
		font-style:italic;
		color:gray;
		border-top:solid gray;
		padding-top:20px;
		margin-top:20px;
	}
</style>
</head><body>";
			$messageHTML.="<h1>Bonjour ".ucfirst($donnee["prenom"])." ".strtoupper($donnee["nom"]).",</h1>

	<p>Vous avez récemment obtenu les évaluations suivantes :</p>".$passage_ligne.$passage_ligne;
			//Liste des criteres
			$domaine=-1;
			$competence=-1;
			$indicateur=-1;


			while($data2=$req2->fetch())	//Pour chaque compétence
			{
				if($domaine!=intval($data2["idGr"]) && $domaine!=-1 || $competence!=intval($data2["idComp"]) && $competence!=-1) //Si on finit une section...
					$messageHTML.="</table>".$passage_ligne;//On finit un tableau
				if($domaine!=intval($data2["idGr"]))//Si on change de domaine
				{
					$domaine=intval($data2["idGr"]);
					$messageHTML.="<h2>".$data2["nomGr"]."</h2>".$passage_ligne;
					$messageTXT.=$passage_ligne."=============================".$passage_ligne.$data2["nomGr"].$passage_ligne."=============================".$passage_ligne;
				}
				if($competence!=intval($data2["idComp"]))//Si on change de competences
				{
					$competence=intval($data2["idComp"]);
					$messageHTML.=$passage_ligne.$passage_ligne."<h3>".$data2["nomComp"]."</h3>".$passage_ligne;
					$messageHTML.="<table>".$passage_ligne;
					$messageTXT.=$passage_ligne."*** ".$data2["nomComp"]." ***".$passage_ligne;
				}

				$messageHTML.="<tr>".$passage_ligne."	<td class=\"nomCritere\">".$data2["nomInd"]."</td>".$passage_ligne."	<td>".afficheNiveauDansMailHTML($data2["note"],$data2["niveaux"])."</td>".$passage_ligne."</tr>".$passage_ligne;

				$messageTXT.="  - ".(sizeof($data2["nomInd"])<40?substr($data2["nomInd"]."                                                                                             ",0,40):$data2["nomInd"])."   ".afficheNiveauDansMailTXT($data2["note"],$data2["niveaux"]).$passage_ligne;

			}
			$messageHTML.="</table>".$passage_ligne;
			$messageHTML.="<div id=\"signature\">".$passage_ligne."	<strong>Robot de SkillCenter<strong>".$passage_ligne."	<br/><span style=\"font-size:small;\">Ce message a été envoyé automatiquement.<br/>Merci de ne pas y répondre.</br>Si vous ne souhaitez plus recevoir ce genre de mail,<br/>contactez votre enseignant, administrateur ou responsable de l'évaluation.</span>".$passage_ligne."</div>".$passage_ligne;
			$messageHTML.="</body></html>".$passage_ligne;
			$messageTXT.="-- ".$passage_ligne."SkillCenter".$passage_ligne.$passage_ligne."Ce message a été envoyé automatiquement.".$passage_ligne."Merci de ne pas y répondre.".$passage_ligne."Si vous ne souhaitez plus recevoir ce genre de mail,".$passage_ligne."contactez votre enseignant, administrateur ou responsable de l'évaluation.";
		
			//Message complet
			$message = $passage_ligne."--".$boundary.$passage_ligne;
			//Message au format TXT
			$message.="Content-Type: text/plain; charset=\"UTF-8\"".$passage_ligne;
			$message.="Content-Transfer-Encoding: 8bit".$passage_ligne;
			$message.=$passage_ligne.$messageTXT.$passage_ligne;
			//Fin du message TXT
			$message .= $passage_ligne."--".$boundary.$passage_ligne;
			//Ajout du message HTML
			$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
			$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
			$message.= $passage_ligne.$messageHTML.$passage_ligne;
			//==========
			$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
			$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
			//==========
			//Envoi


			return mail($mail,mb_encode_mimeheader(utf8_decode($sujet),"UTF-8"),$message);//,$header);
		}//Fin du "si on peut notifier"
	}
	return 0;
}

//Fonction qui affiche les petites cases de niveau
function afficheNiveauDansMailHTML($val,$max)
{
	global $INTITULES_NIVEAUX_CRITERES,$passage_ligne;
	$val=intval($val);$max=intval($max);
	if($max>sizeof($INTITULES_NIVEAUX_CRITERES)) $max=sizeof($INTITULES_NIVEAUX_CRITERES);
	if($val>$max) $val=$max;
	$contenu="";
	for($i=0;$i<=$max;$i++)
	{
		$contenu.="<div class=\"case_critere\" style=\"background-color:".($i<=$val?setArcEnCiel($val,$max).";":"#DDDDDD;")."\"><span>";
		if($i!=$val)	$contenu.="&nbsp;";
		else	$contenu.=substr($INTITULES_NIVEAUX_CRITERES[$max-1][$val]." ",0,2);
		$contenu.="</span></div>".$passage_ligne;
	}
	return $contenu;
}
function afficheNiveauDansMailTXT($val,$max)
{
	global $INTITULES_NIVEAUX_CRITERES;
	$val=intval($val);$max=intval($max);
	if($max>sizeof($INTITULES_NIVEAUX_CRITERES)) $max=sizeof($INTITULES_NIVEAUX_CRITERES);
	if($val>$max) $val=$max;

	$contenu="";
	for($i=0;$i<=$max;$i++)
	{
		$contenu.="[";
		if($i>$val)	$contenu.="  ";
		elseif($i<$val)	$contenu.="**";
		else	$contenu.=substr($INTITULES_NIVEAUX_CRITERES[$max-1][$val]." ",0,2);
		$contenu.="]";
	}
	return $contenu;
}

// ==============================================
// BILAN
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


	//On liste les bilans et leur bareme
	$requete="SELECT
	dom.id AS idDomaine, dom.nom AS nom, SUM( ind.niveaux ) AS sommeNiveaux
	FROM ".$BDD_PREFIXE."indicateurs AS ind
	JOIN ".$BDD_PREFIXE."liensClassesIndicateurs AS lie ON ind.id = lie.indicateur
	JOIN ".$BDD_PREFIXE."competences AS comp ON ind.competence = comp.id
	JOIN ".$BDD_PREFIXE."groupes_competences AS dom ON comp.groupe = dom.id
	WHERE lie.classe ='".$_SESSION['classe']."'
	GROUP BY dom.id";

	$req = $bdd->query($requete);//$requeteSommeIndicateurClasseCompetencesDomaine);

	while($donnees=$req->fetch())
	{
		$domaine=array(		"nom"=>$donnees["nom"],
							"id"=>$donnees["idDomaine"],
							"sommeNiveaux"=>intval($donnees["sommeNiveaux"]),
							"sommeEleve"=>0
				);
		$bilan[$donnees["idDomaine"]]=$domaine;
	}


	//On liste la somme des notes par domaine, de l'eleve
	$requete="SELECT
	SUM(note) AS sommeNote, idDomaine
	FROM
	(
	SELECT MAX(notes.note) AS note, dom.id AS idDomaine
	FROM ".$BDD_PREFIXE."notation AS notes 
	JOIN ".$BDD_PREFIXE."indicateurs AS ind ON notes.indicateur=ind.id
	JOIN ".$BDD_PREFIXE."liensClassesIndicateurs AS lie ON lie.indicateur=ind.id
	JOIN ".$BDD_PREFIXE."competences AS comp ON ind.competence=comp.id
	JOIN ".$BDD_PREFIXE."groupes_competences AS dom ON comp.groupe=dom.id
	WHERE lie.classe='".$_SESSION['classe']."' AND notes.eleve='".$_SESSION['id']."'
	GROUP BY ind.id
	) AS maxs
	GROUP BY idDomaine";

	$req = $bdd->query($requete);

	while($donnees=$req->fetch())
		$bilan[$donnees["idDomaine"]]['sommeEleve']=intval($donnees["sommeNote"]);

	//Retour
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

//Renvoie une couleur de l'arc en ciel entre rouge et vert (pour les compétences)
//Utile pour les mails
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
/*function printEchelleCouleur($note,$maxi,$modifiable=false,$indicateur=0)
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
}*/





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
