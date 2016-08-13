/************************************************
	HISTORIQUE
********************************************/

ouvreBoite_modifNotation=function(id,val,maxi)
{
	$('#modifNotation_input').val(val);	//$('#historique_".$donnees['id']." .note_historique span').text()
	$('#modifNotation_input').attr('max',maxi);
	$('#modifNotation_max').text(maxi);
	$('#modifNotation_num_critere').text(id);
	$('#dialog-modifNotation').dialog('open');
}

ouvreBoite_supprimeNotation=function(id)
{
	$('#supprEvaluNumId').text(id);
	$('#dialog-supprimeNotation').dialog('open');
}

modifieNotation=function(id,note)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"modifieNotation",
				idNotation:id,
				note:note
			},
			modifieNotation_CallBack,	//Fonction callback
			"json"	//Type de réponse
	);
}

modifieNotation_CallBack=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	var id=reponse.idNotation;
	$("#historique_"+id+" .note_historique strong span").text(reponse.note);//MAJ note
	$("#historique_"+id+" .prof_historique").text(reponse.evaluateur);//MAJ prof
	$("#historique_"+id+" .date_historique").text(reponse.date);//MAJ prof
	//$("#historique_"+id).insertBefore($("#liste_historique").children()[0]);
	$("#historique_"+id).slideUp(function(){$(this).insertBefore($("#liste_historique").children()[0]).slideDown();});
}

supprimeNotation=function(id)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"supprimeNotation",
				idNotation:id
			},
			supprimeNotation_CallBack,	//Fonction callback
			"json"	//Type de réponse
	);
}

supprimeNotation_CallBack=function(reponse)
{
	afficheMessage(reponse.messageRetour);
	var id=reponse.idNotation;
	$("#historique_"+id).remove();
}

 
