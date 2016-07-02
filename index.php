<?php
include_once('./sources/PHP/options.php');
include_once('./sources/PHP/fonctions.php');



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
        <title>Skillcenter</title>
		<link rel="stylesheet" href="./sources/style/style.css" />
		<link rel="stylesheet" href="./sources/style/styleCompetences.css" />
		<link rel="stylesheet" href="./sources/JS/libraries/jquery-ui/jquery-ui.css">
		<script type="text/javascript" src="./sources/JS/libraries/jquery-ui/external/jquery/jquery.js"></script>
		<script type="text/javascript" src="./sources/JS/libraries/jquery-ui/jquery-ui.min.js"></script>

		<script type="text/javascript" src="./sources/JS/fonctions.js"></script>

		<script type="text/javascript" src="./sources/JS/actionsEvenements.js"></script>


		<?php if($_SESSION["id"]>0) { //Si connecté ?>
		<script type="text/javascript" src="./sources/JS/fonctions_notation.js"></script>
		<script type="text/javascript" src="./sources/JS/actionsEvenements_notation.js"></script>
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
		</script>
    </head>

	<body style="display:none;">
	
		<?php
			if($messageRetour!="" && 0)
			{
				echo "<div id=\"messageRetour\">".$messageRetour."</div>";
			}
		?>
	
		<?php include("./sources/PHP/entete.php");?>
		
		
		<div id="contenu">
			<div id="tab-onglets">
				<ul>
					<li><a href="#tab-home"><img src="./sources/images/home.png" alt="[Home]"/><br/>Home</a></li>
					<?php if($_SESSION['statut']=="admin") echo '<li><a href="#tab-users"><img src="./sources/images/icone-user.png"/><br/>Utilisateurs</a></li>';?>
					
					<?php if($_SESSION['id']) echo '<li><a href="#tab-notation"><img src="./sources/images/icone-checklist.png"/><br/>Notation</a></li>';?>
					
					<?php if($_SESSION['statut']=="admin") echo '<li><a href="#tab-competences"><img src="./sources/images/icone-checklist-edit.png"/><br/>Compétences</a></li>';?>
				</ul>
				<div id="tab-home">
					Home
				</div>
				
				<?php	if($_SESSION['statut']=="admin")	//Si admin...
					include("./sources/PHP/users.php");	//Administration utilisateur
				
					if($_SESSION['id'])	//Si connecté
					include("./sources/PHP/notation.php"); //Notation
				
					if($_SESSION['statut']=="admin")	//Si admin
					include("./sources/PHP/competences.php");//Administration Compétences



				?>
	
			</div>
		</div>

	

		<?php include('./sources/PHP/boites.php');?>
		
		<footer>
			<!-- Placez ici le contenu du pied de page -->
		</footer>
	</body>
</html>
