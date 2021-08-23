/* ====================================
		MAIN
====================================== */

$(function()//Fonction lancée au chargement de la page
{
	//Liste des onglets ****************
	$("#tab-onglets ul li").each(function(){listeOnglets.push($(this).text());});
	//Création des onglets (jquery-ui)
	$("#tab-onglets").tabs({active: tabDefaut,	//tab actif par défaut
				activate:onChangeTab	//Fonction à exécuter lorsqu'on active une table
				});		


	//Messages ************************
	if(messageRetour!="")
		afficheMessage(messageRetour);



	//Initialisation des pages ***********************
	if(STATUT=="admin")
		getListeUsersAdmin("[ALL]");
	
	if(STATUT=="admin" || STATUT=="evaluateur")
		updateListesClasses();// Met a jour les listes des classes

	//Mise en page des menus déroulants ******************
	$("#userAdminSelectClasse").selectBoxIt();
	$("#notationFormulaireListesClasseEtEleves select").selectBoxIt();
	
	if(STATUT=="admin" || STATUT=="evaluateur")
	{
		$("#menu_bilan_general select").selectBoxIt();
		$("#BILAN_GENERAL_type_evaluation").data('selectBox-selectBoxIt').refresh();
	}

	//Génère (une premiere fois) la liste des contextes
	if(STATUT != "")
		updateContextesFromServer()
	if(STATUT == "admin" || STATUT=="evaluateur")
		updateListeClasseFromServer();


	//Enregistrement du dernier context ********
	//Utile si on veut pas le retaper à chaque fois
	DERNIER_CONTEXT="";


	//Affichage de la page, une fois chargée ***********
	$('body').css("display","block")



	//Test si on est toujours connecté (toutes les minutes)
	INTERVALLE_RELANCE=setInterval(testeConnecte,TIMEOUT_RELANCE*60*1000)

});




