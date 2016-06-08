// ****************************************************
// TABS (onglets)
// ****************************************************

// Fonction appelée quand on change de Tab
onChangeTab=function(event,ui)
{

	//1er affichage de l'onglet "Notation"
	if($('#tab-onglets').tabs('option', 'active')==2 && !NOTATION_LOADED)//Si la page 3 n'a jamais été chargée
	{
		NOTATION_LOADED=true;
		var classe=$("#notationListeClasses").val();
		NotationGetListeEleves(classe);
	}
	if($('#tab-onglets').tabs('option', 'active')==3 && !ADMIN_COMPETENCES_LOADED)//Si la page 3 n'a jamais été chargée
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

// ****************************************************
// ADMINISTRATION UTILISATEURS
// ****************************************************
//GET LIST USERS
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

updateListeUsersAdmin=function(reponse)
{
	debug(reponse);//Debug la réponse
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
	retour= ""
+"										<div class=\"user\" id=\"user_"+user.id+"\">"
+"											<span class=\"nom-user\">"+user.nom+"</span>"
+"											<span class=\"prenom-user\">"+user.prenom+"</span>"
+"											<span class=\"classe-user\">"+user.classe+"</span>"
+"											<span class=\"login-user\">"+user.login+"</span>"
+"											<span class=\"boutons_user\">"
+"												<img id=\"boutonModifieInfosUser_"+user.id+"\" src=\"./sources/images/icone-modif.png\" title=\"Modifier l'utilisateur\" alt=\"[Modif]\" onclick=\"ouvreBoiteModifieUser("+user.id+")\"/>"
+"												"+getBoutonSupprimeUserFromJSON(user)
+"												"+getBoutonUpAndDowngradeUserFromJSON(user)
+"											</span>"
+"										</div>";

return retour;
}


//AJOUTE / UPDATE USER
ajouteUpdateUser=function()
{
	var nom=$( "#newUser_nom").val();
	var prenom=$( "#newUser_prenom").val();
	var classe=$( "#newUser_classe").val();
	var login=$( "#newUser_login").val();
	var mdp=$( "#newUser_psw").val();
	var id=$( "#newUser_id").val();
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

//Fonction qui met à jour la liste des utilisateur
valideNewUpdateUser=function(reponse)
{
	debug(reponse);//Debug la réponse
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


// SUPPRIMER USER
ouvreBoiteSupprimeUser=function(i,nom)
{
	$( "#dialog-deleteUser-id").text(i);
	$( "#dialog-deleteUser-nom").text(nom);
	$( "#dialog-deleteUser").dialog( "open" );
}

supprimeUser=function(i)
{
	$.post(
			'./sources/PHP/actionneur.php',//Requete appelée
			{	//Les données à passer par POST
				action:"delUser",
				id:i
			},
			recoitValideRecharge,	//Fonction callback
			"text"	//Type de réponse
	);
}


//UPGRADE USER
ouvreBoiteUpgradeUser=function(id,nom)
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
	/*$("#boutonModifieStatut_"+$("#boiteUpgrade-id").text()).attr('src', './sources/images/super.png');*/
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
}







// ****************************************************
// ADMINISTRATION COMPETENCES
// ****************************************************


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
	
}



//UDAPTE COMPETENCES PAR CLASSE
updateCompetencesSelonClasse=function(classe)
{
		$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"updateCompetencesSelonClasse",
				classe:classe
			},
			updateCompetencesSelonClasse_Callback,	//Fonction callback
			"json"	//Type de réponse
	);
}

//UDAPTE COMPETENCES PAR CLASSE (CALLBACK)
updateCompetencesSelonClasse_Callback=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	//VARIABLES GLOABLES !!
	numeroCompetence=0;
	numeroIndicateur=0;

	$("#liste_competences").empty();
	var listeGroupes=reponse.listeGroupes;
	for(idGr in listeGroupes)
	{
		var groupe=listeGroupes[idGr];
		ajouteGroupeCompetences(groupe,"#liste_competences");



/*	if(0)
	{
		rendu+=""+
"			<div class=\"groupe_competences\" id=\"groupe_competence_"+groupe.id+"\">"+
"				<div class=\"entete_groupe_competences\">"+
"					<div class=\"boutonAjouteCompetence\" onclick=\"ouvreBoiteAddCompetence('"+groupe.nom+"',"+groupe.id+")\">"+
"						Ajouter une compétence"+
"					</div>"+
"					<h3 onclick=\"$(this).parent().parent().find('.groupe_contenu').toggle('easings');\">"+
"						"+groupe.nom+
"					</h3>"+
"				</div>"+
"				<div class=\"groupe_contenu\">";
		for(idComp in groupe.listeCompetences)
		{
			var competence=groupe.listeCompetences[idComp];
			numeroCompetence++;
			numeroIndicateur=0;
			rendu+=""+
"					<div class=\"competence\">"+
"						<div class=\"boutonAjouterIndicateur\" onclick=\"ouvreBoiteAddIndicateur('"+competence.nom+"',"+competence.id+")\">"+
"							[+Indicateur]"+
"						</div>"+
"						<h3>"+
"							"+numeroCompetence+" - "+competence.nom+
"						</h3>"+
"						<div class=\"listeIndicateurs\">"+
"							<table class=\"indicateurs\">";
			for(idInd in competence.listeIndicateurs)
			{
				numeroIndicateur++;
				var indicateur=competence.listeIndicateurs[idInd];
				rendu+=""+
"								<tr>"+
"									<td class=\"intituleIndicateur\">"+
"										"+numeroCompetence+"."+numeroIndicateur+" - "+indicateur.nom+
"									</td>"+
"									<td class=\"detailIndicateur\">"+
"										<img src=\"./sources/images/icone-info.png\" alt=\"[i]\"  style=\"cursor:help;\" title=\""+competence.details+"\"/>"+
"										<img src=\"./sources/images/supprime.png\" alt=\"[X]\" style=\"cursor:not-allowed;\" title=\"Supprimer l'indicateur\"/>"+
"									</td>"+
"									<td class=\"niveauxIndicateur\">";
					for(i=0;i<=NB_NIVEAUX_MAX;i++)
					{
						if(i<=competence.niveaux)
						{
							rendu+=""+
"										<div class=\"indicateurAllume\" style=\"background-color:"+setArcEnCiel(i,competences.niveaux)+";\">"+i+"</div>";
						}
						else
						{
							rendu+=""+
"										<div class=\"indicateurEteint\">"+i+"</div>";
						}
					}
					rendu+=""+
"									</td>"+
"								</tr>";
				}
			rendu+=""+
"							</table>"+
"						</div>"+
"					</div>";
		}
		rendu+=""+
"				</div>"+
"			</div>";
	

	$("#liste_competences").append(rendu);
	}*/
	}//FIN DU IF 0
}




