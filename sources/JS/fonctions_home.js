
//Fonction trace le graphique récapitulatif des domaines ******************************
//Context = un canvas
// donnees = liste de valeurs
// labels = liste de labels
// id domaine = id pour savoir quand on clique dessus
// legende = true si on affiche la legende (par defaut) et false sinon
function traceGraphiqueRecap_Domaine(context_,donnees_,labels_,idDomaines_,legende_)
{
	var context=$(context_);//context jquery
	var legende_=(typeof legende_=="undefined")?true:legende_;
	var type="polarArea";
	

	var data={
						labels:labels_,
						datasets:[{
												data : donnees_,
												backgroundColor:LISTE_COULEURS_ARC_EN_CIEL,
												borderWidth: 0
										}],
						idDomaines:idDomaines_
					}
	var options={
			legend:{display:legende_},
			responsive: false,
			scale:{
				ticks:{max:100,display: false}
				}
			}
	var	chart=new Chart(context,{type,data,options});//Création du graphique

	return chart;
}

//Fonction qui ferme les graphiques de competences ***************
ouvreGraphiqueCompetence=function(idDomaine)
{
//	$("#HOME_graphiquesCompetences").css("display","inline-block");

	if(!$("#HOME_graphe_competences_Domaine_"+idDomaine).is(':visible'))//si le graphique n'est pas DEJA apparant
	{
		if(!$("#HOME_graphiquesCompetences").is(':visible'))//Si on n'a pas encore ouvert
		{
			$(".HOME_graphe_competence").hide();//{duration:1000, start:function(){if ($(this).is(':visible'))$(this).css('display','inline-block');}});
			$("#HOME_graphe_competences_Domaine_"+idDomaine).show();//{duration:1000, start:function(){if ($(this).is(':visible'))$(this).css('display','inline-block');}});		
			$("#HOME_graphiquesCompetences").show({duration:500, start:function(){if ($(this).is(':visible'))$(this).css('display','inline-block');}});
		}
		else
		{
			$(".HOME_graphe_competence").hide({duration:500, start:function(){if ($(this).is(':visible'))$(this).css('display','inline-block');}});
			$("#HOME_graphe_competences_Domaine_"+idDomaine).show({duration:500, start:function(){if ($(this).is(':visible'))$(this).css('display','inline-block');}});		
		}
	}
}

//Fonction qui ferme les graphiques de competences ***************
fermeGraphiquesCompetences=function()
{
	$("#HOME_graphiquesCompetences").hide(500);
}


//Fonction trace le graphique récapitulatif des competences ******************************
function traceGraphiqueRecap_Competence(context_,donnees_,labels_,titre_,couleur_)
{
	var context=$(context_);//context jquery

	var data={
								labels:labels_,
								datasets:[{
														data:donnees_,
														backgroundColor: couleur_,
														borderColor: couleur_
													}]
						};
var options={
								responsive: false,
								legend:{
												display:false
											},
								title:{
												display:true,
												text:titre_
											}
					}

	//Modification de l'échelle selon le type de graphique
	if(labels_.length>2)
	{
		var type="radar";
		options.scale={ticks:{max:100,min:0}};//Pour les radars
	}
	else
	{
		var type="bar"
		options.scales={yAxes:[{ticks:{max:100,min:0}}]};//Pour les barres
	}


	return new Chart(context,{type,data,options});
}





//Renvoie la couleur du graphique n°i (entier) ************************************
function getCouleurGraphique(i)
{
	return LISTE_COULEURS_ARC_EN_CIEL[i%LISTE_COULEURS_ARC_EN_CIEL.length];//LISTE_COULEURS_ARC_EN_CIEL[i%(LISTE_COULEURS_ARC_EN_CIEL.length)];
}
