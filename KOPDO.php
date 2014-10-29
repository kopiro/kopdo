<?php

class KOPDO {

	public static $pdo;

	public static function connect($conn, $usr='root', $pwd='', $opt=[]) {
		static::$pdo = new PDO($conn, $usr, $pwd, array_merge([
			PDO::ATTR_ERRMODE 				=> PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND 	=> 'SET CHARSET UTF-8',
			PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_OBJ
		], $opt));
	}

	public static function unserialize($value) {
		$substr = substr($value, 0, 1);
		if ($substr === '{' || $substr === '[') {
			$uns = json_decode($value);
			if (is_array($uns) || is_object($uns))  {
				return $uns;
			}
		}
		return $value;
	}

	private static function prepare_data($data, $prefix = ':') {
		foreach ($data as $key => $value) {
			if (is_array($value) || is_object($value)) {
				$value = json_encode($value);
			}
			$pdata[ $prefix . $key ] = $value;
		}
		return $pdata;
	}

	public static function exec($str) {
		return static::$pdo->exec($str);
	}

	public static function query($query, $data=[]) {
		$st = static::$pdo->prepare($query);
		return $st->execute($data);
	}

	public static function all($table, $fields, $where = '1', $data = []) {
		$query = 'SELECT ' . $fields . ' FROM ' . $table . ' WHERE ' . $where;

		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetchAll(PDO::FETCH_OBJ);

		if (!isset($res) || !is_array($res)) {
			return null;
		}

		foreach ($res as $i => $row) {
			foreach ($row as $k => $v) {
				$res[$i][$k] = static::unserialize($v);
			}
		}

		return $res;
	}

	public static function val($table, $field, $where='1', $data=[]) {
		$query = 'SELECT ' . $field . ' FROM ' . $table ' WHERE ' . $where ' LIMIT 1';

		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetch(PDO::FETCH_NUM);

		if (!isset($res) || empty($res)) {
			return null;
		}

		return static::serialize($res[0]);
	}

	public static function first($table, $fields, $where = '1', $data = []) {
		$query = 'SELECT ' . $field . ' FROM ' . $table ' WHERE ' . $where ' LIMIT 1';

		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetch(PDO::FETCH_OBJ);

		if (!isset($res) || !is_object($res)) {
			return null;
		}

		foreach ($res as $k => $v) {
			$res[$k] = static::serialize($v);
		}

		return $res;
	}

	public static function indexed($table, $field, $where = '1', $data = []) {
		$query = 'SELECT ' . $field . ' FROM ' . $table ' WHERE ' . $where;

		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetchAll(PDO::FETCH_NUM);

		if (!isset($res) || !is_array($res)) {
			return null;
		}

		$list = [];
		foreach ($res as $row) {
			if (isset($row[0])) {
				$list[] = static::serialize($row[0]);
			}
		}

		return $list;
	}

	public static function insert($table, $data) {
		$pdo_data = static::prepare_data($data);
		$query = 'INSERT INTO ' . $table . '(' . implode(',', array_keys($data)) . ') VALUES (' . implode(',', array_keys($pdo_data)) . ')';

		$st = static::$pdo->prepare($query);
		$res = $st->execute($pdo_data);

		return static::$pdo->lastInsertId();
	}

	public static function update($table, $update_data, $where = '0', $where_data = []) {
		$pdo_update_data = static::prepare_data($update_data, ':__');
		$uparray = array_map(function($x){
			return $x . ' = :__' . $x;
		}, array_keys($update_data));

		$query = 'UPDATE ' . $table . ' SET ' . implode(',', $uparray) . ' WHERE ' . $where;

		$st = static::$pdo->prepare($query);
		return $st->execute(array_merge($pdo_update_data, $where_data));
	}

	public static function delete($table, $where = '0', $data = []) {
		$query = 'DELETE FROM ' . $table . ' WHERE ' . $where;
		$st = static::$pdo->prepare($query);

		return $st->execute($data);
	}

	public static function truncate($table) {
		return static::exec('TRUNCATE TABLE ' . $table);
	}

}
