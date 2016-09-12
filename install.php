<?php 	session_start();

$dOptions='./sources/PHP/';//Chemin du fichier d'option

$etape="debut";
//if(!isset($_SESSION['etape']))	$_SESSION['etape']="debut";
if(isset($_POST['etape']))	$etape=$_POST['etape'];
//$etape=$_SESSION['etape'];

if(isset($_POST['TITRE_PAGE']))				$_SESSION['TITRE_PAGE']=$_POST['TITRE_PAGE'];
if(isset($_POST['COULEUR_BANDEAU']))	$_SESSION['COULEUR_BANDEAU']=$_POST['COULEUR_BANDEAU'];

if(isset($_POST['BDD_SERVER']))		$_SESSION['BDD_SERVER']=$_POST['BDD_SERVER'];
if(isset($_POST['BDD_NOM_BDD']))	$_SESSION['BDD_NOM_BDD']=$_POST['BDD_NOM_BDD'];
if(isset($_POST['BDD_LOGIN']))		$_SESSION['BDD_LOGIN']=$_POST['BDD_LOGIN'];
if(isset($_POST['BDD_MOT_DE_PASSE']))	$_SESSION['BDD_MOT_DE_PASSE']=$_POST['BDD_MOT_DE_PASSE'];
if(isset($_POST['BDD_PREFIXE']))	$_SESSION['BDD_PREFIXE']=$_POST['BDD_PREFIXE'];

if(isset($_POST['NB_NIVEAUX']))		$_SESSION['NB_NIVEAUX']=$_POST['NB_NIVEAUX'];
if(isset($_POST['NIVEAU_DEFAUT']))	$_SESSION['NIVEAU_DEFAUT']=$_POST['NIVEAU_DEFAUT'];

if(isset($_POST['input_NOM_NIVEAU_1-0']))	//Recupere les noms de niveaux
{
	unset($_SESSION['NOMS_NIVEAUX']);
	$_SESSION['NOMS_NIVEAUX']=array();
	$i=1;
	while(isset($_POST['input_NOM_NIVEAU_'.$i.'-0']))
	{
		$_SESSION['NOMS_NIVEAUX'][$i]=array();
		for($j=0;$j<=$i;$j++)
		{
			if(isset($_POST['input_NOM_NIVEAU_'.$i.'-'.$j]))
				$_SESSION['NOMS_NIVEAUX'][$i-1][$j]=$_POST['input_NOM_NIVEAU_'.$i.'-'.$j];
		}
		$i+=1;
	}
}

if(isset($_POST['AUTORISE_BADGES']))	$_SESSION['AUTORISE_BADGES']=$_POST['AUTORISE_BADGES']=="oui";
if(isset($_POST['AUTORISE_GRAPHIQUES']))	$_SESSION['AUTORISE_GRAPHIQUES']=$_POST['AUTORISE_GRAPHIQUES']=="oui";

