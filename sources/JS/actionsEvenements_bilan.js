// ****************************************************
// ÉVÉNEMENTS - NOTATION
// ****************************************************



// ****************************************************
// BOITES
// ****************************************************


function ouvreFermeBilanGroupe(id)
{
	var groupe=$("#NOTATION_groupe_"+id);
	//groupe.find('.listeIndicateurs').slideToggle('easings');
	if(groupe.find('.groupe_contenu').is(":hidden"))
		groupe.find('.listeIndicateurs').show();
	groupe.find('.groupe_contenu').slideToggle('easings');
}

// ****************************************************
// UPDATE NOTES
// ****************************************************

//METTRE A JOUR LISTE ELEVES (notation) ****************************
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
	afficheBarreChargement();
}

//Callback
updateListeEleves=function(reponse)
{
	cacheBarreChargement();
	$("#notationListeEleves").empty();
	var listeEleves=reponse.listeEleves;
	a=reponse;
	for(var idEleve in listeEleves)
	{
		var eleve=listeEleves[idEleve]
		$("#notationListeEleves").append("<option value='"+eleve.id+"'>"+eleve.nom+" "+eleve.prenom+"</option>");
	}

	$("#notationFormulaireListesClasseEtEleves #notationListeEleves").data("selectBox-selectBoxIt").refresh();//Mise a jour SelectBoxIT
	
	NOTATION_REDESSINE_DE_ZERO=true;	//Pour effacer puis tout redessiner
	getNotationEleve($("#notationListeEleves").val());
}


//Passage à l'éleve suivant ***************************
bilanBoutonEleveSuivant=function()
{
	$('#notationListeEleves').data("selectBox-selectBoxIt").moveDown();
}



//METTRE A JOUR LA NOTATION POUR UN ELEVE**********************
getNotationEleve=function(eleve,contexte)
{
	contexte=(typeof contexte !== 'undefined') ? contexte : "ALL_CONTEXTE";
	eleve=(typeof eleve !== 'undefined') ? eleve : ID_COURANT;

	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"getNotationEleves",
				eleve:eleve,
				contexte:contexte
			},
			updateNotationEleve,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}

//Met à jour l'affichge des notes d'un élève (recu par ajax)
//Y compris le graphique
updateNotationEleve=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	//VARIABLES GLOABLES !!
	numeroCompetence=0;
	numeroIndicateur=0;
	var sommeNiveaux=0;		//Somme des niveaux (~notes) de l'eleve pour chaque critere de ce domaine
	var sommeNiveauxMax=0;	//Somme des niveaux maxi atteignables



	if(NOTATION_REDESSINE_DE_ZERO)
	{
		$("#RecapNotationEleve").empty();
	}

	var listeGroupes=reponse.listeGroupes;

	AAA = reponse;
	//Preparation pour le graphique bilan
	if(STATUT=="admin" || STATUT=="evaluateur")
	{
		var listeEvaluationsDomaines=Array();//Liste des evaluations (en pourcentage)
		var listeLabelsDomaines=Array();//Liste des evaluations (en pourcentage)
		var listeIdsDomaines=Array();//Liste des evaluations (en pourcentage)
		$("#dialog_graphique_toile_competences_conteneur").empty();
	}

	var i=0;

	for(idGr in listeGroupes)
	{
		var groupe=listeGroupes[idGr];
		var evaluation=NOTATION_ajouteGroupeCompetences(groupe,"#RecapNotationEleve","RecapNote",NOTATION_REDESSINE_DE_ZERO,getCouleurGraphique(i));
		sommeNiveaux+=evaluation.niveau;
		sommeNiveauxMax+=evaluation.niveauMax;

		
		if(STATUT=="admin" || STATUT=="evaluateur")
		{
			listeEvaluationsDomaines.push(evaluation.niveau/evaluation.niveauMax*100);	
			listeLabelsDomaines.push(groupe.nom);
			listeIdsDomaines.push(groupe.id);
		}
		i+=1;
	}
	

	//Gestion des graphiques

	if(STATUT=="admin" || STATUT=="evaluateur")
	{
		$("#dialiog_graphique_camembert_domaines_conteneur").empty();//On enleve l'ancien graphique
		$("#dialiog_graphique_camembert_domaines_conteneur").append("<canvas id=\"dialiog_graphique_camembert_domaines\" width=\"400\" height=\"400\"></canvas>");
		traceGraphiqueRecap_Domaine("#dialiog_graphique_camembert_domaines",listeEvaluationsDomaines,listeLabelsDomaines,listeIdsDomaines);//On ajoute le nouveau

		$("#bouton_bilan_graphe_icone").empty();//On enleve l'ancien graphique
		$("#bouton_bilan_graphe_icone").append("<canvas id=\"bouton_bilan_graphe_icone\" width=\"130\" height=\"130\"></canvas>");//On enleve l'ancien graphique	
		traceGraphiqueRecap_Domaine("#bouton_bilan_graphe_icone canvas",listeEvaluationsDomaines,listeLabelsDomaines,listeIdsDomaines,false);//On le met aussi en icone
	}

	//Mise à jour de l'affichage du pourcentage
	if(STATUT=="admin" || STATUT=="evaluateur")
		$("#BILAN_pourcentage").text("(Total : "+parseInt(sommeNiveaux/sommeNiveauxMax*100)+"%)");

	
	
	NOTATION_REDESSINE_DE_ZERO=true;// =false //Si on change d'élève, on ne redessine pas tout (avant c'était à False, mais plus maintenant)
}



