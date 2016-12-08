			
				<div id="tab-notation">



					<div id="enteteTabNotation">

						<div class="bouton_top" onclick="$('#RecapNotationEleve .groupe_contenu,#RecapNotationEleve .listeIndicateurs').slideUp();">
							<img src="./sources/images/icone-reduire.png" alt="[&#8607;]"/>
							<strong>Tout Réduire</strong>
						</div>

						<div class="bouton_top" onclick="$('#RecapNotationEleve .groupe_contenu,#RecapNotationEleve .listeIndicateurs').slideDown();">
							<img src="./sources/images/icone-etendre.png" alt="[&#8609;]"/>
							<strong>Tout Étendre</strong>
						</div>
						
						<?php
						//Affichage du menu de sélection des classes et des élèves
						if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur")
						{?>
						<form id="notationFormulaireListesClasseEtEleves" style="float:right;">
							<select name="notationListeClasses" id="notationListeClasses" onchange="NotationGetListeEleves($(this).val());">
							</select>
							<select name="notationListeEleves" id="notationListeEleves" onchange="getNotationEleve($(this).val());">
							</select>
							<span id="bilanBoutonEleveSuivant" class="selectboxit test selectboxit-enabled selectboxit-btn" onclick="bilanBoutonEleveSuivant();" title="Élève suivant"><span>&#8626;</span></span>
						</form>
						<?php
						}
						?>

						<h2>Évaluations <span id="BILAN_pourcentage"></span></h2>
					</div>
					
					
					<div id="RecapNotationEleve">
						
					</div>
				</div>

