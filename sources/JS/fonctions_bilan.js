


//Fonction qui affiche les groupes de compétences ******************************
//groupe : Groupe a ajouter (objet JSON)
//conteneur : conteneur (HTML) dans lequel ajouter le groupe
//modeNotation : true si c'est un prof (qui note), false si c'est un élève (qui consulte)
//couleur = couleur pour les graphes
function NOTATION_ajouteGroupeCompetences(groupe,conteneur,modeNotation,recreeDeZero,couleur)
{
	//Paramètres par défaut (ancien)
	var recreeDeZero = typeof recreeDeZero !== 'undefined' ? recreeDeZero : false;
	var sommeNiveaux=0;		//Somme des niveaux (~notes) de l'eleve pour chaque critere de ce domaine
	var sommeNiveauxMax=0;	//Somme des niveaux maxi atteignables
	couleur=(typeof couleur=="undefined"?"black":couleur);

	//Si le groupe n'existe pas (ou si il faut recréer de zéro) --> on le crée
	if(recreeDeZero || !$("#NOTATION_groupe_"+groupe.id).length)
	{
		var rendu=""+
"			<div class=\"groupe_competences\" id=\"NOTATION_groupe_"+groupe.id+"\">"+
"				<div class=\"entete_groupe_competences\" onclick=\"ouvreFermeBilanGroupe("+groupe.id+");\">"+//$(this).parent().find('.groupe_contenu').slideToggle('easings');$(this).parent().find('.listeIndicateurs').slideToggle('easings');
"					<h3>"+
"						"+String(romanize(groupe.position))+" - "+groupe.nom+
"					</h3>"+
"				</div>"+
"				<div class=\"groupe_contenu\">"+
"				</div>"+
"			</div>";

		$(conteneur).append(rendu);
	}

	//Tableau pour les graphiques du bilan
	if(STATUT=="admin" || STATUT=="evaluateur")
	{
		var listeEvaluationsCompetences=Array();//Liste des evaluations (en pourcentage)
		var listeLabelsCompetences=Array();//Liste des evaluations (en pourcentage)
	}



	// Remettre les competences dans l'ordre
	var listeComp = Array();
	for(idComp in groupe.listeCompetences)//On copie dans un tableau normal
		listeComp.push(groupe.listeCompetences[idComp])
	trieCompetencesParPosition(listeComp)// On trie par ordre de position

	//Ajout des competences
	for(idComp in listeComp)
	{
		var competence=listeComp[idComp];
		var evaluation=NOTATION_ajouteCompetence(competence,"#NOTATION_groupe_"+groupe.id+" .groupe_contenu",modeNotation,recreeDeZero);$
		sommeNiveaux+=evaluation.niveau;
		sommeNiveauxMax+=evaluation.niveauMax;
		
		if(STATUT=="admin" || STATUT=="evaluateur")
		{
			listeEvaluationsCompetences.push(evaluation.niveau/evaluation.niveauMax*100);
			listeLabelsCompetences.push(competence.nomAbrege);
		}
	}
	
	//Graphe bilan
	if(STATUT=="admin" || STATUT=="evaluateur")
	{
		$("#dialog_graphique_toile_competences_conteneur").append("<div id=\"BILAN_graphe_comptence_"+groupe.id+"\" class=\"BILAN_graphe_comptence\"><canvas width=\"300\" height=\"200\"></canvas></div>")
		traceGraphiqueRecap_Competence("#BILAN_graphe_comptence_"+groupe.id+" canvas",listeEvaluationsCompetences,listeLabelsCompetences,groupe.nom,couleur);
	}

	//Renvoie la valeur de l'evaluation et le nombre de niveaux max
	return {niveau:sommeNiveaux,niveauMax:sommeNiveauxMax};
}



