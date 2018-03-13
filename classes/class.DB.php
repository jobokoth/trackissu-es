<?php
class DB {
	
	const serverName = Var_CONFIG_ServerName;	//"localhost:3310"
	const dbUsername = Var_CONFIG_DbUsername;
	const dbPassword = Var_CONFIG_DbPassword;
	const dbName = Var_CONFIG_DatabaseName;

	public static function myconn() {
		$link = mysqli_connect(self::serverName,self::dbUsername,self::dbPassword) or die (print mysql_error());
		$mysqli = mysqli_select_db($link, self::dbName);
		return $link;
	}

	public static function QueryCount($query) {
		$list_count = 0;
		
		$mysqli = DB::myconn();

		$stmt = mysqli_prepare($mysqli, $query);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);		
		$list_count = mysqli_stmt_num_rows($stmt);

		return $list_count;
	}
   
}

?>