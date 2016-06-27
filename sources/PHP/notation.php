			
				<div id="tab-notation">
					<h2>Notation</h2>
					


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

