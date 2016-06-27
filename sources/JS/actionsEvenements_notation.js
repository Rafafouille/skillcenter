// ****************************************************
// ÉVÉNEMENTS - NOTATION
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

	$("#RecapNotationEleve").empty();
	var listeGroupes=reponse.listeGroupes;
	for(idGr in listeGroupes)
	{
		var groupe=listeGroupes[idGr];
		NOTATION_ajouteGroupeCompetences(groupe,"#RecapNotationEleve","RecapNote");
	}
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


 
