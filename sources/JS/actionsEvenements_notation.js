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

	$("#notationFormulaireListesClasseEtEleves #notationListeEleves").data("selectBox-selectBoxIt").refresh();//Mise a jour SelectBoxIT
	
	NOTATION_REDESSINE_DE_ZERO=true;	//Pour effacer puis tout redessiner
	getNotationEleve($("#notationListeEleves").val());
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
}


 
