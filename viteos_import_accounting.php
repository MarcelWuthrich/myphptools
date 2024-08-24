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
            'ID' => $line[0],
            'Libelle' => $line[1],
            'Montant' => $line[2],
            'Date' => $line[3]
        );
    }
    // Fermeture du fichier
    fclose($handle);
    // echo "File read successfully";
} else {
    echo "Error opening file";
}


// Boucle infinie
while (true) {
    // Parcourir chaque ligne du tableau
    foreach ($csv_data as $line) {
        // Afficher les valeurs
        echo "ID: " . $line['ID'] . "<br>";
        echo "Libellé: " . $line['Libelle'] . "<br>";
        echo "Montant: " . $line['Montant'] . "<br>";
        echo "Date: " . $line['Date'] . "<br><br>";
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