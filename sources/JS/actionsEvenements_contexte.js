// ****************************************************
// ÉVÉNEMENTS - CONTEXTE
// ****************************************************



// Envoie un nouveau contexte au serveur ****************
ajouteContexte = function()
{
	var nom = $("#newContexte_nom").val();
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action : "newContexte",
				nom : nom
			},
			nouveauContexte_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}


nouveauContexte_callback=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	
	//Mise à jour des menus de contexte dans bilan (entre autre)
	updateContextesFromServer(); //Récupere et met à jour la liste (notamment dans le menu)
	updateGrilleContextesFromServer(); //Met a jour l'affichage de la grille venant du serveur
}



// Modif un contexte ****************

ouvreBoiteModifContexte = function(idCont)
{
	$("#modifContexte_nom").val($("#titre_contexte_"+String(idCont)+" .contexte_titre_contexte_seul").text());
	$("#modifContexte_id").val(idCont);
	$("#dialog-modifContexte").dialog("open");
}



modifContexte = function()
{
	var nom = $("#modifContexte_nom").val();
	var id = $("#modifContexte_id").val();
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action : "modifContexte",
				nom : nom,
				id : id
			},
			modifContexte_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}


modifContexte_callback=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	
	//Mise à jour des menus de contexte dans bilan (entre autre)
	updateContextesFromServer(); //Récupere et met à jour la liste (notamment dans le menu)
	updateGrilleContextesFromServer(); //Met a jour l'affichage de la grille venant du serveur
}





// Change l'etat du lien ********************************
activeDesactiveLienContexteIndicateur = function(cont,ind)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action : "activeDesactiveLienContexteIndicateur",
				contexte : cont,
				indicateur : ind
			},
			activeDesactiveLienContexteIndicateur_callback,	//Fonction callback
			"json"	//Type de réponse
	);	
}


activeDesactiveLienContexteIndicateur_callback = function(reponse)
{
	var context = parseInt(reponse.lienIndicateurContexte.contexte);
	var indicateur = parseInt(reponse.lienIndicateurContexte.indicateur);
	var valider = reponse.lienIndicateurContexte.etat=="valider";
	var element = $("#lienContexteIndicateur_"+String(context)+"_"+String(indicateur));
	if(valider)
	{
		element.removeClass('invalide')
		element.addClass('valide')
	}
	else
	{
		element.removeClass('valide')
		element.addClass('invalide')
	}
}



// SUPPRIMER LE CONTEXTE ****************************
supprimeContexte_ouvreBoite = function(idCont)
{
	$("#id_valideSupprContexte").text(idCont);
	$("#nom_valideSupprContexte").text($("#titre_contexte_"+String(idCont)+" .contexte_titre_contexte_seul").text());
	$("#dialog-delContexte").dialog("open");
}


supprimeContexte = function(idCont)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action : "supprimeContexte",
				contexte : idCont
			},
			supprimeContexte_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}


supprimeContexte_callback = function(reponse)
{
	updateContextesFromServer(); //Récupere et met à jour la liste (notamment dans le menu)
	updateGrilleContextesFromServer(); 
}