//AJOUTE GROUPE COMPETENCES
addGroupeCompetences=function(nom)
{
	$.post(
			'./sources/PHP/actionneur.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addGroupeCompetences",
				nom:nom
			},
			recoitValideRecharge,	//Fonction callback
			"text"	//Type de réponse
	);
}



//AJOUTE COMPETENCES
ouvreBoiteAddCompetence=function(groupe,i)
{
	$( "#dialog-addCompetence .dialog-addCompetence_nomGroupe").text(groupe);
	$( "#dialog-addCompetence-idGroupe").val(i);
	$( "#dialog-addCompetence").dialog( "open" );
}

addCompetence=function(nom,idGroupe)
{
	$.post(
			'./sources/PHP/actionneur.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addCompetence",
				nom:nom,
				idGroupe:idGroupe
			},
			recoitValideRecharge,	//Fonction callback
			"text"	//Type de réponse
	);
}

//Fonction qui lier ou délie une classe avec un indicateur
function lierDelierIndicateurClasse(ind,classe,lier)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"lierDelierIndicateurClasse",
				indicateur:ind,
				classe:classe,
				lier:lier
			},
			lierDelierIndicateurClasse_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}

function lierDelierIndicateurClasse_callback(reponse)
{
	var styleClass="indicateur";
	if(!reponse.lier)
		styleClass+="_unselected";
	$("#indicateur_"+reponse.indicateur).attr('class',styleClass);

	//RAJOUTER POUR COLORER LES TITRES (COMPETENCES/GROUPES...)

	afficheMessage(reponse.messageRetour);
}

// ****************************************************
// ADMINISTRATION NOTATION
// ****************************************************


//AJOUTE UN INDICATEUR
ouvreBoiteAddIndicateur=function(competence,i)
{
	$( "#dialog-addIndicateur .dialog-addCompetence_nomCompetence").text(competence);
	$( "#dialog-addIndicateur-idCompetence").val(i);
	$( "#dialog-addIndicateur").dialog("open");
}

addIndicateur=function(nom,details,niveaux,idCompetence)
{
	$.post(
			'./sources/PHP/actionneur.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addIndicateur",
				nom:nom,
				details:details,
				niveaux:niveaux,
				idCompetence:idCompetence
			},
			recoitValideRecharge,	//Fonction callback
			"text"	//Type de réponse
	);
}

//METTRE A JOUR LISTE ELEVES (notation)
//Recupere la liste (ajax)
NotationGetListeEleves=function(classe)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"getListeEleves",
				classe:classe//$("#notationListeClasses").val()
			},
			updateListeEleves,	//Fonction callback
			"json"	//Type de réponse
	);
}


updateListeEleves=function(reponse)
{
	//$("#notationListeEleves").html(reponse);
	$("#notationListeEleves").empty();
	var listeEleves=reponse.listeEleves;
	a=reponse;
	for(var idEleve in listeEleves)
	{
		var eleve=listeEleves[idEleve]
		$("#notationListeEleves").append("<option value='"+eleve.id+"'>"+eleve.nom+" "+eleve.prenom+"</option>");
	}

	getNotationEleve($("#notationListeEleves").val());
}

//METTRE A JOUR LA NOTATION POUR UN ELEVE

getNotationEleve=function(eleve)
{
	$.post(
			'./sources/PHP/actionneur.php',//Requete appelée
			{	//Les données à passer par POST
				action:"getNotationEleves",
				eleve:eleve//$("#notationListeEleves").val()
			},
			updateNotationEleve,	//Fonction callback
			"text"	//Type de réponse
	);
}

//Met à jour l'affichge des notes d'un élève (recu par ajax)
updateNotationEleve=function(reponse)
{
	$("#RecapNotationEleve").html(reponse);
}


//Envoie une nouvelle note au serveur
donneNote=function(note,eleve,indicateur)
{
	$.post(
			'./sources/PHP/actionneur.php',//Requete appelée
			{	//Les données à passer par POST
				action:"newNote",
				eleve:eleve,
				note:note,
				indicateur:indicateur
			},
			valideNouvelleNote,	//Fonction callback
			"text"	//Type de réponse
	);
}


//Met à jour l'affichge des notes d'un élève (recu par ajax)
valideNouvelleNote=function(reponse)
{
	getNotationEleve();
}




// ====================================================
// EDITION DES COMPETENCES
// ====================================================


