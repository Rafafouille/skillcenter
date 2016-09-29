// ****************************************************
// ÉVÉNEMENT - ADMINISTRATION UTILISATEURS
// ****************************************************

//Recupere la liste des utilisateurs pour une classe donnée (arg facultatif) ----
getListeUsersAdmin=function(classe)
{
	if(typeof classe == 'undefined') classe="";
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"getUsersList",
				classe:classe
			},
			updateListeUsersAdmin,	//Fonction callback
			"json"	//Type de réponse
	);
}

//Callback de getListeUsersAdmin pour mettre à jour l'affichage
updateListeUsersAdmin=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	$("#tableau_utilisateurs").empty();//On vide la liste des utilisateurs
	for(i=0;i<reponse.listeUsers.length;i++)
	{
		var user=reponse.listeUsers[i];
		$("#tableau_utilisateurs").append(getUserHTMLfromJSON(user));
	}
}


//Transforme les donées users de JSON en code HTML
getUserHTMLfromJSON=function(user)
{
	var sousClasse=user.statut;
	var onClickBoutonModif="ouvreBoiteSupprimeUser("+user.id+",'"+user.prenom+" "+user.nom+" ("+user.login+")');";
	var onClickBoutonStatut="ADMIN_USER_change_statut("+user.id+");"


	if(user.id==ID_COURANT)//Si c'est nous qu'on affiche...
	{
		sousClasse+=" connecte";
		onClickBoutonModif="";
		var onClickBoutonStatut="";
	}

	retour= ""
+"										<div class=\"user "+sousClasse+"\" id=\"user_"+user.id+"\"  onmouseenter =\"$(this).find('.boutons_user').css('visibility','visible');\" onmouseleave=\"$(this).find('.boutons_user').css('visibility','hidden');\">"
+"											<span class=\"iconeUser\"></span>"
+"											<span class=\"nom-user\" onclick=\"ouvreBoiteModifieUser("+user.id+")\">"+user.nom+"</span>"
+"											<span class=\"prenom-user\" onclick=\"ouvreBoiteModifieUser("+user.id+")\">"+user.prenom+"</span>"
+"											<span class=\"classe-user\" onclick=\"ouvreBoiteModifieUser("+user.id+")\">"+user.classe+"</span>"
+"											<span class=\"login-user\" onclick=\"ouvreBoiteModifieUser("+user.id+")\" >"+user.login+"</span>"
+"											<span class=\"boutons_user\" >"
+"												<span class=\"bouton_user ADMIN_USER_bouton_Modif\" title=\"Modifier l'utilisateur\" onclick=\"ouvreBoiteModifieUser("+user.id+")\"></span>"
+"												<span class=\"bouton_user ADMIN_USER_bouton_Supprime\" title=\"Supprimer l'utilisateur\" onclick=\""+onClickBoutonModif+"\"></span>"
+"												<span class=\"bouton_user ADMIN_USER_bouton_Change_Statut\" title=\"Modifier le statut de l'utilisateur\" onclick=\""+onClickBoutonStatut+"\"></span>"
+"											</span>"
+"										</div>";

return retour;
}


//AJOUTE / UPDATE USER
ajouteUpdateUser=function()
{
	var nom=$("#newUser_nom").val();
	var prenom=$("#newUser_prenom").val();
	var classe=$("#newUser_classe").val();
	var login=$("#newUser_login").val();
	var mdp=$("#newUser_psw").val();
	var id=$("#newUser_id").val();
	if(id==-1)
		var action="addUser";
	else
		var action="updateUser";
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:action,
				id:id,
				newUser_nom:nom,
				newUser_prenom:prenom,
				newUser_classe:classe,
				newUser_login:login,
				newUser_psw:mdp
			},
			valideNewUpdateUser,	//Fonction callback
			"json"	//Type de réponse
	);
}

//Fonction (Callback de ajouteUpdateUser) qui met à jour la liste des utilisateur
valideNewUpdateUser=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	getListeUsersAdmin($("#userAdminSelectClasse").val());
}

