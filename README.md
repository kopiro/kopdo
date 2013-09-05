# KOPDO

## PDO wrapper in PHP

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
KOPDO::select('test', 'name, surname, friends, 'phone');
```

And automatically unserialized when requested

### Update an existing row

```php
KOPDO::update('test', ['name'=> 'Flavioooo'], 'id=:id', [ ':id'=>$id ]);
```

### Select a single row

```php
KOPDO::select_first('test', '*', 'id=:id', [':id'=>$id]);
KOPDO::select_first('test', "name LIKE '%:name%'", [':name'=>'flavio']);
```

or 

```php
KOPDO::select_first('test', '*', 'id=1');
```

### Select a list of things

```php
KOPDO::select_list('test', 'id');
```

### Delete rows

```php
KOPDO::delete('test', "name LIKE '%:name%'", [':name'=>'flavio']);
```


