<?php

class cl_person

{

    
    public function getPersonFromNameFirstname($per_name, $per_firstname) {
    
        
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
            $sql  = 'select distinct * from gbl_person where per_name = \'' . $per_name . '\' and per_firstname = \'' . $per_firstname . '\' ;';
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

    
    public function getPersonFromPerId($per_id) {
    
        
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
            $sql  = 'select * from gbl_person where per_id = \'' . $per_id . '\';';
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

    
  public function getPersonFromPersonalNumber($per_number) {

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
        $dbh = new pdo($dsn,$user,$pass);
    }
    catch (PDOException $e) {
        // tenter de réessayer la connexion après un certain délai, par exemple
        echo 'Error by opening connection<BR>';
    }

    try {
        $sql = 'select per.* from gbl_contact_detail as ctd inner join gbl_person as per on ctd.per_id = per.per_id where ctd.ctd_number = \'' . $per_number . '\' and ctd.ctd_type = \'Professional\'';
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