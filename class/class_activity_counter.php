<?php

class cl_activity_counter

{

    
    public function get_wkt_holiday_entitlement_by_year($per_id, $avc_id, $year) {
    
        
        // Connexion à la DB
        $user='root';
        $pass='root';

        try {
            $dbh = new PDO('mysql:host=localhost;dbname=vysual', 'root', 'root');
            //$dbh = new PDO('mysql:host=db;dbname=vysual', 'root'', 'test''); // connexion to vagrant
            //echo 'Connection opened<BR>';
        }
        catch (PDOException $e) {
            // tenter de réessayer la connexion après un certain délai, par exemple
            echo 'Error by opening connection<BR>';
        }

        try {
            $sql = 'select * from vtm_timesheet where tst_date = \'' . $tst_date . '\' and per_id = \'' . $per_id . '\';';

            $sql = 'select sum(aca_amount/3600000/8), sum(aca_real_amount/3600000/8) from vtm_activity_counter_accounting ';
            $sql .= 'where avc_id = \'' . $avc_id .   '\' and date(aca_date_time) >= \'2023-01-01\' and date(aca_date_time) <= \'2023-12-31\' and ';
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