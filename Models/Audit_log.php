<?php

/**
 * Class AuditLog
 * 
 * Handles logging actions performed by users in the audit_logs table.
 * Provides methods to log user actions and retrieve audit logs.
 * 
 * @author Danny Chanda
 */
class AuditLog {
    /**
     * @var PDO $conn The database connection instance.
     */
    private $conn;

    /**
     * @var string $table_name The name of the table in the database where logs are stored.
     */
    private $table_name = "audit_logs";

    /**
     * AuditLog constructor.
     * 
     * @param PDO $db The PDO database connection instance.
     */
    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Logs an action performed by a user into the audit_logs table.
     * 
     * @param int $userId The ID of the user who performed the action.
     * @param string $action A description of the action performed.
     * @param string $details Additional details about the action.
     * 
     * @return bool Returns true if the log entry was successfully inserted, otherwise false.
     */
    public function logAction(int $userId, string $action, string $details): bool {
        // Prepare the SQL query with placeholders.
        $query = "INSERT INTO " . $this->table_name . " (user_id, action, details) VALUES (:user_id, :action, :details)";
        
        // Prepare the statement for execution.
        $stmt = $this->conn->prepare($query);
        
        // Bind the parameters to the query placeholders.
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':details', $details, PDO::PARAM_STR);
        
        // Execute the query and return the result.
        return $stmt->execute();
    }

    /**
     * Retrieves all audit logs from the database, ordered by timestamp in descending order.
     * 
     * @return array An associative array of audit logs.
     */
    public function getLogs(): array {
        // Prepare the SQL query to fetch all logs.
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY timestamp DESC";
        
        // Prepare the statement for execution.
        $stmt = $this->conn->prepare($query);
        
        // Execute the query.
        $stmt->execute();
        
        // Fetch all results as an associative array.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
