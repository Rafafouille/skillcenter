// ****************************************************
// ÉVÉNEMENTS PAR DEFAUT...
// ****************************************************


// ****************************************************
// TABS (onglets)
// ****************************************************

// Fonction appelée quand on change de Tab
onChangeTab=function(event,ui)
{

	var iOnglet=$('#tab-onglets').tabs('option', 'active');

	//1er affichage de l'onglet "Notation"
	if(listeOnglets[iOnglet]=="Évaluation" && !NOTATION_LOADED)//Si la page "notation" n'a jamais été chargée
	{
		NOTATION_LOADED=true;
		if(STATUT=="admin")	//Si c'est un prof qui est connecté
		{
			var classe=$("#notationListeClasses").val();
			NotationGetListeEleves(classe);
		}
		else	// Si c'est un élève qui est connecté
		{
			getNotationEleve(ID_COURANT);
		}
	}
	if(listeOnglets[iOnglet]=="Compétences" && !ADMIN_COMPETENCES_LOADED)//Si la page 3 n'a jamais été chargée
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


