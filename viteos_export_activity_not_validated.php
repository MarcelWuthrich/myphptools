<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Viteos Export Activity Not Validated</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body >

<ul>
  <li><a class="active" href="index.php">Tools</a></li>
  <li><a href="viteos.php">Viteos</a></li>
</ul>



<BR><form action="viteos_export_activity_not_validated.php" method="post">


<?php


// include 'class/class_display.php';
include 'class/class_activity_sheet.php';
include 'class/class_time_code.php';

if (!empty($_POST)) {
    $_POST = $_POST;
}


//echo '$_SERVER["SERVER_ADDR"]' . $_SERVER["SERVER_ADDR"] . '<BR>';

echo "<BR>Begin export<BR>";
echo date('Y-m-d H:i:s') . "<BR><BR>";

$myactivitysheet = new cl_activity_sheet;
$mytco = new cl_time_code;


try {
    $myactivities = $myactivitysheet->getActivity('2025-01-07','2025-01-07');
    foreach ($myactivities as $myactivity) {

        $myValidators = $myactivitysheet->getValidators($myactivity['ast_id']);
        if (count($myValidators) >= 2) continue;

        $myCode = $mytco->get_time_code_with_tco_id($myactivity['tco_id']);
        if (is_null($myCode[0]['tco_external_id'])) continue;
              
        echo $myactivity['ast_date'] . ';';
        echo $myactivity['ast_resource_name'] . ';';
        echo $myactivity['ast_amount'] . ';';
        echo $myactivity['ast_tco_name'] . ';';
        echo count($myValidators) . ';';
        echo '<br>';
    }
}
catch (PDOException $e) {
    echo "Failed: " . $e->getMessage() . '<BR>';
}




echo "<BR>export successfully terminated<BR>";
echo date('Y-m-d H:i:s') . "<BR>";




?>


<BR><BR>



</form>

</body>
</html>