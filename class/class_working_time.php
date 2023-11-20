<?php

class cl_working_time

{


    public function get_active_per_id_between_date($start_date, $end_date) {
    
        
        // Connexion à la DB
        //include 'class/class_constant.php';

        $user='root';
        $pass='root';

        try {

            $dbh = new PDO('mysql:host=localhost;dbname=vysual', 'root', 'root');
            //$dbh = new PDO();
            //$dbh = new PDO('mysql:host=db;dbname=vysual', 'root'', 'test''); // connexion to vagrant
            //echo 'Connection opened<BR>';
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

    

        


}
    

?>