<div id="tab-home">

<h2>Page principale</h2>


<?php

if(!$_SESSION['id']) //Si pas connecté
{
	?>
	<p>Bienvenue sur ce site d'évaluation des compétences.<br/>
	<strong>Connectez-vous !</strong></p>
	<?php
}
else //Si connecté 
{
?>

En travaux... (ne regardez pas ce qu'il y a en dessous...)
<div width="50" height="50">
<canvas id="radar">
</canvas>
</div>




<?php
$requeteGroupes="SELECT nom
FROM
   (SELECT DISTINCT groupe
   FROM
      (
      SELECT DISTINCT competence FROM
         (SELECT DISTINCT indicateur FROM utilisateurs AS u JOIN liensClassesIndicateurs AS l ON l.classe=u.classe WHERE u.id=".$_SESSION['id'].") AS ul JOIN indicateurs as i
          ON ul.indicateur=i.id) as uli
   JOIN
      competences as c
   ON uli.competence=c.id) AS ulic
JOIN
   groupes_competences AS g
ON ulic.groupe=g.id";

$req=$bdd->prepare($requeteGroupes);
$req->execute(array('id'=>$_SESSION['id']));

$listeGroupes=array();
while($donnees=$req->fetch())
{
	array_push($listeGroupes,$donnees['nom']);
}
?>
<script>





var contenantRadar = $("#radar");	//Recupere le contenant (canvas)
var myChart = new Chart(contenantRadar, {
    type: 'radar',
    data: {
        labels: [<?php foreach($listeGroupes as $i) echo "'".$i."',";?>],
        datasets: [{
            label: 'Bilan des domaines',
            data: [<?php foreach($listeGroupes as $i) echo strval(rand(0,100)).",";?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>



<?php
}
?>

</div>
