	
				<div id="tab-contextes">


					<div class="bouton_ajoute" onclick="$('#dialog-addContexte').dialog('open');">
						<img src="./sources/images/icone-plus.png" alt="[+]"/>
						<strong>Ajouter un contexte</strong>
					</div>


					<div id="contenant_tableau_contextes">
					<?php
					
						echo getTableauContextesHTML(); 	//Fonction définie dans "fonctions.php"
						
					?>
					</div>


					
				</div>