?>
<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8" />
        <title>Skillcenter</title>
	<script type="text/javascript" src="./sources/JS/libraries/jquery-ui/external/jquery/jquery.js"></script>
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
			margin-left:auto;
			margin-right:auto;
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
	$TITRE_PAGE="Skillcenter";	//Titre de la page
	$COULEUR_BANDEAU="#000000";	//Couleur du bandeau

	$BDD_SERVER="";	//Adresse BDD
	$BDD_NOM_BDD="";	//Nom de la BDD
	$BDD_LOGIN="";			//Login du compte de BDD
	$BDD_MOT_DE_PASSE="";		//Mot de passed u compte de BDD
	$BDD_PREFIXE="";		//Préfixe de toute les tables de la BDD

	$NB_NIVEAUX_MAX=5;		//Nombre de niveau max possible à donner aux élèves
	$NIVEAU_DEFAUT=4;		//Niveau par défaut quand on crée un critères
	$INTITULES_NIVEAUX_CRITERES=Array();	//Noms des niveaux

	$AUTORISE_BADGES=true;	//Utilise les badges ou non
	$AUTORISE_GRAPHIQUES=true;	//Utilise les graphiques dans home ou non

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
			Ce fichier sera écrasé seulement après avoir rentré les paramètres (si jamais vous annulez avant...).
		</p>
		<table class="boutons"><tr>
			<td>
				<form action="" method="POST" style="display:inline;">
					<input type="hidden" name="etape" value="debut"/>
					<input type="submit" class="bouton" value="<-- Précédent"/>
				</form>
			</td>
			<td>
				<form action="" method="POST" style="display:inline;">
					<input type="hidden" name="etape" value="rentreConfigGenerale"/>
					<input type="submit" class="bouton" value="Suivant -->"/>
				</form>
			</td>
		</tr></table>
	</div>
	<?php
		include_once($dOptions."options.php");
	}
	else//Si pas de fichier option.php --> on passe à la suite
		$etape="rentreConfigGenerale";

	$_SESSION['TITRE_PAGE']=$TITRE_PAGE;	//Titre de la page
	$_SESSION['COULEUR_BANDEAU']=$COULEUR_BANDEAU;	//Couleur d'arrière plan du bandeau

	$_SESSION['BDD_SERVER']=$BDD_SERVER;	//Adresse BDD
	$_SESSION['BDD_NOM_BDD']=$BDD_NOM_BDD;	//Nom de la BDD
	$_SESSION['BDD_LOGIN']=$BDD_LOGIN;	//Login du compte de BDD
	$_SESSION['BDD_MOT_DE_PASSE']=$BDD_MOT_DE_PASSE;		//Mot de passed u compte de BDD
	$_SESSION['BDD_PREFIXE']=$BDD_PREFIXE;		//Préfixe de toute les tables de la BDD

	$_SESSION['NB_NIVEAUX']=$NB_NIVEAUX_MAX;		//Nombre de niveau max possible à donner aux élèvess
	$_SESSION['NIVEAU_DEFAUT']=$NIVEAU_DEFAUT;
	$_SESSION['NOMS_NIVEAUX']=$INTITULES_NIVEAUX_CRITERES;

	$_SESSION['AUTORISE_BADGES']=$AUTORISE_BADGES;
	$_SESSION['AUTORISE_GRAPHIQUES']=$AUTORISE_GRAPHIQUES;
}




// etape 2 BIS : Config générales ===========================================
if($etape=="rentreConfigGenerale")
{?>
	<div class="boite">
		<h2>Configuration générale</h2>
		<p>
			<form method="POST" action="" id="formRentreConfigGenerale">
				<table>
					<tr>
						<td><label for="input_TITRE_PAGE">Titre de la page <span title="Ce titre sera affiché sur le bandeau d'entête de la page, et de la fenêtre du navigateur." alt="[i]"/></span> :</label></td>
						<td><input type="text" placeholder="Écrire un titre" id="input_TITRE_PAGE" name="TITRE_PAGE" value="<?php echo $_SESSION['TITRE_PAGE'];?>"/></td>
					</tr>
					<tr>
						<td><label for="input_COULEUR_BANDEAU">Couleur du bandeau :</label></td>
						<td><input type="color" placeholder="Ex : #FF0000" id="input_COULEUR_BANDEAU" name="COULEUR_BANDEAU" value="<?php echo $_SESSION['COULEUR_BANDEAU'];?>"/></td>
					</tr>
				</table>
				<input type="hidden" name="etape" value="rentreBDD"/>
			</form>
			
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="debut"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
						<div class="bouton" onclick="$('#formRentreConfigGenerale').submit();"/>Suivant --></div>
				</td>
			</tr></table>
		</div>
	</div>
<?php }








