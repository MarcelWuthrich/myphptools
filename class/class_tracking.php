<?php

class cl_tracking

{

    
    public function getTrackingFromDatePerId($per_id, $trk_booking_date_time_from) {
    
        
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