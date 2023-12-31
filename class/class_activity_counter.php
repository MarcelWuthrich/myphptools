<?php

class cl_activity_counter

{

    
    public function get_wkt_holiday_entitlement_by_year($per_id, $avc_id, $year) {
    
        
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
            $sql = 'select sum(aca_amount), sum(aca_real_amount) from vtm_activity_counter_accounting ';
            $sql .= 'where avc_id = \'' . $avc_id .   '\' and date(aca_date_time) >= \'2020-01-01\' and date(aca_date_time) <= \'2020-12-31\' and ';
            $sql .= 'per_id = \'' . $per_id . '\' and aca_type = \'WORKING_TIME\';';



            $sth = $dbh->query($sql);
            $rows = $sth->fetchAll();
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