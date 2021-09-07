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
	afficheBarreChargement();
}

//Callback de getListeUsersAdmin pour mettre à jour l'affichage
updateListeUsersAdmin=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	$("#tableau_utilisateurs").empty();//On vide la liste des utilisateurs
	for(i=0;i<reponse.listeUsers.length;i++)
	{
		var user=reponse.listeUsers[i];
		$("#tableau_utilisateurs").append(getUserHTMLfromJSON(user));
	}
}


//Transforme les données users de JSON en code HTML
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
+"										<div class=\"user "+sousClasse+"\" id=\"user_"+user.id+"\"  data-id=\""+user.id+"\" data-mail=\""+user.mail+"\" data-notifiemail=\""+user.notifieMail+"\" onmouseenter =\"$(this).find('.boutons_user').css('visibility','visible');\" onmouseleave=\"$(this).find('.boutons_user').css('visibility','hidden');\" data-login=\""+user.login+"\">"
+"											<span class=\"iconeUser\"></span>"
+"											<span class=\"nom-user\" onclick=\"ouvreBoiteModifieUser("+user.id+")\">"+user.nom+"</span>"
+"											<span class=\"prenom-user\" onclick=\"ouvreBoiteModifieUser("+user.id+")\">"+user.prenom+"</span>"
+"											<span class=\"classe-user\" onclick=\"ouvreBoiteModifieUser("+user.id+")\">"+user.classe+"</span>"
+""//"											<span class=\"login-user\" onclick=\"ouvreBoiteModifieUser("+user.id+")\" >"+user.login+"</span>"
+"											<span class=\"boutons_user\" >";
		if(user.mail!="" && parseInt(user.notifieMail)==1)
	retour += ""
+"												<span class=\"bouton_user ADMIN_USER_bouton_envoie_bilan\" title=\"Envoyer le bilan\" onclick=\"envoieBilan("+user.id+")\"></span>";
	retour+= ""
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
	var login=$("#newUser_login").val().toLowerCase();
	var mdp=$("#newUser_psw").val();
	var id=$("#newUser_id").val();
	var mail=$("#newUser_mail").val();
	var notifieMail=$("#newUser_notifieMail").prop('checked')?"1":"0";

	if(id==-1)	var action="addUser";
	else				var action="updateUser";
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:action,
				id:id,
				newUser_nom:nom,
				newUser_prenom:prenom,
				newUser_classe:classe,
				newUser_login:login,
				newUser_psw:mdp,
				newUser_mail:mail,
				newUser_notifieMail:notifieMail,
			},
			valideNewUpdateUser,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}

//Fonction (Callback de ajouteUpdateUser) qui met à jour la liste des utilisateur
valideNewUpdateUser=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	getListeUsersAdmin($("#userAdminSelectClasse").val());
}

// NOUVEAU USER
ouvreBoiteAddUser=function()
{
	$( "#newUser_nom").val("");
	$( "#newUser_prenom").val("");
	//var classe=$( "#newUser_classe").val($("#user_"+i+" .classe-user").text());
	$( "#newUser_login").val("");
	$( "#newUser_psw").val("");
	$( "#newUser_id").val(-1);
	$("#newUser_mail").val("");
	$("#newUser_notifieMail").prop('checked', true);
	
	$('#dialog-addUser').dialog('open');
}

// MODIF USER
ouvreBoiteModifieUser=function(i)
{
	var nom=$( "#newUser_nom").val($("#user_"+i+" .nom-user").text());
	var prenom=$( "#newUser_prenom").val($("#user_"+i+" .prenom-user").text());
	var classe=$( "#newUser_classe").val($("#user_"+i+" .classe-user").text());
	var login=$( "#newUser_login").val($("#user_"+i).attr("data-login"));
	var mdp=$( "#newUser_psw").val("");
	var id=$( "#newUser_id").val(i);
	var mail=$("#newUser_mail").val($("#user_"+i).attr("data-mail"));
	if($("#user_"+i).attr("data-notifiemail")=="1")
		$("#newUser_notifieMail").prop('checked', true);
	else
		$("#newUser_notifieMail").prop('checked', false);

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
supprimeUser=function(i,suppEval=true)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"delUser",
				id:i,
				suppEval:suppEval
			},
			callBack_supprimeUser,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}

//Callback de supprimeUser ----------
callBack_supprimeUser=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	$("#user_"+reponse.idSupprime).remove();
}


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
	afficheBarreChargement();
}

callBack_changeStatut=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	var user=reponse.user;
	//$("#boutonModifieStatut_"+user.id).replaceWith(getBoutonUpAndDowngradeUserFromJSON(user));
	$("#user_"+user.id).attr('class', 'user '+user.statut);
}

//Fonction qui envoie un bilan à l'utilisateur *************************************
envoieBilan=function(idEleve)
{
	$.post(
		'./sources/PHP/actionneurJSON.php',//Requete appelée
		{	//Les données à passer par POST
			action:"envoieBilan",
			id:idEleve
		},
		callBack_envoieBilan,	//Fonction callback
		"json"	//Type de réponse
	);
	afficheBarreChargement();
}

callBack_envoieBilan=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
}



//Fonction qui envoie un bilan à plusieurs utilisateurs *************************************
envoiePlusieursBilans=function(classe)
{
	$.post(
		'./sources/PHP/actionneurJSON.php',//Requete appelée
		{	//Les données à passer par POST
			action:"envoiePlusieursBilans",
			classe:classe
		},
		callBack_envoiePlusieursBilans,	//Fonction callback
		"json"	//Type de réponse
	);
	afficheBarreChargement();
}

callBack_envoiePlusieursBilans=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
}



