
				<div id="tab-users">
					<h2>Liste des utilisateurs</h2>
					
					<div class="bouton_ajoute" onclick="$('#newUser_id').val(-1);$('#dialog-addUser').dialog('open');">
						<img src="./sources/images/icone-plus.png" alt="[+]"/>
						<strong>Ajouter un utilisateur</strong>
					</div>
					
					<div>
						<form>
							<select name="userAdminSelectClasse" id="userAdminSelectClasse" onchange="getListeUsersAdmin($(this).val());">
								<option value="[ALL]">Tous les utilisateurs</option>
								<?php
								$reponse = $bdd->query('SELECT DISTINCT classe FROM utilisateurs');
								while($donnees=$reponse->fetch())
								{
									if($donnees['classe']!="")
										echo "\n								<option value=\"".$donnees['classe']."\">".$donnees['classe']."</option>";
									else

										echo "\n								<option value=\"".$donnees['classe']."\">(Pas de classe)</option>";
								}
								?>
							</select>
						</form>
					</div>

					<div id="tableau_utilisateurs">
						
					
					
						<?php if($_SESSION['statut']!="admin")
						{echo "<p>Interdit</p>";}
						else{
							?>
							
								<?php
								/*$reponse = $bdd->query('SELECT * FROM utilisateurs');
								while ($donnees = $reponse->fetch())
								{
									echo "
										<div class=\"user\" id=\"user_".$donnees['id']."\">
											<span class=\"nom-user\">".$donnees['nom']."</span>
											<span class=\"prenom-user\">".$donnees['prenom']."</span>
											<span class=\"classe-user\">".$donnees['classe']."</span>
											<span class=\"login-user\">(".$donnees['login'].")</span>
											<span class=\"boutons_user\">
												<img id=\"boutonModifieInfosUser_".$donnees['id']."\" src=\"./sources/images/icone-modif.png\" title=\"Modifier l'utilisateur\" alt=\"[Modif]\" onclick=\"ouvreBoiteModifieUser(".$donnees['id'].")\"/>";
												//Bouton supprime
												if($donnees['id']!=$_SESSION['id'])//si c'est pas nous...
												{echo "
												<img style=\"cursor:pointer;\" src=\"./sources/images/icone-supprime_utilisateur.png\" title=\"Supprimer l'utilisateur\" alt=\"[Suppr]\" onclick=\"ouvreBoiteSupprimeUser(".$donnees['id'].",'".$donnees['prenom']." ".$donnees['nom']." (".$donnees['login'].")');\"/>";
												}
												else
												{echo "
												<img style=\"cursor:auto;\" src=\"./sources/images/icone-supprime_utilisateur-OFF.png\" title=\"Vous ne pouvez pas vous auto-supprimer\" alt=\"[Suppr]\" />";
												}
												//Bouton SU
												if($donnees['statut']=="admin")
												{
													if($donnees['id']!=$_SESSION['id'])//si c'est pas nous...
													{
														echo "
												<img id=\"boutonModifieStatut_".$donnees['id']."\" src=\"./sources/images/super.png\" title=\"Super utilisateur (rendre normal)\" alt=\"[SU]\" onclick=\"ouvreBoiteDowngradeUser(".$donnees['id'].",'".$donnees['prenom']." ".$donnees['nom']." (".$donnees['login'].")');\"/>";
													}
													else	//Si c'est nous (interdit de nous auto-supprimer...)
													{
														echo "
												<img id=\"boutonModifieStatut_".$donnees['id']."\" style=\"cursor:auto;\" src=\"./sources/images/super-OFF.png\" title=\"Vous ne pouvez pas vous auto-rétrograder\" alt=\"[SU]\" />";
													}
												}
												else
												{
												echo "
												<img id=\"boutonModifieStatut_".$donnees['id']."\" src=\"./sources/images/student.png\" title=\"Utilisateur normal (rendre SuperUtilisateur)\" alt=\"[o_o]\" onclick=\"ouvreBoiteUpgradeUser(".$donnees['id'].",'".$donnees['prenom']." ".$donnees['nom']." (".$donnees['login'].")');\"/>";
												}
												echo "
											</span>
										</div>";
								}*/
								?>
							
						<?php };?>
					</div>
				</div>
