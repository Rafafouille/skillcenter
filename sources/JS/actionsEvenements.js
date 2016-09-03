// ****************************************************
// ÉVÉNEMENTS PAR DEFAUT...
// ****************************************************


// ****************************************************
// TABS (onglets)
// ****************************************************

// Fonction appelée quand on change de Tab
onChangeTab=function(event,ui)
{

	var iOnglet=$('#tab-onglets').tabs('option', 'active');

	//1er affichage de l'onglet "Notation"
	if(listeOnglets[iOnglet]=="Bilan" && !NOTATION_LOADED)//Si la page "notation" n'a jamais été chargée
	{
		NOTATION_LOADED=true;
		if(STATUT=="admin" || STATUT=="evaluateur")	//Si c'est un prof qui est connecté
		{
			var classe=$("#notationListeClasses").val();
			NotationGetListeEleves(classe);
		}
		else	// Si c'est un élève qui est connecté
		{
			getNotationEleve(ID_COURANT);
		}
	}
	if(listeOnglets[iOnglet]=="Paramétrage" && !ADMIN_COMPETENCES_LOADED)//Si la page 3 n'a jamais été chargée
	{
		ADMIN_COMPETENCES_LOADED=true;
		var classe=$("#selectClasseCompetences").val();
		updateCompetencesSelonClasse(classe);
	}
}


// ****************************************************
// LOGIN / LOGOUT
// ****************************************************

// Connection (commande AJAX). login et mdp = strings
login=function(login,mdp)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"login",
				login:login,
				mdp:mdp
			},
			recoitValideRecharge,	//Fonction callback
			"json"	//Type de réponse
	);
}
// Déconnection (commande AJAX)
logout=function()
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"logout"
			},
			recoitValideRecharge,	//Fonction callback
			"json"	//Type de réponse
	);
}





/**********************************************
		LISTE CLASSES
***********************************************/


// Fonction qui met à jour les listes de classe
// (sur plusieurs pages)
updateListesClasses=function()
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"getListeClasses"
			},
			updateListesClasses_CallBack,	//Fonction callback
			"json"	//Type de réponse
	);
}

//Callback de la fonction
updateListesClasses_CallBack=function(reponse)
{

	//ATTENTION : Les pages à raffraichir ne sont peut être pas chargée (selon le statut de l'utilisateur)


	//Liste dans la page "Competences"
	$("#selectClasseCompetences").empty();
	//$("#selectClasseCompetences").append("<option value=\"[ALL]\">Toutes les classes</option>");
	//Liste dans la page "Notation"
	$("#notationListeClasses").empty();

	for(var i=0;i<reponse.listeClasses.length;i++)
	{
		var classe=reponse.listeClasses[i];
		//Ajout dans la page "Competences"
		$("#selectClasseCompetences").append("<option value=\""+classe+"\">"+classe+"</option>");
		//Ajout dans la page "Notation"
		$("#notationListeClasses").append("<option value=\""+classe+"\">"+classe+"</option>");
	}
	
	//Rafraichissement des boites dropdown
	if($("#notationFormulaireListesClasseEtEleves #notationListeClasses").length)//Si la page de notation a été chargée...
		$("#notationFormulaireListesClasseEtEleves #notationListeClasses").data("selectBox-selectBoxIt").refresh();//Mise a jour SelectBoxIT
	if($("#userAdminSelectClasse").length)//Si la page des utilisateur existe
		$("#userAdminSelectClasse").data("selectBox-selectBoxIt").refresh();//Mise a jour SelectBoxIT

}


