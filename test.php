<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Test</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body >

<ul>
  <li><a class="active" href="index.php">Tools</a></li>
  <li><a href="tracking.php">Tracking</a></li>
  <li><a href="tracking_check_balance_year_end.php">Check balance at year-end</a></li>
</ul>



<BR><form action="test.php" method="post">


<?php
{

        include_once 'constant.php';

        $myip = $_SERVER["SERVER_ADDR"] ;
        $user = USER;

        if (($myip == IP1HOME) or ($myip == IP2HOME)) {
          $pass = PASSWORDHOME;
          $dsn  = DSNHOME;
        }

        if (($myip == IP1WORK)) {
          $pass = PASSWORDWORK;
          $dsn  = DSNWORK;  
        }

        try {

          $dbh = new PDO($dsn, $user, $pass);

        }
        catch (PDOException $e) {
            echo "Failed: " . $e->getMessage() . '<BR>';
        }

        try {
            
            $sql = 'select ety_name from gbl_entity;';

            $sth = $dbh->query($sql);
            $rows = $sth->fetchAll();
        }
        catch (PDOException $e) {
            echo "Failed: " . $e->getMessage() . '<BR>';
        }
      echo $rows[1]['ety_name'];

    
}





?>
<BR><BR>



<input type="submit" value="Execute">

</form>

</body>
</html>