//etape 3bis : tester la connection SQL ============================
if($etape=="testBDD")
{
	$connexionReussie=false;
	try
	{
		$bdd = new PDO('mysql:host='.$_SESSION['BDD_SERVER'].';dbname='.$_SESSION['BDD_NOM_BDD'].';charset=utf8',$_SESSION['BDD_LOGIN'],$_SESSION['BDD_MOT_DE_PASSE']);
		
		$connexionReussie=true;
	}
	catch(PDOException $e)
	{
		$connexionReussie=false; //Inutile...
	}
	if($connexionReussie)
		$etape="valideBDD";
	else
		$etape="rentreBDD";
}


// etape 3 : Rentre BDD ===========================================
if($etape=="rentreBDD")
{?>
	<div class="boite">
		<h2>Paramètres de la base de données (BDD)</h2>
		<p>
			Pour utiliser SkillCenter, vous devez avoir une base de données MySQL.
			<br/>Merci de rentrer les paramètres de connexion ci-dessous.
		</p>

			<?php if(isset($connexionReussie))
						{if(!$connexionReussie)
								echo "<p style=\"color:red;font-weight:bold;\">Le test de connexion à la base de données a échoué. Les paramètres ne semblent pas être bons...</p>";
						}
			?>
		<p>
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
				<input type="hidden" name="etape" value="testBDD"/>
			</form>
			
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="debut"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
						<div class="bouton" onclick="$('#rentreBDD').submit();"/>Suivant --></div>
				</td>
			</tr></table>
		</div>
	</div>
<?php }



// etape 3ter : Valide BDD ===========================================
if($etape=="valideBDD")
{?>
	<div class="boite">
		<h2>Paramètres de la BDD validés</h2>
		<p>
			La connexion-test à la base de donnée a réussie.
			Les paramètres fournis sont donc corrects.
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="rentreBDD"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="rentreNotation"/>
						<input type="submit" class="bouton" value="Suivant -->"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
<?php }

				


