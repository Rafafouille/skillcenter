<?php 	session_start();

$dOptions='./sources/PHP/';//Chemin du fichier d'option

$etape="debut";
//if(!isset($_SESSION['etape']))	$_SESSION['etape']="debut";
if(isset($_POST['etape']))	$etape=$_POST['etape'];
//$etape=$_SESSION['etape'];


if(isset($_POST['BDD_SERVER']))		$_SESSION['BDD_SERVER']=$_POST['BDD_SERVER'];
if(isset($_POST['BDD_NOM_BDD']))	$_SESSION['BDD_NOM_BDD']=$_POST['BDD_NOM_BDD'];
if(isset($_POST['BDD_LOGIN']))		$_SESSION['BDD_LOGIN']=$_POST['BDD_LOGIN'];
if(isset($_POST['BDD_MOT_DE_PASSE']))	$_SESSION['BDD_MOT_DE_PASSE']=$_POST['BDD_MOT_DE_PASSE'];
if(isset($_POST['BDD_PREFIXE']))	$_SESSION['BDD_PREFIXE']=$_POST['BDD_PREFIXE'];
if(isset($_POST['NB_NIVEAUX']))		$_SESSION['NB_NIVEAUX']=$_POST['NB_NIVEAUX'];
if(isset($_POST['NIVEAU_DEFAUT']))	$_SESSION['NIVEAU_DEFAUT']=$_POST['NIVEAU_DEFAUT'];



?>
<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8" />
        <title>Skillcenter</title>
	<style>
		body
		{
			font-family:"Comics Sans MS", Arial;
			height:100%;
			padding:0px;
			margin:0px;
		}

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


		h1
		{
			margin:0px;
			color:white;
			background-color:black;
			width:100%;
			padding-left:30px;
			padding-top:10px;
			height:50px;
			margin-bottom:30px;
		}


		.boite
		{
			margin:auto;
			padding:0px;
			border-radius:10px;
			overflow:hidden;
			background-color:#CCCCFF;
			max-width:600px;
			box-shadow: 4px 4px 6px #555;
			vertical-align:middle;
		}

		.boite h2, .boite .boutons
		{
			width:100%;
			margin:0px;
			text-align:center;
			background-color:#000088;
			height:30px;
			color:white;
		}

		.boite h2
		{
			padding-top:10px;
			font-style:italic;
			font-size:20px;
		}

		.boite .boutons .bouton
		{
			border-radius:5px;
			cursor:pointer;
			font-size:normal;
			font-weight:bold;
			text-align:center;
			vertical-align:middle;
			display:inline;
			margin-top:2px;
			padding-top:3px;
			padding-bottom:3px;
			padding-left:10px;
			padding-right:10px;
		}

		.boite .boutons .bouton:hover
		{
			background-color:#BBBBFF;
			color:black;
		}

		.boite p
		{
			width:80%;
			padding:20px;
		}
	</style>
</head>
<body>


<h1>Installation</h1>


<?php



// etape 1 : LE DEBUT ===========================================
if($etape=="debut")
{?>
	<div class="boite">
		<h2>Début de l'installation</h2>
		<p>
			Vous êtes sur le point d'installer (ou de mettre à jour)
			l'application "SkillCenter";
		</p>
		<div class="boutons">
			<form action="" method="POST">
			<input type="hidden" name="etape" value="sauvOptions"/>
			<input type="submit" class="bouton" value="Commencer"/>
			</form>
		</div>
	</div>
<?php }






// etape 2 : Sauvegarde ===========================================
if($etape=="sauvOptions")
{
	//Chargement des options par défaut
	$BDD_SERVER="";	//Adresse BDD
	$BDD_NOM_BDD="";	//Nom de la BDD
	$BDD_LOGIN="";			//Login du compte de BDD
	$BDD_MOT_DE_PASSE="";		//Mot de passed u compte de BDD
	$BDD_PREFIXE="";		//Préfixe de toute les tables de la BDD
	$NB_NIVEAUX_MAX=5;		//Nombre de niveau max possible à donner aux élèvess
	$NIVEAU_DEFAUT=4;		//Niveau par défaut quand on crée un critères

	//Récupération des valeurs existantes
	$_SESSION['optionsExiste']=file_exists($dOptions."options.php");
	if($_SESSION['optionsExiste'])
	{
	?>
	<div class="boite">
		<h2>Fichier "options.php" trouvé !</h2>
		<p>
			Un fichier "<strong>options.php</strong>" est visiblement déjà présent.
			Cela veut dire que SkillCenter est <strong>déjà installé</strong> dans ce dossier.
			<br/><br/>
			Les données de ce fichier vont être récupérées.
			<br/><br/>
			Ce fichier sera écrasé en toute fin d'installation (si jamais vous annulez avant...)
		</p>
		<table class="boutons"><tr>
			<td>
				<form action="" method="POST" style="display:inline;">
					<input type="hidden" name="etape" value="debut"/>
					<input type="submit" class="bouton" value="<-- Présent"/>
				</form>
			</td>
			<td>
				<form action="" method="POST" style="display:inline;">
					<input type="hidden" name="etape" value="rentreBDD"/>
					<input type="submit" class="bouton" value="Suivant -->"/>
				</form>
			</td>
		</tr></table>
	</div>
	<?php
		include_once($dOptions."options.php");
	}
	else//Si pas de fichier option.php --> on passe à la suite
		$etape="rentreBDD";

	$_SESSION['BDD_SERVER']=$BDD_SERVER;	//Adresse BDD
	$_SESSION['BDD_NOM_BDD']=$BDD_NOM_BDD;	//Nom de la BDD
	$_SESSION['BDD_LOGIN']=$BDD_LOGIN;	//Login du compte de BDD
	$_SESSION['BDD_MOT_DE_PASSE']=$BDD_MOT_DE_PASSE;		//Mot de passed u compte de BDD
	$_SESSION['BDD_PREFIXE']=$BDD_PREFIXE;		//Préfixe de toute les tables de la BDD
	$_SESSION['NB_NIVEAUX']=$NB_NIVEAUX_MAX;		//Nombre de niveau max possible à donner aux élèvess
	$_SESSION['NIVEAU_DEFAUT']=$NIVEAU_DEFAUT;
}


