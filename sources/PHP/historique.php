				<div id="tab-historique">
					<h3>Historique des dernières évaluations</h3>

					<div id="liste_historique">
					<?php
						$req=$bdd->query("SELECT nee.*,i.id AS idIndicateur,i.nom AS nomIndicateur, i.niveaux FROM (SELECT ne.*,uu.nom AS nomProf, uu.prenom AS prenomProf FROM (SELECT n.id, n.note,n.date,n.indicateur AS idIndicateur,n.examinateur AS idExaminateur, u.nom AS nomEleve, u.prenom AS prenomEleve FROM ".$BDD_PREFIXE."notation AS n JOIN ".$BDD_PREFIXE."utilisateurs AS u ON n.eleve=u.id) AS ne JOIN ".$BDD_PREFIXE."utilisateurs AS uu ON ne.idExaminateur=uu.id) AS nee JOIN ".$BDD_PREFIXE."indicateurs as i ON nee.idIndicateur=i.id ORDER BY date DESC LIMIT 0,20");
						while($donnees=$req->fetch())
						{
							echo "
						<div id=\"historique_".$donnees['id']."\" class=\"element_historique\">
							<div class=\"id_historique\"><div>".$donnees['id']."</div></div>
							<div class=\"eleve_historique\">".$donnees['prenomEleve']." ".$donnees['nomEleve']."</div>
							<div class=\"prof_date_historique\">(<img style=\"height:20px; vertical-align:middle;\" src=\"./sources/images/teaching.png\"/><span class=\"prof_historique\">".$donnees['prenomProf']." ".$donnees['nomProf']."</span><br/><span class=\"date_historique\">".$donnees['date']."</span>)</div>
							<div class=\"note_historique\">Éval.: <strong><span>".$donnees['note']."</span>/".$donnees['niveaux']."</strong></div>
							<div class=\"intitule_historique\">\"".$donnees['nomIndicateur']."\"</div>
							<div class=\"menu_historique\">
								<img src=\"./sources/images/icone-modif.png\" alt=\"[Modif]\" title=\"Modifier l'évaluation\" onclick=\"ouvreBoite_modifNotation(".$donnees['id'].",$('#historique_".$donnees['id']." .note_historique strong span').text(),".$donnees['niveaux'].");\"/>
								<img src=\"./sources/images/poubelle.png\" alt=\"[Suppr]\" title=\"Supprimer l'évaluation\" onclick=\"ouvreBoite_supprimeNotation(".$donnees['id'].")\"/>
							</div>
						</div>";
						}
					?>
					</div>

				</div> 
