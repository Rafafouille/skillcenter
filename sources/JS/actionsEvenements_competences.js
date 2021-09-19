// ****************************************************
//ÉVÉNEMENTS - ADMINISTRATION COMPETENCES
// ****************************************************




// MAINTENANCE
// ****************************************************
//NETTOYAGE DE LA BASE DE DONNEEs *********************************
nettoyerLaBase=function()
{
		$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"nettoyerLaBase",
				nettoyer_supprimer_notes_fantomes:$("#nettoyer_supprimer_notes_fantomes").prop("checked"),
				nettoyer_supprimer_notes_sans_critere:$("#nettoyer_supprimer_notes_sans_critere").prop("checked"),
				nettoyer_depasse_critere_max:$("#nettoyer_depasse_critere_max").prop("checked"),
				nettoyer_depasse_critere_max_option:$("#nettoyer_depasse_critere_max_option").val(),
				nettoyer_supprimer_comp_orphelins:$("#nettoyer_supprimer_comp_orphelins").prop("checked"),
				nettoyer_supprimer_comp_orphelins_et_ses_criteres:$("#nettoyer_supprimer_comp_orphelins_et_ses_criteres").prop("checked"),
				nettoyer_supprimer_notes_criteres_orphelins:$("#nettoyer_supprimer_criteres_orphelins").prop("checked"),
				nettoyer_reordonner:$("#nettoyer_reordonner").prop("checked")
			},
			nettoyerLaBase_Callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}
//********************************************
nettoyerLaBase_Callback=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	
	texte="<p>Aucune modification n'a été apporté à la base de données.</p>"
	
	if(reponse.bilan_nettoyage.modif_apportee)//reponse.bilan_nettoyage.notes_supprimees.plus_user+reponse.bilan_nettoyage.notes_supprimees.plus_indicateur+reponse.bilan_nettoyage.comp_supprimees+reponse.bilan_nettoyage.ind_supprimees)
	{
		var texte="<p>Lors du nettoyage de la base, les opérations suivantes ont été faites :</p>\n<ul>";
		if((reponse.bilan_nettoyage.notes_supprimees.plus_user+reponse.bilan_nettoyage.notes_supprimees.plus_indicateur))
		{
			texte+="<li><strong style='color:red;'>"+(reponse.bilan_nettoyage.notes_supprimees.plus_user+reponse.bilan_nettoyage.notes_supprimees.plus_indicateur)+"</strong> évaluation(s) ont été supprimées (";
			if(reponse.bilan_nettoyage.notes_supprimees.plus_user)
				{texte+=reponse.bilan_nettoyage.notes_supprimees.plus_user+" suppression(s) car l'utilisateur associé a été supprimé";
				if(reponse.bilan_nettoyage.notes_supprimees.plus_indicateur)
					texte+=" ; ";
				else
					texte+=".";
				}
			if(reponse.bilan_nettoyage.notes_supprimees.plus_indicateur)
				texte+=reponse.bilan_nettoyage.notes_supprimees.plus_indicateur+" suppression(s) car le critère associé n'existe plus.";;
			texte+=")</li>"
			
		}
		
		if((reponse.bilan_nettoyage.criteres_depasse.sup+reponse.bilan_nettoyage.criteres_depasse.inf))
		{
			texte+="<li><strong style='color:red;'>"+(reponse.bilan_nettoyage.criteres_depasse.sup+reponse.bilan_nettoyage.criteres_depasse.inf)+"</strong> indicateur(s) étaient en dehors des bornes d'évaluation (";
			if(reponse.bilan_nettoyage.criteres_depasse.sup)
			{
				texte+=reponse.bilan_nettoyage.criteres_depasse.sup+" au dessus de la limite";
				if(reponse.bilan_nettoyage.criteres_depasse.inf)
					texte+=" ; ";
				else
					texte+=".";
			}
			if(reponse.bilan_nettoyage.criteres_depasse.inf)
			{
				texte+=reponse.bilan_nettoyage.criteres_depasse.inf+" en dessous de zéro.";
			}
			texte+="). ";
			if($("#nettoyer_depasse_critere_max_option").val()=="depasse_critere_max_tronque")
				texte+="Elles ont été tronquées."
			else
				texte+="Elles ont été supprimées."
			texte+="</li>";
		}
		
		if(reponse.bilan_nettoyage.comp_supprimees)
			texte+="<li><strong style='color:red;'>"+reponse.bilan_nettoyage.comp_supprimees+"</strong> compétence(s) 'orphelines' (n'appartenant à aucun groupe) ont été supprimées.</li>";
		
		if(reponse.bilan_nettoyage.ind_supprimees)
			texte+="<li><strong style='color:red;'>"+reponse.bilan_nettoyage.ind_supprimees+"</strong> indicateurs orphelins (n'appartenant à aucune compétence) ont été supprimés.</li>";
		
		if($("#nettoyer_reordonner").prop("checked"))
			texte+="<li>Les numérotations des indicateurs, compétences et domaines ont été refaites (en interne dans la BDD).</li>";

		texte+="</ul>";
	}
	$("#dialog-nettoyerLaBaseCallBack").html(texte);
}









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
	afficheBarreChargement();
}

