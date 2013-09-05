# KOPDO

## PDO wrapper in PHP

### Insert a new row

```php
$id = KOPDO::insert('test', [
	'name'=> 'Flavio',
	'surname'=> 'Kopiro',
	'friends' => [ 'John', 'Steve', 'Tim' ]
]);
```

### Update an existing row

```php
KOPDO::update('test', ['name'=> 'Flavioooo'], 'id=:id', [ ':id'=>$id ]);
```

### Select a single row

```php
KOPDO::select_first('test', '*', 'id=:id', [':id'=>$id])
```

### Select rows

```php
KOPDO::select('test', 'name, surname, friends')
```

### Select a list of things

```php
KOPDO::select_list('test', 'id')
```




