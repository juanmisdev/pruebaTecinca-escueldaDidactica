<?php
/**
 * Manejador de conexión a la base de datos usando PDO.
 * 
 * Esta clase gestiona la conexión a la base de datos MySQL usando PDO.
 * 
 * Uso:
 *   $db = Database::getInstance()->getConnection();
 * Esto genera una instacia de la clase Database y devuelve la conexión PDO
 */

class Database
{
    /**
     * Instancia única de la clase.
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * Conexión PDO.
     * @var PDO
     */
    private PDO $connection;

    /**
     * Configuración de la base de datos.
     */
    private string $host = '127.0.0.1';
    private string $dbName = 'prueba-escueladidactica';
    private string $username = 'root'; // Cambia segun sea necesario
    private string $password = '12345';     // Cambia segun sea necesario
    private string $charset = 'utf8mb4';

    /**
     * Constructor privado para evitar la instanciación directa.
     */
    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Registrar el error en producción
            throw new RuntimeException('Fallo en la conexión a la base de datos: ' . $e->getMessage());
        }
    }

    /**
     * Obtener la instancia única de Database.
     * 
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance; // Si ya existe, devuelve la instancia existente
    }

    /**
     * Obtener la conexión PDO.
     * 
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}