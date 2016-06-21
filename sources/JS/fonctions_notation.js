


//Fonction qui affiche les groupes de compétences ******************************
//groupe : Groupe a ajouter (objet JSON)
//conteneur : conteneur (HTML) dans lequel ajouter le groupe
//modeNotation : true si c'est un prof (qui note), false si c'est un élève (qui consulte)
function NOTATION_ajouteGroupeCompetences(groupe,conteneur,modeNotation)
{
	var rendu=""+
"			<div class=\"groupe_competences\" id=\"NOTATION_groupe_"+groupe.id+"\">"+
"				<div class=\"entete_groupe_competences\">"+
"					<h3 onclick=\"$(this).parent().parent().find('.groupe_contenu').toggle('easings');\">"+
"						"+groupe.nom+
"					</h3>"+
"				</div>"+
"				<div class=\"groupe_contenu\">"+
"				</div>"+
"			</div>";

	$(conteneur).append(rendu);

	//Ajout des competences
	for(idComp in groupe.listeCompetences)
	{
		var competence=groupe.listeCompetences[idComp];
		NOTATION_ajouteCompetence(competence,"#NOTATION_groupe_"+groupe.id+" .groupe_contenu",modeNotation);
	}
}



//Fonction qui ajoute une competence dans un groupe
function NOTATION_ajouteCompetence(competence,conteneur,modeNotation)
{
	numeroCompetence++;	//Globale
	numeroIndicateur=0;	//Globale

	var rendu=""+
"					<div class=\"competence\" id=\"NOTATION_competence_"+competence.id+"\">"+
"						<h3>"+
"							"+numeroCompetence+" - "+competence.nom+
"						</h3>"+
"						<div class=\"listeIndicateurs\">"+
"							<table class=\"indicateurs\">"+
"							</table>"+
"						</div>"+
"					</div>";
	$(conteneur).append(rendu);


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
"									"+NOTATION_getNiveauxIndicateur(indicateur.niveauEleveMax,indicateur.niveauMax,indicateur.id,true)
"									</td>"+
"								</tr>";
	$(conteneur).append(rendu);
}



//Fonction qui crée la grille arc en ciel
//Full : gere si les couleurs vont de rouge à vert (false) (cas de l'admin competences)
// ou si vont de rouge à ..... la note en cours (cas de la notation - true).
function NOTATION_getNiveauxIndicateur(val,maxi,indicateur, clickable=false)
{

	var rendu="";
	for(var i=0;i<=maxi;i++)
	{
		if(i<=val)
		{
			rendu+=""+
"										<div class=\"indicateurAllumeModifiable\" style=\"background-color:"+setArcEnCiel(i,maxi)+";\" onclick=\"donneNote("+i+",$('#notationListeEleves').val(),"+indicateur+")\" >"+i+"</div>";
		}
		else
		{
			rendu+=""+
"										<div class=\"indicateurEteintModifiable\" onclick=\"donneNote("+i+",$('#notationListeEleves').val(),"+indicateur+")\">"+i+"</div>";
		}
	}
	return rendu;
}
