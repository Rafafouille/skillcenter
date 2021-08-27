


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
		$("#dialog-messageRetour").html(message);

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


//Fonction affiche ou cache la barre de chargement *************************
function afficheBarreChargement()
	{$("#barre_notification").show(200);}
function cacheBarreChargement()
	{$("#barre_notification").hide(200);}
	
	
	
	
	
// Fonction qui indique quel est le n°ID de l'utilisateur dont on veut récupérer la note :
// Si on est prof / evaluateur : cela prend le n°indiqué dans la page Bilan, dans la liste
// Si on est élèves, ca renvoie son propre numéro ID
function getIdEleveCourant()
{
	if($("#notationListeEleves").val()==undefined)
		return ID_COURANT;
	return parseInt($("#notationListeEleves").val());
}






// Fonction qui va chercher la liste des classes sur le serveur et qui met à jour la variable JS LISTE_CLASSES
function updateListeClasseFromServer()
{
	
	$.post(
		'./sources/PHP/actionneurJSON.php',//Requete appelée
		{	//Les données à passer par POST
			action : "getListeClasses"
		},
		updateListeClasseFromServer_callback,	//Fonction callback
		"json"	//Type de réponse
	);
}
function updateListeClasseFromServer_callback(reponse)
{
	LISTE_CLASSES = Array()
	reponse.listeClasses.forEach(function(item)
	{
		LISTE_CLASSES.push(item);
	})
	updateListeClassesDansMenu();
}

//Fonction qui met à jour les menus déroulant des classes, à partir de la variable JS LISTE_CLASSES
// Cette fonction peut être utilisée après updateListeClasseFromServer()
function updateListeClassesDansMenu()
{
	$("#notationListeClasses").empty();
	$("#BILAN_GENERAL_choix_classe").empty()
	
	$("#BILAN_GENERAL_choix_classe").append("										<option value=\"ALL_CLASSES\">Toute classe</option>");
			
	LISTE_CLASSES.forEach(function(item)
	{
		$("#notationListeClasses").append("										<option value=\""+item+"\">"+item+"</option>");
		$("#BILAN_GENERAL_choix_classe").append("										<option value=\""+item+"\">"+item+"</option>");
	})
	
	$('#notationListeClasses').data('selectBox-selectBoxIt').refresh();	//Mise à jour du menu déroulant
	$('#BILAN_GENERAL_choix_classe').data('selectBox-selectBoxIt').refresh();	//Mise à jour du menu déroulant
}






// Met en chiffres romains
function romanize(num)
{
    if (isNaN(num))
        return NaN;
    var digits = String(+num).split(""),
        key = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
               "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
               "","I","II","III","IV","V","VI","VII","VIII","IX"],
        roman = "",
        i = 3;
    while (i--)
        roman = (key[+digits.pop() + (i * 10)] || "") + roman;
    return Array(+digits.join("") + 1).join("M") + roman;
}





// Fonction qui trie un tableau de compétence (ou de domain, ou d'indicateur) par position.
// Il faut que ce soit un tableau contenant des petits tableaux associatifs avec la clé "position"
// Ne renvoie rien (modifie directement le tableau)
function trieCompetencesParPosition(tab)
{
	for(i=0;i<tab.length;i++)
	{
		for(j=0;j<tab.length-i-1;j++)
		{
			if(tab[j].position > tab[j+1].position)
			{
				a=tab[j]
				b=tab[j+1]
				tab[j]=b
				tab[j+1]=a
			}
		}
	}
}