//Fonction qui ajoute une competence dans un groupe
//Renvoie la valeur de la somme des evaluation et la somme de niveaux max
function NOTATION_ajouteCompetence(competence,conteneur,modeNotation,recreeDeZero)
{
	//Paramètres par défaut (ancien)
	var recreeDeZero = typeof recreeDeZero !== 'undefined' ? recreeDeZero : false;

	var sommeNiveaux=0;		//Somme des niveaux (~notes) de l'eleve pour chaque critere de cette competence
	var sommeNiveauxMax=0;	//Somme des niveaux maxi atteignables
	
	numeroCompetence++;	//Globale
	numeroIndicateur=0;	//Globale

	//Si la compétence n'existe pas (ou si il faut recréer de zéro) --> on la crée
	if(recreeDeZero || !$("#NOTATION_competence_"+competence.id).length)
	{
		var rendu=""+
"					<div class=\"competence\" id=\"NOTATION_competence_"+competence.id+"\">"+
"						<h3 onclick=\"$(this).parent().find('.listeIndicateurs').slideToggle('easings');\">"+
"							"+String(competence.position)+" - "+competence.nom+
"						</h3>"+
"						<div class=\"listeIndicateurs\">"+
"							<table class=\"indicateurs\">"+
"							</table>"+
"						</div>"+
"					</div>";
		$(conteneur).append(rendu);
	}

	//Ajout des indicateurs
	
	// Remettre les indicateurs dans l'ordre
	var listeInd = Array();
	for(idInd in competence.listeIndicateurs)//On copie dans un tableau normal
		listeInd.push(competence.listeIndicateurs[idInd])
	trieCompetencesParPosition(listeInd)// On trie par ordre de position

	
	
	for(idInd in listeInd)
	{
		var indicateur=listeInd[idInd];
		var evaluation=NOTATION_ajouteIndicateur(indicateur,"#NOTATION_competence_"+competence.id+" .listeIndicateurs table",competence);

		sommeNiveaux+=evaluation.niveau;
		sommeNiveauxMax+=evaluation.niveauMax;

	}
	
	//Renvoie la valeur de l'evaluation et le nombre de niveaux max
	return {niveau:sommeNiveaux,niveauMax:sommeNiveauxMax};
}

