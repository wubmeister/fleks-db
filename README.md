# Fleks DB

Fleks DB offers a basic abstraction layer for PDO database connections in PHP. The core functions of Fleks DB are:

- Providing a database adapter interface to run the most trivial tasks
- Providing a flexible and powerful SQL query builder to provide a PHP way for creating extendible queries

## The database adapter

Fleks DB provides several database adapters for different database engines (MySQL, SQLite, etc.), all implementing the same interface.

### Creating an adapter instance

Choose an adapter which suits the database engine you are using. At this moment, the following classes can be used:

```
Fleks\Db\MySQL
```

To create an instance to communicate with, for example, a MySQL database:

```
$db = new Fleks\Db\MySQL([
    'host' => '127.0.0.1', 
    'dbname' => 'my_database', 
    'username' => 'username',
    'password' => 'p455w0rd'
]);
```

In the parameter of the constructor you can supply an array with whatever DSN options are needed for your engine of choice.

### Preparing and executing queries

PDO provides a way to prepare statements and bind values afterwards. The Fleks DB adapter merges these two actions. You can prepare a statement and supply an array with values to bind:

```
$statement = $db->prepare('SELECT * FROM my_table WHERE id = :id', [ ':id' => $theId ]);
```

In this example, the SELECT satement will be prepared and the value of $theId will be bound to the parameter ':id' afterwards. Statement should now contain an instance of PDOStatement.

To execute the statement with the bound parameters directly, you can use 'execute' instead of 'prepare'. It does exactly the same, except that it also calls 'execute()' on the statement.

```
$statement = $db->execute('SELECT * FROM my_table WHERE id = :id', [ ':id' => $theId ]);
```

### Fetching rows

To take this one level higher, you can provide a SELECT statement with parameters to bind and tell the adapter to fetch one or all rows matching the query.

```
$row = $db->fetchRow('SELECT * FROM my_table WHERE id = :id', [ ':id' => $theId ]);
$rows = $db->fetchAll('SELECT * FROM my_table WHERE status = :status', [ ':status' => $theStatus ]);
```

### Advanced: quoting identifiers

Database engines usually provide a way to quote identifiers (database, table and column names) when these would be the same as certain keywords. The problem is that not all engines use the same quotation character. To make your application flexible enough to support all engines, the Fleks DB adapter provides a method to quote these identifiers in the way that the engine would understand.

```
$quoted = $db->quoteIdentifier('column_name');
// Would return something like `column_name` or "column_name", depending on the engine
$quoted = $db->quoteIdentifier('table_name.column_name');
// Would return something like `table_name`.`column_name` or "table_name"."column_name", depending on the engine
```

## Query builders

We will discuss all the separate builders below. Query builders are objects which can be cast to a string. The __toString() method returns the resulting SQL query. All query builders require a database adapter, because they need it to quote identifiers. In the following examples we will therefore provide each query builder constructor with a Fleks DB adapter.

All build methods are chainable, which means that they can be concatenanted. Check this article for explanation of method chaining: [http://www.techflirt.com/tutorials/oop-in-php/php-method-chaining.html]

### Bind parameters

When building a query, the query builder might create some parameters to bind when preparing a statement. For all quoery builders, these parameters can be retrieved with the getBind() method.

```
$db->execute((string)$select, $select->getBind());
```

## Building SELECT queries

To start building a SELECT query, enter this code:

```
use Fleks\Db\MySQL;
use Fleks\Db\Query\Select;

$db = new MySQL(...);

$select = new Select($db);
```

The SELECT builder provides various methods to build the query. All methods are chainable, which means that you can glue them together with '->'.

```
$select
	->from('table_name')
	->joinLeft('other_table', 'other_table.id = table_name.other_id')
	->joinRight(...)
	->where($conditions)
	->orWhere($conditions)
	->group('column_name')
	->order('column_name')
	->offset(12)
	->limit(24)
	->having($conditions)
	->orHaving($conditions);
```

Please check the reference to see what parameters to pass to each method.

### WHERE and HAVING

Here something on how to pass conditions like a boss.

## Building INSERT queries

To start building an INSERT query, enter this code:

```
use Fleks\Db\MySQL;
use Fleks\Db\Query\Insert;

$db = new MySQL(...);

$insert = new Insert($db);

$insert
	->into('table_name')
	->ignore()
	->values([ 'column_name' => 'value', ... ])
	->onDuplicateKey(Insert::IGNORE) // Does the same as ignore()
	->onDuplicateKey(Insert::UPDATE) // Updates all passed columns in values()
	->onDuplicateKey(Insert::UPDATE, [ 'column_name_1', 'column_name_2' ]); // Updates only the specified columns
```

## Building UPDATE queries

To start building an UPDATE query, enter this code:

```
use Fleks\Db\MySQL;
use Fleks\Db\Query\Update;

$db = new MySQL(...);

$update = new Update($db);

$update
	->table('table_name')
	->values([ 'column_name' => 'value', ... ])
	->where($conditions)
	->orWhere($conditions);
```

See 'WHERE and HAVING' under 'Building SELECT queries' to see how to pass the conditions for where() and orWhere()

## Building DELETE queries

To start building a DELETE query, enter this code:

```
use Fleks\Db\MySQL;
use Fleks\Db\Query\Delete;

$db = new MySQL(...);

$delete = new Delete($db);

$delete
	->from('table_name')
	->where($conditions)
	->orWhere($conditions);
```

See 'WHERE and HAVING' under 'Building SELECT queries' to see how to pass the conditions for where() and orWhere()