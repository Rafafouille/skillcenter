		<div class="loginBox">
		<?php
		if(!$_SESSION['id'])	//Si personne n'est connecté
		{
		?>
			<form>
				<table>
					<tr>
						<td>
							<label for="loginBox-login">Identifiant : </label>
						</td>
						<td>
							<input type="text" name="loginBox-login" id="loginBox-login" placeholder="Votre identifiant" size="20"  tabindex="1" autofocus onKeyPress="if(event.keyCode==13) {login($('#loginBox-login').val(),$('#loginBox-pwd').val());}"/>
						</td>
						<td rowspan="2">
							<div id="boutonLogin" onclick="login($('#loginBox-login').val(),$('#loginBox-pwd').val());" tabindex="3" onKeyPress="if(event.keyCode==13) {login($('#loginBox-login').val(),$('#loginBox-pwd').val());}"></div>
						</td>
					</tr>
					<tr>
						<td>
							<label for="loginBox-login">Mot de passe : </label>
						</td>
						<td>
							<input type="password" name="loginBox-pwd" id="loginBox-pwd" placeholder="Votre mot de passe"  size="20"  tabindex="2" onKeyPress="if(event.keyCode==13) {login($('#loginBox-login').val(),$('#loginBox-pwd').val());}"/>
						</td>
					</tr>
				</table>
			</form>
		<?php
		}
		else //Si connecté
		{
		?>
		
			<table>
				<tr>
					<td>
						Utilisateur :<br/>
						<?php echo $_SESSION['prenom']." ".$_SESSION['nom'];?>

					</td>
					<td>
						<div id="boutonLogout" onclick="logout();" tabindex="3" onKeyPress="if(event.keyCode==13) {login($('#loginBox-login').val(),$('#loginBox-pwd').val());}"></div>
					</td>
				</tr>
			</table>
			<form action="" method="POST">
				<p>
					Utilisateur : <?php echo $_SESSION['prenom']." ".$_SESSION['nom'];?>
					
				</p>
				<input type="hidden" name="action" value="unlog"/>
				<input type="submit" value="Déconnection"/>
			</form>
		<?php
		}
		?>
		</div>
