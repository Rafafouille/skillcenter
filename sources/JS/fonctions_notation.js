


//Fonction qui affiche les groupes de compétences ******************************
//groupe : Groupe a ajouter (objet JSON)
//conteneur : conteneur (HTML) dans lequel ajouter le groupe
//modeNotation : true si c'est un prof (qui note), false si c'est un élève (qui consulte)
function NOTATION_ajouteGroupeCompetences(groupe,conteneur,modeNotation,recreeDeZero)
{
	//Paramètres par défaut (ancien)
	var recreeDeZero = typeof recreeDeZero !== 'undefined' ? recreeDeZero : false;


	//Si le groupe n'existe pas (ou si il faut recréer de zéro) --> on le crée
	if(recreeDeZero || !$("#NOTATION_groupe_"+groupe.id).length)
	{
		var rendu=""+
"			<div class=\"groupe_competences\" id=\"NOTATION_groupe_"+groupe.id+"\">"+
"				<div class=\"entete_groupe_competences\" onclick=\"$(this).parent().find('.groupe_contenu').slideToggle('easings');$(this).parent().find('.listeIndicateurs').slideToggle('easings');\">"+
"					<h3>"+
"						"+groupe.nom+
"					</h3>"+
"				</div>"+
"				<div class=\"groupe_contenu\">"+
"				</div>"+
"			</div>";

		$(conteneur).append(rendu);
	}

	//Ajout des competences
	for(idComp in groupe.listeCompetences)
	{
		var competence=groupe.listeCompetences[idComp];
		NOTATION_ajouteCompetence(competence,"#NOTATION_groupe_"+groupe.id+" .groupe_contenu",modeNotation,recreeDeZero);
	}
}



//Fonction qui ajoute une competence dans un groupe
function NOTATION_ajouteCompetence(competence,conteneur,modeNotation,recreeDeZero)
{
	//Paramètres par défaut (ancien)
	var recreeDeZero = typeof recreeDeZero !== 'undefined' ? recreeDeZero : false;


	numeroCompetence++;	//Globale
	numeroIndicateur=0;	//Globale

	//Si la compétence n'existe pas (ou si il faut recréer de zéro) --> on la crée
	if(recreeDeZero || !$("#NOTATION_competence_"+competence.id).length)
	{
		var rendu=""+
"					<div class=\"competence\" id=\"NOTATION_competence_"+competence.id+"\">"+
"						<h3 onclick=\"$(this).parent().find('.listeIndicateurs').slideToggle('easings');\">"+
"							"+numeroCompetence+" - "+competence.nom+
"						</h3>"+
"						<div class=\"listeIndicateurs\">"+
"							<table class=\"indicateurs\">"+
"							</table>"+
"						</div>"+
"					</div>";
		$(conteneur).append(rendu);
	}

	//Ajout des indicateurs
	for(idInd in competence.listeIndicateurs)
	{
		var indicateur=competence.listeIndicateurs[idInd];
		NOTATION_ajouteIndicateur(indicateur,"#NOTATION_competence_"+competence.id+" .listeIndicateurs table");
	}
}

//Fonction qui ajoute une indicateur dans une compétence
function NOTATION_ajouteIndicateur(indicateur,conteneur)
{
	numeroIndicateur++;


	var rendu=""+
"								<tr class=\"indicateur\" id=\"NOTATION_indicateur_"+indicateur.id+"\" >"+
"									<td class=\"intituleIndicateur\">"+
"										"+numeroCompetence+"."+numeroIndicateur+" - "+indicateur.nom+
"									</td>"+
"									<td class=\"detailIndicateur\">"+
"										<img src=\"./sources/images/icone-info.png\" alt=\"[i]\"  style=\"cursor:help;\" title=\""+indicateur.details+"\"/>"+
"									</td>"+
"									<td class=\"niveauxIndicateur\">"+
"									"+NOTATION_getNiveauxIndicateur(indicateur.niveauEleveMax,indicateur.niveauMax,indicateur.id,STATUT=="admin" || STATUT=="evaluateur" || STATUT=="autoeval",false)
"									</td>"+
"								</tr>";

	//Si l'indicateur existe deja, on le remplace
	if($("#NOTATION_indicateur_"+indicateur.id).length)
		$("#NOTATION_indicateur_"+indicateur.id).replaceWith(rendu);
	else	//Sinon on l'ajoute
		$(conteneur).append(rendu);
}



//Fonction qui crée la grille arc en ciel
//Full : gere si les couleurs vont de rouge à vert (false) (cas de l'admin competences)
// ou si vont de rouge à ..... la note en cours (cas de la notation - true).
// degrade = true si couleur dégradée, ou false si toutes les cases prennent la couleur de la case maximum
function NOTATION_getNiveauxIndicateur(val,maxi,indicateur, clickable,degrade)
{
	//Paramètres par défaut (anciennes versions)
	var clickable = typeof clickable !== 'undefined' ? clickable : false;
	var degrade = typeof degrade !== 'undefined' ? degrade : false;


	var rendu="";
	for(var i=0;i<=maxi;i++)
	{
		
		//Ajout du javascript (onClick)
		var actionOnClick="";
		if(clickable)
		{
			//Choix de l'action a faire au moment du click (et choix de l'élève à noter)
			var idEleveStr="0";
			if(STATUT=="admin" || STATUT=="evaluateur")//Si on est admin/evaluateur...
				idEleveStr="$('#notationListeEleves').val()";//...le num de l'élève a noter sera cel
			if(STATUT=="autoeval")//Si c'est un auto-évaluateur
				idEleveStr=ID_COURANT;	//Par defaut on note celui qui
			actionOnClick="donneNote("+i+","+idEleveStr+","+indicateur+")";
		}
		
		if(i<=val)
		{
			var cl="indicateurAllume";
			if(clickable)
				cl+="Modifiable";
			if(degrade)
				var couleur=setArcEnCiel(i,maxi);
			else
				var couleur=setArcEnCiel(val,maxi);

			rendu+=""+
"										<div class=\""+cl+"\" style=\"background-color:"+couleur+";\" onclick=\""+actionOnClick+"\" >"+i+"</div>";
		}
		else
		{
			var cl="indicateurEteint";
			if(clickable)
				cl+="Modifiable";
			rendu+=""+
"										<div class=\""+cl+"\" onclick=\""+actionOnClick+"\">"+i+"</div>";
		}
	}
	return rendu;
}
