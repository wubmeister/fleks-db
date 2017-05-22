<?php

/**
 * Helper to construct UPDATE queries for SQL
 *
 * @package    fleks-db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 */

namespace Fleks\Db\Query;

/**
 * Update query builder
 */
class Update extends Buildable
{
    /**
     * Sets the table for this query
     *
     * @param string|array $table Either a table name or an associative array
     *   with one element. In case of an array, the element's key should be the
     *   alias and the value the real table name.
     * @param string|array $columns The column(s) to select from the table. Defaults to '*'.
     * @return static Provides method chaining
     */
    public function table($table)
    {
        $this->tableName = $this->tableStr($table, []);

        return $this;
    }

    /**
     * ToString function returns the UPDATE query
     *
     * @return string The UPDATE query
     */
    public function __toString()
    {
        $sql = "UPDATE {$this->tableName}";
        if (count($this->valueKeys)) {
            $sql .= " SET " . implode(', ', array_map(function ($key) { return "`{$key}` = :{$key}"; }, $this->valueKeys));
        }
        if ($this->whereClause) {
            $sql .= " WHERE {$this->whereClause}";
        }

        return $sql;
    }
}
