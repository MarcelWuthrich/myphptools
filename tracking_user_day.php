<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Tracking one user one day</title>
</head>

<body >

<a href="index.php">Tools</a>→<a href="tracking.php">Tracking</a><BR><BR>

<form action="tracking_user_day.php" method="post">


<?php


include 'class/class_tracking.php';



$_POST["per_id"] = '000001-20170904-0000000110';
$_POST["this_date"] = '2023-07-14';

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

    foreach($myresults as $myresult ){
        echo $myresult["trk_id"] . ' ';
        echo $myresult["trk_booking_date_time"] . ' ';

        switch($myresult["trk_action"]) {

            case 210:
                echo 'Stop Activity';
                break;
    
                case 240:
                    echo 'Start Activity';
                    break;
        
                    case 254:
                        echo 'Out';
                        break;
            
                        case 255:
                            echo 'In';
                            break;
                
                                        
        }
        echo '<BR>';
        
        //echo date_format($myresult["trk_booking_date_time"],"Y/m/d");
        //echo date_format($myresult["trk_booking_date_time"],"H:i:s") . '<BR>';
        
    }

}




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