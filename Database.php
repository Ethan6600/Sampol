<?php
/**
 * Database.php
 * Handles the PDO database connection using the Singleton pattern.
 * Provides convenient methods for executing queries and fetching results.
 *
 * IT Capstone Project - Dispatch Management System
 */

// Include database configuration from config.php (not functions.php to avoid circular dependency)
require_once 'config.php';

class Database {    
    private static $instance = null; // Stores the single instance oF the Database connection
    private $pdo; // Stores the PDO object

    /**
     * Private constructor to prevent direct instantiation (enforces Singleton).
     * Establishes the PDO connection using credentials from config.php.
     */
    private function __construct() {
        // Build the DSN (Data Source Name) string
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        // Set PDO options for error handling and fetching modes
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation for real prepared statements
        ];

        try {
            // Attempt to create a new PDO connection
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log the detailed error message (for developer debugging)
            error_log('Database connection failed: ' . $e->getMessage());
            // Show a generic, user-friendly message and terminate execution
            die('Database connection failed. Please contact the administrator.');
        }
    }

    /**
     * Get the single instance of the Database connection.
     * This is the entry point to access the database object (Singleton pattern).
     *
     * @return Database The single Database instance.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get the raw PDO object.
     * Use this if you need direct access to PDO methods (e.g., for transactions).
     *
     * @return PDO The PDO database connection object.
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Executes a SQL query using prepared statements.
     * This is a fundamental method that other utility methods build upon.
     *
     * @param string $sql The SQL query string.
     * @param array $params An associative array of parameters to bind to the query.
     * @return PDOStatement The executed PDOStatement object.
     * @throws Exception if query execution fails.
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params); // Pass parameters directly to execute for named or positional placeholders
            return $stmt;
        } catch (PDOException $e) {
            // Log the error securely for debugging
            error_log("SQL Query failed: " . $sql . " - Error: " . $e->getMessage());
            // Re-throw a generic exception to calling code
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }

    /**
     * Fetches all rows from a SELECT query.
     *
     * @param string $sql The SQL SELECT query string.
     * @param array $params An associative array of parameters to bind.
     * @return array An array of associative arrays representing the fetched rows.
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetches a single row from a SELECT query.
     *
     * @param string $sql The SQL SELECT query string.
     * @param array $params An associative array of parameters to bind.
     * @return array|false An associative array representing the single fetched row, or false if no row is found.
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Executes an INSERT, UPDATE, or DELETE query and returns the number of affected rows.
     *
     * @param string $sql The SQL INSERT, UPDATE, or DELETE query string.
     * @param array $params An associative array of parameters to bind.
     * @return int The number of rows affected by the statement.
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @return string The ID of the last inserted row.
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

// Global helper function to easily get the Database instance
// This function can be called throughout your application files.
function get_db() {
    return Database::getInstance();
}
?>