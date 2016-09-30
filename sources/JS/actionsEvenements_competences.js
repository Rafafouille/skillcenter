// ****************************************************
//ÉVÉNEMENTS - ADMINISTRATION COMPETENCES
// ****************************************************





// AFFICHAGE GROUPE / COMPETENCES / INDICATEURS
// ****************************************************



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




//AJOUTE GROUPE COMPETENCES --------------------------
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
//Callback qui ajoute le groupe dans la page ---------------
addGroupeCompetences_callback=function(reponse)
{
	var groupe=reponse.groupe;
	afficheMessage(reponse.messageRetour);
	var rendu=ADMIN_COMPETENCES_rendu_HTML_groupe(groupe.nom,groupe.id,"groupe_competences_unselected");
	$("#liste_competences").append(rendu);
}


//SUPPRIMER UN DOMAINE DE COMPETENCE  -------------------
ouvreBoiteSupprimeDomaine=function(nomDomaine,i)
{
	$( "#dialog-supprimeDomaine .dialog-supprimeDomaine_nomDomaine").text(nomDomaine);
	$( "#dialog-supprimeDomaine-idDomaine").val(i);
	$( "#dialog-supprimeDomaine").dialog("open");
}

supprimeDomaine=function(idDomaine,supprimeCompetencesInternes,supprimeIndicateursInternes)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"supprimeDomaine",
				idDomaine:idDomaine,
				supprimeCompetences:supprimeCompetencesInternes,
				supprimeCriteres:supprimeIndicateursInternes
			},
			supprimeDomaine_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}

//Callback qui supprime le domaine dans la page -----
supprimeDomaine_callback=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	var id=reponse.domaine.id;
	
	$("#ADMIN_COMPETENCES_groupe_"+id).remove();
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de suppression d'indicateur
}


//BOITE AJOUTE COMPETENCES -----------------------------
ouvreBoiteAddCompetence=function(groupe,i)
{
	$( "#dialog-addCompetence .dialog-addCompetence_nomGroupe").text(groupe);
	$( "#dialog-addCompetence-idGroupe").val(i);
	$( "#dialog-addCompetence").dialog( "open" );
}

//AJOUTE COMPETENCE -----------------------------
addCompetence=function(nom,idGroupe)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addCompetence",
				nom:nom,
				idGroupe:idGroupe
			},
			addCompetence_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}
//Callback qui ajoute la compétence dans la page -----
addCompetence_callback=function(reponse)
{
	var comp=reponse.competence;
	afficheMessage(reponse.messageRetour);
	var rendu=ADMIN_COMPETENCES_rendu_HTML_competence(comp.nom,comp.id,0,"competence_unselected");
	$("#ADMIN_COMPETENCES_groupe_"+comp.groupe+" .groupe_contenu").append(rendu);
}



//BOITE MODIF COMPETENCES -----------------------------
ouvreBoiteModifCompetence=function(idCompetence)
{
	//Intitule
	var intitule=$("#ADMIN_COMPETENCES_competence_"+idCompetence).find(".ADMIN_PARAMETRES_titre_competence_dans_h3").text();
	$("#dialog-modifCompetence-nom").val(intitule);
	//idCompetence
	var idCompetence=$("#ADMIN_COMPETENCES_competence_"+idCompetence).attr("data-id");
	$("#dialog-modifCompetence").attr("data-idcompetence",idCompetence)
	//idDomaine
	var idDomaine=$("#ADMIN_COMPETENCES_competence_"+idCompetence).parent().parent().data("id");
	getDomainesInFormulaireOption("#dialog-modifCompetence-idDomaine",idDomaine);
	//$("#dialog-modifCompetence-idDomaine").val(idDomaine);

	$("#dialog-modifCompetence").dialog( "open" );
}

//MODIF COMPETENCE -----------------------------
modifCompetence=function(nom,idDomaine,idCompetence)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"modifCompetence",
				nom:nom,
				idDomaine:idDomaine,
				idCompetence:idCompetence
			},
			modifCompetence_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}
//Callback qui ajoute la compétence dans la page -----
modifCompetence_callback=function(reponse)
{
	var comp=reponse.competence;
	afficheMessage(reponse.messageRetour);
	//Recharge la page de parametrage
	var classe=$("#selectClasseCompetences").val();
	updateCompetencesSelonClasse(classe);
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de nouvelle indicateur
}




//SUPPRIMER UNE COMPETENCE  -------------------
ouvreBoiteSupprimeCompetence=function(nomCompetence,i)
{
	$( "#dialog-supprimeCompetence .dialog-supprimeCompetence_nomCompetence").text(nomCompetence);
	$( "#dialog-supprimeCompetence-idCompetence").val(i);
	$( "#dialog-supprimeCompetence").dialog("open");
}


