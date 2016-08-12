				<div id="tab-historique">
					<h3>Historique des dernières évaluations</h3>

					<div id="liste_historique">
					<?php
						$req=$bdd->query("SELECT nee.*,i.id AS idIndicateur,i.nom AS nomIndicateur, i.niveaux FROM (SELECT ne.*,uu.nom AS nomProf, uu.prenom AS prenomProf FROM (SELECT n.id, n.note,n.date,n.indicateur AS idIndicateur,n.examinateur AS idExaminateur, u.nom AS nomEleve, u.prenom AS prenomEleve FROM notation AS n JOIN utilisateurs AS u ON n.eleve=u.id) AS ne JOIN utilisateurs AS uu ON ne.idExaminateur=uu.id) AS nee JOIN indicateurs as i ON nee.idIndicateur=i.id ORDER BY date DESC LIMIT 0,20");
						while($donnees=$req->fetch())
						{
							echo "
						<div id=\"historique_".$donnees['id']."\" class=\"element_historique\">
							<div class=\"id_historique\"><div>".$donnees['id']."</div></div>
							<div class=\"eleve_historique\">".$donnees['prenomEleve']." ".$donnees['nomEleve']."</div>
							<div class=\"prof_date_historique\">(<img style=\"height:20px; vertical-align:middle;\" src=\"./sources/images/teaching.png\"/>".$donnees['prenomProf']." ".$donnees['nomProf']."<br/>".$donnees['date'].")</div>
							<div class=\"note_historique\">Note : ".$donnees['note']."/".$donnees['niveaux']."</div>
							<div class=\"intitule_historique\">\"".$donnees['nomIndicateur']."\"</div>
							<div class=\"menu_historique\">
								<img src=\"./sources/images/icone-modif.png\" alt=\"[Modif]\" title=\"Modifier l'évaluation\" onclick=\"\"/>
								<img src=\"./sources/images/poubelle.png\" alt=\"[Suppr]\" title=\"Supprimer l'évaluation\" onclick=\"$('#supprEvaluNumId').text(".$donnees['id'].");$('#dialog-supprimeNotation').dialog('open');\"/>
							</div>
						</div>";
						}
					?>
					</div>

				</div> 