// etape 4 : Rentre Notation ===========================================
if($etape=="rentreNotation")
{?>
	<script>
		//Script pour changer les nom des niveaux...
		updateNomsNiveaux=function()
		{
			$(".inputNomsNiveaux").parent().parent().remove();
			
			nbMax=parseInt($("#input_NB_NIVEAUX").val());
			
			//Tableau des noms dejà existants
			tabNoms=Array(<?php
			if(isset($_SESSION['NOMS_NIVEAUX']))
			{
				for($i=1;$i<=sizeof($_SESSION['NOMS_NIVEAUX']);$i++)
				{
					echo "Array(";
					$ligne=$_SESSION['NOMS_NIVEAUX'][$i-1];
					for($j=0;$j<sizeof($ligne);$j++)
					{
						$nom=$ligne[$j];
						echo "\"".$nom."\"";
						if($j<sizeof($ligne)-1)
							echo ",";
					}
					echo ")";//Fin du sous-tableau
					if($i<sizeof($_SESSION['NOMS_NIVEAUX']))
						echo ",";
				}
			}
			?>);	//Fin tableau entier
			
			
			for(var i=1;i<=nbMax;i++)
			{
					var texte=""+
"					<tr>"+
"						<td style=\"text-align:right;\"><label for=\"input_NOM_NIVEAU_"+i+"-0\">"+i+" niveau(x) :</label></td>"+
"						<td>";
				for(var j=0;j<=i;j++)
				{
					
					//Noms par défaut...
					if(tabNoms[i-1]==undefined)
							nomNiveau=j;
					else
						if(tabNoms[i-1][j]==undefined)
							nomNiveau=j;
						else
							nomNiveau=tabNoms[i-1][j];
					
					texte+="<input type=\"text\" size=\"2\" maxlength=\"2\" name=\"input_NOM_NIVEAU_"+i+"-"+j+"\" id=\"input_NOM_NIVEAU_"+i+"-"+j+"\" class=\"inputNomsNiveaux\" value=\""+nomNiveau+"\">";
				}
				texte+="</td>"+
"					</tr>";
				$("#input_NIVEAU_DEFAUT").parent().parent().before(texte);
			}
		}
	</script>

	<div class="boite">
		<h2>Options des compétences</h2>
		<p>
			Les données ci-dessous concernent les options par défaut lorsque vous créerez
			de nouvelles compétences.

			<form method="POST" action="" id="formRentreNotation">
				<table>
					<tr>
						<td><label for="input_NB_NIVEAUX">Nombre de niveaux maximum pour chaque critère <span title="Il s'agit du nombre maximum de niveaux qui seront proposés lors de la création d'un nouveau critère." alt="[i]"/></span> :</label></td>
						<td><input type="number" min="1" step="1" placeholder="Nombre supérieur à 0" id="input_NB_NIVEAUX" name="NB_NIVEAUX" value="<?php echo $_SESSION['NB_NIVEAUX'];?>" onchange="updateNomsNiveaux();"/></td>
					</tr>
					<tr>
						<td>Intitulés des niveaux : </td>
						<td></td>
					</tr>
					<tr>
						<td><label for="input_NIVEAU_DEFAUT">Niveau par défaut pour un critère <span title="Il s'agit du niveau maximum par défaut proposé lors de la création d''une compétence."><img src="./sources/images/icone-info.png" alt="[i]"/></span> :</label></td>
						<td><input type="number" min="1" step="1" placeholder="Nombre supérieur à 0" id="input_NIVEAU_DEFAUT" name="NIVEAU_DEFAUT" value="<?php echo $_SESSION['NIVEAU_DEFAUT'];?>"/></td>
					</tr>
					<tr>
						<td><label for="input_AUTORISE_BADGES">Autorise l'acquisition des badges <span title="Les 'badges' sont des récompenses acquises par l'élève lorsque certaines actions sont réalisée (comme 'atteindre un certain nombre de compétences...')"><img src="./sources/images/icone-info.png" alt="[i]"/></span> :</label></td>
						<td><select id="input_AUTORISE_BADGES" name="AUTORISE_BADGES">
									<option value="oui" <?php if($_SESSION['AUTORISE_BADGES']) echo "selected";?>>Oui</options>
									<option value="non" <?php if(!$_SESSION['AUTORISE_BADGES']) echo "selected";?>>Non</options>
								</select>
						</td>
					</tr>
					<tr>
						<td><label for="input_AUTORISE_GRAPHIQUES">Autorise l'affichage des graphiques <span title="Les graphiques sont un bilan visuel affiché sur la page d'accueil de l'élève "><img src="./sources/images/icone-info.png" alt="[i]"/></span> :</label></td>
						<td><select id="input_AUTORISE_GRAPHIQUES" name="AUTORISE_GRAPHIQUES">
									<option value="oui" <?php if($_SESSION['AUTORISE_GRAPHIQUES']) echo "selected";?>>Oui</options>
									<option value="non" <?php if(!$_SESSION['AUTORISE_GRAPHIQUES']) echo "selected";?>>Non</options>
								</select>
						</td>
					</tr>
				</table>
				<input type="hidden" name="etape" value="ecritFichier"/>
			</form>
			
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="rentreBDD"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
						<div class="bouton" onclick="$('#formRentreNotation').submit();"/>Créer le fichier 'options.php' --></div>
				</td>
			</tr></table>
		</div>
	</div>
	<script>updateNomsNiveaux();</script>
<?php }



