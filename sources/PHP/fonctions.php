<?php
include_once('options.php');

// GRAINS DE SEL pour le cryptage
$SALT = "$232#;E";
$SALT2 = "$232#;E$232#;E$232#;E$232#;E$232#;E$232#;E$232#;E";


// SUPPRIMER LES WARNING POUR EVITER D'AVOIR DES PARASITES DANS LES RETOURS
error_reporting(E_ERROR | E_PARSE);

//Lance la session et initialise les variables associées
function initSession()
{
	session_start();
	if(!isset($_SESSION['statut']))
	{
		$_SESSION['statut']="";
		$_SESSION['nom']="";
		$_SESSION['prenom']="";
		$_SESSION['id'] = 0;	//ATTENTION : 0 = pas connecté.
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

//Récupere le prochain id de libre pour la table "table"
//On suppose que la BDD est déjà ouverte
function getNextFreeIdOfTable($DB,$table)
{
	$req=$DB->query("SELECT IFNULL(MAX(id),0) AS m FROM ".$table);
	$data=$req->fetch();
	return intval($data['m'])+1;
}

# Verifie qu'un mot de passe rentrée (non crypté) d'un utilisateur ($mdp_check, non crypté)
# matche avec un mot de passe crypté de la BDD ($mdp_crypte_BDD)
# Teste selon les versions du site
function verif_MDP_BDD_crypt($mdp_check,$mdp_crypte_BDD)
{
	if($mdp_check == $mdp_crypte_BDD)	# Vieilles versions de Skillcenter (non crypté). Est-ce bien de le garder ?
		return true;
	if(crypt($mdp_check,$SALT) == $mdp_crypte_BDD)	# Version avec crypt (et petit salt)
		return true;
	if(password_verify($mdp_check,$mdp_crypte_BDD))	# Version avec password_hash (plus récent)
		return true;
	return false;
}

//Fonction qui permet d'envoyer un mail *************************************
// La bibliotheque PHPMailerAutoload.php doit être chargée
function envoieMail($adresse,$sujet,$contenuHTML,$contenuTXT)
{
	global $AUTORISE_MAILS,$MAIL_SMTP,$MAIL_SMTP_SECURE,$MAIL_SMTP_HOTE,$MAIL_SMTP_PORT,$MAIL_SMTP_LOGIN,$MAIL_SMTP_MDP,$MAIL_MAIL_EXPEDITEUR,$MAIL_NOM_EXPEDITEUR,$MAIL_MAIL_REPONDRE_A;
	if($AUTORISE_MAILS)
	{
		$mail = new PHPMailer\PHPMailer\PHPMailer();	//Nouveau mail
		if($MAIL_SMTP)
			$mail->IsSMTP();	//Indique qu'on passe par du SMTP
		$mail->SMTPDebug = 0;	//Permet de degguer (0= non, 1=tout, 2=juste message, 3=erreurs de réseau/serveur)
		$mail->CharSet="UTF-8";
		$mail->SMTPSecure = $MAIL_SMTP_SECURE;	//Type de cryptage
//		$mail->SMTPAutoTLS = false;
		$mail->Host = $MAIL_SMTP_HOTE;	//Hote SMTP
		$mail->Port = $MAIL_SMTP_PORT;	//Port
		$mail->Username = $MAIL_SMTP_LOGIN;	//Login
		$mail->Password = $MAIL_SMTP_MDP;
		$mail->SMTPAuth = true;	//Active l'autentification SMTP
		$mail->AddAddress($adresse);
		$mail->From = $MAIL_MAIL_EXPEDITEUR;
		$mail->FromName = $MAIL_NOM_EXPEDITEUR;
		$mail->AddReplyTo($MAIL_MAIL_REPONDRE_A, 'Skillcenter');

		$mail->IsHTML(true);
		$mail->Subject    = $sujet;
		$mail->AltBody    = $contenuTXT;
		$mail->Body    = $contenuHTML;
		

		if(!$mail->Send())
			return  ":(Erreur d'envoi de mail : ". $mail->ErrorInfo;
		else
			return ":)Mail envoyé";
	}
	else //Si pas autorise mail
		return ":(L'envoi de mail est desactivé";
}


//Fonction qui renvoie la version "francaise" de la date *******************
function getDateEnFrancaisFromDateSQL($date)
{
	if($date=="0000-00-00 00:00:00")
		return "début";

	$timestamp=strtotime($date);

	setlocale(LC_TIME, "fr_FR");
	return strftime("%A %e %B %Y - %k:%M",$timestamp);//date("l d F Y - H:i",$timestamp);
}





// ============================================================================================================
// UTILISATEUR
// ============================================================================================================


//Entete du mail TXT *********************************************************
function debutMailBilanTXT($prenom,$nom,$date_derniere_evaluation)
{
	global $TITRE_PAGE;
	return "Bonjour ".ucfirst($prenom)." ".strtoupper($nom)." !\n\nVous avez récemment obtenu les évaluations suivantes sur le site \"".$TITRE_PAGE."\" :\n(Depuis le ".getDateEnFrancaisFromDateSQL($date_derniere_evaluation).")\n\n";
}

//Entete du mail HTML ********************************************************
function debutMailBilanHTML($prenom,$nom,$date_derniere_evaluation)
{
	global $TITRE_PAGE;
	$texte="<html><head>
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
</head><body>\n";
			$texte.="<h1>Bonjour ".ucfirst($prenom)." ".strtoupper($nom).",</h1>

	<p>Vous avez récemment obtenu les évaluations suivantes sur le site \"".$TITRE_PAGE."\":</p>\n
	<p style=\"font-size:small;\">(Depuis le ".getDateEnFrancaisFromDateSQL($date_derniere_evaluation).")</p>\n\n";

	return $texte;
}

//Renvoi le tableau bilan de l'utilisateur n°id, sous forme de tableau [$messageTXT,$messageHTML] *********************************************
function milieuMailBilan($id,$date_dernier_envoi_bilan)
{
	global $bdd,$BDD_PREFIXE;

	$messageTXT="";
	$messageHTML="";

	//On recupere les valeurs sur la BDD
	$req2=$bdd->prepare("SELECT MAX(n.note) AS note,i.nom AS nomInd, i.id AS idInd, i.niveaux AS niveaux, c.nom AS nomComp, c.id AS idComp, g.nom AS nomGr, g.id AS idGr FROM ".$BDD_PREFIXE."notation AS n JOIN ".$BDD_PREFIXE."indicateurs AS i ON n.indicateur=i.id JOIN ".$BDD_PREFIXE."competences AS c ON i.competence=c.id JOIN ".$BDD_PREFIXE."groupes_competences AS g ON c.groupe=g.id WHERE n.date>'".$date_dernier_envoi_bilan."' AND n.eleve=:id GROUP BY n.indicateur ORDER BY g.id,c.id,i.id");
	$req2->execute(array('id'=>$id));

	//Liste des criteres
			$domaine=-1;
			$competence=-1;
			$indicateur=-1;
			while($data2=$req2->fetch())	//Pour chaque compétence
			{
				if($domaine!=intval($data2["idGr"]) && $domaine!=-1 || $competence!=intval($data2["idComp"]) && $competence!=-1) //Si on finit une section...
					$messageHTML.="</table>\n";//On finit un tableau
				if($domaine!=intval($data2["idGr"]))//Si on change de domaine
				{
					$domaine=intval($data2["idGr"]);
					$messageHTML.="<h2>".$data2["nomGr"]."</h2>\n";
					$messageTXT.="\n=============================\n".$data2["nomGr"]."\n=============================\n";
				}
				if($competence!=intval($data2["idComp"]))//Si on change de competences
				{
					$competence=intval($data2["idComp"]);
					$messageHTML.="\n\n<h3>".$data2["nomComp"]."</h3>\n";
					$messageHTML.="<table>\n";
					$messageTXT.="\n*** ".$data2["nomComp"]." ***\n";
				}

				$messageHTML.="<tr>\n <td class=\"nomCritere\">".$data2["nomInd"]."</td>\n <td>".afficheNiveauDansMailHTML($data2["note"],$data2["niveaux"])."</td>\n</tr>\n";

				$messageTXT.="  - ".(strlen($data2["nomInd"])<40?substr($data2["nomInd"]."                                                                                             ",0,40):$data2["nomInd"])."   ".afficheNiveauDansMailTXT($data2["note"],$data2["niveaux"])."\n";

			}
			$messageHTML.="</table>\n";
			$messageTXT.="\n=============================\n";

	return array($messageTXT,$messageHTML);	
}

//Signature à mettre en fin de mail TXT *************************************************************************
function signatureMailBilanTXT()
{
	return "\n-- \nSkillCenter\nCe message a été envoyé automatiquement.\nMerci de ne pas y répondre.\nSi vous ne souhaitez plus recevoir ce genre de mail,\ncontactez votre enseignant, administrateur ou responsable de l'évaluation.";
}

//Signature a mettre en fin de mail HTML *************************************************************************
function signatureMailBilanHTML()
{
	$texte="<div id=\"signature\">\n	<strong>Robot de SkillCenter<strong>\n	<br/><span style=\"font-size:small;\">Ce message a été envoyé automatiquement.<br/>Merci de ne pas y répondre.</br>Si vous ne souhaitez plus recevoir ce genre de mail,<br/>contactez votre enseignant, administrateur ou responsable de l'évaluation.</span>\n</div>\n";
	$texte.="</body></html>\n";
	return $texte;
}


//Fonction qui dit si, oui ou non, il y a eu des évaluations depuis la dernière évaluation pour l'user n°id*****************************
function aEuDesEvaluationDepuisLaDerniereDate($id,$date_dernier_envoi)
{
	//$date_dernier_envoi = "2020-08-23 15:42:13";//A SUPPRIMER
	global $bdd,$BDD_PREFIXE;
	$req=$bdd->prepare("SELECT * FROM ".$BDD_PREFIXE."notation WHERE eleve=:id AND date>:dateDernierEnvoi LIMIT 1");
	$req->execute(array('id'=>$id,'dateDernierEnvoi'=>$date_dernier_envoi));
	return $data=$req->fetch();
}


//Envoie un bilan à l'utilisateur. Renvoie 1 si ca a marché et false sinon. *******************************************
function envoieBilan($id)
{
	global $bdd,$BDD_PREFIXE;
	$req=$bdd->prepare("SELECT nom, prenom, mail, date_dernier_envoi_bilan, notifieMail FROM ".$BDD_PREFIXE."utilisateurs WHERE id=:id AND mail<>''");
	$req->execute(array('id'=>$id));
	if($donnee=$req->fetch())
	{
		if(intval($donnee["notifieMail"]))//Si on peut envoyer le mail (l'utilisateur accepte et le mail existe)
		{
			if($donnee['mail']!="")
			{
				if(aEuDesEvaluationDepuisLaDerniereDate($id,$donnee['date_dernier_envoi_bilan']))
				{
					$sujet="[SkillCenter] Bilan des compétences";
					$adresse=$donnee["mail"];

					//Début message
					$messageTXT=debutMailBilanTXT($donnee["prenom"],$donnee["nom"],$donnee['date_dernier_envoi_bilan']);
					$messageHTML = debutMailBilanHTML($donnee["prenom"],$donnee["nom"],$donnee['date_dernier_envoi_bilan']);

					//Bilan (milieu message)
					list($mTXT,$mHTML)=milieuMailBilan($id,$donnee['date_dernier_envoi_bilan']);
					$messageHTML.=$mHTML;
					$messageTXT.=$mTXT;

					//Signature
					$messageHTML.=signatureMailBilanHTML();
					$messageTXT.=signatureMailBilanTXT();

					//Update de la derniere date
					$req3=$bdd->prepare("UPDATE ".$BDD_PREFIXE."utilisateurs SET date_dernier_envoi_bilan=NOW() WHERE id=:id");
					$req3->execute(array('id'=>$id));

					//Envoi
					//return mail($mail,mb_encode_mimeheader(utf8_decode($sujet),"UTF-8"),$message);//,$header);
					return envoieMail($adresse,$sujet,$messageHTML,$messageTXT);
				}
				else
					return ":|Il n'y a pas de nouvelle évaluation depuis la dernière fois.";
			}
			else
				return ":(L'utilisateur n'a pas de mail.";
		}//Fin du "si on peut notifier"
		else
			return ":(L'utilisateur ne souhaite pas recevoir de mails.";
	}
	return 0;
}

//Fonction qui affiche les petites cases de niveau *************
function afficheNiveauDansMailHTML($val,$max)
{
	global $INTITULES_NIVEAUX_CRITERES;
	$val=intval($val);$max=intval($max);
	if($max>sizeof($INTITULES_NIVEAUX_CRITERES)) $max=sizeof($INTITULES_NIVEAUX_CRITERES);
	if($val>$max) $val=$max;
	$contenu="";
	for($i=0;$i<=$max;$i++)
	{
		$contenu.="<div class=\"case_critere\" style=\"background-color:".($i<=$val?setArcEnCiel($val,$max).";":"#DDDDDD;")."\"><span>";
		if($i!=$val)	$contenu.="&nbsp;";
		else	$contenu.=substr($INTITULES_NIVEAUX_CRITERES[$max-1][$val]." ",0,2);
		$contenu.="</span></div>\n";
	}
	return $contenu;
}

//***************************************************
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

//Fonction qui supprime une compétenceVERIF
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









// ================================================
// COMPETENCES
// ================================================

//Fonction qui renvoie la réponse de la requete qui récupère tous les indicateurs éventuellement filtrés par un contexte
function requeteGetListeIndicateurs($classe="ALL_CLASSES", $contexte=0)
{
	global $bdd,$BDD_PREFIXE;
	
	$variableRequette = array();
	
	
	$requete_liste_indicateurs_dans_contexte ='SELECT

gr.id AS idGroup, gr.nom AS nomGroup, gr.position AS positionGroup,
comp.id AS idComp, comp.nom AS nomComp, comp.nomAbrege AS nomCompAbrege, comp.position AS positionComp,
ind.id AS idInd, ind.nom AS nomInd, ind.details AS detailsInd, ind.niveaux AS niveauxInd, ind.lien AS lienInd, ind.position AS positionInd

FROM '.$BDD_PREFIXE.'indicateurs AS ind JOIN '.$BDD_PREFIXE.'competences AS comp JOIN '.$BDD_PREFIXE.'groupes_competences AS gr
JOIN '.$BDD_PREFIXE.'liensClassesIndicateurs AS lci '.
($contexte!="ALL_CONTEXTE"?'JOIN '.$BDD_PREFIXE.'liensIndicateursContextes AS lic JOIN '.$BDD_PREFIXE.'contextes AS cont':'').'

ON ind.competence=comp.id AND comp.groupe=gr.id
AND lci.indicateur=ind.id '.
($contexte!="ALL_CONTEXTE"?'AND lic.indicateur=ind.id AND lic.contexte=cont.id':'');

if($contexte or $classe != "ALL_CLASSES")
	$requete_liste_indicateurs_dans_contexte .= " WHERE ";

if($contexte)
	{
		//echo "contexte !!!!\n";
		$requete_liste_indicateurs_dans_contexte .= " cont.id=:contexte";
		$variableRequette["contexte"] = $contexte;
	}

if($contexte and $classe != "ALL_CLASSES")
	$requete_liste_indicateurs_dans_contexte .= " AND ";
	
if($classe != "ALL_CLASSES")
	{
		//echo "classe !!!!\n";
		$requete_liste_indicateurs_dans_contexte .= " lci.classe=:classe";
		$variableRequette["classe"] = $classe;
	}

//echo $requete_liste_indicateurs_dans_contexte;


	$requete = $bdd->prepare($requete_liste_indicateurs_dans_contexte);
	$requete->execute($variableRequette);

	return $requete;
}




// Identique à requeteGetListeIndicateurs, mais qui renvoie le résultat sous forme de talbeau associatif hierarchique :
// GROUPE 1
//	-> id
//	-> nom
//	-> seleted (?)
//	-> listecompetences
//		-> idCompetence1
//			-> id
//			-> nom
//			-> nomAbrege
//			-> selected (?)
//			-> listeInicateurs
//				-> idIndicateur1
//					-> id
function getListeIndicateursInArray($classe="ALL_CLASSES", $contexte=0)
{
	$requete = requeteGetListeIndicateurs($classe,$contexte);
	
	$tableau = array();
	
	
	while($reponse=$requete->fetch())
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
		if(!isset($tableau[$idGroup]))
		{
			$tableau[$idGroup]["id"]=$idGroup;
			$tableau[$idGroup]["nom"]=$nomGroup;
			$tableau[$idGroup]["selected"]=true;
			$tableau[$idGroup]["listeCompetences"]=array();
			$tableau[$idGroup]["nbIndicateurs"]=0;
			$tableau[$idGroup]["nbCompetences"]=0;
		}
		//Si la compétence n'existe pas...
		if(!isset($tableau[$idGroup]["listeCompetences"][$idComp]))
		{
			$tableau[$idGroup]["listeCompetences"][$idComp]["id"]=$idComp;
			$tableau[$idGroup]["listeCompetences"][$idComp]["nom"]=$nomComp;
			$tableau[$idGroup]["listeCompetences"][$idComp]["nomAbrege"]=$nomCompAbrege;
			$tableau[$idGroup]["listeCompetences"][$idComp]["selected"]=true;
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"]=array();
			$tableau[$idGroup]["listeCompetences"][$idComp]["nbIndicateurs"]=0;
			//Comptage
			$tableau[$idGroup]["nbCompetences"]++;
		}

		if(!isset($tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]))
		{

			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["id"]=$idInd;
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["nom"]=$nomInd;
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["details"]=$detailsInd;
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauMax"]=$niveauxInd;
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["lien"]=$lienInd;
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMax"]=-1;//Par defaut
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveMoy"]=-1;//Par defaut
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["niveauEleveLast"]=-1;//Par defaut
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["commentaires"]=false;//Dit si il y a des commentaires ou non
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["selected"]=true;
			$tableau[$idGroup]["listeCompetences"][$idComp]["listeIndicateurs"][$idInd]["position"]=$positionInd;
			//Comptage
			$tableau[$idGroup]["nbIndicateurs"]++;
			$tableau[$idGroup]["listeCompetences"][$idComp]["nbIndicateurs"]++;
		}
	}
				
	return $tableau;		
}









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




