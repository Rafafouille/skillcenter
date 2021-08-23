function getBilanGeneral(classe,contexte,type)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"getBilanGeneral",
				classe:classe,
				contexte:contexte,
				type:type
			},
			getBilanGeneral_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}

function getBilanGeneral_callback(reponse)
{
	cacheBarreChargement();
	$("#grille_bilan_general").empty();
	$("#grille_bilan_general").append(reponse.HTML);
}
