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


//Met à jour l'affichge des notes d'un élève (recu par ajax)
valideNouvelleNote=function(reponse)
{
	a=reponse;
	afficheMessage(reponse.messageRetour);

	//MAJ de l'arc en ciel
	var html_barre_arc_en_ciel=NOTATION_getNiveauxIndicateur(reponse.note.max,reponse.note.niveauMax,reponse.note.idIndicateur,true);
	$("#NOTATION_indicateur_"+reponse.note.idIndicateur+" .niveauxIndicateur").html(html_barre_arc_en_ciel);

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


 
