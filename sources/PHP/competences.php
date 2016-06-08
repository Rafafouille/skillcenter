
				<div id="tab-competences">
					
					<div class="bouton_ajoute" onclick="$('#dialog-addGroupeCompetences').dialog('open');">
						<img src="./sources/images/icone-plus.png" alt="[+]"/>
						<strong>Ajouter un domaine</strong>
					</div>
					
					
					<h2>Compétences (édition)</h2>

					<div>
						<form>
							<select id="selectClasseCompetences" onChange="updateCompetencesSelonClasse($(this).val());">
							</select>
						</form>
					</div>

					
					<div id="liste_competences">
					
					</div>
				</div>
