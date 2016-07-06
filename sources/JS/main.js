/* ====================================
		MAIN
====================================== */

$(function()//Fonction lancée au chargement de la page
{
	//Liste des onglets
	$("#tab-onglets ul li").each(function(){listeOnglets.push($(this).text());});
	//Création des onglets (jquery-ui)
	$("#tab-onglets").tabs({active: tabDefaut,	//tab actif par défaut
				activate:onChangeTab	//Fonction à exécuter lorsqu'on active une table
				});		


	//Messages
	if(messageRetour!="")
		afficheMessage(messageRetour);



	//Initialisation des pages
	if(STATUT=="admin")
	{
		getListeUsersAdmin("[ALL]");
		updateListesClasses();// Met a jour les listes des classes
	}

	//Mise en page des menus déroulants
	 $("#userAdminSelectClasse").selectBoxIt();
	 $("#notationFormulaireListesClasseEtEleves select").selectBoxIt();
	
	
	//Affichage de la page, une fois chargée
	$('body').css("display","block")
});



