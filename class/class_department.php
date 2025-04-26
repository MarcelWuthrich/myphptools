<?php

class cl_department

{


    public function getDepartmentResponsable($per_id) {
    
        
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
            echo 'Error by opening connection: ' . $e->getMessage() . '<br>';
            // echo 'Error by opening connection<BR>';
        }

        try {
            $sql = 'SELECT dpt.dpt_name FROM gbl_department AS dpt INNER JOIN gbl_person_department AS pdp ON dpt.dpt_id = pdp.dpt_id ';
            $sql .= 'WHERE pdp.pdp_main=1 AND per_id = \'' . $per_id . '\'';

            $sql = 'SELECT per_resp.per_id AS responsable_per_id FROM (';
            $sql .= 'SELECT dpt.dpt_id AS search_dpt_id, 0 AS niveau FROM gbl_department dpt INNER JOIN gbl_person_department pdp ON dpt.dpt_id = pdp.dpt_id ';
            $sql .= 'INNER JOIN gbl_person per ON pdp.per_id = per.per_id WHERE pdp.pdp_main = 1 AND per.per_id = \'' . $per_id . '\' ' ;
            $sql .= 'AND per.per_role = \'time_employee\' UNION ALL ';
            $sql .= 'SELECT dpt.parent_id AS search_dpt_id, 1 AS niveau FROM gbl_department dpt INNER JOIN gbl_person_department pdp ON dpt.dpt_id = pdp.dpt_id ';
            $sql .= 'INNER JOIN gbl_person per ON pdp.per_id = per.per_id WHERE pdp.pdp_main = 1 AND per.per_id = \'' . $per_id . '\' ';
            $sql .= 'AND per.per_role = \'time_manager\') AS search_departments ';
            $sql .= 'LEFT JOIN gbl_person_department pdp_resp ON pdp_resp.dpt_id = search_departments.search_dpt_id ';
            $sql .= 'LEFT JOIN gbl_person per_resp ON pdp_resp.per_id = per_resp.per_id ';
            $sql .= 'WHERE per_resp.per_role = \'time_manager\' ORDER BY search_departments.niveau ASC LIMIT 1;';


            $sth = $dbh->query($sql);
            $rows = $sth->fetchAll();
        } 
            

        catch (PDOException $e) {
            echo "Failed: " . $e->getMessage() . '<BR>';
        }
        
        return $rows;
    }




    
    public function getDepartmentFromPerId($per_id) {
    
        
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
            echo 'Error by opening connection: ' . $e->getMessage() . '<br>';
            // echo 'Error by opening connection<BR>';
        }

        try {
            $sql = 'SELECT dpt.dpt_name FROM gbl_department AS dpt INNER JOIN gbl_person_department AS pdp ON dpt.dpt_id = pdp.dpt_id ';
            $sql .= 'WHERE pdp.pdp_main=1 AND per_id = \'' . $per_id . '\'';


            $sth = $dbh->query($sql);
            $rows = $sth->fetchAll();
        } 
            

        catch (PDOException $e) {
            echo "Failed: " . $e->getMessage() . '<BR>';
        }
        
        return $rows;
    }

    
  
        


}
    

?>