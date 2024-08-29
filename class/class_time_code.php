<?php

class cl_time_code

{

    
    public function get_time_code_with_tco_name($tco_name) {
    
        
        // Connexion à la DB
        include_once 'constant.php';
        // Define database connexion
        $myip = $_SERVER["SERVER_ADDR"] ;
        $user = USER;
        if ($myip == IP1HOME) {
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
            // tenter de réessayer la connexion après un certain délai, par exemple
            echo 'Error by opening connection<BR>';
        }

        try {
            
            $sql = 'SELECT * FROM vtm_time_code WHERE tco_name = \'' . $tco_name . '\' LIMIT 1;';

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