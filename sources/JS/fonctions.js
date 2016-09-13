


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

		//Ouverture de la boite
		var focus=$(":focus");//Magouille pour annuler l'autofocus à louverture de la boite
		$("#dialog-messageRetour").dialog("open");
		if(focus!=[])
			setTimeout(function(){focus.focus();},510);//On remets le focus juste apres l'animation

		//Fermeture au bout de quelques secondes
		setTimeout(function(){
																var focus=$(":focus");//Magouille pour supprimer l'autofocus au moment de la fermeture
																$("#dialog-messageRetour").dialog("close");
																if(focus!=[])
																	focus.focus();
												}, 5000)
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




//Renvoie une couleur de l'arc en ciel entre rouge et vert (pour les compétences)
function setArcEnCiel(val,maxi)
{
	//Extrêmes
	if(val<0)
		return "#FF0000";
	if(val>maxi)
		return "#00FF00";

	//Valeurs intermédiaires
	n=val/maxi;
	non_linearite=0.1;//Compris entre 0 et 1/(2pi)
	n+=Math.sin(n*2*3.1415926535)*non_linearite;	//Création d'une non-linéarité

	if(n<0.5)
	{
		var a=Math.floor(2*n*255);
		return "#FF"+("00"+a.toString(16)).substr(-2,2)+"00";
	}
	else
	{
		var a=Math.floor((2-2*n)*255);
		return "#"+("00"+a.toString(16)).substr(-2,2)+"FF00";
	}
}



//Fonction qui choisit quoi écrire dans les critère
function intitule_critere(val,maxi)
{
		if(maxi<=NOMS_NIVEAUX.length)
		{
			if(val<=NOMS_NIVEAUX[maxi-1].length-1)
				return NOMS_NIVEAUX[maxi-1][val];
			else
				return "_";
		}
		else
			return "-";
}



//Fonction qui permet d'échapper les apostrophes des chaines de caractères, etc.
function addslashes(ch) {
ch = ch.replace(/\\/g,"\\\\")
ch = ch.replace(/\'/g,"\\'")
ch = ch.replace(/\"/g,"\\\"")
return ch
}


