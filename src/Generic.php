<?php

/**
 * Generic PDO wrapper, also the base class and interface for any PDO wrapper
 *
 * @package    fleks-db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/fleks-db
 */

namespace Fleks\Db;

use PDO;
use Exception;

/**
 * Generic PDO wrapper
 */
class Generic
{
    /**
     * The PDO object
     * @var PDO
     */
    protected $pdo;

    /**
     * The DSN prefix
     * @var string
     */
    protected $dsnPrefix = 'generic';

    /**
     * Constructor, takes DSN params, a user name and password
     *
     * @param array $dsnParams The parameters for the DSN, in most cases a host
     *   and database name. E.G. [ 'host' => 'localhost', 'dbname' => 'my_database' ]
     * @param string $username The user name to authenticate with the database server
     * @param string $password The password to authenticate with the database server
     */
    public function __construct(array $dsnParams, $username = null, $password = null)
    {
        if ($this->dsnPrefix != 'generic') {
            $dsn = $this->dsnPrefix . ':';
            $dsnPairs = [];
            foreach ($dsnParams as $key => $value) {
                $dsnPairs[] = "{$key}={$value}";
            }
            $dsn .= implode(';', $dsnPairs);
            $this->pdo = new PDO($dsn, $username, $password);
        }
    }

    /**
     * Gets the internal PDO object
     *
     * @return PDO The PDO object
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Quotes an identifier for safe use in SQL queries
     *
     * @param string $identifier An unquoted identifier. An already quoted string
     *   will be stripped from its quotes and then handled as an SQL expression
     * @return string The quoted identifier
     */
    public function quoteIdentifier(string $identifier)
    {
        if ($identifier[0] == "'") return trim($identifier, "'");
        $identifier = '"' . str_replace('.', '"."', $identifier) . '"';
        return str_replace('"*"', '*', $identifier);
    }

    /**
     * Checks if a string is likely to be an identifier
     *
     * @param string $string The string to check
     * @return bool
     */
    public function isIdentifier(string $string)
    {
        return $string[0] == '"' && substr($string, -1) == '"';
    }

    /**
     * Prepares a PDO statement and binds value parameters if any are specified
     *
     * @param string $query The SQL query
     * @param array $bind The bind parameters
     * @return PDOStatement The prepared PDOStatement
     */
    public function prepare($query, $bind = [])
    {
        $statement = $this->pdo->prepare((string)$query);

        if ($statement) {
            foreach ($bind as $key => $value) {
                $statement->bindValue($key, $value);
            }
        }

        return $statement;
    }

    /**
     * Prepares and executes a PDO statement and binds value parameters if any
     * are specified
     *
     * @param string $query The SQL query
     * @param array $bind The bind parameters
     * @return PDOStatement The prepared PDOStatement or false if the preparing or execution failed
     *
     * @throws Exception If the execution fails
     */
    public function execute($query, $bind = [])
    {
        $statement = $this->prepare($query, $bind);
        if ($statement && $statement->execute()) {
            return $statement;
        } else {
            $errorInfo = $statement->errorInfo();
            throw new Exception("[SQLSTATE {$errorInfo[0]}] {$errorInfo[2]}");
        }
    }

    /**
     * Fetches all the rows resulting from the specified SQL query
     *
     * @param string $query The SQL query
     * @param array $bind The bind parameters
     * @param int $fetchMode {@link http://php.net/manual/en/pdostatement.fetch.php}
     * @return array
     */
    public function fetchAll($query, $bind = [], int $fetchMode = PDO::FETCH_ASSOC)
    {
        $stmt = $this->execute($query, $bind);
        return $stmt ? $stmt->fetchAll($fetchMode) : [];
    }

    /**
     * Fetches a single row resulting from the specified SQL query
     *
     * @param string $query The SQL query
     * @param array $bind The bind parameters
     * @param int $fetchMode {@link http://php.net/manual/en/pdostatement.fetch.php}
     * @return mixed
     */
    public function fetchRow($query, $bind = [], int $fetchMode = PDO::FETCH_ASSOC)
    {
        $stmt = $this->execute($query, $bind);
        return $stmt ? $stmt->fetch($fetchMode) : null;
    }

    /**
     * Executes the passed queries in one transaction
     *
     * @param array $queries The queries
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function commitTransaction($queries)
    {
        $this->pdo->beginTransaction();
        foreach ($queries as $query) {
            $this->pdo->exec($query);
        }
        return $this->pdo->commit();
    }
}
