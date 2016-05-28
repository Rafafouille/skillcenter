/* ====================================
		MAIN
====================================== */

window.onload=function()
{
	//Création des onglets (jquery-ui)
	$("#tab-onglets").tabs({active: tabDefaut});
	
	//Messages
	if(messageRetour!="")
		afficheMessage(messageRetour);



	//Initialisation des pages
	if(STATUT=="admin")
	{
		getListeUsersAdmin("[ALL]");
		updateListesClasses();
	}
}
