<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Viteos Import Accounting</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body >

<ul>
  <li><a class="active" href="index.php">Tools</a></li>
  <li><a href="viteos.php">Viteos</a></li>
</ul>



<BR><form action="viteos_import_accounting.php" method="post">


<?php

include 'class/class_display.php';

/*
include 'class/class_tracking.php';
include 'class/class_working_time.php';
*/

//$_POST["per_id"] = '000001-20170904-0000000110';
//$_POST["this_date"] = '2023-07-14';
 
if (!empty($_POST)) {
    $_POST = $_POST;
}







?>


<BR><BR>

<input type="submit" value="Import">

</form>

</body>
</html>