//UDAPTE COMPETENCES PAR CLASSE (CALLBACK) --------------------
updateCompetencesSelonClasse_Callback=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	//VARIABLES GLOABLES !!
	numeroCompetence=0;
	numeroIndicateur=0;

	$("#liste_competences").empty();
	var listeGroupes = Array();
	for(idGr in reponse.listeGroupes)//On copie dans un tableau normal
		listeGroupes.push(reponse.listeGroupes[idGr])
	trieCompetencesParPosition(listeGroupes)// On trie par ordre de position
	for(idGr in listeGroupes)
	//listeGroupes.forEach(function(groupe)
	{
		var groupe=listeGroupes[idGr];
		ADMIN_COMPETENCES_ajouteGroupe(groupe,"#liste_competences","adminComp");
	};
}




//AJOUTE GROUPE COMPETENCES --------------------------
addGroupeCompetences=function(nom,pos)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addGroupeCompetences",
				position:pos,
				nom:nom
			},
			addGroupeCompetences_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}
//Callback qui ajoute le groupe dans la page ---------------
addGroupeCompetences_callback=function(reponse)
{
	cacheBarreChargement();
	var groupe=reponse.groupe;
	afficheMessage(reponse.messageRetour);
	var rendu=ADMIN_COMPETENCES_rendu_HTML_groupe(groupe.nom,groupe.id,"groupe_competences_unselected",groupe.position);
	$("#liste_competences").append(rendu);
}

//BOITE MODIF DOMAINE -----------------------------
ouvreBoiteModifDomaine=function(idDomaine)
{
	//Intitule
	var intitule=$("#ADMIN_COMPETENCES_groupe_"+idDomaine).find(".ADMIN_PARAMETRES_titre_domaine_dans_h3").text();
	$("#dialog-modifDomaine-nom").val(intitule);
	//idDomaine
	var idDomaine=$("#ADMIN_COMPETENCES_groupe_"+idDomaine).attr("data-id");
	var posDomaine = $("#ADMIN_COMPETENCES_groupe_"+idDomaine).attr("data-position");
	$("#dialog-modifDomaine").attr("data-iddomaine",idDomaine)
	$("#dialog-modifDomaine-position").val(posDomaine);
	
	$("#dialog-modifDomaine").dialog("open" );
}

//MODIF DOMAINE --------------------------
modifDomaine=function(nom,idDomaine,posDomaine)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"modifDomaine",
				nom:nom,
				idDomaine:idDomaine,
				posDomaine:posDomaine
			},
			modifDomaine_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}
//Callback qui ajoute le groupe dans la page ---------------
modifDomaine_callback=function(reponse)
{
	cacheBarreChargement();
	var domaine=reponse.domaine;
	afficheMessage(reponse.messageRetour);
	
	var classe=$("#selectClasseCompetences").val();
	updateCompetencesSelonClasse(classe);
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de nouvelle indicateur
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
	afficheBarreChargement();
}

//Callback qui supprime le domaine dans la page -----
supprimeDomaine_callback=function(reponse)
{
	cacheBarreChargement();
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
addCompetence=function(nom,nomAbrege,idGroupe)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addCompetence",
				nom:nom,
				nomAbrege:nomAbrege,
				idGroupe:idGroupe
			},
			addCompetence_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}

//Callback qui ajoute la compétence dans la page -----
addCompetence_callback=function(reponse)
{
	cacheBarreChargement();
	var comp=reponse.competence;
	afficheMessage(reponse.messageRetour);
	var rendu=ADMIN_COMPETENCES_rendu_HTML_competence(comp.nom,comp.nomAbrege,comp.id,0,"competence_unselected");
	$("#ADMIN_COMPETENCES_groupe_"+comp.groupe+" .groupe_contenu").append(rendu);
}



