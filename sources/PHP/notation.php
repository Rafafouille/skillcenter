			
				<div id="tab-notation">



					<div class="bouton_top" onclick="$('#RecapNotationEleve .groupe_contenu,#RecapNotationEleve .listeIndicateurs').slideUp();">
						<img src="./sources/images/icone-reduire.png" alt="[&#8607;]"/>
						<strong>Tout Réduire</strong>
					</div>

					<div class="bouton_top" onclick="$('#RecapNotationEleve .groupe_contenu,#RecapNotationEleve .listeIndicateurs').slideDown();">
						<img src="./sources/images/icone-etendre.png" alt="[&#8609;]"/>
						<strong>Tout Étendre</strong>
					</div>


					<h2>Évaluations</h2>
					


					<?php
					//Affichage du menu de sélection des classes et des élèves
					if($_SESSION['statut']=="admin")
					{?>
					<form>
						<select name="notationListeClasses" id="notationListeClasses" onchange="NotationGetListeEleves($(this).val());">
						</select>
						<select name="notationListeEleves" id="notationListeEleves" onchange="getNotationEleve($(this).val());">
						</select>
					</form>
					<?php
					}
					?>
					
					<div id="RecapNotationEleve">
						
					</div>
				</div>

