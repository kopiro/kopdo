### Connect

```php
<?php
KOPDO::connect('localhost', 'root', 'toor');
```

### Insert a new row

```php
<?php
/* SQL:
INSERT INTO database (name, surname, friends, phones)
VALUES ('Flavio', 'Kopiro', serialize(['A', 'B', 'C']), serialize([ 'ita' => '+123', 'uk' => ['456', '789'] ]))
*/
$id = KOPDO::insert('database', [
	'name' => 'Flavio',
	'surname' => 'Kopiro',
	'friends' => ['A', 'B', 'C'],
	'phones' => [
		'ita' => '+123',
		'uk' => ['456', '789']
	]
]);
```

The attributes passed as array or objects are automatically serialized
into the database using php built-in `serialize` function.

It will return the ID (primary key) of the just inserted row.

### Select rows

```php
<?php
// SQL: SELECT name, surname, friends, phone FROM database
KOPDO::all('database', 'name, surname, friends, phone');
```

Data are automatically unserialized upon request.

The `::all` method returns an indexed array that contains PHP stdClass objects:

```php
[
	(stdClass)[
		'name' => 'Flavio',
		'surname' => 'Kopiro',
		'friends' => ['A', 'B', 'C'],
		'phones' => [
			'ita' => '+123',
			'uk' => ['456', '789']
		]
	],
	(stdClass)[ ... ],
	(stdClass)[ ... ],
	...
]
```

### Update an existing row

```php
<?php
// SQL: UPDATE database SET name = 'Flavioooo' WHERE id = 1
$id = 1;
KOPDO::update('database', [ 'name' => 'Flavioooo' ], 'id = :id', [ ':id' => $id ]);
```

It will return `true`/`false` if the row got updated.

### Select a single row

```php
<?php
$id = 1;
// SQL: SELECT * FROM database WHERE id = 1
KOPDO::first('database', '*', 'id = :id', [ ':id' => $id ]);
// SQL: SELECT surname FROM database WHERE name LIKE '%Flavio%'
KOPDO::first('database', 'surname', "name LIKE '%:name%'", [ ':name' => 'Flavio' ]);
```

The `::first` method returns a PHP stdClass object containing the first row returned from the query.

It will return:

```php
(stdClass)[
	'name' => 'Flavio',
	'surname' => 'Kopiro',
	'friends' => ['A', 'B', 'C'],
	'phones' => [
		'ita' => '+123',
		'uk' => ['456', '789']
	]
]
```

##### Plain query

Not recommended, but if you have constant values you can query directly without passing the named attributes.

```php
<?php
// SQL: SEECT * FROM database WHERE id = 1
KOPDO::first('database', '*', 'id=1');
```

### Select a list of things

```php
<?php
// SQL: SELECT id FROM database
KOPDO::indexed('database', 'id');
```

It will return an indexed array of IDs like: `[ 1, 2, 3, 4 ]`

### Delete rows

```php
<?php
// SQL: DELETE FROM database WHERE name LIKE '%flavio%'
KOPDO::delete('database', "name LIKE '%:name%'", [ ':name' => 'flavio' ]);
```

### Truncate table

```php
<?php
// SQL: TRUNCATE TABLE database
KOPDO::truncate('database');
```
