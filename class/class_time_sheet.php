<?php

class cl_time_sheet

{

    
    public function getTimeSheetFromDatePerId($per_id, $tst_date) {
    
        
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
            $sql = 'select * from vtm_timesheet where tst_date = \'' . $tst_date . '\' and per_id = \'' . $per_id . '\';';
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