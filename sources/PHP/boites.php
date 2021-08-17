<?php
/*********************************************
	LISTE DE TOUTES LES BOITES DE DIALOGUE
**************************************************/

?>



		<!-- MESSAGE RETOUR --------------------------------->
		<div id="dialog-messageRetour" title="Message-retour">
		testsss
		</div>
		<script>
			$( "#dialog-messageRetour").dialog({
				autoOpen: false,
				modal: false,
				show: {
					effect: "blind",
					duration: 500
				  },
				 hide: {
					effect: "fade",
					duration: 500
				  },
				  position:{my:"center top",at:"center top"},
				  minHeight:50
			});
			$("#dialog-messageRetour").siblings('div.ui-dialog-titlebar').remove();//Supprime la barre de titre
		</script>




<?php

	//Chargement des boites selon le statut.

	if($_SESSION['statut']=="admin")
	{
		include_once("./sources/PHP/BOITES_utilisateurs.php");
		include_once("./sources/PHP/BOITES_parametres.php");
		include_once("./sources/PHP/BOITES_contexte.php");
	}
	
	if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['statut']=="autoeval")
		include_once("./sources/PHP/BOITES_historique.php");

	if($_SESSION['id']>0)//Si connecté
		include_once("./sources/PHP/BOITES_bilan.php");
?>	
		

	
		<!-- BOITE ERREUR ------------------- -->
		<div id="dialog-error" title="Erreur">
			Erreur.
		</div>
		<script>
			$( "#dialog-error").dialog({
				autoOpen: false,
				modal: true
			});
		</script>
