<?php

class cl_workflow

{
   
    public function getWorkflowFromName($wkf_name) {
    
        
    include_once 'class_database.php';
    $rows = [];

    try {
        // Récupération de l'instance singleton
        $db = ClassDatabase::getInstance();
        $dbh = $db->getConnection();

        // Requête sécurisée avec préparation
        $sql  = 'SELECT * FROM wkf_workflow WHERE wkf_name LIKE :wkf_name';
        $sth = $dbh->prepare($sql);
        $sth->execute(['wkf_name' => '%' . $wkf_name . '%']);
        $rows = $sth->fetchAll();
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }

    return $rows;
}




}
    

?>