// ****************************************************
// NOTATION
//********************************************************

//Envoie une nouvelle note au serveur
donneNote=function(note,eleve,indicateur)
{
	$("#NOTATION_indicateur_"+indicateur+" .niveauxIndicateur .indicateurAllumeModifiable[data-valeur='"+note+"'] .indicateur_initiales_note").css("display","none");
	$("#NOTATION_indicateur_"+indicateur+" .niveauxIndicateur .indicateurAllumeModifiable[data-valeur='"+note+"'] .indicateur_chargement_note").css("display","inline");
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"newNote",
				eleve:eleve,
				note:note,
				indicateur:indicateur
			},
			valideNouvelleNote,	//Fonction callback
			"json"	//Type de réponse
	);
	afficheBarreChargement();
}


//Met à jour l'affichge des notes d'un élève (recu par ajax) **********************
valideNouvelleNote=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);

	//MAJ de l'arc en ciel
			//Selection du type de note (on va regarder dans le dropdown sur menu bilan)
			var noteEleve=0;
			if($("#bilanTypeEvaluation").val()=="last")	noteEleve=parseInt(reponse.note.last);
			if($("#bilanTypeEvaluation").val()=="max")	noteEleve=parseInt(reponse.note.max);
			if($("#bilanTypeEvaluation").val()=="avg")	noteEleve=parseInt(reponse.note.moy);

	var html_barre_arc_en_ciel=NOTATION_getNiveauxIndicateur(noteEleve,reponse.note.niveauMax,reponse.note.idIndicateur,true);
	$("#NOTATION_indicateur_"+reponse.note.idIndicateur+" .niveauxIndicateur").html(html_barre_arc_en_ciel);


	//Ouverture des commentaires
	if(AUTORISE_CONTEXT || AUTORISE_COMMENTAIRES) //Si les commentaires sont autorisés
	{
		var idIndicateur=reponse.note.idIndicateur;
		var idEval=reponse.notation.id;
		valideAllCommentaireEval();//Valide et ferme tous les commentaires encore ouverts
		bilanOuvreCommentaire(idIndicateur,idEval);//Ouverture de la ligne de commentaire
	}


	//Ajout dans l'historique
	var contenu="\n"+
"						<div id=\"historique_"+reponse.notation.id+"\" class=\"element_historique\">"+
"							<div class=\"id_historique\"><div>"+reponse.notation.id+"</div></div>"+
"							<div class=\"eleve_historique\">"+reponse.notation.prenomEleve+" "+reponse.notation.nomEleve+"</div>"+
"							<div class=\"prof_date_historique\">(<img style=\"height:20px; vertical-align:middle;\" src=\"./sources/images/teaching.png\"/><span class=\"prof_historique\">"+reponse.notation.prenomProf+" "+reponse.notation.nomProf+"</span><br/><span class=\"date_historique\">"+reponse.notation.date+"</span>)</div>"+
"							<div class=\"note_historique\">Éval.: <strong><span>"+reponse.notation.note+"</span>/"+reponse.notation.niveaux+"</strong></div>"+
"							<div class=\"intitule_historique\">\""+reponse.notation.nomIndicateur+"\"</div>"+
"							<div class=\"menu_historique\">"+
"								<img src=\"./sources/images/icone-modif.png\" alt=\"[Modif]\" title=\"Modifier l'évaluation\" onclick=\"ouvreBoite_modifNotation("+reponse.notation.id+",$('#historique_"+reponse.notation.id+" .note_historique strong span').text(),"+reponse.notation.niveaux+");\"/>"+
"								<img src=\"./sources/images/poubelle.png\" alt=\"[Suppr]\" title=\"Supprimer l'évaluation\" onclick=\"ouvreBoite_supprimeNotation("+reponse.notation.id+")\"/>"+
"							</div>"+
"						</div>";
	$("#liste_historique").prepend(contenu);
}


/* *****************************
COMMENTAIRES
*********************************** */

