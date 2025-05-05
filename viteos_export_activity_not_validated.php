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

// phpinfo();

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

$outfilename = "viteos activites sans validation.csv";
$outfile = fopen($outfilename, "w");
if ($outfile) {
    fwrite($outfile, "\xEF\xBB\xBF");  // <<< BOM UTF-8
    fclose($outfile);
}

$outfile = fopen($outfilename, "a+");

$myOutpuLine = 'Date;';
$myOutpuLine .= 'Personne;';
$myOutpuLine .= 'Durée;';
$myOutpuLine .= 'Activités;';
$myOutpuLine .= 'Nbre validation;';
$myOutpuLine .= 'Responsable activité;';
$myOutpuLine .= 'Responsable hiérarchique;';
$myOutpuLine .= 'Validation;';
if ($outfile) fwrite($outfile, $myOutpuLine . "\n");   


$outfilenameSQL = "viteos activation manuelle.sql";
$outfileSQL = fopen($outfilenameSQL, "w");
if ($outfileSQL) {
    fwrite($outfileSQL, "\xEF\xBB\xBF");  // <<< BOM UTF-8
    fclose($outfileSQL);
}

$outfileSQL = fopen($outfilenameSQL, "a+");

$myRHPerson = new cl_person;
$myPerId = $myRHPerson->getPersonFromNameFirstname('RH','auto-validation');
$per_id = $myPerId[0]['per_id'];



try {
    $myactivities = $myactivitysheet->getActivity('2025-01-01','2025-03-31');
    foreach ($myactivities as $myactivity) {

        // on arrête s'il y a plus de 2 validateurs
        $myValidators = $myactivitysheet->getValidators($myactivity['ast_id']);
        if (count($myValidators) >= 2) continue;

        // on arrête si l'activité a été refusée
        if (($myValidators[0]['vas_accepted'] == 0) and (count($myValidators) == 1)) continue;


        if ($myValidators[0]['vas_accepted'] == 0) continue;

        // on arrête si c'est une activité propre à Vysual (hors Proconcept)
        $myCode = $mytco->get_time_code_with_tco_id($myactivity['tco_id']);
        if (is_null($myCode[0]['tco_external_id'])) continue;

        echo $myactivity['ast_date'] . ';';
        echo $myactivity['ast_resource_name'] . ';';
        echo number_format($myactivity['ast_amount'] / 3600000, 2) . ';';
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
        if (count($myValidators) == 0) {
            echo ';';
        }
        else {
            //echo 'Validé par ' . $myValidators[0]['vas_created_by'] . ' le ' . date('d.m.Y', strtotime($myValidators[0]['vas_created_date'])) . ' à ' . date('H:i:s', strtotime($myValidators[0]['vas_created_date'])) . ';';
            echo 'Validé le ' . date('d.m.Y', strtotime($myValidators[0]['vas_created_date'])) . ' à ' . date('H:i:s', strtotime($myValidators[0]['vas_created_date'])) . ' par ' . $myValidators[0]['vas_created_by'] . ';';
        }
        echo '<br>';

        $myOutpuLine=$myactivity['ast_date'] . ';';
        $myOutpuLine .= $myactivity['ast_resource_name'] . ';';
        $myOutpuLine .= number_format($myactivity['ast_amount'] / 3600000, 2) . ';';
        $myOutpuLine .= $myactivity['ast_tco_name'] . ';';
        $myOutpuLine .= count($myValidators) . ';';
        $myOutpuLine .= $activityResponsable[0]['per_name'] . ' ' . $activityResponsable[0]['per_firstname'] . ';';
        $myOutpuLine .= $myResponsable[0]['per_name'] . ' ' . $myResponsable[0]['per_firstname'] . ';';
        if (count($myValidators) == 0) {
            $myOutpuLine .= ';';
        }
        else {
            //echo 'Validé par ' . $myValidators[0]['vas_created_by'] . ' le ' . date('d.m.Y', strtotime($myValidators[0]['vas_created_date'])) . ' à ' . date('H:i:s', strtotime($myValidators[0]['vas_created_date'])) . ';';
            $myOutpuLine .= 'Validé le ' . date('d.m.Y', strtotime($myValidators[0]['vas_created_date'])) . ' à ' . date('H:i:s', strtotime($myValidators[0]['vas_created_date'])) . ' par ' . $myValidators[0]['vas_created_by'] . ';';
        }

        if (count($myValidators) == 0) {
            $myOutpuLineSQL = 'INSERT into vtm_activity_sheet_validation (vas_id,ast_id,per_id,vas_accepted,vas_comment,vas_created_by,vas_created_date) VALUES ';
            $myOutpuLineSQL .= '(getnextid(),\'' . $myactivity['ast_id'] . '\',\'' . $per_id .  '\',1,\'validation auto\',\'admin\',NOW());';
            if ($outfileSQL) fwrite($outfileSQL, $myOutpuLineSQL . "\n");   
            $myOutpuLineSQL = 'INSERT into vtm_activity_sheet_validation (vas_id,ast_id,per_id,vas_accepted,vas_comment,vas_created_by,vas_created_date) VALUES ';
            $myOutpuLineSQL .= '(getnextid(),\'' . $myactivity['ast_id'] . '\',\'' . $per_id .  '\',1,\'validation auto\',\'admin\',NOW());';
                }
        else {
            $myOutpuLineSQL = 'INSERT into vtm_activity_sheet_validation (vas_id,ast_id,per_id,vas_accepted,vas_comment,vas_created_by,vas_created_date) VALUES ';
            $myOutpuLineSQL .= '(getnextid(),\'' . $myactivity['ast_id'] . '\',\'' . $per_id .  '\',1,\'validation auto\',\'admin\',NOW());';    
        }


        if ($outfile) fwrite($outfile, $myOutpuLine . "\n");   
        if ($outfileSQL) fwrite($outfileSQL, $myOutpuLineSQL . "\n");   

    }
}
catch (PDOException $e) {
    echo "Failed: " . $e->getMessage() . '<BR>';
}



fclose($outfile);
fclose($outfileSQL);


echo "<BR>export successfully terminated<BR>";
echo date('Y-m-d H:i:s') . "<BR>";




?>


<BR><BR>



</form>

</body>
</html>