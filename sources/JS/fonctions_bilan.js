


//Fonction qui affiche les groupes de compétences ******************************
//groupe : Groupe a ajouter (objet JSON)
//conteneur : conteneur (HTML) dans lequel ajouter le groupe
//modeNotation : true si c'est un prof (qui note), false si c'est un élève (qui consulte)
function NOTATION_ajouteGroupeCompetences(groupe,conteneur,modeNotation,recreeDeZero)
{
	//Paramètres par défaut (ancien)
	var recreeDeZero = typeof recreeDeZero !== 'undefined' ? recreeDeZero : false;
	var sommeNiveaux=0;		//Somme des niveaux (~notes) de l'eleve pour chaque critere de ce domaine
	var sommeNiveauxMax=0;	//Somme des niveaux maxi atteignables

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
		var evaluation=NOTATION_ajouteCompetence(competence,"#NOTATION_groupe_"+groupe.id+" .groupe_contenu",modeNotation,recreeDeZero);$
		sommeNiveaux+=evaluation.niveau;
		sommeNiveauxMax+=evaluation.niveauMax;
		
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
		var evaluation=NOTATION_ajouteIndicateur(indicateur,"#NOTATION_competence_"+competence.id+" .listeIndicateurs table");

		sommeNiveaux+=evaluation.niveau;
		sommeNiveauxMax+=evaluation.niveauMax;

	}
	
	//Renvoie la valeur de l'evaluation et le nombre de niveaux max
	return {niveau:sommeNiveaux,niveauMax:sommeNiveauxMax};
}

//Fonction qui ajoute une indicateur dans une compétence
//Renvoie la valeur de l'evaluation et le nombre de niveaux max
function NOTATION_ajouteIndicateur(indicateur,conteneur)
{
	numeroIndicateur++;


	var rendu=""+
"								<tr class=\"indicateur\" id=\"NOTATION_indicateur_"+indicateur.id+"\" data-id=\""+indicateur.id+"\">"+
"									<td class=\"intituleIndicateur\">"+
"										<div class=\"titreIndicateur\"  nowrap=\"nowrap\">"+
"											"+numeroCompetence+"."+numeroIndicateur+" - "+indicateur.nom+
"										</div>"+
"										<div class=\"commentaireIndicateur\">"+
"											<form data-ideval=\"0\">"+
"												<img class=\"boutonValideCommentaireEval\" alt=\"[V]\" src=\"./sources/images/valide.png\" onclick=\"valideCommentaireEval("+indicateur.id+");\"/>"+
"												<img class=\"boutonAnnuleCommentaireEval\" alt=\"[X]\" src=\"./sources/images/invalide.png\" onclick=\"bilanFermeCommentaire("+indicateur.id+");\"/>"+
(AUTORISE_CONTEXT?"												<input list=\"listeContexteAutocompletion\" type=\"text\" class=\"commentaireIndicateur-contexte\" name=\"commentaireIndicateur-contexte\" placeholder=\"Contexte (ex : TP1)\" size=\"15\"/>":"")+
(AUTORISE_COMMENTAIRES?"											<input type=\"text\" class=\"commentaireIndicateur-commentaire"+(!AUTORISE_COMMENTAIRES?"-invisible":"")+"\" name=\"commentaireIndicateur-commentaire\" placeholder=\"Commentaire (ex : N'a pas posé les hypothèses)\" size=\"38\"/>":"")+
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
"									"+NOTATION_getNiveauxIndicateur(indicateur.niveauEleveMax,indicateur.niveauMax,indicateur.id,STATUT=="admin" || STATUT=="evaluateur" || STATUT=="autoeval",false)
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
	return {niveau:parseInt((parseInt(indicateur.niveauEleveMax)+Math.abs(parseInt(indicateur.niveauEleveMax)))*0.5),niveauMax:parseInt(indicateur.niveauMax)};
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
bilanOuvreCommentaire=function(idInd,idEval)
{
	if(AUTORISE_CONTEXT || AUTORISE_COMMENTAIRES)
	{
		//Update idEval
		$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur form").attr("data-ideval",idEval);
		//Propose le dernier contexte utilise
	/*	var lastContext=parseInt($("#listeContexteAutocompletion").attr("data-lastused"));
		if(lastContext>=0)
			$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur-contexte").val(lastContext);*/
		//Animation
		$("#NOTATION_indicateur_"+idInd+" .titreIndicateur").hide("slide",{direction: "left" }, 500);
		setTimeout(function(){$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur").show("slide", { direction: "right" }, 500);},510);
	}
}

// Fonction qui ouvre la ligne de commentaire
bilanFermeCommentaire=function(idInd)
{
	//Update idEval
	$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur form").attr("data-ideval",0);
	//Animation
	$("#NOTATION_indicateur_"+idInd+" .commentaireIndicateur").hide("slide", { direction: "left" }, 500);
	setTimeout(function(){	$("#NOTATION_indicateur_"+idInd+" .titreIndicateur").show("slide", { direction: "right" }, 500 );},510);
}



//Fonction qui vérivie si un contexte est déjà présent dans la data-liste contexte, et qui l'ajoute le cas échéant.
//Renvoie vrai si le contexte éxistait deja
ajouteListeContextSiAbsent=function(contexte)
{
	if($("#listeContexteAutocompletion option[value='"+contexte+"']").size()==0)//Si le contexte n'a pas été ajouté à la liste...
	{
		$("#listeContexteAutocompletion").append("<option value=\""+contexte+"\">");//On le rajoute
		return false;
	}
	return true;
}

//Fonction qui vérivie si un contexte est déjà présent dans la data-liste contexte, et qui l'ajoute le cas échéant.
//Renvoie vrai si le contexte éxistait deja
updateListeContexteDansMenu=function()
{
	$("#BILAN_listeContextes").empty();
	$("#BILAN_listeContextes").append("<option value=\"ALL_CONTEXTE\">Choix du contexte</option>");
	$("#listeContexteAutocompletion option").each(function(index)
	{
		var contexte=$(this).val();
		$("#BILAN_listeContextes").append("<option value=\""+contexte+"\">"+contexte+"</option>");
	});
	$("#BILAN_listeContextes").data("selectBox-selectBoxIt").refresh();
}