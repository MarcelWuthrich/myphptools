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
  <li><a href="workflow.php">Change one model with another</a></ki>
</ul>


<BR>


<?php


include '../class_workflow.php';
//include 'class_timecode.php';
//include 'classe_person.php';

$mywkf = new cl_workflow;

$test = $mywkf->getWorkflowFromName('ABS');

var_dump($test);



?>


</body>
</html>