//Fonction qui ajoute une indicateur dans une compétence
//Renvoie la valeur de l'evaluation et le nombre de niveaux max
function NOTATION_ajouteIndicateur(indicateur,conteneur,competence)
{
	numeroIndicateur++;


	//Selection du type de note (on va regarder dans le dropdown sur menu bilan)
	var noteEleve=0;
	if($("#bilanTypeEvaluation").val()=="last")	noteEleve=parseInt(indicateur.niveauEleveLast);
	if($("#bilanTypeEvaluation").val()=="max")	noteEleve=parseInt(indicateur.niveauEleveMax);
	if($("#bilanTypeEvaluation").val()=="avg")	noteEleve=parseInt(indicateur.niveauEleveMoy);

	var rendu=""+
"								<tr class=\"indicateur\" id=\"NOTATION_indicateur_"+indicateur.id+"\" data-id=\""+indicateur.id+"\">"+
"									<td class=\"intituleIndicateur\">"+
"										<div class=\"titreIndicateur\"  nowrap=\"nowrap\">"+
"											"+String(competence.position)+"."+String(indicateur.position)+" - "+indicateur.nom+
"										</div>"+
"										<div class=\"commentaireIndicateur\">"+
"											<form data-ideval=\"0\">"+
"												<img class=\"boutonValideCommentaireEval\" alt=\"[V]\" src=\"./sources/images/valide.png\" onclick=\"valideCommentaireEval("+indicateur.id+");\"/>"+
"												<img class=\"boutonAnnuleCommentaireEval\" alt=\"[X]\" src=\"./sources/images/invalide.png\" onclick=\"bilanFermeCommentaire("+indicateur.id+");\"/>";
	if(AUTORISE_CONTEXT)
	{
		rendu += "												<select name=\"commentaireIndicateur_contexte\" class=\"commentaireIndicateur_contexte\">"+
			"												    <option value=\"0\">Pas de contexte</option>";
		rendu += "												</select>";
//		<input list=\"listeContexteAutocompletion\" type=\"text\" class=\"commentaireIndicateur-contexte\" name=\"commentaireIndicateur-contexte\" placeholder=\"Contexte (ex : TP1)\" size=\"15\"/>":"");
	}
	if(AUTORISE_COMMENTAIRES)
	{
		rendu += "												<input type=\"text\" class=\"commentaireIndicateur-commentaire"+(!AUTORISE_COMMENTAIRES?"-invisible":"")+"\" name=\"commentaireIndicateur-commentaire\" placeholder=\"Commentaire (ex : N'a pas posé les hypothèses)\" size=\"38\"/>";
	}
	
	rendu += ""+
"											</form>"+
"										</div>"+
"									</td>"+
"									<td class=\"boutonsIndicateur\">"+
"										<img class=\"boutonCommentaires\" src=\"./sources/images/icone-comment.png\" alt=\"[c]\" style=\""+(indicateur.commentaires==""?"visibility:hidden;":"")+"cursor:pointer;\" title=\"Commentaires d'évaluation\" onclick=\"ouvreBoiteCommentairesBilan("+indicateur.id+")\"/>";

	if(indicateur.lien=="")	{rendu+=""+
"										<img style=\"visibility:hidden\" src=\"./sources/images/icone-internet.png\"/>";}
		else				{rendu+=""+
"										<a href=\""+indicateur.lien+"\" onclick=\"window.open(this.href);return false;\"><img src=\"./sources/images/icone-internet.png\" alt=\"[i]\"  style=\"cursor:pointer;\" title=\"Lien vers ressources : "+indicateur.lien+"\"/></a>";}
	if(indicateur.details==""){rendu+=""+
	"									<img style=\"visibility:hidden;\" src=\"./sources/images/icone-info.png\"/>";}
		else				{rendu+=""+
		"								<img src=\"./sources/images/icone-info.png\" alt=\"[i]\"  style=\"cursor:help;\" title=\""+indicateur.details+"\"/>";}
	rendu+=""+
"									</td>"+
"									<td class=\"niveauxIndicateur\">"+
"									"+NOTATION_getNiveauxIndicateur(noteEleve,indicateur.niveauMax,indicateur.id,STATUT=="admin" || STATUT=="evaluateur" || STATUT=="autoeval",false)
"									</td>"+
"								</tr>";

	//Si l'indicateur existe deja, on le remplace
	if($("#NOTATION_indicateur_"+indicateur.id).length)
		$("#NOTATION_indicateur_"+indicateur.id).replaceWith(rendu);
	else	//Sinon on l'ajoute
		$(conteneur).append(rendu);

		
	/*a=indicateur.niveauEleveMax;
	console.log((a+Math.abs(a)));
	console.log(((a+Math.abs(a))*0.5)+" - "+parseInt(indicateur.niveauMax));*/
		
	//Renvoie la valeur de l'evaluation et le nombre de niveaux max
	//return {niveau:parseInt((parseInt(indicateur.niveauEleveMax)+Math.abs(parseInt(indicateur.niveauEleveMax)))*0.5),niveauMax:parseInt(indicateur.niveauMax)};
	//return {niveau:parseInt((parseInt(indicateur.niveauEleveMax)+Math.abs(parseInt(indicateur.niveauEleveMax)))*0.5),niveauMax:parseInt(indicateur.niveauMax)};

	//On tronque les notes négatives
	return {niveau:noteEleve<0?0:noteEleve,niveauMax:parseInt(indicateur.niveauMax)};
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
		
		
		var alpha=0.1+(i==val)*0.9;//Transparence des lettres
		
		
		if(i<=val)//Attention ! cela inclut le zero (d'ou le -1 par defaut s'il n'y a pas de note)
		{
			var cl="indicateurAllume";
			if(clickable)
				cl+="Modifiable";
			if(degrade)
				var couleur=setArcEnCiel(i,maxi);
			else
				var couleur=setArcEnCiel(val,maxi);
			
			rendu+=""+
"										<div class=\""+cl+"\" data-valeur=\""+i+"\" style=\"background-color:"+couleur+";\" onclick=\""+actionOnClick+"\" ><span class=\"indicateur_initiales_note\" style=\"color:rgba(0,0,0,"+alpha+");\">"+intitule_critere(i,maxi)+"</span><span class=\"indicateur_chargement_note\"><img src=\"./sources/images/chargement.gif\" alt=\"&#8987;\"/></span></div>";
		}
		else
		{
			var cl="indicateurEteint";
			if(clickable)
				cl+="Modifiable";
			rendu+=""+
"										<div class=\""+cl+"\" data-valeur=\""+i+"\" onclick=\""+actionOnClick+"\"><span style=\"color:rgba(0,0,0,"+alpha+");\">"+intitule_critere(i,maxi)+"</span></div>";
		}
	}
	return rendu;
}



