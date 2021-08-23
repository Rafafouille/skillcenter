					
					
					
					
					
<!-- Liste de contextes pour l'auto-completion -->

<?php
$tableau_contextes = array();
$reponse=$bdd->query("SELECT id, nom FROM ".$BDD_PREFIXE."contextes");
while($donnees=$reponse->fetch())
{
	array_push($tableau_contextes,["nom"=>$donnees["nom"],'id'=>$donnees['id']]);
}?>
<datalist id="listeContexteAutocompletion" data-lastused="-1">
	<?php
	foreach($tableau_contextes as $cont)
	{
		echo "<option value=\"".$cont["nom"]."\">\n";
	}
	?>
</datalist>
<script>
//Liste des contextes, qui sert dans "fonction_bilan"
// A NOTER QUE C'EST UN PEU OBOLETE, PUISQUE DURANT LE DÉROULÉ, ON UTILISE getcontexteFromServeur() qui met à jour automatiquement ce tableau
	var LISTE_CONTEXTES = [<?php
	$first = true;
	foreach($tableau_contextes as $cont)
	{
		if($first)
			$first = false;
		else
			echo ",";
			
		echo "{'id':".strval($cont['id']).",'nom':'".$cont['nom']."'}";
	}
	?>];
</script>

<!-- Liste des classes pour l'auto-completion -->
<?php
	$liste_classes = array();
	$reponse=$bdd->query("SELECT DISTINCT classe FROM ".$BDD_PREFIXE."utilisateurs WHERE classe<>''");
	while($donnees=$reponse->fetch())
	{
		array_push($liste_classes,$donnees["classe"]);
	}
	?>
<datalist id="listeClassesAutocompletion">
	<?php
	foreach($liste_classes as $cl)
	{
		echo "<option value=\"".$cl."\">\n";
	}
	?>
</datalist>

<script>
	LISTE_CLASSES = [<?php
	$first = true;
	foreach($liste_classes as $cl)
	{
		if($first)
			$first = false;
		else
			echo ",";
			
		echo "\"".$cl."\"";
	}
	?>];
</script>

