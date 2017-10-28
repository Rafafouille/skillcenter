<!-- -------------------------------
BOITES BILAN
------------------------ -->



 		<!-- BOITE afficher les commentaires------------------- -->
		<div id="dialog-commentaireEvaluation" title="Commentaires d'évaluation">
			<div class="commentairesListeContextes">
			</div>
			<p><img src="./sources/images/anime-comment.gif" alt="[⌛ Chargement...]"/></p>
		</div>
		<script>
			$( "#dialog-commentaireEvaluation").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
						"Fermer": fermerFenetreCommenairesBilan
					},
				width:600
			});
		</script>




 		<!-- BOITE Affiche graphique------------------- -->
		<div id="dialog_graphique" title="Bilan en graphique">
			<div id="dialiog_graphique_camembert_domaines_conteneur">
				<canvas id="dialiog_graphique_camembert_domaines" width="400" height="400">
				</canvas>
			</div>
		</div>
		<script>
			$( "#dialog_graphique").dialog({
				autoOpen: false,
				modal: true,
				buttons: {
						"Fermer": 	function(){$("#dialog_graphique").dialog("close");}
					},
				width:600
			});
		</script>

