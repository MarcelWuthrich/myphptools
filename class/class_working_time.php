<?php

class cl_working_time

{



    public function get_active_per_id_between_date($start_date, $end_date) {
    
        
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
    
    
    

        TRY {
            $dbh = new PDO($dsn, $user, $pass);
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

    
    public function get_active_per_id_at_one_date($this_date) {
    

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
    

    

    
    
    
    
        TRY {
            $dbh = new PDO($dsn, $user, $pass);
        }
        catch (PDOException $e) {
            // tenter de réessayer la connexion après un certain délai, par exemple
            echo 'Error by opening connection<BR>';
        }

        try {
            
            //$sql = 'select per_id from vtm_working_time where wkt_start_date <= \'' . $this_date . '\' ';
            //$sql .= 'and  (wkt_end_date >=\'' . $this_date . '\' or wkt_end_date is null);';

            $sql = 'select wkt.per_id,per.per_name,per.per_firstname from vtm_working_time as wkt inner join gbl_person as per on wkt.per_id = per.per_id ';
            $sql .= 'and  (wkt_end_date >=\'' . $this_date . '\' or wkt_end_date is null) order by per.per_name,per.per_firstname asc;';

        
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

    

    public function get_active_wkt_from_per_id($per_id) {
    
        
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
    

    
    

        TRY {
            $dbh = new PDO($dsn, $user, $pass);
        }
        catch (PDOException $e) {
            // tenter de réessayer la connexion après un certain délai, par exemple
            echo 'Error by opening connection<BR>';
        }

        try {
          
            $sql = 'SELECT * FROM vtm_working_time WHERE per_id = \'' . $per_id . '\' AND wkt_start_date <= DATE(NOW()) ';
            $sql .= 'AND (wkt_end_date >= DATE(NOW()) OR wkt_end_date IS NULL);';
        

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


    public function get__wkt_from_wkt_id($wkt_id) {
    
        
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
    

    

        TRY {
            $dbh = new PDO($dsn, $user, $pass);
        }
        catch (PDOException $e) {
            // tenter de réessayer la connexion après un certain délai, par exemple
            echo 'Error by opening connection<BR>';
        }

        try {
          
            $sql = 'SELECT * FROM vtm_working_time WHERE wkt_id = \'' . $wkt_id . '\';';
        

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

        

public function get_responsible_at_given_date($per_id, $date)
    {
        include_once 'constant.php';

        $myhost = $_SERVER["SERVER_NAME"];
        $user = USER;
        $pass = ($myhost == HOSTNAMEHOME) ? PASSWORDHOME : PASSWORDWORK;
        $dsn  = ($myhost == HOSTNAMEHOME) ? DSNHOME : DSNWORK;

        try {
            $dbh = new PDO($dsn, $user, $pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Erreur de connexion : ' . $e->getMessage();
            return null;
        }

        try {
            // Étape 1 : Trouver le département actif de la personne
            $sql_dpt = "
                SELECT dpt_id
                FROM vtm_working_time
                WHERE per_id = :per_id
                AND wkt_start_date <= :date
                AND (wkt_end_date >= :date OR wkt_end_date IS NULL)
                ORDER BY wkt_start_date DESC
                LIMIT 1
            ";
            $stmt = $dbh->prepare($sql_dpt);
            $stmt->execute([':per_id' => $per_id, ':date' => $date]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result || empty($result['dpt_id'])) {
                return null;
            }

            $current_dpt = $result['dpt_id'];

            // Étape 2 : Remonter la hiérarchie pour trouver un time_manager ≠ per_id
            while ($current_dpt !== null) {
                $sql_responsable = "
                    SELECT 
                        wkt.wkt_id,
                        wkt.wkt_start_date,
                        wkt.wkt_end_date,
                        per.per_id AS responsable_id,
                        per.per_name,
                        per.per_firstname,
                        per.per_role
                    FROM vtm_working_time wkt
                    JOIN gbl_person per ON per.per_id = wkt.per_id
                    WHERE wkt.dpt_id = :dpt_id
                    AND wkt.wkt_start_date <= :date
                    AND (wkt.wkt_end_date >= :date OR wkt.wkt_end_date IS NULL)
                    AND per.per_role = 'time_manager'
                    AND per.per_id != :per_id
                    ORDER BY wkt.wkt_start_date DESC
                    LIMIT 1
                ";

                $stmt2 = $dbh->prepare($sql_responsable);
                $stmt2->execute([
                    ':dpt_id' => $current_dpt,
                    ':date' => $date,
                    ':per_id' => $per_id // Pour ne pas retourner lui-même
                ]);
                $responsable = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($responsable) {
                    return $responsable;
                }

                // Monter dans la hiérarchie
                $sql_parent = "SELECT parent_id FROM gbl_department WHERE dpt_id = :dpt_id";
                $stmt3 = $dbh->prepare($sql_parent);
                $stmt3->execute([':dpt_id' => $current_dpt]);
                $parent = $stmt3->fetch(PDO::FETCH_ASSOC);
                $current_dpt = $parent ? $parent['parent_id'] : null;
            }

            return null;

        } catch (PDOException $e) {
            echo "Erreur SQL : " . $e->getMessage();
            return null;
        }
    }



}
    

?>