// Fonction qui renvoie un tableau [domaine1 : [competence1 : [indicateur1, indicateur2, ....], competence2:...], domaine 2 :[...] ]
function getListeCompetences()
{
	global $bdd,$BDD_PREFIXE;
	
	$req = $bdd->prepare('SELECT g.nom AS groupe_nom, g.id AS groupe_id, c.nom AS comp_nom, c.id AS comp_id, i.nom AS ind_nom, i.id AS ind_id FROM '.$BDD_PREFIXE.'indicateurs AS i JOIN '.$BDD_PREFIXE.'competences AS c JOIN '.$BDD_PREFIXE.'groupes_competences AS g ON i.competence=c.id AND c.groupe=g.id ORDER BY g.position, c.position, i.position');
	$req->execute();
	
	$tab = array();
	while($data = $req->fetch())
	{
		//Si le groupe n'existe pas
		if(!isset($tab[$data['groupe_id']]))
		{
			$tab[$data['groupe_id']]["nom"] = $data['groupe_nom'];

			$tab[$data['groupe_id']]["id"] = $data['groupe_id'];
			$tab[$data['groupe_id']]["nb_indicateurs"] = 0;
			$tab[$data['groupe_id']]["competences"] = array();
		}
		//Si la competence n'existe pas
		if(!isset($tab[$data['groupe_id']]['competences'][$data['comp_id']]))
		{
			$tab[$data['groupe_id']]['competences'][$data['comp_id']]['nom'] = $data["comp_nom"];
			$tab[$data['groupe_id']]['competences'][$data['comp_id']]['id'] = $data["comp_id"];
			$tab[$data['groupe_id']]['competences'][$data['comp_id']]['nb_indicateurs'] = 0;
			$tab[$data['groupe_id']]['competences'][$data['comp_id']]["indicateurs"] = array();
		}
		// On ajoute l'indicateur
		$tab[$data['groupe_id']]['competences'][$data['comp_id']]["indicateurs"][$data['ind_id']]['nom'] = $data['ind_nom'];
		$tab[$data['groupe_id']]['competences'][$data['comp_id']]["indicateurs"][$data['ind_id']]['id'] = $data['ind_id'];
		$tab[$data['groupe_id']]["nb_indicateurs"]++;
		$tab[$data['groupe_id']]['competences'][$data['comp_id']]['nb_indicateurs']++;
	}	
	
	return $tab;
	
}












