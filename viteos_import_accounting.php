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

// include 'class/class_display.php';
include 'class/class_person.php';
include 'class/class_working_time.php';
include 'class/class_activity_counter.php';
include 'class/class_time_code.php';

 
if (!empty($_POST)) {
    $_POST = $_POST;
}



 // Chemin vers votre fichier CSV
$csv_file = '2024-08-26 Export_Soldes_ProTime_mini.csv';

// Initialisation du tableau pour stocker les données
$csv_data = array();

// Ouverture du fichier en lecture
if (($handle = fopen($csv_file, "r")) !== FALSE) {
    // Parcourir chaque ligne du fichier
    while (($line = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Ajouter chaque ligne au tableau
        $csv_data[] = array(
            'PersonalNumber' => $line[0],
            'Counter' => $line[1],
            'Amount' => $line[2],
            'Date' => $line[3]
        );
    }
    // Fermeture du fichier
    fclose($handle);
    // echo "File read successfully";
} else {
    echo "Error opening file<BR>";
}


$outfilename = "insert_accounting.sql";
$outfile = fopen($outfilename, "w");
if ($outfile) {
    fclose($outfile);
}


// Parcourir chaque ligne du tableau
foreach ($csv_data as $line) {
    
    $myperson = new cl_person;

    try {
        $myperson = $myperson->getPersonFromPersonalNumber($line['PersonalNumber']);
    }
    catch (PDOException $e) {
        echo "Failed: " . $e->getMessage() . '<BR>';
    }
    
    
    $mytimecode = new cl_time_code;
    $mycounter = new cl_activity_counter;

    switch ($line['Counter']) {
        
        case 'Heures BAL':
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Balance (compteur)');
            break;

        case 'Heures SUP':
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Balance (compteur)');
            break;

        case 'Pont':
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Récupération (système)');
            break;

        case 'Vacances':
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Vacances');
            break;

        case 'Unité Piquet':
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Piquet cumul 50h (année en cours)');
            break;
            //exit;

        default:
            echo "Unknow counter : " . $line['Counter'] . "<BR>";
            break;
            //exit;

    }
    

/*
    echo "PersonalNumber: " . $line['PersonalNumber'] . "<br>";
    echo "Counter: " . $line['Counter'] . "<br>";
    echo "Amount: " . $line['Amount'] . "<br>";
    echo "Date: " . $line['Date'] . "<br><br>";
*/

    $myworkingtime = new cl_working_time;

    $mySQLInsertCommand = "INSERT INTO vtm_activity_counter_accounting ";
    $mySQLInsertCommand .= "(aca_id,avc_id,per_id,tco_id,wkt_id,aca_date_time,aca_type,aca_amount,aca_real_amount,aca_comment,aca_created_by,aca_created_date) ";
    $mySQLInsertCommand .= "VALUES (";
    $mySQLInsertCommand .= "GetNextId(),"; // aca_id
    $mySQLInsertCommand .= "'" . $mytimecode[0]['avc_id'] . "',"; // avc_id
    $mySQLInsertCommand .= "'" . $myperson[0]['per_id'] . "',"; // per_id
    $mySQLInsertCommand .= "'" . $mytimecode[0]['tco_id'] . "',"; // tco_id
    $per_id = $myperson[0]['per_id'];
    $wkt  = $myworkingtime->get_active_wkt_from_per_id($per_id);
    $mySQLInsertCommand .= "'" . $wkt[0]['wkt_id'] . "',"; // wkt_id
    $mySQLInsertCommand .= "STR_TO_DATE('" . $line['Date'] . "', '%d.%m.%Y'),"; // aca_date_time
    $mySQLInsertCommand .= "'USER',"; // aca_type
    $mySQLInsertCommand .= floatval($line['Amount'] * 3600000) . ","; // aca_amount
    $mySQLInsertCommand .= floatval($line['Amount'] * 3600000) . ","; // aca_real_amount
    // $mySQLInsertCommand .= "'Ajout du solde (" . $line['Counter'] . " dans " . $mycounter[0]['avc_name'] . ")',"; // aca_comment
    $mySQLInsertCommand .= "'Ajout du solde (" . $line['Counter'] . " dans " . $mytimecode[0]['tco_name'] . ") : " . floatval($line['Amount']) .  " h',"; // aca_comment
    $mySQLInsertCommand .= "'admin',";  // created by
    $mySQLInsertCommand .= "NOW()";  // created date
    $mySQLInsertCommand .= ");";
    echo $mySQLInsertCommand . "<br><br>";

    $outfile = fopen($outfilename, "a+");
    if ($outfile) {
        fwrite($outfile, $mySQLInsertCommand . "\n");
        fclose($outfile);
    }
    
    
 
}







?>


<BR><BR>



</form>

</body>
</html>