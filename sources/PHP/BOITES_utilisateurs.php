
		<!-- BOITE AJOUTER UN UTILISATEUR ------------------- -->
		<div id="dialog-addUser" title="Ajouter/Mettre à jour un utilisateur">
			<form action="#" method="POST">
				<table>
					<tr>
						<td><label for="newUser_nom">Nom : </label></td>
						<td><input type="text" name="newUser_nom" id="newUser_nom" placeholder="Nom de l'élève" required/></td>
					</tr>
					<tr>
						<td><label for="newUser_prenom"/>Prénom : </label></td>
						<td><input type="text" name="newUser_prenom" id="newUser_prenom" placeholder="Prénom de l'élève" required/></td>
					</tr>
					<tr>
						<td><label for="newUser_classe"/>Classe : </label></td>
						<td><input type="text" name="newUser_classe" list="listeClassesAutocompletion" id="newUser_classe" placeholder="Classe" /></td>
					</tr>
					<tr>
						<td><label for="newUser_login"/>Nom d'utilisateur : </label></td>
						<td><input type="text" name="newUser_login" id="newUser_login" placeholder="Nom de connexion" required/></td>
					</tr>
					<tr>
						<td><label for="newUser_psw"/>Mot de passe : </label></td>
						<td><input type="password" name="newUser_psw" id="newUser_psw" placeholder="(Secret)" required /></td>
					</tr>
					<tr>
						<td><label for="newUser_mail"/>Adresse courriel : </label></td>
						<td><input type="mail" name="newUser_mail" id="newUser_mail" placeholder="Ex : comte@serveur.fr"/></td>
					</tr>
					<tr>
						<td><label for="newUser_notifieMail"/>Recevoir des notifications ? </label></td>
						<td><input type="checkbox" name="newUser_notifieMail" id="newUser_notifieMail" checked="checked"/></td>
					</tr>
				</table>
				<input type="hidden" name="newUser_id" id="newUser_id" value="-1"/>
			</form>
		</div>
		<script>
			$( "#dialog-addUser").dialog({
				autoOpen: false,
				modal: true,
				minWidth: 500,
				buttons: {
							"Ajouter/MAJ": function() {ajouteUpdateUser();$("#dialog-addUser").dialog( "close" );},
							"Annuler": function() {$("#dialog-addUser").dialog( "close" );}
						}
			});
		</script>
		

		<!-- BOITE UPGRADE UN UTILISATEUR ------------------- -->
		<div id="dialog-upgradeUser" title="Rendre super-Utilisateur">
			Voulez-vous vraiment augmenter l'utilisateur n°<span id="boiteUpgrade-id"></span> (<span id="boiteUpgrade-nom"></span>) ?
		</div>
		<script>
			$( "#dialog-upgradeUser").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Oui": function() {$("#dialog-upgradeUser").dialog( "close" );upgradeUser($("#boiteUpgrade-id").text());},
							"Non": function() {$("#dialog-upgradeUser").dialog( "close" );}
						}
			});
		</script>
		
		<!-- BOITE DOWNGRADE UN UTILISATEUR ------------------- -->
		<div id="dialog-downgradeUser" title="Rendre Utilisateur Normal">
			Voulez-vous vraiment diminuer l'utilisateur n°<span id="boiteDowngrade-id"></span> (<span id="boiteDowngrade-nom"></span>) ?
		</div>
		<script>
			$( "#dialog-downgradeUser").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Oui": function() {$("#dialog-downgradeUser").dialog( "close" );downgradeUser($("#boiteDowngrade-id").text());},
							"Non": function() {$("#dialog-downgradeUser").dialog( "close" );}
						}
			});
		</script>

		
		<!-- BOITE POUR SUPPRIMER UN UTILISATEUR ------------------- -->
		<div id="dialog-deleteUser" title="Supprimer l'utilisateur">
			Voulez-vous vraiment supprimer l'utilisateur n°<span id="dialog-deleteUser-id">0</span> (<span id="dialog-deleteUser-nom">inconnu</span>) ?
		</div>
		<script>
			$( "#dialog-deleteUser").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
							"Oui": function() {$("#dialog-deleteUser").dialog( "close" );supprimeUser(parseInt($("#dialog-deleteUser-id").text()));},
							"Non": function() {$("#dialog-deleteUser").dialog( "close" );}
						}
			});
		</script>
	