// etape 5 : ecritFichier ===========================================
if($etape=="ecritFichier")
{

	$contenu="<?php
/* *****************************************************
		PARAMETRES DE L'APPLICATION
***************************************************** */

//Paramètres généraux ************************
\$TITRE_PAGE=\"".$_SESSION['TITRE_PAGE']."\";	//Titre de la page (du navigateur + du bandeau)
\$COULEUR_BANDEAU=\"".$_SESSION['COULEUR_BANDEAU']."\";	//Couleur d'arriere plan du bandeau

//Paramètres pour la base de données SQL ***********
\$BDD_SERVER=\"".$_SESSION['BDD_SERVER']."\";	//Adresse de la base de données
\$BDD_NOM_BDD=\"".$_SESSION['BDD_NOM_BDD']."\";	//Nom de la base de données
\$BDD_LOGIN=\"".$_SESSION['BDD_LOGIN']."\";	//Nom d'utilisateur de la base de données
\$BDD_MOT_DE_PASSE=\"".$_SESSION['BDD_MOT_DE_PASSE']."\";	//Mot de passe associé au nom d'utilisateur
\$BDD_PREFIXE=\"".$_SESSION['BDD_PREFIXE']."\";		//Préfixe à donner aux tables de la BDD

//Paramètre des niveaux des critères ********
\$NB_NIVEAUX_MAX=".$_SESSION['NB_NIVEAUX'].";		//Nombre de niveaux maximums qu'un critère pourra prendre
\$NIVEAU_DEFAUT=".$_SESSION['NIVEAU_DEFAUT'].";		//Niveau max initialement proposé lors de la création d'un critère
//Noms des criteres :
\$INTITULES_NIVEAUX_CRITERES=[";
	for($i=1;$i<=$_SESSION['NB_NIVEAUX'];$i++)
	{
		$contenu.="[";
		for($j=0;$j<=$i;$j++)
		{
			$contenu.="'".$_SESSION['NOMS_NIVEAUX'][$i-1][$j]."'";
			if($j!=$i)
				$contenu.=",";
		}
		$contenu.="]";
		if($i!=$_SESSION['NB_NIVEAUX'])
			$contenu.=",";
	}
$contenu.="];\n\n";

/*for($i=1;$i<=$_SESSION['NB_NIVEAUX'];$i++)
{
	$contenu.="//     Critere de niveau max : ".$i."\n";
	for($j=0;$j<=$i;$j++)
	{
		$contenu.="       \$INTITULE_NIVEAU_".$i."_".$j."=\"".$_SESSION['NOMS_NIVEAUX'][$i][$j]."\";\n";
	}
}*/

$contenu.="
//Autres ************************************
\$AUTORISE_BADGES=".($_SESSION['AUTORISE_BADGES']?"true":"false").";		//Autorise (ou non) les étudiants à recevoir des badges de validation
\$AUTORISE_GRAPHIQUES=".($_SESSION['AUTORISE_GRAPHIQUES']?"true":"false").";		//Autorise (ou non) à afficher les graphiques sur la page d'accueil

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
			if(file_exists($dOptions."options_old.php"))
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
						<input type="hidden" name="etape" value="rentreNotation"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="creerBDD_Info"/>
						<input type="submit" class="bouton" value="Suivant -->"/>
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
			dans le dossier "<em>sources/PHP</em>", à l'aide d'un éditeur de texte en copiant le texte suivant :
			<form>
				<textarea rows="15" cols="70"><?php echo $contenu;?></textarea>
			</form>
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="rentreNotation"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="ecritFichier"/>
						<input type="submit" class="bouton" value="Réessayer"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="creerBDD_Info"/>
						<input type="submit" class="bouton" value="Passer -->"/>
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
						<input type="hidden" name="etape" value="rentreNotation"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="creerBDD_Info"/>
						<input type="submit" class="bouton" value="Réessayer"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="creerBDD_Info"/>
						<input type="submit" class="bouton" value="Passer -->"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
	<?php
	}
}





// etape 6 : Creer BDD information ===========================================
if($etape=="creerBDD_Info")
{?>
	<div class="boite">
		<h2>Créaction / Actualisation des tables de la BDD</h2>
		<p>
			Nous allons maintenant installer (ou mettre à jour) les tables de la base de données.
			Pour ce faire, il faut être sûr que l'utilisateur de la base SQL (que vous avez rentré précédement) 
			ait les droits d'écriture dans la base de donnée...
			<br/>
			Note : cela peut prendre quelques secondes, soyez patient...
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="rentreNotation"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="creerBDD"/>
						<input type="submit" class="bouton" value="Suivant -->"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
<?php }





