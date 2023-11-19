<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Check balance at year-end</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body >

<ul>
  <li><a class="active" href="index.php">Tools</a></li>
  <li><a href="tracking.php">Tracking</a></li>
  <li><a href="tracking_check_balance_year_end.php">Check balance at year-end</a></li>
</ul>



<BR><form action="tracking_user_day.php" method="post">


<?php


include 'class/class_time_sheet.php';
include 'class/class_display.php';
include 'class/class_activity_counter.php';
include 'class/class_person.php';
include 'class/class_working_time.php';


$my0101date = '2019-01-01';
$my3112date = '2018-12-31';
$mybalance0101 = '';
$mybalance3112 = '';
$holiday_entitlement_by_year = '';

// $per_id = '000001-20181107-0000000072';     /* Althaus Anita chez Zesar */ 

$per_id = '';
$avc_id = '000001-20150713-0000005285';     /* Vacances */

$myactiveworkingtime = new cl_working_time;
$allpersons = $myactiveworkingtime->get_active_per_id_between_date($my3112date,$my0101date);

foreach ($allpersons as $person) {

    $person = $person;
    $per_id = $person['per_id'];


        
    $myperson = new cl_person;
    $myperson = $myperson->getPersonFromPerId($per_id);

    $mytst = new cl_time_sheet;
    $mytst = $mytst->getTimeSheetFromDatePerId($per_id,$my0101date);
    $my0101balancejson = $mytst[0]['tst_soldes'];
    $my0101theoriticaltodo = $mytst[0]['tst_theoretical_todo'];
    $mytst = null;

    $mytst2 = new cl_time_sheet;
    $mytst2 = $mytst2->getTimeSheetFromDatePerId($per_id,$my3112date);
    $my3112balancejson = $mytst2[0]['tst_soldes'];
    $my3112theoriticaltodo = $mytst2[0]['tst_theoretical_todo'];
    $mytst2 = null;

    /*
    echo $my3112balancejson . '<BR><BR>';
    echo $my0101balancejson . '<BR><BR>';
    echo $my0101theoriticaltodo . '<BR><BR>';
    echo $my3112theoriticaltodo . '<BR><BR>';
    */

    $my0101balancejsonarray = json_decode($my0101balancejson);
    $my3112balancejsonarray = json_decode($my3112balancejson);
    $myjson =$myjson;


    foreach ($my3112balancejsonarray as $balances) {
        foreach ($balances as $balance) {
            $balance = $balance;
            if ($balance->avcId == $avc_id) {
                /*
                echo 'name : ' . $balance->name . '<BR>';
                echo 'avcId : ' . $balance->avcId . '<BR>';
                echo 'humanReadableAmount : ' . $balance->humanReadableAmount . '<BR>';
                echo 'amount : ' . $balance->amount . '<BR>';
                echo 'computeAmount : ' . $balance->computeAmount . '<BR>';
                echo '<br>';
                */
                $mybalance3112 = $balance->amount;
            }
        }
    }

    foreach ($my0101balancejsonarray as $balances) {
        foreach ($balances as $balance) {
            $balance = $balance;
            if ($balance->avcId == $avc_id) {
                /*
                echo 'name : ' . $balance->name . '<BR>';
                echo 'avcId : ' . $balance->avcId . '<BR>';
                echo 'humanReadableAmount : ' . $balance->humanReadableAmount . '<BR>';
                echo 'amount : ' . $balance->amount . '<BR>';
                echo 'computeAmount : ' . $balance->computeAmount . '<BR>';
                echo '<br>';    
                */
                $mybalance0101 = $balance->amount;
            }
        }
    }

    $myaca = new cl_activity_counter;
    $myholiday = $myaca->get_wkt_holiday_entitlement_by_year($per_id,$avc_id,'2023');
    $holiday_entitlement_by_year = $myholiday[0][0];


    echo 'Employé : ' . $myperson[0]['per_name'] . ' ' . $myperson[0]['per_firstname'] . '<br>';
    echo 'Solde au ' . $my3112date . ' : ' . $mybalance3112 . '<br>';
    echo 'Solde au ' . $my0101date . ' : ' . $mybalance0101 . '<br>';
    echo 'Augmentation : ' . $mybalance0101 - $mybalance3112 . '<br>';
    echo 'Droit aux vacances : ' . $holiday_entitlement_by_year . '<br>';

    $mydiff = $mybalance0101 - $mybalance3112 - $holiday_entitlement_by_year;
    if ($mydiff <> 0) {
        echo 'ATTENTION - Différence : ' . $mybalance0101 - $mybalance3112 - $holiday_entitlement_by_year . '<br><br>';
    } else {
        echo 'Différence : ' . $mybalance0101 - $mybalance3112 - $holiday_entitlement_by_year . '<br><br>';
    }
    


    
}





?>
<BR><BR>



<input type="submit" value="Execute">

</form>

</body>
</html>