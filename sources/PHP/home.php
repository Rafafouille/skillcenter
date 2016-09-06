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
?>

	<!-- Graphiques ------- -->
	<div id="graphiquesHome">
		<h3>Bilan par domaines</h3>
		<canvas id="radarDomaines" width="300" height="300">
		</canvas>
	</div>




<?php

//Récupérer la liste des 

$listeBilanDomaines=getBilanDomaines();


?>
<script>
var radarDomaines=$("#radarDomaines");

var donneesRadarDomaines=[];
var labelRadarDomaines=[];

<?php
foreach($listeBilanDomaines as &$dom)
{
	echo "donneesRadarDomaines.push(".intval($dom['sommeEleve']/$dom['sommeNiveaux']*100).");\n";
	echo "labelRadarDomaines.push('".$dom['nom']."');\n";
}
?>;


var polarData = {
    labels: labelRadarDomaines,
    datasets: [
        {
            data: donneesRadarDomaines
        }]
};
 



var myRadarChart = new Chart(radarDomaines, {
    type: 'polarArea',
    data: {
    				labels: labelRadarDomaines,
    				datasets: [
        			{
									data: donneesRadarDomaines,
									backgroundColor:['red','lime','blue','yellow','fuchsia','aqua','green','purple','silver','teal']
        			}]
					},
		options:{
							responsive: false,
							scale:{
											ticks:{max:100,display: false}
										}
						}
});



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
