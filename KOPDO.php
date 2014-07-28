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

	public static function unserialize_if_serialized($value, &$result = null) {
		if (!is_string($value)) return false;
		if (strlen($value)<3) return false;

		if ($value === 'b:0;') {
			$result = false;
			return true;
		}

		$length = strlen($value);
		$end = '';

		switch ($value[0]) {
			case 's':
			if ($value[$length - 2] !== '"') return false;
			case 'b':
			case 'i':
			case 'd':
			$end .= ';';
			case 'a':
			case 'O':
			$end .= '}';

			if ($value[1] !== ':') return false;

			switch ($value[2]) {
				case 0:
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				break;

				default:
				return false;
			}
			case 'N':
			$end .= ';';

			if ($value[$length - 1] !== $end[0]) return false;
			break;

			default:
			return false;
		}

		if (($result = @unserialize($value)) === false) {
			$result = null;
			return false;
		}

		return true;
	}

	private static function prepare_data($data, $prefix=':') {
		foreach ($data as $key => $value) {
			if (is_array($value) || is_object($value)) $value = serialize($value);
			$pdata[$prefix.$key] = $value;
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

	public static function all($table, $fields, $where='1', $data=[]) {
		$query = "SELECT $fields FROM $table WHERE $where";

		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetchAll(PDO::FETCH_OBJ);

		if (false===isset($res) || false===is_array($res)) return null;

		foreach ($res as $i => $row) {
			foreach ($row as $k => $v) {
				static::unserialize_if_serialized($v, $v);
			}
		}

		return $res;
	}

	public static function val($table, $field, $where='1', $data=[]) {
		$query = "SELECT $field FROM $table WHERE $where LIMIT 1";

		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetch(PDO::FETCH_NUM);

		if (false===isset($res) || true===empty($res)) return null;

		$v = $res[0];
		static::unserialize_if_serialized($v, $v);

		return $v;
	}

	public static function first($table, $fields, $where='1', $data=[]) {
		$query = "SELECT $fields FROM $table WHERE $where LIMIT 1";

		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetch(PDO::FETCH_OBJ);

		if (false===isset($res) || false===is_object($res)) return null;

		foreach ($res as &$v) {
			static::unserialize_if_serialized($v, $v);
		}

		return $res;
	}

	public static function indexed($table, $field, $where='1', $data=[]) {
		$query = "SELECT $field FROM $table WHERE $where";

		$st = static::$pdo->prepare($query);
		$st->execute($data);
		$res = $st->fetchAll(PDO::FETCH_NUM);

		if (false===isset($res) || false===is_array($res)) return null;

		$list = [];
		foreach ($res as &$row) {
			if (isset($row[0])) {
				$v = $row[0];
				static::unserialize_if_serialized($v, $v);
				$list[] = $v;
			}
		}

		return $list;
	}

	public static function insert($table, $data) {
		$pdo_data = static::prepare_data($data);
		$query = "INSERT INTO $table (".implode(',', array_keys($data)).") VALUES (".implode(',', array_keys($pdo_data)).")";

		$st = static::$pdo->prepare($query);
		$res = $st->execute($pdo_data);

		return static::$pdo->lastInsertId();
	}

	public static function update($table, $update_data, $where='0', $where_data=[]) {
		$pdo_update_data = static::prepare_data($update_data, ':__');
		$uparray = array_map(function($x){
			return $x.'=:__'.$x;
		}, array_keys($update_data));

		$query = "UPDATE $table SET ".implode(',', $uparray)." WHERE $where";

		$st = static::$pdo->prepare($query);
		return $st->execute(array_merge($pdo_update_data, $where_data));
	}

	public static function delete($table, $where='0', $data=[]) {
		$query = "DELETE FROM $table WHERE $where";
		$st = static::$pdo->prepare($query);

		return $st->execute($data);
	}

	public static function truncate($table) {
		return static::exec('TRUNCATE TABLE '.$table);
	}

}
