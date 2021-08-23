<div id="tab-bilan_general">
	<div id="menu_bilan_general">
		<form>
			<label for="BILAN_GENERAL_choix_classe">Choix de la classe :</label>
			<select id="BILAN_GENERAL_choix_classe" name="BILAN_GENERAL_choix_classe">
			</select>
			
			<label for="BILAN_GENERAL_choix_contexte">Choix du contexte :</label>
			<select id="BILAN_GENERAL_choix_contexte" name="BILAN_GENERAL_choix_contexte">
			</select>
			
			
			<label for="BILAN_GENERAL_type_evaluation">Type d'évaluation :</label>
			<select id="BILAN_GENERAL_type_evaluation" name="BILAN_GENERAL_type_evaluation">
				<option value="last">Dernière évaluation</option>
				<option value="maxi">Meilleure évaluation</option>
				<option value="moy">Moyenne des évaluations</option>
			</select>
		</form>
		
		
		<br/>
		<div class="bouton_top" onclick="getBilanGeneral($('#BILAN_GENERAL_choix_classe').val(),parseInt($('#BILAN_GENERAL_choix_contexte').val()),$('#BILAN_GENERAL_type_evaluation').val())">Générer le bilan</div>
	</div>
	
	<div id="grille_bilan_general">
	</div>
</div>