supprimeCompetence=function(idCompetence,supprimeIndicateursInternes)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"supprimeCompetence",
				idCompetence:idCompetence,
				supprimeIndicateur:supprimeIndicateursInternes
			},
			supprimeCompetence_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}
//Callback qui supprime la competence dans la page -----
supprimeCompetence_callback=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	var id=reponse.competence.id;
	
	$("#ADMIN_COMPETENCES_competence_"+id).remove();
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de suppression d'indicateur
}






//AJOUTE UN CRITERE  -------------------
ouvreBoiteAddIndicateur=function(competence,idComp)
{
	//$("#dialog-addIndicateur").dialog('option', 'title', 'Ajouter un critère');
	$( "#dialog-addIndicateur .dialog-addIndicateur_nomCompetence").text(competence);
	$( "#dialog-addIndicateur-idCompetence").val(idComp);
	//$( "#dialog-addIndicateur-idIndicateur").val(0);
	$( "#dialog-addIndicateur").dialog("open");
}

addIndicateur=function(nom,details,niveaux,idCompetence,classe)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addIndicateur",
				nom:nom,
				details:details,
				niveaux:niveaux,
				idCompetence:idCompetence,
				classe: classe//Pour lier tout de suite la classe au nouvel indicateur
			},
			addIndicateur_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}
//Callback qui ajoute l'indicateur dans la page -----
addIndicateur_callback=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	var indicateur=reponse.indicateur;
	var rendu=ADMIN_COMPETENCES_rendu_HTML_indicateur(indicateur,0,0,"indicateur");
	$("#ADMIN_COMPETENCES_competence_"+indicateur.competence+" .listeIndicateurs .indicateurs").append(rendu);
	
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de nouvelle indicateur
}

//MODIFIE UN CRITERE  -------------------
ouvreBoiteModifCritere=function(idCrit)
{
	//Option pour les competences
	var idCompetenceParent=$("#ADMIN_COMPETENCES_indicateur_"+idCrit).parent().parent().parent().data("id");
	getCompetencesInFormulaireOption("#dialog-modifIndicateur-idCompetence",idCompetenceParent);
	//Nom
	var nom=$("#ADMIN_COMPETENCES_indicateur_"+idCrit).find(".ADMIN_PARAMETRES_titre_critere").text();
	$("#dialog-modifIndicateur-nom").val(nom);
	//Details
	var detail=$("#ADMIN_COMPETENCES_indicateur_"+idCrit).find(".icone-info").attr("title");
	$("#dialog-modifIndicateur-details").val(detail);
	//Niveau
	var niveau=$("#ADMIN_COMPETENCES_indicateur_"+idCrit).find(".niveauxIndicateur").data("niveau");
	$("#dialog-modifIndicateur-niveaux").val(niveau);
	//idCritere à modifier
	$("#dialog-modifIndicateur").data("id_critere",idCrit)

	$("#dialog-modifIndicateur").dialog("open");
}

modifCritere=function(nom,details,niveaux,idCompetence,idCritere)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"modifCritere",
				nom:nom,
				details:details,
				niveaux:niveaux,
				idCompetence:idCompetence,
				idCritere:idCritere
			},
			modifCritere_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}
//Callback qui ajoute l'indicateur dans la page -----
modifCritere_callback=function(reponse)
{
	afficheMessage(reponse.messageRetour);	
	//Recharge la page de parametrage
	var classe=$("#selectClasseCompetences").val();
	updateCompetencesSelonClasse(classe);
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de nouvelle indicateur
}



 //SUPPRIME UN INDICATEUR - BOITE-------------------------------------------
ouvreBoiteSupprimeIndicateur=function(nomIndicateur,i)
{
	$( "#dialog-supprimeIndicateur .dialog-supprimeIndicateur_nomIndicateur").text(nomIndicateur);
	$( "#dialog-supprimeIndicateur-idIndicateur").val(i);
	$( "#dialog-supprimeIndicateur").dialog("open");
}

//SUPPRIMER UN INDICATEUR  -------------------
supprimeIndicateur=function(idIndicateur)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"supprimeIndicateur",
				idIndicateur:idIndicateur
			},
			supprimeIndicateur_callback,	//Fonction callback
			"json"	//Type de réponse
	);
}
//Callback qui supprime l'indicateur dans la page -----
supprimeIndicateur_callback=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	var id=reponse.indicateur.id;
	
	$("#ADMIN_COMPETENCES_indicateur_"+id).remove();
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de suppression d'indicateur
	
}





// PARAMETRE DES COMPETENCES 
//============================================

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
 

 

