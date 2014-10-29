### Connect

```php
<?php
KOPDO::connect(DB_STRING, DB_USER, DB_PASS);
```

### Insert a new row

```php
<?php
$id = KOPDO::insert('test', [
	'name'=> 'Flavio',
	'surname'=> 'Kopiro',
	'friends'=> ['A','B','C']
	'phones'=> [
		'ita'=> '+391231231234',
		'usa'=> '+02998898',
		'uk'=> ['09956','923752']
	]
]);
```

The data passed as array/objects are automatically serialized in the database.

### Select rows

```php
<?php
KOPDO::all('test', 'name, surname, friends, phone');
```

Data are automatically unserialized when requested.

Will return an indexed array with inside an associative array with datas.


### Update an existing row

```php
<?php
KOPDO::update('test', ['name'=> 'Flavioooo'], 'id=:id', [ ':id'=>$id ]);
```

### Select a single row

```php
<?php
KOPDO::first('test', '*', 'id=:id', [':id'=>$id]);
KOPDO::first('test', '*', "name LIKE '%:name%'", [':name'=>'flavio']);
```

or

```php
<?php
KOPDO::first('test', '*', 'id=1');
```

Will return an associative array with datas.

### Select a list of things

```php
<?php
KOPDO::indexed('test', 'id');
```

Will return an indexed array of IDs. `[1,2,3,4]`

### Delete rows

```php
<?php
KOPDO::delete('test', "name LIKE '%:name%'", [':name'=>'flavio']);
```


