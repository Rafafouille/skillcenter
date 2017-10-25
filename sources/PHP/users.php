
				<div id="tab-users">
					
					<div class="bouton_ajoute" onclick="$('#newUser_id').val(-1);$('#dialog-addUser').dialog('open');">
						<img src="./sources/images/icone-plus.png" alt="[+]"/>
						<strong>Ajouter un utilisateur</strong>
					</div>
					<div class="bouton_ajoute" onclick="$('#dialog-sendAllMails-classe').text($('#userAdminSelectClasse').find(':selected').text());$('#dialog-sendAllMails').dialog('open');">
						<img style="height:20px;vertical-align:middle;" src="./sources/images/icone-envoieBilan.png" alt="[v]"/>
						<strong>Envoyer bilans</strong>
					</div>

					<h2>Liste des utilisateurs</h2>
					
					<div>
						<form>
							<select name="userAdminSelectClasse" id="userAdminSelectClasse" onchange="getListeUsersAdmin($(this).val());">
								<option value="[ALL]">Tous les utilisateurs</option>
								<?php
								$reponse = $bdd->query('SELECT DISTINCT classe FROM '.$BDD_PREFIXE.'utilisateurs');
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
							?>
					</div>
				</div>
