<?php

class cl_activity_sheet

{




    public function getActivity($datefrom, $dateto) {

           
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
            
            $sql = 'select ast_id,per_id,tco_id,ast_date,ast_resource_name,ast_tco_name,ast_amount from vtm_activity_sheet ';
            $sql .= 'where ast_date >= \'' . $datefrom .  '\' and ast_date <= \'' . $dateto . '\' and tco_id is not null order by ast_date,ast_resource_name asc';

            $sth = $dbh->query($sql);
            $rows = $sth->fetchAll();
        }
        catch (PDOException $e) {
            echo "Failed: " . $e->getMessage() . '<BR>';
        }
        
        return $rows;

    }




    public function getValidators($ast_id) {

           
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
            
            $sql = 'select * from vtm_activity_sheet_validation where ast_id = \'' . $ast_id .  '\' and vas_archived_by is NULL;';

            // echo '<BR>' . $sql . '<BR>';
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