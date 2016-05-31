//Fonction qui affiche les groupes de compétences
function ajouteGroupeCompetences(groupe,conteneur)
{
	//Choix du style (si sélectionné ou pas)
	var styleClass="groupe_competences";
	if(!groupe.selected)
		styleClass+="_unselected";

	var rendu=""+
"			<div class=\""+styleClass+"\" id=\"groupe_competence_"+groupe.id+"\">"+
"				<div class=\"entete_groupe_competences\">"+
"					<div class=\"boutonAjouteCompetence\" onclick=\"ouvreBoiteAddCompetence('"+groupe.nom+"',"+groupe.id+")\">"+
"						Ajouter une compétence"+
"					</div>"+
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
		ajouteCompetence(competence,"#groupe_competence_"+groupe.id+" .groupe_contenu");
	}
}



//Fonction qui ajoute une competence dans un groupe
function ajouteCompetence(competence,conteneur)
{
	numeroCompetence++;	//Globale
	numeroIndicateur=0;	//Globale

	//Choix du style (si sélectionné ou pas)
	var styleClass="competence";
	if(!competence.selected)
		styleClass+="_unselected";


	var rendu=""+
"					<div class=\""+styleClass+"\" id=\"competence_"+competence.id+"\">"+
"						<div class=\"boutonAjouterIndicateur\" onclick=\"ouvreBoiteAddIndicateur('"+competence.nom+"',"+competence.id+")\">"+
"							[+Indicateur]"+
"						</div>"+
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
		ajouteIndicateur(indicateur,"#competence_"+competence.id+" .listeIndicateurs table");
	}
}

//Fonction qui ajoute une indicateur dans une compétence
function ajouteIndicateur(indicateur,conteneur)
{
	numeroIndicateur++;

	//Choix du style (si sélectionné ou pas)
	var styleClass="indicateur";
	if(!indicateur.selected)
		styleClass+="_unselected";


	var rendu=""+
"								<tr id=\"indicateur_"+indicateur.id+"\" class=\""+styleClass+"\">"+
"									<td>"+
"										<form>"+
"											<input type=\"checkbox\" name=\"selectIndicateur"+indicateur.id+"\" value=\""+indicateur.id+"\"";
	if(indicateur.selected)
		rendu+=" checked";
	rendu+=" onChange=\"lierDelierIndicateurClasse("+indicateur.id+",$('#selectClasseCompetences').val(),$(this).is(':checked'))\">"+
"										</form>"+
"									</td>"+
"									<td class=\"intituleIndicateur\">"+
"										"+numeroCompetence+"."+numeroIndicateur+" - "+indicateur.nom+
"									</td>"+
"									<td class=\"detailIndicateur\">"+
"										<img src=\"./sources/images/icone-info.png\" alt=\"[i]\"  style=\"cursor:help;\" title=\""+indicateur.details+"\"/>"+
"										<img src=\"./sources/images/supprime.png\" alt=\"[X]\" style=\"cursor:not-allowed;\" title=\"Supprimer l'indicateur\"/>"+
"									</td>"+
"									<td class=\"niveauxIndicateur\">"+
"									"+getNiveauxIndicateur(indicateur.niveaux,NB_NIVEAUX_MAX)
"									</td>"+
"								</tr>";
	$(conteneur).append(rendu);
}


function getNiveauxIndicateur(val,maxi)
{
	var rendu="";
	for(var i=0;i<=maxi;i++)
	{
		if(i<=val)
		{
			rendu+=""+
"										<div class=\"indicateurAllume\" style=\"background-color:"+setArcEnCiel(i,val)+";\">"+i+"</div>";
		}
		else
		{
			rendu+=""+
"										<div class=\"indicateurEteint\">"+i+"</div>";
		}
	}
	return rendu;
}
