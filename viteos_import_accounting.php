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

echo "<BR>Begin export<BR>";
echo date('Y-m-d H:i:s') . "<BR><BR>";




 // Chemin vers votre fichier CSV
$csv_file = '2024-08-26 Export_Soldes_ProTime_mini.csv';
//$csv_file = '2024-08-26 Export_Soldes_ProTime.csv';

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
    echo "Input file read successfully<BR>";
} else {
    echo "Error opening file<BR>";
}


$outfilename = "insert_accounting.sql";
$outfile = fopen($outfilename, "w");
if ($outfile) {
    fclose($outfile);
}

$previous_per_id='';

$outfile = fopen($outfilename, "a+");

echo "Output file created successfully<BR>";

echo "<BR>";
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
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Balance');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Balance (compteur)');
            break;

        case 'Heures SUP':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Balance');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Balance (compteur)');
            break;

        case 'Pont':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Récupération');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Récupération (système)');
            break;

        case 'Vacances':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Vacances');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Vacances');
            break;

        case 'Unité Piquet':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Piquet cumul 50h (année en cours)');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Piquet cumul 50h (année en cours)');
            break;
            //exit;

        default:
            echo "Unknow counter : " . $line['Counter'] . "<BR>";
            break;
            //exit;

    }
    
    $avc_id = $myactivitycounter[0]['avc_id'];
    $tco_id = $mytimecode[0]['tco_id'];
    $per_id = $myperson[0]['per_id'];
    $per_name = $myperson[0]['per_name'];
    $per_firstname = $myperson[0]['per_firstname'];

   
    echo $per_name . ' ' . $per_firstname . ' (per_id : ' . $per_id . ') (user_name : ' . $line['PersonalNumber'] . ') (counter :  ' . $line['Counter'] . ') (amount : ' . $line['Amount'] . ')<BR>';
    $mytitle = '// ' . $per_name . ' ' . $per_firstname . ' : ' . $line['Counter'];
    if ($outfile) fwrite($outfile, "\n" . $mytitle . "\n");

    //si le solde est 0, on ne fait rien
    if ($line['Amount'] == 0) {
        echo $per_name . ' ' . $per_firstname . ' : ' . $line['Counter'] . ' value is 0, nothing done<BR>';
        continue;
    }

    $myworkingtime = $myworkingtime->get_active_wkt_from_per_id($per_id);
    $wkt_id = $myworkingtime[0]['wkt_id'];
    $wkt_parent_id = $myworkingtime[0]['parent_id'];
    $myparentworkingtime = new cl_working_time;
    $myparentworkingtime = $myparentworkingtime->get__wkt_from_wkt_id($wkt_parent_id);
    $wkt_model_name = $myparentworkingtime[0]['wkt_model_name'];

    //si c'est un modèle de contrat pour les intérimaire et que ce sont les vacances ou le pont, on ne fait rien.
    if (stripos($wkt_model_name, 'intérimaire') !== false && (($line['Counter'] == 'Pont') || ($line['Counter'] == 'Vacances'))) {
        echo $per_name . ' ' . $per_firstname . ' : ' . $line['Counter'] . ' not for temporary employee, nothing done<BR>';
        continue;
    }
        

    $aca_date_time = DateTime::createFromFormat('d.m.Y', $line['Date'])->format('Y-m-d');
    $mytimesheet = $mytimesheet->getTimeSheetFromDatePerId($per_id,$aca_date_time);
    $tst_theoretical_todo = $mytimesheet[0]['tst_theoretical_todo'];
    $amount_in_hour = intval(floatval($line['Amount'] * 3600000));
    $amount_in_day = intval(floatval($line['Amount'] * $tst_theoretical_todo));
    
    if ($line['Counter'] == 'Vacances') {
        if ($tst_theoretical_todo == 0) {
            echo 'error : tst_theoretical_todo = 0 --> cannot convert days in hours !!!<BR>';
            continue;
        }
        $amount = $amount_in_day;
        $aca_comment = "Ajout du solde de " . $line['Counter'] . " dans " .  $mytimecode[0]['tco_name'] . " : " . number_format(floatval($amount / $tst_theoretical_todo ), 2) . " jour(s)";
    } else {
        $amount = $amount_in_hour;
        $aca_comment = "Ajout du solde de " . $line['Counter'] . " dans " .  $mytimecode[0]['tco_name'] . " : " . number_format(floatval($amount / 3600000),2) . " heure(s)";
    }   
    
    
   
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

    $mySQLInsertCommand .= "GetNextId(),";              // aca_id
    $mySQLInsertCommand .= "'" . $avc_id . "',";        // avc_id
    $mySQLInsertCommand .= "'" . $per_id . "',";        // per_id
    $mySQLInsertCommand .= "'" . $tco_id . "',";        // tco_id
    $mySQLInsertCommand .= "'" . $wkt_id . "',";        // wkt_id
    $mySQLInsertCommand .= "'" . $aca_date_time . "',"; // aca_date_time
    $mySQLInsertCommand .= "'USER',";                   // aca_type
    $mySQLInsertCommand .= $amount . ",";               // aca_amount
    $mySQLInsertCommand .= $amount . ",";               // aca_real_amount
    $mySQLInsertCommand .= "GetNextId(),";              // aca_series
    $mySQLInsertCommand .= "'" . $aca_comment . "',";   // aca_comment
    $mySQLInsertCommand .= "'admin',";                  // created by
    $mySQLInsertCommand .= "NOW()";                     // created date
    $mySQLInsertCommand .= ");";

   
    
    

    if ($outfile) fwrite($outfile, $mySQLInsertCommand . "\n");
        
    $previous_per_id = $per_id;
    
 
 
}

fclose($outfile);

echo "<BR>export successfully terminated<BR>";
echo date('Y-m-d H:i:s') . "<BR>";




?>


<BR><BR>



</form>

</body>
</html>