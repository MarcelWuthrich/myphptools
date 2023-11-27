<?php

class cl_working_time

{


    public function get_active_per_id_between_date($start_date, $end_date) {
    
        
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

        TRY {
            $dbh = new PDO($dsn, $user, $pass);
        }
        catch (PDOException $e) {
            // tenter de réessayer la connexion après un certain délai, par exemple
            echo 'Error by opening connection<BR>';
        }

        try {
            
            $sql = 'select per_id from vtm_working_time where wkt_start_date <= \'' . $start_date . '\' ';
            $sql .= 'and  (wkt_end_date >=\'' . $end_date . '\' or wkt_end_date is null);';

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

    
    public function get_active_per_id_at_one_date($this_date) {
    
        
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

        TRY {
            $dbh = new PDO($dsn, $user, $pass);
        }
        catch (PDOException $e) {
            // tenter de réessayer la connexion après un certain délai, par exemple
            echo 'Error by opening connection<BR>';
        }

        try {
            
            //$sql = 'select per_id from vtm_working_time where wkt_start_date <= \'' . $this_date . '\' ';
            //$sql .= 'and  (wkt_end_date >=\'' . $this_date . '\' or wkt_end_date is null);';

            $sql = 'select wkt.per_id,per.per_name,per.per_firstname from vtm_working_time as wkt inner join gbl_person as per on wkt.per_id = per.per_id ';
            $sql .= 'and  (wkt_end_date >=\'' . $this_date . '\' or wkt_end_date is null) order by per.per_name,per.per_firstname asc;';

        
        ;

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