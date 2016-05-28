
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
					<?php
					$numCompetence=1;
					$reponse = $bdd->query('SELECT * FROM groupes_competences ORDER BY position');
					while ($donnees = $reponse->fetch())
					{
						?>
						
						<div class="groupe_competences" id="groupe_competence_<?php
						echo $donnees['id'];
						?>">
							<div class="entete_groupe_competences">
								<div class="boutonAjouteCompetence" onclick="ouvreBoiteAddCompetence('<?php echo $donnees['nom'];?>',<?php echo $donnees['id'];?>)">
									Ajouter une compétence
								</div>
								<h3 onclick="$(this).parent().parent().find('.groupe_contenu').toggle('easings');">
									<?php echo $donnees['nom']?>

								</h3>
							</div>
							<div class="groupe_contenu">
								<?php
								$numIndicateur=1;
								$reponseComp = $bdd->query('SELECT * FROM competences WHERE groupe='.$donnees['id'].' ORDER BY position');
								while ($donneesComp = $reponseComp->fetch())
									{
										?>
								<div class="competence">
									<div class="boutonAjouterIndicateur" onclick="ouvreBoiteAddIndicateur('<?php echo $donneesComp['nom'];?>',<?php echo $donneesComp['id'];?>)">
										[+Indicateur]
									</div>
									<h3><?php echo $numCompetence++." - ".$donneesComp["nom"];?></h3>
									<div class="listeIndicateurs">
										<table class="indicateurs">
										<?php
										$reponseInd = $bdd->query('SELECT * FROM indicateurs WHERE competence='.$donneesComp['id'].' ORDER BY position');
										while ($donneesInd = $reponseInd->fetch())
										{?>
											<tr>
												<td class="intituleIndicateur">
													<?php echo ($numCompetence-1).".".$numIndicateur." - ".$donneesInd['nom']?>
												</td>
												<td class="detailIndicateur">
													<img src="./sources/images/icone-info.png" alt="[i]"  style="cursor:help;" title="<?php echo $donneesInd['details']?>"/>
													<img src="./sources/images/supprime.png" alt="[X]" style="cursor:not-allowed;" title="Supprimer l'indicateur"/>
												</td>
												<td class="niveauxIndicateur">
													<?php
													for($i=0;$i<=$NB_NIVEAUX_MAX;$i++)
													{
														if($i<=$donneesInd['niveaux'])
														{
														echo '
													<div class="indicateurAllume" style="background-color:'.setArcEnCiel($i,$donneesInd['niveaux']).';">'.$i.'</div>';
														}
														else
														{
														echo '
													<div class="indicateurEteint">'.$i.'</div>';
														}
													}
													?>
												</td>
											</tr>
										<?php }?>
										</table>
									</div>
								</div>
								<?php
									}
								?>
							</div>
						</div>
						<?php
					}
					?>
					</div>
					a Faire...
				</div>
