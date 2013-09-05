<?php

require 'KOPDO.php';

KOPDO::connect('mysql:host=localhost;dbname=test', 'tester');

KOPDO::truncate('test');

$id = KOPDO::insert('test', [
	'name'=> 'Flavio',
	'surname'=> 'Kopiro',
	'friends' => [ 'John', 'Steve', 'Tim' ]
]);

KOPDO::insert('test', [
	'name'=> 'Daniele',
	'surname'=> 'Appleseed',
	'phones'=> [
		'ita'=> '+391231231234',
		'usa'=> '+02998898',
		'uk'=> ['09956','923752']
	]
]);

KOPDO::update('test', ['name'=> 'Flavioooo'], 'id=:id', [ ':id'=>$id ]);


var_dump( KOPDO::select_first('test', '*', 'id=:id', [':id'=>$id]) );

var_dump( KOPDO::select('test', '*', '1') );
var_dump( KOPDO::select('test', 'name,surname,phones') );
var_dump( KOPDO::select_list('test', 'id') );


/*

Out:


array(5) {
  'id' =>
  string(1) "1"
  'name' =>
  string(9) "Flavioooo"
  'surname' =>
  string(6) "Kopiro"
  'friends' =>
  array(3) {
    [0] =>
    string(4) "John"
    [1] =>
    string(5) "Steve"
    [2] =>
    string(3) "Tim"
  }
  'phones' =>
  NULL
}
array(2) {
  [0] =>
  array(5) {
    'id' =>
    string(1) "1"
    'name' =>
    string(9) "Flavioooo"
    'surname' =>
    string(6) "Kopiro"
    'friends' =>
    array(3) {
      [0] =>
      string(4) "John"
      [1] =>
      string(5) "Steve"
      [2] =>
      string(3) "Tim"
    }
    'phones' =>
    NULL
  }
  [1] =>
  array(5) {
    'id' =>
    string(1) "2"
    'name' =>
    string(7) "Daniele"
    'surname' =>
    string(9) "Appleseed"
    'friends' =>
    NULL
    'phones' =>
    array(3) {
      'ita' =>
      string(13) "+391231231234"
      'usa' =>
      string(9) "+02998898"
      'uk' =>
      array(2) {
        [0] =>
        string(5) "09956"
        [1] =>
        string(6) "923752"
      }
    }
  }
}
array(2) {
  [0] =>
  array(3) {
    'name' =>
    string(9) "Flavioooo"
    'surname' =>
    string(6) "Kopiro"
    'phones' =>
    NULL
  }
  [1] =>
  array(3) {
    'name' =>
    string(7) "Daniele"
    'surname' =>
    string(9) "Appleseed"
    'phones' =>
    array(3) {
      'ita' =>
      string(13) "+391231231234"
      'usa' =>
      string(9) "+02998898"
      'uk' =>
      array(2) {
        [0] =>
        string(5) "09956"
        [1] =>
        string(6) "923752"
      }
    }
  }
}
array(2) {
  [0] =>
  string(1) "1"
  [1] =>
  string(1) "2"
}

*/