//BOITE MODIF COMPETENCES -----------------------------
ouvreBoiteModifCompetence=function(idCompetence)
{
	//Intitule
	var intitule=$("#ADMIN_COMPETENCES_competence_"+idCompetence).find(".ADMIN_PARAMETRES_titre_competence_dans_h3").text();
	$("#dialog-modifCompetence-nom").val(intitule);
	//Nom abregé
	nomAbrege=$("#ADMIN_COMPETENCES_competence_"+idCompetence).attr("data-nomAbrege");
	
	$("#dialog-modifCompetence-nomAbrege").val(nomAbrege);
	//idCompetence
	var idCompetence=$("#ADMIN_COMPETENCES_competence_"+idCompetence).attr("data-id");
	$("#dialog-modifCompetence").attr("data-idcompetence",idCompetence)
	//idDomaine
	var idDomaine=$("#ADMIN_COMPETENCES_competence_"+idCompetence).parent().parent().data("id");
	getDomainesInFormulaireOption("#dialog-modifCompetence-idDomaine",idDomaine);
	
	var posCompetence = $("#ADMIN_COMPETENCES_competence_"+idCompetence).attr("data-position");
	$("#dialog-modifCompetence-position").val(posCompetence);

	$("#dialog-modifCompetence").dialog( "open" );
}

//MODIF COMPETENCE -----------------------------
modifCompetence=function(nom,nomAbrege,idDomaine,idCompetence,position)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"modifCompetence",
				nom:nom,
				nomAbrege:nomAbrege,
				idDomaine:idDomaine,
				position:position,
				idCompetence:idCompetence
			},
			modifCompetence_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}
//Callback qui ajoute la compétence dans la page -----
modifCompetence_callback=function(reponse)
{
	cacheBarreChargement();
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
	afficheBarreChargement();
}
//Callback qui supprime la competence dans la page -----
supprimeCompetence_callback=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	var id=reponse.competence.id;
	
	$("#ADMIN_COMPETENCES_competence_"+id).remove();
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de suppression d'indicateur
}






//AJOUTE UN CRITERE  -------------------
ouvreBoiteAddIndicateur=function(competence,idComp)
{
	//Efface
	$("#dialog-addIndicateur-nom").val("");
	$("#dialog-addIndicateur-details").val("");
	$("#dialog-addIndicateur-lien").val("");
	$("#dialog-addIndicateur-position").val(parseInt($("#dialog-addIndicateur-position").val() || 0)+1);	//Le || Permet de transformer NaN en 0



	//$("#dialog-addIndicateur").dialog('option', 'title', 'Ajouter un critère');
	$( "#dialog-addIndicateur .dialog-addIndicateur_nomCompetence").text(competence);
	$( "#dialog-addIndicateur-idCompetence").val(idComp);
	//$( "#dialog-addIndicateur-idIndicateur").val(0);
	$( "#dialog-addIndicateur").dialog("open");
}

addIndicateur=function(nom,details,niveaux,idCompetence,classe,lien,position)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"addIndicateur",
				nom:nom,
				details:details,
				niveaux:niveaux,
				idCompetence:idCompetence,
				lien:lien,
				position:position,
				classe: classe//Pour lier tout de suite la classe au nouvel indicateur
			},
			addIndicateur_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}
//Callback qui ajoute l'indicateur dans la page -----
addIndicateur_callback=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	var indicateur=reponse.indicateur;
	var rendu=ADMIN_COMPETENCES_rendu_HTML_indicateur(indicateur,$("#ADMIN_COMPETENCES_competence_"+String(indicateur.competence)).data("position"),indicateur.position,"indicateur");
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
	//Lien
	var lien=$("#ADMIN_COMPETENCES_indicateur_"+idCrit).data("lien");
	$("#dialog-modifIndicateur-lien").val(lien);
	//idCritere à modifier
	$("#dialog-modifIndicateur").data("id_critere",idCrit)
	
	var position = $("#ADMIN_COMPETENCES_indicateur_"+idCrit).data("position");
	$("#dialog-modifIndicateur-position").val(position);

	$("#dialog-modifIndicateur").dialog("open");
}

modifCritere=function(nom,details,niveaux,idCompetence,idCritere,lien,position)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"modifCritere",
				nom:nom,
				details:details,
				niveaux:niveaux,
				idCompetence:idCompetence,
				lien:lien,
				position:position,
				idCritere:idCritere
			},
			modifCritere_callback,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}
//Callback qui ajoute l'indicateur dans la page -----
modifCritere_callback=function(reponse)
{
	cacheBarreChargement();
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
	afficheBarreChargement();
}
//Callback qui supprime l'indicateur dans la page -----
supprimeIndicateur_callback=function(reponse)
{
	cacheBarreChargement();
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
	afficheBarreChargement();
	PARAMETRES_update_selected_unselected_domaines_competences();//Met à jour le style des éléments sélectionné (ou pas)
}

function lierDelierIndicateurClasse_callback(reponse)
{
	cacheBarreChargement();
	var styleClass="indicateur";
	if(!reponse.lier)
		styleClass+="_unselected";
	$("#ADMIN_COMPETENCES_indicateur_"+reponse.indicateur).attr('class',styleClass);

	//RAJOUTER POUR COLORER LES TITRES (COMPETENCES/GROUPES...)

	afficheMessage(reponse.messageRetour);
}
 

 

