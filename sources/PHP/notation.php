			
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
						

						<form id="notationFormulaireListesClasseEtEleves" style="float:right;">
							<div class="dropdown_menu_notation">
									Filtre :<br/>
									<select id="BILAN_listeContextes" name="BILAN_listeContextes" onchange="getNotationEleve($('#notationListeEleves').val(),$(this).val());">
										<option value="ALL_CONTEXTE">Choix du contexte</option>
									</select>
							</div>
							<?php
								//Affichage du menu de sélection des classes et des élèves
								if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur")
								{?>
							<div class="dropdown_menu_notation">
									Classe :<br/>
									<select name="notationListeClasses" id="notationListeClasses" onchange="NotationGetListeEleves($(this).val());">
									</select>
							</div>
							<div class="dropdown_menu_notation">
									Élève :<br/>
									<select name="notationListeEleves" id="notationListeEleves" onchange="getNotationEleve($(this).val(),$('#BILAN_listeContextes').val());">
									</select>
							</div>
							<div class="dropdown_menu_notation">
									<br/>
									<span id="bilanBoutonEleveSuivant" class="selectboxit test selectboxit-enabled selectboxit-btn" onclick="bilanBoutonEleveSuivant();" title="Élève suivant"><span>&#8626;</span></span>
							</div>
							<?php
								}
							?>
							<div class="dropdown_menu_notation">
									Type d'évaluation :<br/>
									<select name="bilanTypeEvaluation" id="bilanTypeEvaluation" onchange="getNotationEleve($('#notationListeEleves').val(),$('#BILAN_listeContextes').val());">
											<option value="last" selected>Dernière évaluation</option>
											<option value="max">Évaluation maximum</option>
											<option value="avg">Évaluation moyenne</option>
									</select>
							</div>
						</form>

						<h2>Évaluations <span id="BILAN_pourcentage"></span></h2>
					</div>
					
					
					<div id="RecapNotationEleve">
						
					</div>
				</div>

