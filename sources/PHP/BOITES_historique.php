	
		
		<!-- BOITE POUR MODIFIER UNE NOTATION------------------- -->
		<div id="dialog-modifNotation" title="Modifier une évaluation">
			<p>Évaluation n°<span id="modifNotation_num_critere"></span> : </p>
			<form>
				<label for="modifNotation_input_evaluation">Critère : </label>
				<input type="number" placeholder="0" size="2" id="modifNotation_input_evaluation" name="modifNotation_input_evaluation" min="0" max="0" step="1"/>
				/<span id="modifNotation_max">0</span>
				<br/>
				<label for="modifNotation_contexte">Contexte : </label>
				<input type="text" placeholder="Ex : TP, DM1, ..." id="modifNotation_contexte" list="listeContexteAutocompletion" name="modifNotation_contexte"/>
				<br/>
				<label for="modifNotation_commentaire">Commentaire de l'évaluation : </label>
				<textarea  placeholder="A sur faire ceci, doit revoir cela..." id="modifNotation_commentaire" name="modifNotation_commentaire"></textarea>
			</form>
		</div>
		<script>
			$( "#dialog-modifNotation").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Modifier": function() {$("#dialog-modifNotation").dialog( "close" );modifieNotation(parseInt($("#modifNotation_num_critere").text()),parseInt($("#modifNotation_input_evaluation").val()),$("#modifNotation_contexte").val(),$("#modifNotation_commentaire").val());},
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
		