// MODIF USER
ouvreBoiteModifieUser=function(i)
{
	var nom=$( "#newUser_nom").val($("#user_"+i+" .nom-user").text());
	var prenom=$( "#newUser_prenom").val($("#user_"+i+" .prenom-user").text());
	var classe=$( "#newUser_classe").val($("#user_"+i+" .classe-user").text());
	var login=$( "#newUser_login").val($("#user_"+i+" .login-user").text());
	var mdp=$( "#newUser_psw").val("");
	var id=$( "#newUser_id").val(i);
	$('#dialog-addUser').dialog('open');
}


// SUPPRIMER USER --------
ouvreBoiteSupprimeUser=function(i,nom)
{
	$( "#dialog-deleteUser-id").text(i);
	$( "#dialog-deleteUser-nom").text(nom);
	$( "#dialog-deleteUser").dialog( "open" );
}

//Supprime l'utilisateur 'i'
supprimeUser=function(i)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"delUser",
				id:i
			},
			callBack_supprimeUser,	//Fonction callback
			"json"	//Type de réponse
	);
}

//Callback de supprimeUser ----------
callBack_supprimeUser=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	$("#user_"+reponse.idSupprime).remove();
}


// A SUPPRIMER <<<<<
//UPGRADE USER
/*ouvreBoiteUpgradeUser=function(id,nom)
{
	$("#boiteUpgrade-id").text(id);
	$("#boiteUpgrade-nom").text(nom);
	$("#dialog-upgradeUser").dialog("open");
}

upgradeUser=function(id)
{
	$.post(
			'./sources/PHP/actionneur.php',//Requete appelée
			{	//Les données à passer par POST
				action:"upgradeUser",
				id:id
			},
			callBackUpgradeUser,	//Fonction callback
			"text"	//Type de réponse
	);
}

callBackUpgradeUser=function(reponse)
{
	debug(reponse);//Debug la réponse
	afficheMessage(reponse);
	$("#boutonModifieStatut_"+$("#boiteUpgrade-id").text()).replaceWith(getBoutonUpAndDowngradeUserFromJSON({id:$("#boiteUpgrade-id").text(),statut:"admin",nom:$("#boiteUpgrade-nom").text(),prenom:$("#boiteUpgrade-prenom").text()}));
	//$("#boutonModifieStatut_"+$("#boiteUpgrade-id").text()).attr('src', './sources/images/super.png');
}


//DOWNGRADE USER
ouvreBoiteDowngradeUser=function(id,nom)
{
	$("#boiteDowngrade-id").text(id);
	$("#boiteDowngrade-nom").text(nom);
	$("#dialog-downgradeUser").dialog("open");
}

downgradeUser=function(id)
{
	$.post(
			'./sources/PHP/actionneur.php',//Requete appelée
			{	//Les données à passer par POST
				action:"downgradeUser",
				id:id
			},
			callBackDowngradeUser,	//Fonction callback
			"text"	//Type de réponse
	);
}

callBackDowngradeUser=function(reponse)
{
	debug(reponse);//Debug la réponse
	afficheMessage(reponse);
	$("#boutonModifieStatut_"+$("#boiteDowngrade-id").text()).replaceWith(getBoutonUpAndDowngradeUserFromJSON({id:$("#boiteDowngrade-id").text(),statut:"",nom:$("#boiteDowngrade-nom").text(),prenom:$("#boiteDowngrade-prenom").text()}));
//	$("#boutonModifieStatut_"+$("#boiteDowngrade-id").text()).attr('src', './sources/images/student.png');
}*/
//>>>>>>>>>>>>>>>>>>>>>>>>



ADMIN_USER_change_statut=function(id,statut)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"changeStatutUser",
				id:id
			},
			callBack_changeStatut,	//Fonction callback
			"json"	//Type de réponse
	);
}

callBack_changeStatut=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	var user=reponse.user;
	//$("#boutonModifieStatut_"+user.id).replaceWith(getBoutonUpAndDowngradeUserFromJSON(user));
	$("#user_"+user.id).attr('class', 'user '+user.statut);
}
