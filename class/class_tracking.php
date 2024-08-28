<?php

class cl_tracking

{

    
    public function getTrackingFromDatePerId($per_id, $trk_booking_date_time_from) {
    
        
        // Connexion à la DB
        include_once 'constant.php';
        $myip = $_SERVER["SERVER_ADDR"] ;
        $user = USER;
        if (($myip == IP1HOME) or ($myip == IP2HOME)) {
              $pass = PASSWORDHOME;
              $dsn  = DSNHOME;
            }
        if (($myip == IP1WORK) or ($myip == IP2WORK)) {
            $pass = PASSWORDWORK;
            $dsn  = DSNWORK;  
        }
    
    
    

        try {
            $dbh = new PDO($dsn, $user, $pass);        
        }
        catch (PDOException $e) {
            // tenter de réessayer la connexion après un certain délai, par exemple
            echo 'Error by opening connection<BR>';
        }

        try {
            $sql  = 'select * from gbl_tracking where per_id = \'' . $per_id . '\' and date(trk_booking_date_time) = \'' . $trk_booking_date_time_from . '\' order by trk_id asc;';
            $dbh->beginTransaction();
            $sth = $dbh->query($sql);
            $rows = $sth->fetchAll();
            $dbh->rollBack();
            //$sth = null;
            //$dbh = nulll;
        }
        catch (PDOException $e) {
            echo "Failed: " . $e->getMessage() . '<BR>';
        }
        
        return $rows;
    }

    

        


}
    

?>