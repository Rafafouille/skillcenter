
				<div id="tab-competences">

					<div class="bouton_ajoute" onclick="$('#dialog-addGroupeCompetences').dialog('open');">
						<img src="./sources/images/icone-plus.png" alt="[+]"/>
						<strong>Ajouter un domaine</strong>
					</div>

					<div class="bouton_top" onclick="$('#liste_competences .groupe_contenu,#liste_competences .listeIndicateurs').slideUp();">
						<img src="./sources/images/icone-reduire.png" alt="[&#8607;]"/>
						<strong>Tout Réduire</strong>
					</div>

					<div class="bouton_top" onclick="$('#liste_competences .groupe_contenu,#liste_competences .listeIndicateurs').slideDown();">
						<img src="./sources/images/icone-etendre.png" alt="[&#8609;]"/>
						<strong>Tout Étendre</strong>
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
