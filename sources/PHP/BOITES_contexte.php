
		<!-- BOITE AJOUTER UN CONTEXTE ------------------- -->
		<div id="dialog-addContexte" title="Ajouter un contexte">
			Vous êtes sur le point de créer un nouveau <strong>contexte</strong>. Il s'agit d'un groupe de compétences pouvant faire référence à une activité (un TP, un TD, etc.)
			<form action="#" method="POST">
				<table>
					<tr>
						<td><label for="newContexte_nom">Nom : </label></td>
						<td><input type="text" name="newContexte_nom" id="newContexte_nom" placeholder="Nom du contexte (TP1, TD12, etc.)" required/></td>
					</tr>
				</table>
			</form>
		</div>
		<script>
			$( "#dialog-addContexte").dialog({
				autoOpen: false,
				modal: true,
				minWidth: 500,
				buttons: {
							"Ajouter": function() {ajouteContexte();$("#dialog-addContexte").dialog( "close" );},
							"Annuler": function() {$("#dialog-addContexte").dialog( "close" );}
						}
			});
		</script>
		
		
		﻿﻿

		<!-- BOITE MODIFIER UN CONTEXTE ------------------- -->
		<div id="dialog-modifContexte" title="Modifier un contexte">
			<form action="#" method="POST">
				<table>
					<tr>
						<td><label for="modifContexte_nom">Nom : </label></td>
						<td><input type="text" name="modifContexte_nom" id="modifContexte_nom" placeholder="Nom du contexte (TP1, TD12, etc.)" required/></td>
					</tr>
					<tr>
						<td><label for="modifContexte_ordre">Ordre : </label></td>
						<td><input type="number" name="modifContexte_ordre" id="modifContexte_ordre" placeholder="" required/></td>
					</tr>
				</table>
				<input type="hidden" name="modifContexte_id" id="modifContexte_id" value="0"/>
			</form>
		</div>
		<script>
			$( "#dialog-modifContexte").dialog({
				autoOpen: false,
				modal: true,
				minWidth: 500,
				buttons: {
							"Modifier": function() {modifContexte();$("#dialog-modifContexte").dialog( "close" );},
							"Annuler": function() {$("#dialog-modifContexte").dialog( "close" );}
						}
			});
		</script>
		
		
			

		<!-- BOITE SUPPRIMER UN CONTEXTE ------------------- -->
		<div id="dialog-delContexte" title="Supprimer un contexte">
			<p>Êtes-vous bien sûrs de vouloir supprimer le contexte <span id="nom_valideSupprContexte"></span> (n°<span id="id_valideSupprContexte"></span>) ?
		</div>
		<script>
			$( "#dialog-delContexte").dialog({
				autoOpen: false,
				modal: true,
				minWidth: 500,
				buttons: {
							"Supprimer": function() {supprimeContexte(parseInt($("#id_valideSupprContexte").text()));$("#dialog-delContexte").dialog( "close" );},
							"Annuler": function() {$("#dialog-delContexte").dialog( "close" );}
						}
			});
		</script>

