			
				<div id="tab-notation">
					<h2>Notation</h2>
					


					<form class="selecteurs_haut_de_page">
						<select name="notationListeClasses" id="notationListeClasses" onchange="NotationGetListeEleves($(this).val());">
						</select>
						<select name="notationListeEleves" id="notationListeEleves" onchange="getNotationEleve($(this).val());">
						</select>
					</form>
					
					
					
					<div id="RecapNotationEleve">
						
					</div>
				</div>
				
				<script>
				//$("#notationListeClasses,#notationListeEleves").selectmenu();
				</script>
