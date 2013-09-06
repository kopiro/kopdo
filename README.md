# KOPDO

## PDO wrapper in PHP

### Connect

```php
KOPDO::connect(DB_STRING, DB_USER, DB_PASS);
```

### Insert a new row

```php
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
KOPDO::select('test', 'name, surname, friends, phone');
```

Data are automatically unserialized when requested.

Will return an indexed array with inside an associative array with datas.


### Update an existing row

```php
KOPDO::update('test', ['name'=> 'Flavioooo'], 'id=:id', [ ':id'=>$id ]);
```

### Select a single row

```php
KOPDO::select_first('test', '*', 'id=:id', [':id'=>$id]);
KOPDO::select_first('test', '*', "name LIKE '%:name%'", [':name'=>'flavio']);
```

or 

```php
KOPDO::select_first('test', '*', 'id=1');
```

Will return an associative array with datas.

### Select a list of things

```php
KOPDO::select_list('test', 'id');
```

Will return an indexed array of IDs. `[1,2,3,4]`

### Delete rows

```php
KOPDO::delete('test', "name LIKE '%:name%'", [':name'=>'flavio']);
```