//Fonction qui envoie les commentaires d'une évaluation fraichement donnée ********************
valideCommentaireEval = function(idInd)
{

	var idEval=parseInt($("#NOTATION_indicateur_"+idInd).find(".commentaireIndicateur").find("form").attr('data-ideval'));
	var contexte=parseInt($("#NOTATION_indicateur_"+String(idInd)+" .commentaireIndicateur_contexte").val());
	//$("#NOTATION_indicateur_"+idInd).find(".commentaireIndicateur").find(".commentaireIndicateur-contexte").val());
	var commentaire=$("#NOTATION_indicateur_"+idInd).find(".commentaireIndicateur").find(".commentaireIndicateur-commentaire").val();

	
	//On enregistre le contexte dans un coin, en vue de le reproposer automatiquement après
	//DERNIER_CONTEXT=contexte; obsolete
	
	//Mise a jour de la liste d'autocompletion des contextes
	//ajouteListeContextSiAbsent(contexte); Obsolète


	$.post(
		'./sources/PHP/actionneurJSON.php',//Requete appelée
		{	//Les données à passer par POST
			action:"addCommentaireEval",
			idEval:idEval,
			idContexte:contexte,
			commentaire:commentaire
		},
		valideCommentaireEval_callback,	//Fonction callback
		"json"	//Type de réponse
	);
	afficheBarreChargement();
}


//Fonction qui valide tous les commentaires actuellement ouverts
valideAllCommentaireEval=function()
{
	var listeCommentaires=$(".intituleIndicateur .commentaireIndicateur").filter(function(){return $(this).css("display") === 'block';})
	listeCommentaires.each(function(){	//Pour chaque commentaire encore ouvert,
				idCommentaire=parseInt($(this).parent().parent().attr("data-id"));//On récupere le num id du critere
				valideCommentaireEval(idCommentaire);//on valide le commentaire
			})
}

valideCommentaireEval_callback=function(reponse)
{
	cacheBarreChargement();
	afficheMessage(reponse.messageRetour);
	var idIndicateur = reponse.evaluation.indicateur;//Recupere le numero de l'indicateur
	if(reponse.commentaire.commentaire!="")
		$("#NOTATION_indicateur_"+idIndicateur+" .boutonCommentaires").css("visibility","visible");//Affiche la bulle, si elle n'est pas visible
	bilanFermeCommentaire(idIndicateur);
}
 




//Ouvre la boite pour LIRE les commentaires =====================
ouvreBoiteCommentairesBilan=function(idInd)
{
	idEleve=parseInt($("#notationListeEleves").val());//On prend le n° de l'eleve choisi dans la liste
	if(idEleve==undefined)idEleve=-1;	//S'il n'y a pas de liste c'est que c'est l'eleve. Dans ce cas, on ne passe pas le n°Id (-1 par defaut)

	$("#dialog-commentaireEvaluation .commentairesListeContextes").empty();
	$("#dialog-commentaireEvaluation .commentairesListeContextes").css("display","none");
	$("#dialog-commentaireEvaluation p").css("display","block");
	$("#dialog-commentaireEvaluation").dialog( "open" );
	$.post(
		'./sources/PHP/actionneurJSON.php',//Requete appelée
		{	//Les données à passer par POST
			action:"getComments",
			idInd:idInd,
			idEleve:idEleve
		},
		updateBoiteCommentBilan_callback,	//Fonction callback
		"json"	//Type de réponse
	);
	afficheBarreChargement();
}

//Fonction callback qui affiche les commentaires dans la boite de commentaire.==============
updateBoiteCommentBilan_callback=function(reponse)
{
	cacheBarreChargement();
	var commentaires=reponse['commentaires'];
	for(var contexte in commentaires)
	{
		console.log(contexte);
		if(contexte!=0)//S'il y a un context
		{
			var nomContexte = LISTE_CONTEXTES[contexte].nom;
			$("#dialog-commentaireEvaluation .commentairesListeContextes").append("<div class=\"commentairesContexte\">"+nomContexte+"</div>");//On affiche le titre. (sinon, non)
		}
		var comSTR="<div class=\"commentairesListeCommentaires\">";//Liste des commentaires pour un contexte donné
		for(var idCom in commentaires[contexte])
		{
			comSTR+="<div class=\"commentairesCom\"><span class=\"commentairesDate\">["+commentaires[contexte][idCom]['date']+"]</span> "+commentaires[contexte][idCom]['texte']+"</div>";
		}
		comSTR+="</div>";
			$("#dialog-commentaireEvaluation .commentairesListeContextes").append(comSTR);
	}
	$("#dialog-commentaireEvaluation p").css("display","none");
	$("#dialog-commentaireEvaluation .commentairesListeContextes").css("display","block");
}

//Fonction qui referme la boite de commentaires =====================
fermerFenetreCommenairesBilan=function()
{
	$("#dialog-commentaireEvaluation").dialog( "close" );
	$("#dialog-commentaireEvaluation .commentairesListeContextes").empty();
	$("#dialog-commentaireEvaluation .commentairesListeContextes").css("display","none");
	$("#dialog-commentaireEvaluation p").css("display","block");
}





