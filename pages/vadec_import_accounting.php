<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Vadec Import Accounting</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body >

<ul>
  <li><a class="active" href="../index.php">Tools</a></li>
  <li><a href="clients.php">Clients</a></li>
  <li><a href="vadec.php">Vadec</a></li>
</ul>



<BR><form action="vadec_import_accounting.php" method="post">


<?php



include '../class/class_person.php';
include '../class/class_working_time.php';
include '../class/class_activity_counter.php';
include '../class/class_time_code.php';
include '../class/class_time_sheet.php';

 
if (!empty($_POST)) {
    $_POST = $_POST;
}


//echo '$_SERVER["SERVER_ADDR"]' . $_SERVER["SERVER_ADDR"] . '<BR>';

echo "<BR>Begin export<BR>";
echo date('Y-m-d H:i:s') . "<BR><BR>";




 // Chemin vers votre fichier CSV
$csv_file = '../files/vadec.csv';
//$csv_file = '2024-08-31 Export_Soldes_ProTime.csv';

// Initialisation du tableau pour stocker les données
$csv_data = array();

// Ouverture du fichier en lecture
if (($handle = fopen($csv_file, "r")) !== FALSE) {
    // Convertit automatiquement le flux du CSV depuis Windows-1252 vers UTF-8
    stream_filter_append($handle, 'convert.iconv.WINDOWS-1252/UTF-8');

    while (($line = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $csv_data[] = array(
            'Lastname'  => $line[0],
            'Firstname' => $line[1],
            'NrVadir'   => $line[2],
            'Counter'   => $line[3],
            'Amount'    => $line[4],
            'Date'      => $line[5]
        );
    }
    // Fermeture du fichier
    fclose($handle);
    echo "Input file read successfully<BR>";
} else {
    echo "Error opening file<BR>";
}

// echo $csv_data

$outfilename = "../files/vadec_insert_accounting.sql";
$outfile = fopen($outfilename, "w");
if ($outfile) {
    fclose($outfile);
}

$previous_per_id='';

$outfile = fopen($outfilename, "a+");

echo "Output file created successfully<BR>";

// Parcourir chaque ligne du tableau
foreach ($csv_data as $line) {
    
    $myperson = new cl_person;
    $mytimecode = new cl_time_code;
    $myactivitycounter = new cl_activity_counter;
    //$mytimesheet = new cl_time_sheet;
    $myworkingtime = new cl_working_time;

    
    try {
        //$myperson = $myperson->getPersonFromPersonalNumber($line['PersonalNumber']);
        // echo $line['Lastname'],' ',$line['Firstname'];
        $myperson = $myperson->getPersonFromPerExternalId($line['NrVadir']);
    }
    catch (PDOException $e) {
        echo "Failed: " . $e->getMessage() . '<BR>';
    }
    

    switch ($line['Counter']) {
    
        
        case 'Vacances':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Vacances');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Vacances');
            break;

        case 'Solde HB':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Solde HB');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Solde HB');
            break;

        case 'Heures sup >20h':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Heures supplémentaires (> 20h)');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Heures supplémentaires HB (> 20)');
            break;

        case 'Repos compensat':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Repos compensatoire');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Repos compensatoire');
            break;

        case 'Jours impôts':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Jours impôts');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Jours impôts');
            break;

        case 'Fériés':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Fériés');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Fériés');
            break;

        case 'Rattrapage':
            $myactivitycounter = $myactivitycounter->get_activity_counter_from_avc_name('Rattrapage');
            $mytimecode = $mytimecode->get_time_code_with_tco_name('Rattrapage');
            break;


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

    if ($previous_per_id <> $per_id) echo '<BR>';
   
    echo $per_name . ' ' . $per_firstname . ' (per_id : ' . $per_id . ') (user_name : ' .  ') (counter :  ' . $line['Counter'] . ') (amount : ' . $line['Amount'] . ')<BR>';
    $mytitle = '-- ' . $per_name . ' ' . $per_firstname . ' : ' . $line['Counter'];
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


    $aca_date_time = DateTime::createFromFormat('d.m.Y', $line['Date'])->format('Y-m-d');
    $todo_per_day = $myworkingtime[0]['wkt_weekly_hours'] * $myworkingtime[0]['wkt_activity_rate'] / 100 / 5;
    //$mytimesheet = $mytimesheet->getTimeSheetFromDatePerId($per_id,$aca_date_time);
    //$tst_theoretical_todo = $mytimesheet[0]['tst_theoretical_todo'];
    $amount_in_hour = intval(floatval($line['Amount'] * 3600000));
    $amount_in_day = intval(floatval($line['Amount'] * $todo_per_day));
    

    if ($line['Counter'] == 'Vacances') {
        if ($todo_per_day == 0) {
            echo 'error : todo_per_day = 0 --> cannot convert days in hours !!!<BR>';
            continue;
        }
        $amount = $amount_in_day;
        $aca_comment = "Ajout du solde dans " .  $mytimecode[0]['tco_name'] . " : " . number_format(floatval($amount / $todo_per_day ), 2) . " jour(s)";
    } else {
        $amount = $amount_in_hour;
        $aca_comment = "Ajout du solde dans " .  $mytimecode[0]['tco_name'] . " : " . number_format(floatval($amount / 3600000),2) . " heure(s)";
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


try {
    fclose($outfile);    
}
catch (Exception $e) {
    echo "Failed: " . $e->getMessage() . '<BR>';
}




echo "<BR>export successfully terminated<BR>";
echo date('Y-m-d H:i:s') . "<BR>";




?>


<BR><BR>



</form>

</body>
</html>