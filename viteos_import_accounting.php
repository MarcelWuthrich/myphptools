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
include 'class/class_person.php';
include 'class/class_activity_counter.php';

/*
include 'class/class_tracking.php';
include 'class/class_working_time.php';
*/

//$_POST["per_id"] = '000001-20170904-0000000110';
//$_POST["this_date"] = '2023-07-14';
 
if (!empty($_POST)) {
    $_POST = $_POST;
}


$myperson = new cl_person;
$per_id = '000001-20150925-0000005157';
$myperson = $myperson->getPersonFromPerId($per_id);


// Chemin vers votre fichier CSV
$csv_file = '2024-04-25 Export_Soldes_ProTime.csv';

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
    echo "Error opening file";
}

$myperson = new cl_person;

// Boucle infinie
while (true) {
    // Parcourir chaque ligne du tableau
    foreach ($csv_data as $line) {
        // Afficher les valeurs

        
        $myperson = $myperson->getPersonFromPersonalNumber($line['PersonalNumber']);
        
        $mycounter = new cl_activity_counter;
        switch ($line['Counter']) {
            
            case 'Heures BAL':
                $mycounter = $mycounter->get_activity_counter_from_avc_name('Balance');
                break;

            case 'Pont':
                $mycounter = $mycounter->get_activity_counter_from_avc_name('Récupération');
                break;

            case 'Vacances':
                $mycounter = $mycounter->get_activity_counter_from_avc_name('Vacances');
                break;

            case 'Unité Piquet':
                $mycounter = $mycounter->get_activity_counter_from_avc_name('Piquet cumul');
                break;
                    
        }
        


        echo "PersonalNumber: " . $line['PersonalNumber'] . "<br>";
        echo "Counter: " . $line['Counter'] . "<br>";
        echo "Amount: " . $line['Amount'] . "<br>";
        echo "Date: " . $line['Date'] . "<br><br>";

        $mySQLInsertCommand = "INSERT INTO vtm_activity_counter_accounting ";
        $mySQLInsertCommand .= "(aca_id,avc_id,per_id,aca_date_time,aca_type,aca_amount,aca_real_amount,aca_comment,aca_created_by,aca_created_date) ";
        $mySQLInsertCommand .= "VALUES (";
        $mySQLInsertCommand .= "GetNextId(),"; // aca_id
        $mySQLInsertCommand .= "'" . $mycounter[0]['avc_id'] . "',"; // avc_id
        $mySQLInsertCommand .= "'" . $myperson[0]['per_id'] . "',"; // per_id
        $mySQLInsertCommand .= "STR_TO_DATE('" . $line['Date'] . "', '%d.%m.%Y'),"; // aca_date_time
        $mySQLInsertCommand .= "'TRANSFER',"; // aca_type
        $mySQLInsertCommand .= floatval($line['Amount'] * 3600000) . ","; // aca_amount
        $mySQLInsertCommand .= floatval($line['Amount'] * 3600000) . ","; // aca_real_amount
        $mySQLInsertCommand .= "'Ajout du solde (" . $mycounter[0]['avc_name'] . ")',"; // aca_comment
        $mySQLInsertCommand .= "'admin',";  // created by
        $mySQLInsertCommand .= "NOW()";  // created date
        $mySQLInsertCommand .= ");";
        $mySQLInsertCommand .= "<br><br>";
        echo $mySQLInsertCommand;
        $mySQLInsertCommand = $mySQLInsertCommand;
    }

    // Ajouter une petite pause pour éviter que le script ne surconsomme les ressources
    sleep(1);
}







?>


<BR><BR>

<input type="submit" value="Import">

</form>

</body>
</html>