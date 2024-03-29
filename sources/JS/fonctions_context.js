// Récupère la liste des contextes depuis le serveur ********************************
updateContextesFromServer = function()
{
	$.post(
		'./sources/PHP/actionneurJSON.php',//Requete appelée
		{	//Les données à passer par POST
			action : "getContextes"
		},
		updateContextesFromServer_callback,	//Fonction callback
		"json"	//Type de réponse
	);	
}

updateContextesFromServer_callback = function(reponse)
{
	LISTE_CONTEXTES = Array()
	reponse.contextes.forEach(function(item)
	{
		LISTE_CONTEXTES[item.id]={'id':item.id, 'nom':item.nom, 'ordre':item.ordre};
	})
	updateListeContexteDansMenu();
}



// Fonction qui met à jour la liste des contextes dans le menu Bilan ET Bilan général, à partir de LISTE_CONTEXTES
//updateContextesMenuBilan = function()
updateListeContexteDansMenu = function()
{
	$("#BILAN_listeContextes").empty();
	$("#BILAN_GENERAL_choix_contexte").empty();
	
	$("#BILAN_listeContextes").append("										<option value=\"0\">Tout contexte</option>");
	$("#BILAN_GENERAL_choix_contexte").append("										<option value=\"0\">Tout contexte</option>");
	
	
	// C'est un peu moche, mais on recopie le contexte dans l'ordre... (je n'ose pas indexer LISTE_CONTEXTES directement sur l'ordre)
	LISTE_CONTEXTES_TRIES = Array()
	LISTE_CONTEXTES.forEach(function(item)
	{
		LISTE_CONTEXTES_TRIES[item.ordre]=item;
	})
	
	LISTE_CONTEXTES_TRIES.forEach(function(item)
	{
		$("#BILAN_listeContextes").append("										<option value=\""+String(item['id'])+"\">"+item['nom']+"</option>");
		$("#BILAN_GENERAL_choix_contexte").append("										<option value=\""+String(item['id'])+"\">"+item['nom']+"</option>");
	})
	
	$('#BILAN_listeContextes').data('selectBox-selectBoxIt').refresh();	//Mise à jour du menu déroulant
	$('#BILAN_GENERAL_choix_contexte').data('selectBox-selectBoxIt').refresh();	//Mise à jour du menu déroulant
}



updateGrilleContextesFromServer = function()
{
	$.post(
		'./sources/PHP/actionneurJSON.php',//Requete appelée
		{	//Les données à passer par POST
			action : "getGrilleHTMLContextes"
		},
		updateGrilleContextesFromServer_callback,	//Fonction callback
		"json"	//Type de réponse
	);
}


updateGrilleContextesFromServer_callback = function(reponse)
{
	$("#contenant_tableau_contextes").empty();
	$("#contenant_tableau_contextes").append(reponse.HTML);
}
