// ****************************************************
// ÉVÉNEMENTS - NOTATION
// ****************************************************




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

//Callback
updateListeEleves=function(reponse)
{
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
getNotationEleve=function(eleve)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"getNotationEleves",
				eleve:eleve//$("#notationListeEleves").val()
			},
			updateNotationEleve,	//Fonction callback
			"json"	//Type de réponse
	);
}

//Met à jour l'affichge des notes d'un élève (recu par ajax)
updateNotationEleve=function(reponse)
{

	afficheMessage(reponse.messageRetour);
	//VARIABLES GLOABLES !!
	numeroCompetence=0;
	numeroIndicateur=0;

	if(NOTATION_REDESSINE_DE_ZERO)
		$("#RecapNotationEleve").empty();

	var listeGroupes=reponse.listeGroupes;

	for(idGr in listeGroupes)
	{
		var groupe=listeGroupes[idGr];
		NOTATION_ajouteGroupeCompetences(groupe,"#RecapNotationEleve","RecapNote",NOTATION_REDESSINE_DE_ZERO);
	}
	NOTATION_REDESSINE_DE_ZERO=false;//Si on change d'élève, on ne redessine pas tout
}


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
}


//Met à jour l'affichge des notes d'un élève (recu par ajax) **********************
valideNouvelleNote=function(reponse)
{
	afficheMessage(reponse.messageRetour);

	//MAJ de l'arc en ciel
	var html_barre_arc_en_ciel=NOTATION_getNiveauxIndicateur(reponse.note.max,reponse.note.niveauMax,reponse.note.idIndicateur,true);
	$("#NOTATION_indicateur_"+reponse.note.idIndicateur+" .niveauxIndicateur").html(html_barre_arc_en_ciel);


	//Ouverture des commentaires
	if(AUTORISE_CONTEXT || AUTORISE_COMMENTAIRES) //Si les commentaires sont autorisés
	{
		var idIndicateur=reponse.note.idIndicateur;
		var idEval=reponse.notation.id;
		$(".commentaireIndicateur:visible").each(function(index,element)//Fermeture des bilan deja ouverts
					{var i=parseInt($(this).parent().parent().attr("data-id"));
					bilanFermeCommentaire(i)})
		bilanOuvreCommentaire(idIndicateur,idEval);//Ouverture
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



//Fonction qui envoie les commentaires d'une évaluation fraichement donnée ********************
valideCommentaireEval=function(idInd)
{

	var idEval=parseInt($("#NOTATION_indicateur_"+idInd).find(".commentaireIndicateur").find("form").attr('data-ideval'));
	var contexte=$("#NOTATION_indicateur_"+idInd).find(".commentaireIndicateur").find(".commentaireIndicateur-contexte").val();
	var commentaire=$("#NOTATION_indicateur_"+idInd).find(".commentaireIndicateur").find(".commentaireIndicateur-commentaire").val();

	
	//Mise a jour de la liste d'autocompletion des contextes
	ajouteListeContextSiAbsent(contexte);


	$.post(
		'./sources/PHP/actionneurJSON.php',//Requete appelée
		{	//Les données à passer par POST
			action:"addCommentaireEval",
			idEval:idEval,
			contexte:contexte,
			commentaire:commentaire
		},
		valideCommentaireEval_callback,	//Fonction callback
		"json"	//Type de réponse
	);
}

valideCommentaireEval_callback=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	var idIndicateur=reponse.evaluation.indicateur;//Recupere le numero de l'indicateur
	if(reponse.commentaire.commentaire!="")
		$("#NOTATION_indicateur_"+idIndicateur+" .boutonCommentaires").css("visibility","visible");//Affiche la bulle, si elle n'est pas visible
	bilanFermeCommentaire(idIndicateur);

}
 



/* *****************************
COMMENTAIRES
*********************************** */

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
}

//Fonction callback qui affiche les commentaires dans la boite de commentaire.==============
updateBoiteCommentBilan_callback=function(reponse)
{
	var commentaires=reponse['commentaires'];
	for(var context in commentaires)
	{
		if(context!="")//S'il y a un context
			$("#dialog-commentaireEvaluation .commentairesListeContextes").append("<div class=\"commentairesContexte\">"+context+"</div>");//On affiche le titre. (sinon, non)
		var comSTR="<div class=\"commentairesListeCommentaires\">";//Liste des commentaires pour un contexte donné
		for(var idCom in commentaires[context])
		{
			comSTR+="<div class=\"commentairesCom\"><span class=\"commentairesDate\">["+commentaires[context][idCom]['date']+"]</span> "+commentaires[context][idCom]['texte']+"</div>";
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
