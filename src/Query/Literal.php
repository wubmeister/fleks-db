<?php

/**
 * Literal query with bind paramaters
 *
 * @package    fleks-db
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/fleks-db
 */

namespace Fleks\Db\Query;

use Fleks\Db\Generic as GenericDb;

/**
 * Literal query
 */
class Literal extends AbstractQuery
{
    /**
     * The SQL
     * @var string
     */
    protected $sql;

    public function __construct(GenericDb $db, string $sql = '', array $bind = [])
    {
        $this->sql = $sql;
        $this->bind = $bind;
    }

    public function __toString()
    {
        return $this->sql;
    }
}