// ==============================================
// CONTEXTES
// ================================================

// Fonction qui construit le tableau <table> </table> avec les liens indicateurs / contextes
function getTableauContextesHTML()
{
	global $bdd,$BDD_PREFIXE;

	$tab=getListeCompetences(); 	// On recupere la liste des indicateurs


	$res = "
					<table id=\"table_contextes\">
						<!-- ENTETE ======================================================== -->
						<tr>
							<td class=\"contexte_titre_domaine_legende\" >Domaines</td>
							<td class=\"case_blanche_tab\"></td>";

	foreach($tab AS $dom)
	{	
		$res .= "
							<td class=\"contexte_titre_domaine\" colspan=\"".$dom['nb_indicateurs']."\">".$dom['nom']."</td>
							<td class=\"case_blanche_tab\"></td>";
	}
	
	$res .= "
						</tr>
						<tr>
							<td class=\"contexte_titre_competence_legende\">Compétences</td>
							<td class=\"case_blanche_tab\"></td>";

	foreach($tab AS $dom)
	{	
		foreach($dom['competences'] AS $comp)
		{
		$res .= "
							<td class=\"contexte_titre_competence\" colspan=\"".$comp['nb_indicateurs']."\">".$comp['nom']."</td>";
		}
		$res .= "
							<td class=\"case_blanche_tab\"></td>";
	}

	$res .= "
						</tr>
						<tr id=\"titre_indicateurs\">
							<td class=\"contexte_titre_indicateur_legende\">Indicateurs</td>
							<td class=\"case_blanche_tab\"></td>";

	$nb_indicateurs = 0;
	foreach($tab AS $dom)
	{	
		foreach($dom['competences'] AS $comp)
		{
			foreach($comp['indicateurs'] AS $ind)
			{
				$res .= "
							<td class=\"contexte_titre_indicateur\" title=\"".str_replace("\"","&#8223;",strip_tags($ind['nom']))."\">".tronque(strip_tags($ind['nom']),40)."</td>";
				$nb_indicateurs++;
			}
		}
		$res .= "
							<td class=\"case_blanche_tab\"></td>";
	}
	
	$res .= "
						</tr>
						
						<!-- LIGNE BLANCHE ======================================================== -->
						<tr class=\"case_blanche_tab\">";
						
	for($i = 0; $i < $nb_indicateurs+2+sizeof($tab) ;$i++)
	{
		$res .= "
							<td class=\"case_blanche_tab\"></td>";
	}
						
	$res .= "
						</tr>
							<!-- CONTENU ======================================================== -->";


	$req = $bdd->query("SELECT nom,id,ordre FROM ".$BDD_PREFIXE."contextes ORDER BY ordre");
						
	while($data = $req->fetch())
	{
		$id_contexte = $data['id'];
		$ordre_contexte = $data['ordre'];
		$res .= "
						<tr id=\"titre_contexte_".strval($id_contexte)."\" data-id=\"".strval($id_contexte)."\" data-ordre=\"".strval($ordre_contexte)."\">
							<td class=\"contexte_titre_contexte\">
								<div class=\"contexte_titre_contexte_seul\">".$data['nom']."</div>
								<img class=\"boutonSupprimeContexte\" src=\"./sources/images/poubelle.png\" title=\"Supprimer le contexte\" alt=\"[Suppr]\" onclick=\"supprimeContexte_ouvreBoite(".strval($id_contexte).")\"/>
								<img class=\"boutonModifContexte\" src=\"./sources/images/icone-modif.png\" alt=\"[§]\" onclick=\"ouvreBoiteModifContexte(".strval($id_contexte).",".strval($ordre_contexte).");\"/>
							</td>
							<td class=\"case_blanche_tab\"></td>";
		foreach($tab AS $dom)
		{	
			foreach($dom['competences'] AS $comp)
			{
				foreach($comp['indicateurs'] AS $ind)
				{
					//Le contexte est-il déjà associé à l'indicateur ?
					$validite = "invalide";
					$req2 = $bdd->query("SELECT * FROM ".$BDD_PREFIXE."liensIndicateursContextes WHERE contexte=".strval($id_contexte)." AND indicateur=".strval($ind['id']));
					if($d = $req2->fetch())
						$validite = "valide";
									
					$res .= "
									<td id=\"lienContexteIndicateur_".strval($id_contexte)."_".strval($ind['id'])."\" class=\"contexte_indicateur ".$validite."\" onclick=\"activeDesactiveLienContexteIndicateur(".strval($id_contexte).",".strval($ind['id']).")\" title=\"Associer (Vert) / Désassocier (Rouge)\"></td>";
				}
			}
			$res .= "
							<td class=\"case_blanche_tab\"></td>";
		}
		$res .= "
						</tr>";
	}
	
	$res .= "
					</table>";

	return $res;
}







/* =============================================
AUTRES
============================================== */


//Fonction qui tronque le texte à n caractères
// Attention, si dots=true, il y a un caractère en plus (3 points)
function tronque($texte,$n,$dots = true)
{
	if(strlen($texte)>$n)
		return substr($texte,0,$n).($dots ? "⋯" : "");
	return $texte ;
}



?>
