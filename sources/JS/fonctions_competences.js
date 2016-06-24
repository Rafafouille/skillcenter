


//Fonction qui affiche les groupes de compétences ******************************
function ADMIN_COMPETENCES_ajouteGroupe(groupe,conteneur)
{
	//Choix du style (si sélectionné ou pas)
	var styleClass="groupe_competences";
	if(!groupe.selected)
		styleClass+="_unselected";

	var rendu=""+
"			<div class=\""+styleClass+"\" id=\"ADMIN_COMPETENCES_groupe_"+groupe.id+"\">"+
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
		ADMIN_COMPETENCES_ajouteCompetence(competence,"#ADMIN_COMPETENCES_groupe_"+groupe.id+" .groupe_contenu");
	}
}



//Fonction qui ajoute une competence dans un groupe ******************************************
function ADMIN_COMPETENCES_ajouteCompetence(competence,conteneur)
{
	numeroCompetence++;	//Globale
	numeroIndicateur=0;	//Globale

	//Choix du style (si sélectionné ou pas)
	var styleClass="competence";
	if(!competence.selected)
		styleClass+="_unselected";


	var rendu=""+
"					<div class=\""+styleClass+"\" id=\"ADMIN_COMPETENCES_competence_"+competence.id+"\">"+
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
		ADMIN_COMPETENCES_ajouteIndicateur(indicateur,"#ADMIN_COMPETENCES_competence_"+competence.id+" .listeIndicateurs table");
	}
}

//Fonction qui ajoute une indicateur dans une compétence *************************
function ADMIN_COMPETENCES_ajouteIndicateur(indicateur,conteneur)
{
	numeroIndicateur++;

	//Choix du style (si sélectionné ou pas)
	var styleClass="indicateur";
	if(!indicateur.selected)
		styleClass+="_unselected";


	var rendu=""+
"								<tr id=\"ADMIN_COMPETENCES_indicateur_"+indicateur.id+"\" class=\""+styleClass+"\">"+
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
"									"+ADMIN_COMPETENCES_getNiveauxIndicateur(indicateur.niveaux,NB_NIVEAUX_MAX)
"									</td>"+
"								</tr>";
	$(conteneur).append(rendu);
}


//Fonction qui crée la grille arc en ciel
//Full : gere si les couleurs vont de rouge à vert (false) (cas de l'admin competences)
// ou si vont de rouge à ..... la note en cours (cas de la notation - true).
function ADMIN_COMPETENCES_getNiveauxIndicateur(val,maxi, full)
{
	valeurVerte=val;
	if(full)
		valeurVerte=maxi;

	var rendu="";
	for(var i=0;i<=maxi;i++)
	{
		if(i<=val)
		{
			rendu+=""+
"										<div class=\"indicateurAllume\" style=\"background-color:"+setArcEnCiel(i,valeurVerte)+";\">"+i+"</div>";
		}
		else
		{
			rendu+=""+
"										<div class=\"indicateurEteint\">"+i+"</div>";
		}
	}
	return rendu;
}
