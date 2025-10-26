<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Workflow</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body >

<ul>
  <li><a class="active" href="../index.php">Tools</a></li>
  <li><a href="workflow.php">Workflow</a></ki>
  <li><a href="workflow.php">Change time code in start context</a></ki>
</ul>


<BR>


<?php


include '../class/class_workflow.php';
//include 'class_timecode.php';
//include 'classe_person.php';

$mywkf = new cl_workflow;

$allwkf = $mywkf->getWorkflowFromName('ABS');


// Parcourir chaque ligne du tableau
foreach ($allwkf as $line) {
        echo $line['wkf_name'] . "<br>";
}


// var_dump($allwkf);



?>


</body>
</html>