	
		
		<!-- BOITE POUR MODIFIER UNE NOTATION------------------- -->
		<div id="dialog-modifNotation" title="Modifier une évaluation">
			<p>Évaluation n°<span id="modifNotation_num_critere"></span> : </p>
			<form>
				<label for="modifNotation_input">Critère : </label>
				<input type="number" placeholder="0" size="2" id="modifNotation_input" name="modifNotation_input" min="0" max="0" step="1"/>/<span id="modifNotation_max">0</span>
			</form>
		</div>
		<script>
			$( "#dialog-modifNotation").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Modifier": function() {$("#dialog-modifNotation").dialog( "close" );modifieNotation(parseInt($("#modifNotation_num_critere").text()),parseInt($("#modifNotation_input").val()));},
							"Annuler": function() {$("#dialog-modifNotation").dialog( "close" );}
						}
			});
		</script>

		<!-- BOITE POUR SUPPRIMER UNE NOTATION------------------- -->
		<div id="dialog-supprimeNotation" title="Supprimer une évaluation">
			<p>Êtes-vous sur de vouloir supprimer cette évaluation (n°<span id="supprEvaluNumId">-1</span>) ?</p>
		</div>
		<script>
			$( "#dialog-supprimeNotation").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Supprimer": function() {$("#dialog-supprimeNotation").dialog( "close" );supprimeNotation(parseInt($("#supprEvaluNumId").text()));},
							"Annuler": function() {$("#dialog-supprimeNotation").dialog( "close" );}
						}
			});
		</script>
		