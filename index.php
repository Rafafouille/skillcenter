<?php


include_once('./sources/PHP/options.php');
include_once('./sources/PHP/fonctions.php');

$VERSION="16.10.2";	//N° de la version

initSession();
connectToBDD();



$action="";
if(isset($_POST['action']))	$action=$_POST['action'];
$loginBoxLogin="";
if(isset($_POST['loginBox-login']))	$loginBoxLogin=$_POST['loginBox-login'];
$loginBoxPwd="";
if(isset($_POST['loginBox-pwd']))	$loginBoxPwd=$_POST['loginBox-pwd'];
$tabDefaut=0;
if(isset($_GET["tab"])) $tabDefaut=intval($_GET['tab']);
if(isset($_POST["tab"])) $tabDefaut=intval($_POST['tab']);
$messageRetour="";//Message en renvoyer apres une action
if(isset($_GET["message"])) $messageRetour=$_GET['message'];
if(isset($_POST["message"])) $messageRetour=$_POST['message'];



include("./sources/PHP/actions.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <!-- En-tête de la page -->
        <meta charset="utf-8" />
        <title><?php echo $TITRE_PAGE;?></title>
		<link rel="stylesheet" href="./sources/style/style.css" />
		<?php if($_SESSION["statut"]=="admin") { ?>
		<link rel="stylesheet" href="./sources/style/styleUsers.css" />
		<?php }
		if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['statut']=="autoeval") { ?>
		<link rel="stylesheet" href="./sources/style/styleHistorique.css" />
		<?php } ?>
		<link rel="stylesheet" href="./sources/style/styleParametres.css" />
		<link rel="stylesheet" href="./sources/style/styleHome.css" />
		<link rel="stylesheet" href="./sources/style/styleBilan.css" />
		
		<!-- JQUERY -->
		<link rel="stylesheet" href="./sources/JS/libraries/jquery-ui/jquery-ui.css">
		<script type="text/javascript" src="./sources/JS/libraries/jquery-ui/external/jquery/jquery.js"></script>
		<script type="text/javascript" src="./sources/JS/libraries/jquery-ui/jquery-ui.min.js"></script>
		
		<!-- CHART.JS (pour les graphiques) + Moment.js ---------->
		<script type="text/javascript" src="./sources/JS/libraries/chartjs/Chart.min.js"></script>
		<!--<script type="text/javascript" src="./sources/JS/libraries/momentjs/moment.js"></script>-->

		<!-- Menus déroulants -->
		<link type="text/css" rel="stylesheet" href="./sources/JS/libraries/selectBoxIt/jquery.selectBoxIt.css" />
		<script src="./sources/JS/libraries/selectBoxIt/jquery.selectBoxIt.min.js"></script>


		<script type="text/javascript" src="./sources/JS/fonctions.js"></script>
		<script type="text/javascript" src="./sources/JS/actionsEvenements.js"></script>


		<?php if($_SESSION["id"]>0) { //Si connecté ?>
		<script type="text/javascript" src="./sources/JS/fonctions_bilan.js"></script>
		<script type="text/javascript" src="./sources/JS/actionsEvenements_bilan.js"></script>
		<script type="text/javascript" src="./sources/JS/fonctions_home.js"></script>
		<?php }

		if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['statut']=="autoeval") {//Si admin ou prof ?>
		<script type="text/javascript" src="./sources/JS/actionsEvenements_historique.js"></script>
		<?php }

		if($_SESSION["statut"]=="admin") { //Si admin?>
		<script type="text/javascript" src="./sources/JS/fonctions_competences.js"></script>
		<script type="text/javascript" src="./sources/JS/actionsEvenements_utilisateurs.js"></script>
		<script type="text/javascript" src="./sources/JS/actionsEvenements_competences.js"></script>
		<?php } ?>


		<script type="text/javascript" src="./sources/JS/main.js"></script>
		<script>
			listeOnglets=Array();	//Liste des onglets dans un tableau
			tabDefaut=<?php echo $tabDefaut;?>;	//Onglet actif par défaut
			messageRetour="<?php echo $messageRetour;?>";	//Message retour (passé en POST ou GET) En voie de disparition
			ID_COURANT=<?php echo $_SESSION['id'];?>;	//ID de l'utilisateur
			STATUT="<?php echo $_SESSION['statut'];?>";	//Statut de l'utilisateur
			CONNECTE=<?php if($_SESSION['id']) echo "true"; else echo "false";?>;	//Etat de la session (connecté ou pas ?)
			NB_NIVEAUX_MAX=<?php echo $NB_NIVEAUX_MAX;?>;	//Niveau max que peut avoir un indicateur
			
			ADMIN_COMPETENCES_LOADED=false;	//Variable globale qui dit si la page "cometences (admin)" a deja été au moins une fois chargée
			NOTATION_LOADED=false;	//Variable globale qui dit si la page "cometences (admin)" a deja été au moins une fois chargée
			NOTATION_REDESSINE_DE_ZERO=true;
			TIMEOUT_RELANCE=10;//TEMPS ESTIME AVANT QUE LE PROGRAMME SE DECONNECTE TOUT SEUL (en min)

			NOMS_NIVEAUX=[<?php for($i=1;$i<=$NB_NIVEAUX_MAX;$i++)
						{
								echo "[";
								for($j=0;$j<=$i;$j++)
								{
										echo "'".$INTITULES_NIVEAUX_CRITERES[$i-1][$j]."'";
										if($j!=$i)
											echo ",";
								}
								echo "]";
								if($i!=$NB_NIVEAUX_MAX)
									echo ",";
						}
 ?>];
			AUTORISE_CONTEXT=<?php if($AUTORISE_CONTEXT) echo "true"; else echo "false";?>;
			AUTORISE_COMMENTAIRES=<?php if($AUTORISE_COMMENTAIRES) echo "true"; else echo "false";?>;
		</script>
    </head>

	<body style="display:none;">
		<?php include("./sources/PHP/entete.php");?>
		
		
		<div id="contenu">
			<div id="tab-onglets">
				<ul>
					<li><a href="#tab-home"><img src="./sources/images/home.png" alt="[Home]"/><br/>Home</a></li>
					<?php if($_SESSION['statut']=="admin") echo '<li><a href="#tab-users"><img src="./sources/images/icone-user.png"/><br/>Utilisateurs</a></li>';?>
					
					<?php if($_SESSION['id']) echo '<li><a href="#tab-notation"><img src="./sources/images/icone-checklist.png"/><br/>Bilan</a></li>';?>
					
					<?php if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['statut']=="autoeval") echo '<li><a href="#tab-historique"><img src="./sources/images/icone-historique.png"/><br/>Historique</a></li>';?>
					
					<?php if($_SESSION['statut']=="admin") echo '<li><a href="#tab-competences"><img src="./sources/images/icone-checklist-edit.png"/><br/>Paramétrage</a></li>';?>
				</ul>
				
				
				<?php

					include("./sources/PHP/home.php");

					if($_SESSION['statut']=="admin")	//Si admin...
					include("./sources/PHP/users.php");	//Administration utilisateur
				
					if($_SESSION['id'])	//Si connecté
					include("./sources/PHP/notation.php"); //Notation

					if($_SESSION['statut']=="admin" || $_SESSION['statut']=="evaluateur" || $_SESSION['statut']=="autoeval")
					include("./sources/PHP/historique.php");//Historique de notation
				
					if($_SESSION['statut']=="admin")	//Si admin
					include("./sources/PHP/competences.php");//Administration Compétences


				?>
	
			</div>
		</div>

	

		<?php
		//Boites 
		include('./sources/PHP/boites.php');
		
		//Listes de données pour les inputs
		include("./sources/PHP/listes_input.php");?>
		<footer>
			<!-- Placez ici le contenu du pied de page -->
		</footer>

		<?php include("./sources/PHP/barre_notification.php");?>
	</body>
</html>