// etape 3 : Rentre BDD ===========================================
if($etape=="rentreBDD")
{?>
	<div class="boite">
		<h2>Paramètres de la base de données (BDD)</h2>
		<p>
			Pour utiliser SkillCenter, vous devez avoir une base de données MySQL.
			<br/>Merci de rentrer les paramètres de connexion ci-dessous.

			<form id="rentreBDD" method="POST" action="">
				<table>
					<tr>
						<td><label for="input_BDD_SERVER">Adresse du serveur SQL :</label> <span title="Adresse de votre serveur de base de données SQL. En réseau local, c'est souvent : localhost. Chez Free, par exemple, c'est : sql.free.fr. Renseignez-vous auprès de votre hébergeur."></td>
						<td><input type="text" id="input_BDD_SERVER" name="BDD_SERVER" placeholder="Ex : http://mysql.free.fr" value="<?php echo $_SESSION['BDD_SERVER'];?>"/></td>
					</tr>
					<tr>
						<td><label for="input_BDD_NOM_BDD">Nom de la base de donnée <span title="Selon votre herbergeur, il se peut que vous n'ayez qu'une seule base de donnée disponible. Dans ce cas, souvent, vous devez quand même donner son nom. Renseignez-vous auprès de votre hébergeur."><img src="./sources/images/icone-info.png" alt="[i]"/></span> :</label></td>
						<td><input type="text" id="input_BDD_NOM_BDD" name="BDD_NOM_BDD" placeholder="Ex : BDD_competences" value="<?php echo $_SESSION['BDD_NOM_BDD'];?>"/></td>
					</tr>
					<tr>
						<td><label for="input_BDD_LOGIN">Nom d'utilisateur :</label></td>
						<td><input type="text" id="input_BDD_LOGIN" name="BDD_LOGIN" placeholder="Ex : Toto21" value="<?php echo $_SESSION['BDD_LOGIN'];?>"/></td>
					</tr>
					<tr>
						<td><label for="input_BDD_MOT_DE_PASSE">Mot de passe :</label></td>
						<td><input type="text" id="input_BDD_MOT_DE_PASSE" name="BDD_MOT_DE_PASSE" placeholder="Ex : ******" value="<?php echo $_SESSION['BDD_MOT_DE_PASSE'];?>"/></td>
					</tr>
					<tr>
						<td><label for="input_BDD_PREFIXE">Préfixe pour les tables <span title="(OPTIONNEL) Un péfixe peut être mis devant le nom des tables dans la BDD. Cela peut être pratique pour installer plusieurs fois SkillCenter sur une même BDD. Sinon, laisser vide."><img src="./sources/images/icone-info.png" alt="[i]"/></span>:</label></td>
						<td><input type="text" id="input_BDD_PREFIXE" name="BDD_PREFIXE" placeholder="Ex : cpt1_    [OPTIONNEL]" value="<?php echo $_SESSION['BDD_PREFIXE'];?>"/></td>
					</tr>
				</table>
				<input type="hidden" name="etape" value="rentreNotation"/>
				<input type="submit" class="bouton" value="Suivant -->"/>
			</form>
			
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="debut"/>
						<input type="submit" class="bouton" value="<-- Présent"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
<?php }







