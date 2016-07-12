<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8" />
        <title>Skillcenter</title>
	<style>
		.etape
		{
			color : blue;
			font-style:italic;
			margin-left:10px;
		}
		.erreur
		{
			color: red;
			font-weight:bold;
		}
	</style>
</head>
<body>


<h1>Installation</h1>

<?php
$erreur_deplacement=false;
$entete="<?php\n/* *****************************************************\n		PARAMETRES DE L'APPLICATION\n***************************************************** */ \n\n";
$pied="\n\n//**************** FIN DU FICHIER ****************\n?>";

//Paramètres par défaut -------------------------------------------
$BDD_SERVER="localhost";	//Adresse BDD
$BDD_NOM_BDD="competences";	//Nom de la BDD
$BDD_LOGIN="";			//Login du compte de BDD
$BDD_MOT_DE_PASSE="";		//Mot de passed u compte de BDD
$BDD_PREFIXE="";		//Préfixe de toute les tables de la BDD
$NB_NIVEAUX_MAX=5;		//Nombre de niveau max possible à donner aux élèvess
$NIVEAU_DEFAUT=4;		//Niveau par défaut quand on crée un critères



//Fichier options.php
echo "<h2>Fichier \"options.php\"</h2>";


$dOptions='./sources/PHP/';//Chemin du fichier d'option

//Verifie que le fichier existe deja ou non --------------------
if(file_exists($dOptions."options.php"))
{
	echo "<p class=\"etape\">Le fichier \"options.php\" existe déjà. Il va être sauvegardé.</p>";

	//Copie le fichier
	if(rename($dOptions."options.php",$dOptions."options_tmp.php"))
	{
		echo "<p class=\"etape\">Le fichier \"options.php\" a été copié en sauvegarde dans le fichier \"options_tmp.php\".</p>";


		//On importe les paramètres existants, par dessus ceux par défauts
		include_once($dOptions."options_tmp.php");
		echo "<p class=\"etape\">Les anciennes options ont été récupérées.</p>";

	}
	else
	{
		echo "<p class=\"erreur\">Le fichier n'a pas pu être sauvegardé ! Vérifiez les droits d'écriture du dossier \"./sources/PHP/\"!</p>";
		$erreur_deplacement=true;
	}
}
else
{
	echo "<p class=\"etape\">Le fichier \"options.php\" n'existe pas</p>";
}



//Création du nouveau fichier option.php ------------------------------------------
if(!$erreur_deplacement)//Si on a réussi a copier le précédent fichier (ou s'il n'existait pas)
{
	$options_php=fopen($dOptions."options.php",'a') ;
	echo "<p class=\"etape\">Création du nouveau fichier \"options.php\".</p>";

	//Entete -----------------------
	fputs($options_php,$entete);	//Entête

	//BDD-----------------------
	$contenu="//Paramètres pour la base de données SQL ***********
\$BDD_SERVER=\"".$BDD_SERVER."\";	//Adresse de la base de données
\$BDD_NOM_BDD=\"".$BDD_NOM_BDD."\";	//Nom de la base de données
\$BDD_LOGIN=\"".$BDD_LOGIN."\";	//Nom d'utilisateur de la base de données
\$BDD_MOT_DE_PASSE=\"".$BDD_MOT_DE_PASSE."\";	//Mot de passe associé au nom d'utilisateur
\$BDD_PREFIXE=\"".$BDD_PREFIXE."\";		//Préfixe à donner aux tables de la BDD";

	fputs($options_php,$contenu);

	//Niveaux-------------------
	$contenu="\n\n//Paramètre des niveaux des critères ********
\$NB_NIVEAUX_MAX=".$NB_NIVEAUX_MAX.";		//Nombre de niveaux maximums qu'un critère pourra prendre;
\$NIVEAUX_DEFAUT=".$NIVEAU_DEFAUT.";		//Niveau max initialement proposé lors de la création d'un critère";

	fputs($options_php,$contenu);



	//Pied
	fputs($options_php,$pied);


	fclose($options_php);
	echo "<p class=\"etape\">Ecriture des options : effectuées.</p>";

	//Suppression du fichier temporaire
	unlink($dOptions."options_tmp.php");
	echo "<p class=\"etape\">Suppression du fichier temporaire \"options_tmp.php\".</p>";
}
else
		echo "<p class=\"erreur\">Le fichier \"option.php\" n'a pas été créé.</p>";









fclose($options_php);

//BDD
echo "<h2>BDD</h2>";
echo "<em>(Rien)</em>";

//rename($dOptions."options_tmp.php",$dOptions.$fOptions)
?>

</body>
</html>
