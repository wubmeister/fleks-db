<?php

/**
 * Abstract query class
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
 * Literal function or expression wrapper
 */
class AbstractQuery
{
    /**
     * The bind paramters
     *
     * @var array
     */
    protected $bind = [];

    /**
     * The database adapter, which is user for quoting identifiers and such
     *
     * @var Fleks\Db\Generic
     */
    protected $db;

    /**
     * Construct a Query object with a database adapter, which is user for
     * quoting identifiers and such
     *
     * @param Fleks\Db\Generic $db The database adapter
     */
    public function __construct(GenericDb $db)
    {
        $this->db = $db;
    }

    /**
     * Binds a value to a specified parameter. Overwrites a previously bound
     * value to that parameter if any
     *
     * @param string $param The parameter name
     * @param mixed $value The value to bind
     */
    protected function bindParam(string $param, $value) {
        $this->bind[$param] = $value;
    }

    /**
     * Returns the bind parameters
     *
     * @return array The bind parameters ([ ':bindX' => $value ])
     */
    public function getBind()
    {
        return $this->bind;
    }
}
