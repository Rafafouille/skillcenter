/************************************************
	HISTORIQUE
********************************************/

ouvreBoite_modifNotation=function(id,val,maxi,contexte,commentaire)
{
	$('#modifNotation_input_evaluation').val(val);	//$('#historique_".$donnees['id']." .note_historique span').text()
	$('#modifNotation_input_evaluation').attr('max',maxi);
	$('#modifNotation_max').text(maxi);
	$('#modifNotation_num_critere').text(id);
	$('#modifNotation_contexte').val(contexte);
	$('#modifNotation_commentaire').text(commentaire);
	
	$('#dialog-modifNotation').dialog('open');
}



ouvreBoite_supprimeNotation=function(id)
{
	$('#supprEvaluNumId').text(id);
	$('#dialog-supprimeNotation').dialog('open');
}



modifieNotation=function(id,note,contexte,commentaire)
{
	$.post(
			'./sources/PHP/actionneurJSON.php',//Requete appelée
			{	//Les données à passer par POST
				action:"modifieNotation",
				idNotation:id,
				note:note,
				contexte:contexte,
				commentaire:commentaire
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
	
	$("#historique_"+id).attr("data-contexte",reponse.contexte);
	$("#historique_"+id).attr("data-commentaire",reponse.commentaire);
	
	$("#historique_"+id).slideUp(function(){$(this).insertBefore($("#liste_historique").children()[0]).slideDown();});
	
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de modif
}


//Supprime une note
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
	$("#historique_"+id).slideUp();
	setTimeout(function(){$("#historique_"+id).remove();},1000);
	
	NOTATION_LOADED=false;//Impose de recharger la notation en cas de suppression
}

 
