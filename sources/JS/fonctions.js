


rechargePageAvecMessage=function(m)
{
	/*var prefixe=m.substring(0,2);
	if(prefixe==":)" || prefixe==":|" || prefixe==":(")
		m=m.substring(2);*/
	window.location.href="./?message="+m+"&tab="+$("#tab-onglets").tabs('option', 'active');	//Rechrage la page avec le même onglet et un éventuel message retour
}



//fonction callback pour Ajax
//Fonction qui reçoit et affiche le message retour d'une requete AJAX
recoitValideRecharge=function(reponse)
{
	debug(reponse);
	var smile=reponse.messageRetour.substring(0,2);
	var text=reponse.messageRetour.substring(2,reponse.length);
	
	if(smile==":)")
		window.location.href="./?message="+reponse.messageRetour+"&tab="+$("#tab-onglets").tabs('option', 'active');	//Rechrage la page avec le même onglet et un éventuel message retour
	else
		afficheMessage(reponse.messageRetour);
}




//Fonction qui affiche un message au travers d'une boite de dialogue
afficheMessage=function(message)
{
	var prefixe=message.substring(0,2);
	if(prefixe==":)" || prefixe==":|" || prefixe==":(" || prefixe==":X")
		message=message.substring(2)
	if(prefixe!=":X")//Si on autorise l'affichage
	{
		switch(prefixe)
		{
			case ":)":
				$("#dialog-messageRetour").css("background","#AAFFAA");
				break;
			case ":|":
				$("#dialog-messageRetour").css("background","#FFFFAA");
				break;
			case ":(":
				$("#dialog-messageRetour").css("background","#FFAAAA");
				break;
		}
		$("#dialog-messageRetour").text(message);
		$("#dialog-messageRetour").dialog("open");
		setTimeout(function(){$("#dialog-messageRetour").dialog("close");}, 5000)
	}
}

//Fonction qui affiche un éventuel message de debu
//en réponse d'une requete ajax/JSON
debug=function(rep)
{
	deb=rep.debug;
	if(deb!="(no comment)")
		console.log(deb)
}


//Fonctions qui affichent les bouton admin user

getBoutonSupprimeUserFromJSON=function(user)
{
	var id=parseInt(user["id"]);
	var nom=user["nom"];
	var prenom=user["prenom"];
	var login=user["login"];
	if(id!=ID_COURANT)//si c'est pas nous...
		return "<img style=\"cursor:pointer;\" src=\"./sources/images/icone-supprime_utilisateur.png\" title=\"Supprimer l'utilisateur\" alt=\"[Suppr]\" onclick=\"ouvreBoiteSupprimeUser("+id+",'"+prenom+" "+nom+" ("+login+")');\"/>";
	else
		return "<img style=\"cursor:auto;\" src=\"./sources/images/icone-supprime_utilisateur-OFF.png\" title=\"Vous ne pouvez pas vous auto-supprimer\" alt=\"[Suppr]\" />";
}
getBoutonUpAndDowngradeUserFromJSON=function(user)
{
	if(user['statut']=="admin")//Si l'utilisateur est admin...
	{
		if(parseInt(user['id'])!=ID_COURANT)//...et que c'est pas nous
			return "<img id=\"boutonModifieStatut_"+user['id']+"\" src=\"./sources/images/super.png\" title=\"Super utilisateur (rendre normal)\" alt=\"[SU]\" onclick=\"ouvreBoiteDowngradeUser("+user['id']+",'"+user['prenom']+" "+user['nom']+" ("+user['login']+")');\"/>";
		else//...Si c'est nous : pas le droit de s'auto-downgrader
			return "<img id=\"boutonModifieStatut_"+user['id']+"\" style=\"cursor:auto;\" src=\"./sources/images/super-OFF.png\" title=\"Vous ne pouvez pas vous auto-rétrograder\" alt=\"[SU]\" />";
	}
	else
	{
		return "<img id=\"boutonModifieStatut_"+user['id']+"\" src=\"./sources/images/student.png\" title=\"Utilisateur normal (rendre SuperUtilisateur)\" alt=\"[o_o]\" onclick=\"ouvreBoiteUpgradeUser("+user['id']+",'"+user['prenom']+" "+user['nom']+" ("+user['login']+")');\"/>";
	}
	return "";
}


//Renvoie une couleur de l'arc en ciel entre rouge et vert (pour les compétences)
function setArcEnCiel(val,maxi)
{
	if(val<0)
		return "#FF0000";
	if(val>maxi)
		return "#00FF00";
	n=val/maxi;
	if(n<0.5)
	{
		var a=2*n*255;
		return "#FF"+("00"+a.toString(16)).substr(-2,2)+"00";
	}
	else
	{
		var a=(2-2*n)*255;
		return "#"+("00"+a.toString(16)).substr(-2,2)+"FF00";
	}
}


