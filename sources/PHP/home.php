<div id="tab-home">

<h2>Page principale</h2>


<?php

if(!$_SESSION['id']) //Si pas connecté
{
	?>
	<p>Bienvenue sur ce site d'évaluation des compétences.<br/>
	<strong>Connectez-vous !</strong></p>
	<?php
}
else //Si connecté 
{


	//GRAPHIQUES ==================================================
	if($AUTORISE_GRAPHIQUES && $_SESSION['statut']!="admin" && $_SESSION['statut']!="evaluateur" )	//Si on autorise d'afficher les graphiques
	{
		$listeBilanDomaines=getBilanDomaines();//Récupérer la liste des domaines
?>

	<!-- Graphiques ------- -->
	<div id="graphiquesHome">
		<div style="display:inline-block;width:45%;vertical-align:top;text-align:center;"">
			<h3>Bilan par domaines</h3>
			<canvas id="radarDomaines" width="400" height="400">
			</canvas>
		</div>
		<div style="display:inline-block;width:45%;vertical-align:top;"">
			<h3>Bilan par compétences</h3>
			<?php
			foreach($listeBilanDomaines as $dom)
			{
				echo "
			<div class=\"graphe_competence\">
					<canvas style=\"display;inline-block;\" id=\"radarCompetences_Domaine_".$dom["id"]."\" width=\"200\" height=\"200\">
					</canvas>
			</div>";
			}
			?>
		</div>
	</div>

<script>
// TRACAGE DES DOMAINES ===================================

var donneesDomaines=[<?php
$first=true;
foreach($listeBilanDomaines as &$dom)
{
	if($first)	$first=false;
	else	echo ",";
	echo intval($dom['sommeEleve']/$dom['sommeNiveaux']*100);
}
?>];

var labelsDomaines=[<?php
$first=true;
foreach($listeBilanDomaines as &$dom)
{
	if($first)	$first=false;
	else	echo ",";
	echo '"'.$dom['nom'].'"';
}
?>];
 
traceGraphiqueRecap_Domaine("#radarDomaines",donneesDomaines,labelsDomaines);





<?php
// TRACAGE DES COMPETENCES ===================================
$tableauCouleurs=array('red','lime','blue','yellow','fuchsia','aqua','green','purple','silver','teal');
$idCouleur=-1;

$idCompetenceGraphique=1;

foreach($listeBilanDomaines as &$dom)//Pour chaque domaine...
{
	$idCouleur++;
	$idDomaine=$dom["id"];

	$listeBilanCompetence=getBilanCompetence($idDomaine);
?>

	var donneesRadarCompetences=[<?php
		$first=true;
		foreach($listeBilanCompetence as &$comp)
		{
			if($first)	$first=false;
			else	echo ",";
			echo intval($comp['sommeEleve']/$comp['sommeNiveaux']*100);
		}
	?>];

	var labelRadarCompetences=[<?php
		$first=true;
		foreach($listeBilanCompetence as &$comp)
		{
			$label=substr($comp['nomAbrege'],0,20);
			if($comp['nomAbrege']=="")
				$label="Comp n°".strval($idCompetenceGraphique++);
			
			if($first)	$first=false;
			else	echo ",";
			echo '"'.$label.'"';
		}
	?>];

	traceGraphiqueRecap_Competence("#radarCompetences_Domaine_<?php echo $idDomaine?>",donneesRadarCompetences,labelRadarCompetences,<?php echo '"'.$dom['nom'].'"';?>,<?php echo '"'.$tableauCouleurs[$idCouleur].'"'?>);

<?php
}
?>
</script>


<?php
	}





	//BADGES ==================================================
	if($AUTORISE_BADGES)
	{


		$listeBadges=array(
										"badge1ereConnexion"=>array("1<sup>ère</sup> Connexion","badge_1ere_connexion.png","Au moins 1 connexion au site."),
										"badge1ereBrique"=>array("1<sup>ère</sup> Brique !","badge_1ere_brique.png","Au moins 1 critère a été évalué."),
										"badgeDecollage"=>array("Décollage","badge_decollage.png","Au moins 1 critère a été validé."),
										"badgeChosesSerieusesCommencent"=>array("Les choses sérieuses commencent","badge_debutant.png","Au moins 5 critères ont été évalués"),
										"badgeFormule1"=>array("Formule 1","badge_formule1.png","Au moins 5 compétences validées en moins d'un mois"),
										"badgeSeigneur"=>array("Seigneur","badge_seigneur.png","Au moins 1 domaine entièrement validé"),
										"badgeTacheDHuile"=>array("Tache d'Huile","badge_tache_d_huile.png","Au moins un critère évalué et non-acquis"),
									);

		//Recupere les badges
		list ($listeBadgesObtenus,$BDDnouveaux_badgesTXT)=getBadges($_SESSION['id']);//On récupere tous les badges (et les nouveaux)
		$listeNouveauxBadges=explode(",",$BDDnouveaux_badgesTXT);



		?>
	<div id="liste_badges">
		<h3>Badges obtenus</h3>
		<?php
		foreach($listeBadges as $key=>$value)
		{
			?>
			<div class="<?php echo in_array($key,$listeBadgesObtenus) ? "badge_valide":"badge_non-valide"; echo in_array($key,$listeNouveauxBadges) ? "_nouveau":"";?>" title="<?php echo $value[2];?>">
				<img src="./sources/images/<?php echo $value[1];?>" alt="<?php echo $value[0];?>"/>
				<br/>
				<?php echo $value[0];?>
			</div>
			<?php
		}
		?>
	</div>
		<?php



		valideBadges($_SESSION['id']);
	}
}
?>

</div>
