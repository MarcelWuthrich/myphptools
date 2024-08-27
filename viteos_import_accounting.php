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
include 'class/class_time_sheet.php';

 
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
    echo "Input file read successfully";
} else {
    echo "Error opening file<BR>";
}


$outfilename = "insert_accounting.sql";
$outfile = fopen($outfilename, "w");
if ($outfile) {
    fclose($outfile);
}

echo "Output file created successfully";

// Parcourir chaque ligne du tableau
foreach ($csv_data as $line) {
    
    $myperson = new cl_person;
    $mytimecode = new cl_time_code;
    $myactivitycounter = new cl_activity_counter;
    $mytimesheet = new cl_time_sheet;
    $myworkingtime = new cl_working_time;



    try {
        $myperson = $myperson->getPersonFromPersonalNumber($line['PersonalNumber']);
    }
    catch (PDOException $e) {
        echo "Failed: " . $e->getMessage() . '<BR>';
    }
    
    

    switch ($line['Counter']) {
        
        case 'Heures BAL':
            $myactivitycounter = myactivitycounter->get_activity_counter_from_avc_name('Balance');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Balance (compteur)');
            break;

        case 'Heures SUP':
            $myactivitycounter = myactivitycounter->get_activity_counter_from_avc_name('Balance');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Balance (compteur)');
            break;

        case 'Pont':
            $myactivitycounter = myactivitycounter->get_activity_counter_from_avc_name('Récupération');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Récupération (système)');
            break;

        case 'Vacances':
            $myactivitycounter = myactivitycounter->get_activity_counter_from_avc_name('Vacances');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Vacances');
            break;

        case 'Unité Piquet':
            $myactivitycounter = myactivitycounter->get_activity_counter_from_avc_name('Piquet cumul');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Piquet cumul 50h (année en cours)');
            break;
            //exit;

        default:
            echo "Unknow counter : " . $line['Counter'] . "<BR>";
            break;
            //exit;

    }
    

    
    $mytest = DateTime::createFromFormat('d.m.Y', $line['Date'])->format('Y-m-d');
    $tst_theoretical_todo = $mytimesheet->getTimeSheetFromDatePerId($myperson[0]['per_id'],$mytest);
    

    $mySQLInsertCommand = "INSERT INTO vtm_activity_counter_accounting (";
    $mySQLInsertCommand .= "aca_id,";
    $mySQLInsertCommand .= "avc_id,";
    $mySQLInsertCommand .= "per_id,";
    $mySQLInsertCommand .= "tco_id,";
    $mySQLInsertCommand .= "wkt_id,";
    $mySQLInsertCommand .= "aca_date_time,";
    $mySQLInsertCommand .= "aca_type,";
    $mySQLInsertCommand .= "aca_amount,";
    $mySQLInsertCommand .= "aca_real_amount,";
    $mySQLInsertCommand .= "aca_series,";
    $mySQLInsertCommand .= "aca_comment,";
    $mySQLInsertCommand .= "aca_created_by,";
    $mySQLInsertCommand .= "aca_created_date) ";
    
    $mySQLInsertCommand .= "VALUES (";

    $mySQLInsertCommand .= "GetNextId(),";          // aca_id
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
    $mySQLInsertCommand .= "GetNextId(),"; // aca_series
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


echo "export successfully terminated<BR>";




?>


<BR><BR>



</form>

</body>
</html>