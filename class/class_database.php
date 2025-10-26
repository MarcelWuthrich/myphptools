
<?php
class ClassDatabase {
    private static $instance = null; // Instance unique
    private $dbh;

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct() {
        include_once '../constant.php';

        $configs = [
            HOSTNAMEHOME => ['dsn' => DSNHOME, 'pass' => PASSWORDHOME],
            HOSTNAMEWORK => ['dsn' => DSNWORK, 'pass' => PASSWORDWORK]
        ];

        $myhost = $_SERVER["SERVER_NAME"];

        if (!isset($configs[$myhost])) {
            throw new Exception("Host non configuré : $myhost");
        }

        $dsn  = $configs[$myhost]['dsn'];
        $user = USER;
        $pass = $configs[$myhost]['pass'];

        try {
            $this->dbh = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la DB : " . $e->getMessage());
        }
    }

    // Méthode publique pour obtenir l'instance unique
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ClassDatabase();
        }
        return self::$instance;
    }

    // Retourne l'objet PDO
    public function getConnection() {
        return $this->dbh;
    }
}

?>