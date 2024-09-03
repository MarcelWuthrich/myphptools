<?php

class cl_activity_counter

{

    
    public function get_wkt_holiday_entitlement_by_year($per_id, $avc_id, $year) {
    
        
        // Connexion à la DB
        include_once 'constant.php';

        // Define database connexion
        $myhost = $_SERVER["SERVER_NAME"] ;
        $user = USER;
        if ($myhost == HOSTNAMEHOME) {
            $pass = PASSWORDHOME;
            $dsn  = DSNHOME;
        }
        if (($myhost == HOSTNAMEWORK)) {
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

    
    
    public function get_activity_counter_from_avc_name($avc_name) {
    
        
        // Connexion à la DB
        include_once 'constant.php';

        // Define database connexion
        $myhost = $_SERVER["SERVER_NAME"] ;
        $user = USER;
        if ($myhost == HOSTNAMEHOME) {
            $pass = PASSWORDHOME;
            $dsn  = DSNHOME;
        }
        if (($myhost == HOSTNAMEWORK)) {
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

            $sql = 'SELECT * FROM vtm_activity_counter WHERE avc_name = \'' . $avc_name . '\';';
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