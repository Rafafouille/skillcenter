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


