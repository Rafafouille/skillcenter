// ****************************************************
//ÉVÉNEMENTS - ADMINISTRATION COMPETENCES
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



//UDAPTE COMPETENCES PAR CLASSE *********************************
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

//UDAPTE COMPETENCES PAR CLASSE (CALLBACK) --------------------
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
		ADMIN_COMPETENCES_ajouteGroupe(groupe,"#liste_competences","adminComp");
	}
}




//AJOUTE GROUPE COMPETENCES
addGroupeCompetences=function(nom)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addGroupeCompetences",
				nom:nom
			},
			addGroupeCompetences_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}
//Callback qui ajoute la compétence dans la page
addGroupeCompetences_callback=function(reponse)
{
	var groupe=reponse.groupe;
	afficheMessage(reponse.messageRetour);
	var rendu=ADMIN_COMPETENCES_rendu_HTML_groupe(groupe.nom,groupe.id,"groupe_competences_unselected");
	$("#liste_competences").append(rendu);
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

//Fonction qui lier ou délie une classe avec un indicateur *********************
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
	$("#ADMIN_COMPETENCES_indicateur_"+reponse.indicateur).attr('class',styleClass);

	//RAJOUTER POUR COLORER LES TITRES (COMPETENCES/GROUPES...)

	afficheMessage(reponse.messageRetour);
}
 
