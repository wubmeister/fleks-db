<?php

/**
 * MySQL PDO wrapper
 *
 * @package    addsite/db
 * @author     Wubbo Bos <wubbo@addnoise.nl>
 * @copyright  Copyright (c) Add Noise
 */

namespace Addsite\Db;

/**
 * MySQL PDO wrapper
 */
class MySQL extends Generic
{

    /**
     * The DSN prefix
     * @var string
     */
    protected $dsnPrefix = 'mysql';

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifier(string $identifier)
    {
        if ($identifier[0] == "'") return trim($identifier, "'");
        $identifier = '`' . str_replace('.', '`.`', $identifier) . '`';
        return str_replace('`*`', '*', $identifier);
    }

    /**
     * {@inheritDoc}
     */
    public function isIdentifier(string $string)
    {
        return $string[0] == '`' && substr($string, -1) == '`';
    }
}