// etape 4 : Rentre Notation ===========================================
if($etape=="rentreNotation")
{?>
	<div class="boite">
		<h2>Options des compétences</h2>
		<p>
			Les données ci-dessous concernent les options par défaut lorsque vous créerez
			de nouvelles compétences.

			<form>
				<table>
					<tr>
						<td><label for="input_NB_NIVEAUX">Nombre de niveaux maximum <span title="Il s'agit du nombre maximum de niveaux qui seront proposés lors de la création d'un nouveau critère." alt="[i]"/></span> :</label></td>
						<td><input type="number" min="1" step="3" placeholder="Nombre supérieur à 0" id="input_NB_NIVEAUX" name="BDD_NB_NIVEAUX" value="<?php echo $_SESSION['NB_NIVEAUX'];?>"/></td>
					</tr>
					<tr>
						<td><label for="input_NIVEAU_DEFAUT">Niveau par défaut <span title="Il s'agit du niveau maximum par défaut proposé lors de la création d''une compétence."><img src="./sources/images/icone-info.png" alt="[i]"/></span> :</label></td>
						<td><input type="number" min="1" step="3" placeholder="Nombre supérieur à 0" id="input_NIVEAU_DEFAUT" name="NIVEAU_DEFAUT" value="<?php echo $_SESSION['NIVEAU_DEFAUT'];?>"/></td>
					</tr>
				</table>
			</form>
			
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="rentreBDD"/>
						<input type="submit" class="bouton" value="<-- Présent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="ecritFichier"/>
						<input type="submit" class="bouton" value="Suivant -->"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
<?php }



// etape 5 : ecritFichier ===========================================
if($etape=="ecritFichier")
{

	$contenu="<?php
/* *****************************************************
		PARAMETRES DE L'APPLICATION
***************************************************** */

//Paramètres pour la base de données SQL ***********
\$BDD_SERVER=\"".$_SESSION['BDD_SERVER']."\";	//Adresse de la base de données
\$BDD_NOM_BDD=\"".$_SESSION['BDD_NOM_BDD']."\";	//Nom de la base de données
\$BDD_LOGIN=\"".$_SESSION['BDD_LOGIN']."\";	//Nom d'utilisateur de la base de données
\$BDD_MOT_DE_PASSE=\"".$_SESSION['BDD_MOT_DE_PASSE']."\";	//Mot de passe associé au nom d'utilisateur
\$BDD_PREFIXE=\"".$_SESSION['BDD_PREFIXE']."\";		//Préfixe à donner aux tables de la BDD

//Paramètre des niveaux des critères ********
\$NB_NIVEAUX_MAX=".$_SESSION['NB_NIVEAUX'].";		//Nombre de niveaux maximums qu'un critère pourra prendre
\$NIVEAUX_DEFAUT=".$_SESSION['NIVEAU_DEFAUT'].";		//Niveau max initialement proposé lors de la création d'un critère

//**************** FIN DU FICHIER ****************
?>";

	$modifiable=true;

	if(file_exists($dOptions."options.php")) //S'il y a dejà un "option.php", on le sauve
		$modifiable=rename($dOptions."options.php",$dOptions."options_old.php");

	if($modifiable)
	{
		$options_php=fopen($dOptions."options.php",'a') ;
		if($options_php)
		{
			fputs($options_php,$contenu);
			fclose($options_php);
			unlink($dOptions."options_old.php");

		?>	
	<div class="boite">
		<h2>Création du fichier "options.php"</h2>
		<p>
			Le fichier de configuration "options.php" a bien été créé.
			<br/>Le fichier de sauvegarde des anciennes options a été supprimé.
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="ecritFichier"/>
						<input type="submit" class="bouton" value="Réessayer"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
		<?php
		}
		else	//Impossible d'ouvrir pour créer le nouveau options.php
		{
		?>	
	<div class="boite">
		<h2>Création du fichier "options.php"</h2>
		<p style="color:red;text-align:center;">
			<strong>Impossible de créer le nouveau fichier "options.php".</strong>
			<br/>vérifiez que PHP a bien les droits d'écriture dans le dossier "sources/PHP".
			<br/><br/>Sinon, vous pouvez créez (ou remplacer) le fichier "options.php" à la main (n'oubliez pas le "s" !)
			dans le dossier "<em>sources/PHP</em>", à l'aide 'un éditeur de texte en copiant le texte suivant :
			<form>
				<textarea rows="15" cols="70"><?php echo $contenu;?></textarea>
			</form>
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="ecritFichier"/>
						<input type="submit" class="bouton" value="Réessayer"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
		<?php
		}
	}
	else	//Impossible de copier l'ancien options.php
	{
		?>
	<div class="boite">
		<h2>Création du fichier "options.php"</h2>
		<p style="color:red;text-align:center;">
			<strong>Impossible de sauvegarder l'ancien fichier "options.php".</strong>
			<br/>vérifiez que PHP a bien les droits d'écriture dans le dossier "sources/PHP".
			<br/><br/>Sinon, vous pouvez créez (ou remplacer) le fichier "options.php" à la main (n'oubliez pas le "s" !)
			dans le dossier "<em>sources/PHP</em>", à l'aide 'un éditeur de texte en copiant le texte suivant :
			<form>
				<textarea rows="15" cols="70"><?php echo $contenu;?></textarea>
			</form>
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="ecritFichier"/>
						<input type="submit" class="bouton" value="Réessayer"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
	<?php
	}
}
?>






</body>
</html>
