					
					
					
					
					
<!-- Liste de contextes pour l'auto-completion -->
<datalist id="listeContexteAutocompletion" data-lastused="-1">
	<?php
	$reponse=$bdd->query("SELECT DISTINCT contexte FROM ".$BDD_PREFIXE."notation WHERE contexte<>''");
	while($donnees=$reponse->fetch())
	{
		echo "<option value=\"".$donnees["contexte"]."\">".$donnees["contexte"]."</option>\n";
	}
	?>
</datalist>


<!-- Liste des classes pour l'auto-completion -->
<datalist id="listeClassesAutocompletion">
	<?php
	$reponse=$bdd->query("SELECT DISTINCT classe FROM ".$BDD_PREFIXE."utilisateurs WHERE classe<>''");
	while($donnees=$reponse->fetch())
	{
		echo "<option value=\"".$donnees["classe"]."\">\n";
	}
	?>
</datalist>