<?php

class cl_display

{

    
    public function displayFromArray($myarray) {
    
        
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