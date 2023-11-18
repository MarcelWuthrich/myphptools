<?php

class cl_time_sheet

{

    
    public function getTimeSheetFromDatePerId($per_id, $tst_date) {
    
        
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