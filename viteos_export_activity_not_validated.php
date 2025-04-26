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
include 'class/class_person.php';
include 'class/class_department.php';

if (!empty($_POST)) {
    $_POST = $_POST;
}


//echo '$_SERVER["SERVER_ADDR"]' . $_SERVER["SERVER_ADDR"] . '<BR>';

echo "<BR>Begin export<BR>";
echo date('Y-m-d H:i:s') . "<BR><BR>";

$myactivitysheet = new cl_activity_sheet;
$mytco = new cl_time_code;
$myPerson = new cl_person;
$myDpt = new cl_department;


try {
    $myactivities = $myactivitysheet->getActivity('2025-01-01','2025-01-10');
    foreach ($myactivities as $myactivity) {

        // on arrête s'il y a plus de 2 validateurs
        $myValidators = $myactivitysheet->getValidators($myactivity['ast_id']);
        if (count($myValidators) >= 2) continue;

        // on arrête si c'est une activité propre à Vysual (hors Proconcept)
        $myCode = $mytco->get_time_code_with_tco_id($myactivity['tco_id']);
        if (is_null($myCode[0]['tco_external_id'])) continue;
              
        echo $myactivity['ast_date'] . ';';
        echo $myactivity['ast_resource_name'] . ';';
        echo $myactivity['ast_amount'] . ';';
        echo $myactivity['ast_tco_name'] . ';';
        echo count($myValidators) . ';';

        // responsable d'activité
        $activityResponsable = $myPerson->getPersonFromPerId($myCode[0]['per_id']);
        echo $activityResponsable[0]['per_name'] . ' ' . $activityResponsable[0]['per_firstname'] . ';';

        // responsable de département
        $dpt = $myDpt->getDepartmentFromPerId($myactivity['per_id']);
        $responsablePerId = $myDpt->getDepartmentResponsable($myactivity['per_id']);
        $myResponsable = new cl_person;
        
        $myResponsable = $myResponsable->getPersonFromPerId($responsablePerId[0]['responsable_per_id']);
        echo $myResponsable[0]['per_name'] . ' ' . $myResponsable[0]['per_firstname'] . ';';

        // validateur existant
        echo 'Validé par ' . $myValidators[0]['vas_created_by'] . ' le ' . date('d.m.Y', strtotime($myValidators[0]['vas_created_date'])) . ' à ' . date('H:i:s', strtotime($myValidators[0]['vas_created_date'])) . ';';


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