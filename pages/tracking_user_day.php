<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Tracking one user one day</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body >

<ul>
  <li><a class="active" href="../index.php">Tools</a></li>
  <li><a href="tracking.php">Tracking</a></li>
  <li><a href="tracking_user_day.php">Tracking 1 user 1 day</a></li>
</ul>



<BR><form action="tracking_user_day.php" method="post">


<?php


include 'class/class_tracking.php';
include 'class/class_display.php';
include 'class/class_working_time.php';



//$_POST["per_id"] = '000001-20170904-0000000110';
//$_POST["this_date"] = '2023-07-14';
 
if (!empty($_POST)) {
    $_POST = $_POST;
}


echo '<label for="this_date" size="50">date:</label><BR>';
if (empty($_POST["this_date"])) {
    echo '<input type="date" id="this_date" name="this_date" value="' . date("Y-m-d") . '" />';
}
else {
    echo '<input type="date" id="this_date" name="this_date" value="' . $_POST["this_date"] .  '" />';
}
echo '<BR><BR>';

/*
echo '<label for="per_id" size="50">per_id:</label><BR>';
if (empty($_POST["lov_per_id"])) {
    echo '<input type="text" id="per_id" name="per_id" value="" size="30"><br><br>';
}
else {    
    echo '<input type="text" id="per_id" name="per_id" value="' . $_POST["lov_per_id"] . '" size="30"><br><br>';

}
*/

echo '<label>Choix de l\'employé:</label><BR>';
echo '<select name="lov_per_id" id="lov_per_id">';
$myusers = new cl_working_time;

if (empty($_POST["this_date"])) {
    $allusers = $myusers->get_active_per_id_at_one_date(date("Y-m-d"));
}
else {
    $allusers = $myusers->get_active_per_id_at_one_date($_POST["this_date"]);
}

$allusers = $allusers;
foreach ($allusers as $user) {
    if ($_POST["lov_per_id"] ==   $user["per_id"]) {
        echo '<option value="' . $user["per_id"] . '" selected>' . $user["per_name"]  . ' ' . $user["per_firstname"] . '</option>';
    }
    else {
        echo '<option value="' . $user["per_id"] . '">' . $user["per_name"]  . ' ' . $user["per_firstname"] . '</option>';
    }
}
echo '</select>';


if (!empty($_POST)) {

    $mytrk  = new cl_tracking;
    $myresults = $mytrk->getTrackingFromDatePerId($_POST["lov_per_id"],$_POST["this_date"]);

    $mydisplay = new cl_display;
    $mydisplay->display_tracking_user_day($myresults);
    

    
}




?>


<BR><BR>

<input type="submit" value="Execute">

</form>

</body>
</html>