// Fonction qui ouvre la ligne de commentaire
bilanOuvreCommentaire=function(idInd,idEval,commentaire="",contexte=0)
{
	contexte = parseInt(contexte);
	if(contexte==0)	// Si pas de contexte de proposé, on part sur celui qui est sélectionné dans le menu
		contexte = parseInt($("#BILAN_listeContextes").val());
	
	if(AUTORISE_CONTEXT || AUTORISE_COMMENTAIRES)
	{
		//Update idEval (id de l'évaluation, et non pas de l'indicateur !!!)
			$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur form").attr("data-ideval",idEval);
		
		//MAJ des contextes
			var identif_select_contexte = "#NOTATION_indicateur_"+String(idInd)+" .commentaireIndicateur_contexte";
			$(identif_select_contexte).empty();
			if(contexte)//On met en priorité l'élément qui est sélectionné (non nul)
			{
				$(identif_select_contexte).append("<option value=\"0\">Pas de contexte</option>");
				$(identif_select_contexte).append("<option value=\""+String(contexte)+"\" selected=\"selected\">"+$("#BILAN_listeContextes option[value=\""+String(contexte)+"\"]").text()+"</option>");
			}//Sinon, juste le "pas de contexte", sélectionné
			else
				$(identif_select_contexte).append("<option value=\"0\" selected=\"selected\">Pas de contexte</option>");
			//Les autres lignes
			LISTE_CONTEXTES.forEach(function(cont){
				if( cont.id != contexte)
					$(identif_select_contexte).append("<option value=\""+String(cont.id)+"\">"+cont.nom+"</option>");
			})

		// MAJ des commentaires
			var identif_select_commentaire = "#NOTATION_indicateur_"+String(idInd)+" .commentaireIndicateur-commentaire";
			$(identif_select_commentaire).val(commentaire);
			
		//Animation
			$("#NOTATION_indicateur_"+idInd+" .titreIndicateur").hide("slide",{direction: "left" }, 500);
			setTimeout(function(){$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur").show("slide", { direction: "right" }, 500);},510);
	}
}

// Fonction qui ferme la ligne de commentaire
bilanFermeCommentaire=function(idInd)
{
	//Update idEval
	$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur form").attr("data-ideval",0);
	//Animation
	$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur").hide("slide", { direction: "left" }, 500);
	setTimeout(function(){	$("#NOTATION_indicateur_"+idInd+" .titreIndicateur").show("slide", { direction: "right" }, 500 );},510);
}


///fonction qui ferme tous les commentaires
fermeAllCommentaires=function()
{
		var listeCommentaires=$(".intituleIndicateur .commentaireIndicateur").filter(function(){return $(this).css("display") === 'block';})
		listeCommentaires.each(function(){	//Pour chaque commentaire encore ouvert,
				idCommentaire=parseInt($(this).parent().parent().attr("data-id"));//On récupere le num id du critere
				bilanFermeCommentaire(idCommentaire);//on valide le commentaire
								})
}



//Fonction qui vérivie si un contexte est déjà présent dans la data-liste contexte, et qui l'ajoute le cas échéant.
//Renvoie vrai si le contexte éxistait deja
// OBSOLETE depuis les nouveaux contextes
/*ajouteListeContextSiAbsent=function(contexte)
{
	if($("#listeContexteAutocompletion option[value='"+contexte+"']").size()==0)//Si le contexte n'a pas été ajouté à la liste...
	{
		$("#listeContexteAutocompletion").append("<option value=\""+contexte+"\">");//On le rajoute
		return false;
	}
	return true;
}*/

// Fonction qui crée la liste des contextes dans le menu des notations
/*updateListeContexteDansMenu=function()
{
	$("#BILAN_listeContextes").empty();
	$("#BILAN_listeContextes").append("<option value=\"ALL_CONTEXTE\">Tout contexte</option>");
	LISTE_CONTEXTES.forEach(function(contexte){
		$("#BILAN_listeContextes").append("<option value=\""+String(contexte.id)+"\">"+contexte.nom+"</option>");
	});
	$("#BILAN_listeContextes").data("selectBox-selectBoxIt").refresh();
}
*/



