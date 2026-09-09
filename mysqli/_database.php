<?php
/*

INSERT
	DBCreate(Nome da tabela, array de dados){
SELECT
	DBRead(Nome da tabela, Condicoes, campos desejados);
UPDATE
	DBUpdate(Nome da tabela, array de dados, condição);
DELETE
	DBDelete(Nome da tabela, condição);


*/
	//Executar Querys
	function DBExecute($query, $insertId = false){
		$link = DBconnect();
		$result = @mysqli_query($link, $query) or die (mysqli_error());

		if($insertId){
			$result = mysqli_insert_id($link);
		}
		DBClose($link);
		return $result;
	}


	//Gravar registros
	function DBCreate($table, array $data, $insertId = false){
		$data = DBEscape($data);
		$fields = implode(',', array_keys($data));
		$values = "'".implode("', '", $data)."'";

		$query = "INSERT INTO {$table} ({$fields}) values ({$values})";
		return DBExecute($query, $insertId);
	}


	//Ler registros
	function DBRead($table, $params = null, $fields = '*'){
		$params = ($params) ? " {$params}" : null;
		$query = "SELECT {$fields} FROM {$table}{$params}";

		$result = DBExecute($query);
		if(!mysqli_num_rows($result)){
			return false;
		}else{
			while ($res = mysqli_fetch_assoc($result)){
				$data[] =  $res;
			}
			return $data;
		}
	}


	//Alterar Registros
	function DBUpdate($table, array $data, $where = null, $insertId = false){
		foreach($data as $key => $value){
			$fields[] = "{$key} = '{$value}'";
		}
		$fields = implode(', ', $fields);
		$where = ($where) ? " WHERE {$where}": null;

		$query = "UPDATE {$table} SET {$fields} {$where}";
		$result = DBExecute($query, $insertId);
		return $result;
	}


	//Deleta Registros
	function DBdelete($table, $where = null){
		$where = ($where) ? " WHERE {$where}": null;
		$query = "DELETE FROM {$table}{$where}";
		$result = DBExecute($query);
		return $result;
	}
?>
