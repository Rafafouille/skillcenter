//Fonction trace le graphique récapitulatif des domaines ******************************
function traceGraphiqueRecap_Domaine(context_,donnees_,labels_)
{
	var context=$(context_);//context jquery
	var type="polarArea";
	var data={
						labels:labels_,
						datasets:[{
												data : donnees_,
												backgroundColor:['red','lime','blue','yellow','fuchsia','aqua','green','purple','silver','teal']
										}]
					}
	var options={
							responsive: false,
							scale:{
											ticks:{max:100,display: false}
										}
					}
	return new Chart(context,{type,data,options});
}




//Fonction trace le graphique récapitulatif des competences ******************************
function traceGraphiqueRecap_Competence(context_,donnees_,labels_,titre_,couleur_)
{
	var context=$(context_);//context jquery
	var type="radar";
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
								scale:{
												ticks:{beginAtZero:true,max:100}
											},
								legend:{
												display:false
											},
								title:{
												display:true,
												text:titre_
											}
					}
	return new Chart(context,{type,data,options});
}
