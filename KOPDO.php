<?php

class KOPDO {

	public static $pdo;

	public static function connect($conn, $usr='root', $pwd='', $opt=[]) {
		static::$pdo = new PDO($conn, $usr, $pwd, array_merge([
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET CHARSET UTF-8'
			], $opt));
	}

	private static function serialize_data($data) {
		return '$$$'.serialize($data);
	}

	private static function unserialize_data($str) {
		return unserialize(substr($str,3));
	}

	private static function is_seralized($str) {
		return substr($str,0,3)==='$$$';
	}

	private static function shouldbe_serialized($data) {
		return (is_array($data) || is_object($data));
	}

	private static function prepare_data(&$data, $prefix=':') {
		foreach ($data as $key => $value) {
			if (static::shouldbe_serialized($value)) {
				$value = static::serialize_data($value);
			}
			$pdata[$prefix.$key] = $value;
		}
		return $pdata;
	}

	public static function all($table, $fields, $where='1', $data=[]) {
		$query = 'SELECT '.$fields.' FROM '.$table.' WHERE '.$where;
		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetchAll(PDO::FETCH_ASSOC);
		if (!$res) return null;
		foreach ($res as $k => $r) {
			foreach ($r as $_k => $v) {
				if (static::is_seralized($v)) {
					$res[$k][$_k] = static::unserialize_data($v);
				}
			}
		}
		return $res;
	}

	public static function first($table, $fields, $where='1', $data=[]) {
		$query = 'SELECT '.$fields.' FROM '.$table.' WHERE '.$where.' LIMIT 1';
		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetch(PDO::FETCH_ASSOC);
		if (!$res) return null;
		foreach ($res as $k => $v) {
			if (static::is_seralized($v)) {
				$res[$k] = static::unserialize_data($v);
			}
		}
		return $res;
	}

	public static function indexed($table, $field, $where='1', $data=[]) {
		$query = 'SELECT '.$field.' FROM '.$table.' WHERE '.$where;
		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetchAll(PDO::FETCH_NUM);
		$list = [];
		if (!$res) return null;
		foreach ($res as $k => $r) {
			if (static::is_seralized($r[0])) {
				$r[0] = static::unserialize_data($r[0]);
			}
			$list[] = $r[0];
		}
		return $list;
	}

	public static function insert($table, $data) {
		$pdo_data = static::prepare_data($data);
		$query = 'INSERT INTO '.$table.' ('.implode(',', array_keys($data)).') ';
		$query .= 'VALUES ('.implode(',', array_keys($pdo_data)).')';
		$st = static::$pdo->prepare($query);
		$res = $st->execute($pdo_data);
		return static::$pdo->lastInsertId();
	}

	public static function update($table, $update_data, $where='0', $where_data=[]) {
		$pdo_update_data = static::prepare_data($update_data, ':__');
		$uparray = array_map(function($x){
			return $x.'=:__'.$x;
		}, array_keys($update_data));
		$query = 'UPDATE '.$table.' SET '.implode(',', $uparray).' ';
		$query .= 'WHERE '.$where;
		$st = static::$pdo->prepare($query);
		return $st->execute(array_merge($pdo_update_data, $where_data));
	}

	public static function delete($table, $where='0', $data=[]) {
		$query = 'DELETE FROM '.$table.' WHERE '.$where;
		$st = static::$pdo->prepare($query);
		return $st->execute($data);
	}

	public static function truncate($table) {
		return static::$pdo->exec('TRUNCATE TABLE '.$table);
	}

}