// etape 7 : Creer BDD ===========================================
if($etape=="creerBDD")
{

	//Connexion à la BDD
	$bdd = new PDO('mysql:host='.$_SESSION['BDD_SERVER'].';dbname='.$_SESSION['BDD_NOM_BDD'].';charset=utf8',$_SESSION['BDD_LOGIN'],$_SESSION['BDD_MOT_DE_PASSE']);


function creeTable($nom,$attr1)
{
		global $bdd;

		//Vérifie si elle existe
		$rep=$bdd->query("SHOW TABLES FROM ".$_SESSION['BDD_NOM_BDD']." LIKE '".$_SESSION['BDD_PREFIXE'].$nom."'");
		if($donnees=$rep->fetch()) //Si la table existe... on le dit...
			echo "			<li style=\"color:blue;font-style:italic;\">La table '".$_SESSION['BDD_PREFIXE'].$nom."' existe déjà.</li>";
		else	//Sinon, on la crée...
		{
		try
			{$bdd->query("CREATE TABLE ".$_SESSION['BDD_PREFIXE'].$nom." (".$attr1." MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY) CHARACTER SET utf8 COLLATE utf8_bin;");
				echo "			<li style=\"color:green;\">Table '".$_SESSION['BDD_PREFIXE'].$nom."' créée.</li>";}
			catch(Execption $e)
			{echo "			<li style=\"color:red;font-weight:bold;\">Erreur de création de '".$_SESSION['BDD_PREFIXE'].$nom."'.</li>";}
		}
}

function creeAttribut($table,$nom,$type)
{
		global $bdd;
		$rep=$bdd->query("SHOW COLUMNS FROM ".$_SESSION['BDD_PREFIXE'].$table." LIKE  '".$nom."'");
		if($donnees=$rep->fetch())
			echo "			<li style=\"color:blue;font-style:italic;\">&nbsp;&nbsp;&nbsp;Attribut '".$nom."' (Table '".$_SESSION['BDD_PREFIXE'].$table."') existe déjà.</li>";
		else
		{
			try
			{$bdd->query("ALTER TABLE ".$_SESSION['BDD_PREFIXE'].$table." ADD ".$nom." ".$type);
				echo "			<li style=\"color:green;\">&nbsp;&nbsp;&nbsp;Attribut '".$nom."' (Table '".$_SESSION['BDD_PREFIXE'].$table."') créée.</li>";}
			catch(Execption $e)
			{echo "			<li style=\"color:red;font-weight:bold;\">&nbsp;&nbsp;&nbsp;Erreur de création de l'attribut '".$nom."' (Table '".$_SESSION['BDD_PREFIXE'].$table."').</li>";}
		}
}

?>
	<div class="boite">
		<h2>Créaction / Actualisation des tables de la BDD</h2>
		<ul>
<?php

creeTable("competences","id");
	creeAttribut("competences","id","MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
	creeAttribut("competences","nom","TEXT NOT NULL");
	creeAttribut("competences","groupe","MEDIUMINT UNSIGNED DEFAULT 0");
	creeAttribut("competences","position","MEDIUMINT UNSIGNED DEFAULT 0");
	
creeTable("groupes_competences","id");
	creeAttribut("groupes_competences","id","int AUTO_INCREMENT PRIMARY KEY");
	creeAttribut("groupes_competences","nom","TEXT NOT NULL");
	creeAttribut("groupes_competences","position","MEDIUMINT UNSIGNED DEFAULT 0");

creeTable("indicateurs","id");
	creeAttribut("indicateurs","id","MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
	creeAttribut("indicateurs","nom","TEXT NOT NULL");
	creeAttribut("indicateurs","details","TEXT NOT NULL");
	creeAttribut("indicateurs","niveaux","MEDIUMINT UNSIGNED DEFAULT 0");
	creeAttribut("indicateurs","position","MEDIUMINT DEFAULT 0");
	creeAttribut("indicateurs","competence","MEDIUMINT UNSIGNED DEFAULT 0");

creeTable("liensClassesIndicateurs","idLien");
	creeAttribut("liensClassesIndicateurs","idLien","MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
	creeAttribut("liensClassesIndicateurs","indicateur","MEDIUMINT UNSIGNED DEFAULT 0");
	creeAttribut("liensClassesIndicateurs","classe","TEXT NOT NULL");

creeTable("notation","id");
	creeAttribut("notation","id","MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
	creeAttribut("notation","note","MEDIUMINT DEFAULT 0");
	creeAttribut("notation","date","timestamp DEFAULT NOW()");
	creeAttribut("notation","eleve","MEDIUMINT UNSIGNED DEFAULT 0");
	creeAttribut("notation","indicateur","MEDIUMINT UNSIGNED DEFAULT 0");
	creeAttribut("notation","examinateur","MEDIUMINT UNSIGNED DEFAULT 0");
	
creeTable("utilisateurs","id");
	creeAttribut("utilisateurs","id","MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY");
	creeAttribut("utilisateurs","nom","TEXT NOT NULL");
	creeAttribut("utilisateurs","prenom","TEXT NOT NULL");
	creeAttribut("utilisateurs","login","TEXT NOT NULL");
	creeAttribut("utilisateurs","mdp","TEXT NOT NULL");
	creeAttribut("utilisateurs","classe","TEXT NOT NULL");
	creeAttribut("utilisateurs","statut","TEXT NOT NULL");
	creeAttribut("utilisateurs","mail","TEXT NOT NULL");
	creeAttribut("utilisateurs","notifieMail","TINYINT DEFAULT 0");
	creeAttribut("utilisateurs","badges","TEXT NOT NULL");
	creeAttribut("utilisateurs","nouveaux_badges","TEXT NOT NULL");
	creeAttribut("utilisateurs","derniere_connexion","TIMESTAMP NOT NULL DEFAULT 0");



?>
		</ul>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="creerBDD_Info"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="testAdmin"/>
						<input type="submit" class="bouton" value="Suivant -->"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
<?php
}









//ETAPE 8bis : enregistre admin ===========================================
if($etape=="enregistreAdmin")
{
		$bdd = new PDO('mysql:host='.$_SESSION['BDD_SERVER'].';dbname='.$_SESSION['BDD_NOM_BDD'].';charset=utf8',$_SESSION['BDD_LOGIN'],$_SESSION['BDD_MOT_DE_PASSE']);

		$nom="";
		if(isset($_POST['admin_nom'])) $nom=strtoupper($_POST['admin_nom']);
		$prenom="";
		if(isset($_POST['admin_prenom'])) $prenom=ucwords($_POST['admin_prenom']);
		$login="";
		if(isset($_POST['admin_login'])) $login=strtolower($_POST['admin_login']);
		$mdp="";
		if(isset($_POST['admin_mdp'])) $mdp=$_POST['admin_mdp'];
		$mail="";
		if(isset($_POST['admin_mail'])) $mail=strtolower($_POST['admin_mail']);


		if($nom!="" && $login!="" && $mdp!="")
		{
			$req=$bdd->prepare("INSERT INTO ".$_SESSION['BDD_PREFIXE']."utilisateurs(nom,prenom,login,mdp,classe,statut,mail,notifieMail) VALUES(:nom,:prenom,:login,:mdp,'','admin',:mail,1)");
			$req->execute(array(	'nom'=>$nom,
												'prenom'=>$prenom,
												'login'=>$login,
												'mdp'=>$mdp,
												'mail'=>$mail
					));
			$etape="confirmeAdmin";
		}
		else//Si pas valide
		{
			$etape="testAdmin";
		}
}







//ETAPE 8 : 1er admin ===========================================
if($etape=="testAdmin")
{
		$bdd = new PDO('mysql:host='.$_SESSION['BDD_SERVER'].';dbname='.$_SESSION['BDD_NOM_BDD'].';charset=utf8',$_SESSION['BDD_LOGIN'],$_SESSION['BDD_MOT_DE_PASSE']);
		$rep=$bdd->query("SELECT * FROM ".$_SESSION['BDD_PREFIXE']."utilisateurs WHERE statut='admin'");

		if($donnees=$rep->fetch())	//S'il y a un admin
		{?>
	<div class="boite">
		<h2>Administration</h2>
		<p>
			La base de donnée semble déjà avoir un admin.
			Nous n'allons donc pas en créer un supplémentaire.
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="creerBDD"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="supprimeInstall"/>
						<input type="submit" class="bouton" value="Suivant -->"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
		<?php }
		else	//Si pas d'admin
		{?>
	<div class="boite">
		<h2>Administration</h2>
		<p>
			Aucun administrateur n'est visible dans la base de donnée.
			Nous allons vous créer un profil administrateur.
		</p>
		<p>
			<form method="POST" action="">
				<table>
					<tr>
						<td><label for="admin_nom">Nom :</label></td>
						<td><input type="text" name="admin_nom" id="admin_nom" placeholder="(Obligatoire)" required/></td>
					</tr>
					<tr>
						<td><label for="admin_prenom">Prénom :</label></td>
						<td><input type="text" name="admin_prenom" id="admin_prenom" placeholder=""/></td>
					</tr>
					<tr>
						<td><label for="admin_login">Login :</label></td>
						<td><input type="text" name="admin_login" id="admin_login" placeholder="(Obligatoire)" required/></td>
					</tr>
					<tr>
						<td><label for="admin_mdp">Mot de passe :</label></td>
						<td><input type="password" name="admin_mdp" id="admin_mdp" placeholder="(Obligatoire)" required/></td>
					</tr>
					<tr>
						<td><label for="admin_mail">Email :</label></td>
						<td><input type="email" name="admin_mail" id="admin_mail" placeholder=""/></td>
					</tr>
				</table>

						<input type="hidden" name="etape" value="enregistreAdmin"/>
						<input type="submit" class="bouton" value="Suivant -->"/>
			</form>
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="creerBDD"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
		<?php }
}





// etape 9 : Confirme l'admin ===========================================
if($etape=="confirmeAdmin")
{?>
	<div class="boite">
		<h2>Administration</h2>
		<p>
			Le compte administrateur a bien été créé.
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="testAdmin"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="supprimeInstall"/>
						<input type="submit" class="bouton" value="Suivant -->"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
<?php }





// etape 10 : Suppression Fichier ===========================================
if($etape=="supprimeInstall")
{?>
	<div class="boite">
		<h2>Fin de l'installation</h2>
		<p>
			L'installation est maintenant terminée.
			Il reste une étape (qui n'est pas encore automatique...) : <span style="color:red;">Supprimer le fichier "install.php" de votre serveur</span>.
			Car sinon, des petits malins pourront l'utiliser pour modifier vos données.
		</p>
		<div class="boutons">
			<table><tr>
				<td>
					<form action="" method="POST" style="display:inline;">
						<input type="hidden" name="etape" value="testAdmin"/>
						<input type="submit" class="bouton" value="<-- Précédent"/>
					</form>
				</td>
				<td>
					<form action="." method="POST" style="display:inline;">
						<input type="submit" class="bouton" value="Lancer SkillCenter"/>
					</form>
				</td>
			</tr></table>
		</div>
	</div>
<?php }


?>

</body>
</html>
