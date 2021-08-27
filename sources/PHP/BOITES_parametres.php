	
		<!-- BOITE POUR AJOUTER UN DOMAINE DE COMPETENCES ------------------- -->
		<div id="dialog-addGroupeCompetences" title="Ajouter un domaine">
			<form>
				<label for="dialog-addGroupeCompetences-nom">Nom du nouveau domaine :</label>
				<input type="text" name="dialog-addGroupeCompetences-nom" id="dialog-addGroupeCompetences-nom" />
			</form>
		</div>
		<script>
			$( "#dialog-addGroupeCompetences").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Créer": function() {$("#dialog-addGroupeCompetences").dialog( "close" );addGroupeCompetences($("#dialog-addGroupeCompetences-nom").val());},
							"Annuler": function() {$("#dialog-addGroupeCompetences").dialog( "close" );}
						}
			});
		</script>

		<!-- BOITE POUR MODIFIER UN DOMAINE ------------------- -->
		<div id="dialog-modifDomaine" title="Modifier un domaine" data-iddomaine="0">
			<form>
				<label for="dialog-modifDomaine-nom">Intitulé du domaine :</label>
				<input type="text" name="dialog-modifDomaine-nom" id="dialog-modifDomaine-nom" />
				<br/>
				<label for="dialog-modifDomaine-position">Numéro du domaine :</label>
				<input type="number" name="dialog-modifDomaine-position" id="dialog-modifDomaine-position" min="0"/>
			</form>
		</div>
		<script>
			$( "#dialog-modifDomaine").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Modifier": function() {$("#dialog-modifDomaine").dialog( "close" );modifDomaine($("#dialog-modifDomaine-nom").val(),parseInt($("#dialog-modifDomaine").attr("data-iddomaine")),$("#dialog-modifDomaine-position").val());},
							"Annuler": function() {$("#dialog-modifDomaine").dialog( "close" );}
						}
			});
		</script>
		
		
		<!-- BOITE POUR SUPPRIMER UN DOMAINE ------------------- -->
		<div id="dialog-supprimeDomaine" title="Supprimer un domaine de compétences">
			<p>Êtes-vous sur de vouloir supprimer le domaine : "<span class="dialog-supprimeDomaine_nomDomaine"></span>"</p>
			<form>
				<input type="hidden" name="dialog-supprimeDomaine-idDomaine" id="dialog-supprimeDomaine-idDomaine"/>
				<div style="font-size:small;">
					<label for="dialog-supprimeDomaine-supprimerCompetences">Supprimer compétences incluses ?</label> 
					<select name="dialog-supprimeDomaine-supprimerCompetences" id="dialog-supprimeDomaine-supprimerCompetences">
						<option value="Oui">Oui</option>
						<option value="Non">Non</option>
					</select>
					<br/>
					<label for="dialog-supprimeDomaine-supprimerIndicateurs">Supprimer critères inclus ?</label> 
					<select name="dialog-supprimeDomaine-supprimerIndicateurs" id="dialog-supprimeDomaine-supprimerIndicateurs">
						<option value="Oui">Oui</option>
						<option value="Non">Non</option>
					</select>
				</div>
			</form>
		</div>
		<script>
			$( "#dialog-supprimeDomaine").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
					"Supprimer": function() {$("#dialog-supprimeDomaine").dialog("close");supprimeDomaine(parseInt($("#dialog-supprimeDomaine-idDomaine").val()),Number($("#dialog-supprimeDomaine-supprimerCompetences").val()=="Oui"),Number($("#dialog-supprimeDomaine-supprimerIndicateurs").val()=="Oui"));},
							"Annuler": function() {$("#dialog-supprimeDomaine").dialog("close");}
					}
			});
		</script>


		
		<!-- BOITE POUR AJOUTER UNE COMPETENCE ------------------- -->
		<div id="dialog-addCompetence" title="Ajouter une compétence">
			<p>(Domaine : "<span class="dialog-addCompetence_nomGroupe"></span>")</p>
			<form>
				<label for="dialog-addCompetence-nom">Intitulé de la compétence :</label>
				<textarea name="dialog-addCompetence-nom" id="dialog-addCompetence-nom"></textarea>
				<br/>
				<label for="dialog-addCompetence-nomAbrege">Intitulé Abrégé :</label>
				<input type="text" name="dialog-addCompetence-nomAbrege" id="dialog-addCompetence-nomAbrege" />
				<input type="hidden" name="dialog-addCompetence-idGroupe" id="dialog-addCompetence-idGroupe"/>
			</form>
		</div>
		<script>
			$( "#dialog-addCompetence").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Ajouter": function() {$("#dialog-addCompetence").dialog( "close" );addCompetence($("#dialog-addCompetence-nom").val(),$("#dialog-addCompetence-nomAbrege").val(),parseInt($("#dialog-addCompetence-idGroupe").val()));},
							"Annuler": function() {$("#dialog-addCompetence").dialog( "close" );}
						}
			});
		</script>


		
		<!-- BOITE POUR MODIFIER UNE COMPETENCE ------------------- -->
		<div id="dialog-modifCompetence" title="Modifier une compétence" data-idcompetence="0">
			<form>
				<label for="dialog-modifCompetence-idDomaine">Domaine : </label>
				<select id="dialog-modifCompetence-idDomaine" name="dialog-modifCompetence-idDomaine"></select>
				<br/>
				<label for="dialog-modifCompetence-nom">Intitulé de la compétence :</label>
				<textarea name="dialog-modifCompetence-nom" id="dialog-modifCompetence-nom"></textarea>
				<br/>
				<label for="dialog-addCompetence-nomAbrege">Intitulé Abrégé :</label>
				<input type="text" name="dialog-modifCompetence-nomAbrege" id="dialog-modifCompetence-nomAbrege" />
				<label for="dialog-addCompetence-position">Numéro de la compétence :</label>
				<input type="number" name="dialog-modifCompetence-position" id="dialog-modifCompetence-position" min="0"/>
				<!--<input type="hidden" name="dialog-modifCompetence-idDomaine" id="dialog-modifCompetence-idDomaine"/>-->
			</form>
		</div>
		<script>
			$( "#dialog-modifCompetence").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Modifier": function() {$("#dialog-modifCompetence").dialog( "close" );modifCompetence($("#dialog-modifCompetence-nom").val(),$("#dialog-modifCompetence-nomAbrege").val(),parseInt($("#dialog-modifCompetence-idDomaine").val()),parseInt($("#dialog-modifCompetence").attr("data-idcompetence")),parseInt($("#dialog-modifCompetence-position").val()));},
							"Annuler": function() {$("#dialog-modifCompetence").dialog( "close" );}
						}
			});
		</script>
		
		
		<!-- BOITE POUR SUPPRIMER UNE COMPETENCE------------------- -->
		<div id="dialog-supprimeCompetence" title="Supprimer une compétence">
			<p>Êtes-vous sur de vouloir supprimer la compétence : "<span class="dialog-supprimeCompetence_nomCompetence"></span>"</p>
			<form>
				<input type="hidden" name="dialog-supprimeCompetence-idCompetence" id="dialog-supprimeCompetence-idCompetence"/>
				<div style="font-size:small;">
					<label for="dialog-supprimeCompetence-supprimerIndicateur">Supprimer les critères inclus ?</label> 
					<select name="dialog-supprimeCompetence-supprimerIndicateur" id="dialog-supprimeCompetence-supprimerIndicateur">
						<option value="Oui">Oui</option>
						<option value="Non">Non</option>
					</select>
				</div>
			</form>
		</div>
		<script>
			$( "#dialog-supprimeCompetence").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Supprimer": function() {$("#dialog-supprimeCompetence").dialog( "close" );supprimeCompetence(parseInt($("#dialog-supprimeCompetence-idCompetence").val()),Number($("#dialog-supprimeCompetence-supprimerIndicateur").val()=="Oui"));},
							"Annuler": function() {$("#dialog-supprimeCompetence").dialog( "close" );}
						}
			});
		</script>


		
		<!-- BOITE POUR AJOUTER UN CRITERE ------------------- -->
		<div id="dialog-addIndicateur" title="Ajouter un critère">
			<p>(Compétence : "<span class="dialog-addIndicateur_nomCompetence"></span>")</p>
			<form>
					<label for="dialog-addIndicateur-nom">Nom :</label>
					<input type="text" name="dialog-addIndicateur-nom" id="dialog-addIndicateur-nom" />
				<br/>
					<label for="dialog-addIndicateur-details">Détails (facultatif) :</label><br/>
					<input type="text" name="dialog-addIndicateur-details" id="dialog-addIndicateur-details" />
				<br/>
					<label for="dialog-addIndicateur-lien">Lien hypertexte vers une ressource (facultatif) :</label><br/>
					<input type="url" name="dialog-addIndicateur-lien" id="dialog-addIndicateur-lien" />
				<br/>
					<label for="dialog-addIndicateur-niveaux">Nombre de niveaux :</label>
					<select name="dialog-addIndicateur-niveaux" id="dialog-addIndicateur-niveaux">
						<?php
							for($i=1;$i<=$NB_NIVEAUX_MAX;$i++)
								{echo'
						<option value="'.$i.'"';
								if($i==$NIVEAU_DEFAUT)
									echo " selected";
								echo'>'.($i+1).'</option>';
								};
						?>
				</select>

				<input type="hidden" name="dialog-addIndicateur-idCompetence" id="dialog-addIndicateur-idCompetence"/>
				<?php /*<input type="hidden" name="dialog-addIndicateur-idIndicateur" id="dialog-addIndicateur-idIndicateur"/>*/?>
			</form>
		</div>
		<script>
			$( "#dialog-addIndicateur").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Ajouter": function() {$("#dialog-addIndicateur").dialog( "close" );addIndicateur($("#dialog-addIndicateur-nom").val(),$('#dialog-addIndicateur-details').val(),$('#dialog-addIndicateur-niveaux').val(),parseInt($("#dialog-addIndicateur-idCompetence").val()),((true) ? $("#selectClasseCompetences").val() : ""),$("#dialog-addIndicateur-lien").val());},
							"Annuler": function() {$("#dialog-addIndicateur").dialog( "close" );}
						}
			});
		</script>
		
		

		<!-- BOITE POUR MODIFIER UN CRITERE ------------------- -->
		<div id="dialog-modifIndicateur" title="modifier un critère" data-id_critere="0">
			<form>
				<label for="dialog-modifIndicateur-competence">Compétence rattachée : </label>
				<select id="dialog-modifIndicateur-idCompetence" name="dialog-modifIndicateur-idCcompetence">
				</select>
			<br/>
				<label for="dialog-modifIndicateur-nom">Nom :</label>
				<input type="text" name="dialog-modifIndicateur-nom" id="dialog-modifIndicateur-nom" />
			<br/>
				<label for="dialog-modifIndicateur-details">Détails (facultatif) :</label><br/>
				<textarea name="dialog-modifIndicateur-details" id="dialog-modifIndicateur-details"></textarea>
			<br/>
				<label for="dialog-modifIndicateur-lien">Lien hypertexte vers une ressource (facultatif) :</label><br/>
				<input type="url" name="dialog-modifIndicateur-lien" id="dialog-modifIndicateur-lien" />
			<br/>
				<label for="dialog-modifIndicateur-niveaux">Nombre de niveaux :</label>
				<select name="dialog-modifIndicateur-niveaux" id="dialog-modifIndicateur-niveaux">
					<?php
						for($i=1;$i<=$NB_NIVEAUX_MAX;$i++)
							{echo'
					<option value="'.$i.'"';
							if($i==$NIVEAU_DEFAUT)
								echo " selected";
							echo'>'.($i+1).'</option>';
							};
					?>
				</select>
			<br/>
				<label for="dialog-modifIndicateur-position">Numéro de l'indicateur :</label>
				<input type="number" name="dialog-modifIndicateur-position" id="dialog-modifIndicateur-position" min="0"/>

				<!--<input type="hidden" name="dialog-modifIndicateur-idCompetenceOLD" id="dialog-modifIndicateur-idCompetenceOLD"/>-->
				<!--<input type="hidden" name="dialog-modifCritere-idCritere" id="dialog-modifCritere-idCritere"/>-->
			</form>
		</div>
		<script>
			$( "#dialog-modifIndicateur").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Modifier": function() {$("#dialog-modifIndicateur").dialog( "close" );modifCritere($("#dialog-modifIndicateur-nom").val(),$('#dialog-modifIndicateur-details').val(),$('#dialog-modifIndicateur-niveaux').val(),parseInt($("#dialog-modifIndicateur-idCompetence").val()),parseInt($("#dialog-modifIndicateur").data("id_critere")),$("#dialog-modifIndicateur-lien").val(), parseInt($("#dialog-modifIndicateur-position").val()) );},
							"Annuler": function() {$("#dialog-modifIndicateur").dialog( "close" );}
						}
			});
		</script>


		
		
		<!-- BOITE POUR SUPPRIMER UN CRITERE------------------- -->
		<div id="dialog-supprimeIndicateur" title="Supprimer un Indicateur">
			<p>Êtes-vous sur de vouloir supprimer l'indicateur : "<span class="dialog-supprimeIndicateur_nomIndicateur"></span>")</p>
			<form>
				<input type="hidden" name="dialog-supprimeIndicateur-idIndicateur" id="dialog-supprimeIndicateur-idIndicateur"/>
			</form>
		</div>
		<script>
			$( "#dialog-supprimeIndicateur").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Supprimer": function() {$("#dialog-supprimeIndicateur").dialog( "close" );supprimeIndicateur(parseInt($("#dialog-supprimeIndicateur-idIndicateur").val()));},
							"Annuler": function() {$("#dialog-supprimeIndicateur").dialog( "close" );}
						}
			});
		</script>
	
		
		<!-- BOITE POUR NETTOYER LA BASE------------------- -->
		<div id="dialog-nettoyerLaBase" title="Nettoyer la base de données">
			<p><strong>Du fait de mises à jour ou autres manipulations non-prévues, la base de données peut contenir des incohérences.
			Cliquer sur les actions à effectuer :</strong></p>
			<form>
				<input type="checkbox" id="nettoyer_supprimer_notes_fantomes" name="nettoyer_supprimer_notes_fantomes" value="nettoyer_supprimer_notes_fantomes" checked>
				<label for="nettoyer_supprimer_notes_fantomes">Supprimer les évaluations des utilisateurs supprimés.</label>
				<br/>

				<input type="checkbox" id="nettoyer_supprimer_notes_sans_critere" name="nettoyer_supprimer_notes_sans_critere" value="nettoyer_supprimer_notes_sans_critere" checked>
				<label for="nettoyer_supprimer_notes_sans_critere">Supprimer les évaluations dont le critère a été supprimé.</label>
				<br/>

				<input type="checkbox" id="nettoyer_depasse_critere_max" name="nettoyer_depasse_critere_max" value="nettoyer_depasse_critere_max" checked  onchange="$('#nettoyer_depasse_critere_max_option').toggle(200)">
				<label for="nettoyer_depasse_critere_max">Gérer les évaluations qui dépassent les critères maxi.</label>
				<br/>
					<select name="nettoyer_depasse_critere_max_option" id="nettoyer_depasse_critere_max_option" style="margin-left:50px;">
						<option value="depasse_critere_max_tronque" id="depasse_critere_max_tronque">Tronquer les valeurs</option>
						<option value="depasse_critere_max_suppr" id="depasse_critere_max_suppr">Supprimer les évaluations concernées</option>
					</select>
				<br/>

				<input type="checkbox" id="nettoyer_supprimer_comp_orphelins" name="nettoyer_supprimer_comp_orphelins" value="nettoyer_supprimer_comp_orphelins" onchange="$('#nettoyer_supprimer_comp_orphelins_et_ses_criteres').toggle(200);$('#nettoyer_supprimer_comp_orphelins_et_ses_criteres_label').toggle(200)" checked>
				<label for="nettoyer_supprimer_comp_orphelins">Supprimer les compétences qui ne font plus partie d'un domaine de compétence.</label>
				<br/>

					<input type="checkbox" id="nettoyer_supprimer_comp_orphelins_et_ses_criteres" name="nettoyer_supprimer_comp_orphelins_et_ses_criteres" value="nettoyer_supprimer_comp_orphelins_et_ses_criteres" style="margin-left:50px;"  checked>
					<label for="nettoyer_supprimer_comp_orphelins_et_ses_criteres" id="nettoyer_supprimer_comp_orphelins_et_ses_criteres_label"  style="margin-left:50px;">Supprimer aussi les critères associés à cette compétence.</label>
				<br/>

				<input type="checkbox" id="nettoyer_supprimer_criteres_orphelins" name="nettoyer_supprimer_criteres_orphelins" value="nettoyer_supprimer_criteres_orphelins"  checked>
				<label for="nettoyer_supprimer_criteres_orphelins">Supprimer les évaluations qui ne font plus partie d'une compétence.</label>
				<br/>

				<input type="checkbox" id="nettoyer_reordonner" name="nettoyer_reordonner" value="nettoyer_reordonner"  checked>
				<label for="nettoyer_reordonner">Nettoyer l'ordre des criteres/competences/domaines.</label>
				<br/>
			</form>
		</div>
		<script>
			$( "#dialog-nettoyerLaBase").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Nettoyer": function() {
														nettoyerLaBase();
														$("#dialog-nettoyerLaBase").dialog( "close" );
														$("#dialog-nettoyerLaBaseCallBack").text("Nettoyage de la base...");
														$("#dialog-nettoyerLaBaseCallBack").dialog("open");
													},
							"Annuler": function() {
														$("#dialog-nettoyerLaBase").dialog( "close" );
													}
						},
				width:600
			});
		</script>
		
		
		
		
		<!-- BOITE POUR NETTOYER (CALLBACK)------------------- -->
		<div id="dialog-nettoyerLaBaseCallBack" title="Nettoyer la base de données - Bilan">
			
		</div>
		<script>
			$( "#dialog-nettoyerLaBaseCallBack").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Fermer": function() {$("#dialog-nettoyerLaBaseCallBack").dialog( "close" );}
						},
				width:600
			});
		</script>
