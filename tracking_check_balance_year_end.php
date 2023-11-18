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


$my0101date = '2023-01-01';
$my3112date = '2022-12-31';
$per_id = '000001-20181107-0000000072';
$avc_id = '000001-20150119-0000009694';     /* Vacances */


$mytst = new cl_time_sheet;
$mytst = $mytst->getTimeSheetFromDatePerId($per_id,$my0101date);
$my0101balancejson = $mytst[0]['tst_soldes'];
$mytst = null;

$mytst2 = new cl_time_sheet;
$mytst2 = $mytst2->getTimeSheetFromDatePerId($per_id,$my3112date);
$my3112balancejson = $mytst2[0]['tst_soldes'];
$mytst2 = null;

echo $my3112balancejson . '<BR><BR>';
echo $my0101balancejson . '<BR><BR>';

$my0101balancejsonarray = json_decode($my0101balancejson);
$my3112balancejsonarray = json_decode($my3112balancejson);
$myjson =$myjson;

echo '<br><br>';


foreach ($my3112balancejsonarray as $balances) {
    foreach ($balances as $balance) {
        $balance = $balance;
        if ($balance->avcId == $avc_id) {
            echo 'name : ' . $balance->name . '<BR>';
            echo 'avcId : ' . $balance->avcId . '<BR>';
            echo 'humanReadableAmount : ' . $balance->humanReadableAmount . '<BR>';
            echo 'amount : ' . $balance->amount . '<BR>';
            echo 'computeAmount : ' . $balance->computeAmount . '<BR>';
            echo '<br>';    
        }
    }
}

echo '<br><br>';

foreach ($my0101balancejsonarray as $balances) {
    foreach ($balances as $balance) {
        $balance = $balance;
        if ($balance->avcId == $avc_id) {
            echo 'name : ' . $balance->name . '<BR>';
            echo 'avcId : ' . $balance->avcId . '<BR>';
            echo 'humanReadableAmount : ' . $balance->humanReadableAmount . '<BR>';
            echo 'amount : ' . $balance->amount . '<BR>';
            echo 'computeAmount : ' . $balance->computeAmount . '<BR>';
            echo '<br>';    
        }
    }
}

$myaca = new cl_activity_counter;
$myholiday = $myaca->get_wkt_holiday_entitlement_by_year($per_id,$avc_id,'2023');
echo $myholiday[0][0];
$myholiday = '<br>' . $myholiday . '<br>';

/*
select * from vtm_activity_counter where avc_id = '000001-20150119-0000009694';
*/


//$_POST["per_id"] = '000001-20170904-0000000110';
//$_POST["this_date"] = '2023-07-14';
 
$_POST = $_POST;
 /*
echo '<label for="this_date" size="50">date:</label><BR>';
if (empty($_POST["this_date"])) {
    echo '<input type="date" id="this_date" name="this_date" value="' . date("Y-m-d") . '" />';
}
else {
    echo '<input type="date" id="this_date" name="this_date" value="' . $_POST["this_date"] .  '" />';
}
echo '<BR><BR>';

echo '<label for="per_id" size="50">per_id:</label><BR>';
if (empty($_POST["per_id"])) {
    echo '<input type="text" id="per_id" name="per_id" value="" size="30"><br><br>';
}
else {    
    echo '<input type="text" id="per_id" name="per_id" value="' . $_POST["per_id"] . '" size="30"><br><br>';

}



if (!empty($_POST)) {

    $mytrk  = new cl_tracking;
    $myresults = $mytrk->getTrackingFromDatePerId($_POST["per_id"],$_POST["this_date"]);

    $mydisplay = new cl_display;
    $mydisplay->display_tracking_user_day($myresults);
    

    
}

*/



/*

if (!empty($_POST))   {
    echo '<input type="date" id="this_date" name="this_date" value="' . $_POST["this_date"] .  '" />';
}
else {
    // echo '<input type="date" id="this_date" name="this_date" value="' . date("Y-m-d") . '" />';
    echo '<input type="date" id="this_date" name="this_date" value="2022-07-14" />';
}

*/


?>
<BR><BR>



<input type="submit" value="Execute">

</form>

</body>
</html>