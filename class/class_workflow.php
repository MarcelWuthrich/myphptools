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

   public function getWorkflowStartContextFromWkfId($wkf_id) {
            
    include_once 'class_database.php';
    $rows = [];

    try {
        // Récupération de l'instance singleton
        $db = ClassDatabase::getInstance();
        $dbh = $db->getConnection();

        // Requête sécurisée avec paramètre typé
      $sql  = 'SELECT * FROM wkf_start_context WHERE wkf_id = :wkf_id';
        $sth = $dbh->prepare($sql);
        $sth->bindValue(':wkf_id', $wkf_id, PDO::PARAM_INT); // bind du paramètre
        $sth->execute();

        // Récupération d'un seul enregistrement
        $row = $sth->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }

    return $row;
}



}
    

?>