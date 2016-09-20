


//Fonction qui affiche les groupes de compétences ******************************
function ADMIN_COMPETENCES_ajouteGroupe(groupe,conteneur)
{
	//Choix du style (si sélectionné ou pas)
	var styleClass="groupe_competences";
	if(!groupe.selected)
		styleClass+="_unselected";

	var rendu=ADMIN_COMPETENCES_rendu_HTML_groupe(groupe.nom,groupe.id,styleClass);

	$(conteneur).append(rendu);

	//Ajout des competences
	for(idComp in groupe.listeCompetences)
	{
		var competence=groupe.listeCompetences[idComp];
		ADMIN_COMPETENCES_ajouteCompetence(competence,"#ADMIN_COMPETENCES_groupe_"+groupe.id+" .groupe_contenu");
	}
}

// -------------------------
function ADMIN_COMPETENCES_rendu_HTML_groupe(nom,id,styleClass)
{
	return ""+
"			<div class=\""+styleClass+"\" id=\"ADMIN_COMPETENCES_groupe_"+id+"\">"+
"				<div class=\"entete_groupe_competences\">"+
"						<img class=\"boutonSupprimerDomaine\" src=\"./sources/images/poubelle.png\" alt=\"[X]\" onclick=\"ouvreBoiteSupprimeDomaine('"+addslashes(nom)+"',"+id+")\"/>"+
"					<div class=\"boutonAjouteCompetence\" onclick=\"ouvreBoiteAddCompetence('"+addslashes(nom)+"',"+id+");$(this).parent().parent().find('.groupe_contenu').slideDown('easings');\">"+
"						<img src=\"./sources/images/icone-plus.png\" alt=\"[+]\"/>"+
"						Ajouter une compétence"+
"					</div>"+
"					<h3 onclick=\"$(this).parent().parent().find('.groupe_contenu').slideToggle('easings');\">"+
"						"+nom+
"					</h3>"+
"				</div>"+
"				<div class=\"groupe_contenu\">"+
"				</div>"+
"			</div>";
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
toto=competence;
	

	var rendu=ADMIN_COMPETENCES_rendu_HTML_competence(competence.nom,competence.id,numeroCompetence,styleClass)

	$(conteneur).append(rendu);


	//Ajout des indicateurs
	for(idInd in competence.listeIndicateurs)
	{
		var indicateur=competence.listeIndicateurs[idInd];
		ADMIN_COMPETENCES_ajouteIndicateur(indicateur,"#ADMIN_COMPETENCES_competence_"+competence.id+" .listeIndicateurs table");
	}
}


// -------------------------
function ADMIN_COMPETENCES_rendu_HTML_competence(nom,id,numeroCompetence,styleClass)
{
	return ""+
"					<div class=\""+styleClass+"\" id=\"ADMIN_COMPETENCES_competence_"+id+"\">"+
"						<img class=\"boutonSupprimerCompetence\" src=\"./sources/images/poubelle.png\" alt=\"[X]\" onclick=\"ouvreBoiteSupprimeCompetence('"+addslashes(nom)+"',"+id+")\"/>"+
"						<div class=\"boutonAjouterIndicateur\" onclick=\"ouvreBoiteAddIndicateur('"+addslashes(nom)+"',"+id+");$(this).parent().find('.listeIndicateurs').slideDown('easings');\">"+
"							<img src=\"./sources/images/icone-plus.png\" alt=\"[+]\"/>"+
"							Ajouter un critère"+
"						</div>"+
"						<h3 onclick=\"$(this).parent().find('.listeIndicateurs').slideToggle('easings');\">"+
"							"+numeroCompetence+" - "+nom+
"						</h3>"+
"						<div class=\"listeIndicateurs\">"+
"							<table class=\"indicateurs\">"+
"							</table>"+
"						</div>"+
"					</div>";
}




//Fonction qui ajoute une indicateur dans une compétence *************************
function ADMIN_COMPETENCES_ajouteIndicateur(indicateur,conteneur)
{
	numeroIndicateur++;

	//Choix du style (si sélectionné ou pas)
	var styleClass="indicateur";
	if(!indicateur.selected)
		styleClass+="_unselected";


	var rendu = ADMIN_COMPETENCES_rendu_HTML_indicateur(indicateur,numeroCompetence,numeroIndicateur,styleClass);
	
	$(conteneur).append(rendu);
}




// -------------------------
function ADMIN_COMPETENCES_rendu_HTML_indicateur(indicateur,numeroCompetence,numeroIndicateur,styleClass)
{
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
"										<img src=\"./sources/images/poubelle.png\" alt=\"[X]\" style=\"cursor:pointer;\" title=\"Supprimer le critère\" onclick=\"ouvreBoiteSupprimeIndicateur('"+indicateur.nom+"',"+indicateur.id+")\"/>"+
""+//"										<img src=\"./sources/images/icone-modif.png\" alt=\"[§]\" style=\"cursor:pointer;height:25px;\" title=\"Modifier le critère\" onclick=\"ouvreBoiteModifCritere("+indicateur.id+")\"/>"+
"									</td>"+
"									<td class=\"niveauxIndicateur\">"+
"									"+ADMIN_COMPETENCES_getNiveauxIndicateur(indicateur.niveaux,NB_NIVEAUX_MAX)
"									</td>"+
"								</tr>";
return rendu;
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
"										<div class=\"indicateurAllume\" style=\"background-color:"+setArcEnCiel(i,valeurVerte)+";\">"+intitule_critere(i,val)+"</div>";
		}
		else
		{
			rendu+=""+
"										<div class=\"indicateurEteint\">"+intitule_critere(i,val)+"</div>";
		}
	}
	return rendu;
}
