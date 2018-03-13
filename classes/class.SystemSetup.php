<?php
class SystemSetup {

	const myKEYjob = Var_CONFIG_Mykeyjob;
	const fullpath = Var_CONFIG_Basepath_System_Files;

	public static function Get_System_Details($My_Sub_Account_Id = 0){
		$mysqli = DB::myconn();

		if ($My_Sub_Account_Id >= 1){
			
		} else{
			$Session_Array = Users::GetSession();
			$User_Id = $Session_Array['User_Id'];
			$My_Sub_Account_Id = $Session_Array['Sub_Profile_Id'];	
		}		
		
		$query="SELECT * FROM system_setup WHERE Sub_Profile_Id = '$My_Sub_Account_Id'";
		$stmt = mysqli_prepare($mysqli, $query);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
	 	$list_count=mysqli_stmt_num_rows($stmt);
		//exit;
		if ($list_count <= 0){
			return $list_count;
		} else {
			$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

			return $row;
		}
	}

	public static function Add_New_Sub_Profile(){
		extract ($_POST);

		$mysqli = DB::myconn();

		$Session_Array = Users::GetSession();
		$User_Id = $Session_Array['User_Id'];
		$Sub_Profile_Id = $Session_Array['Sub_Profile_Id'];

		foreach ($Account_Name as $key => $val){
			$MyAccount_Name = $val;
			$MyAccount_Type = $Account_Type[$key];
			$MyAccount_Address = $Account_Address[$key];
			$MyHas_Logo = $Has_Logo[$key];
			if(($MyAccount_Name) and ($MyAccount_Type)){

				$query1 = "INSERT INTO system_setup (Sub_Profile_Id, User_Id, Account_Type, Account_Name, Account_Address, Has_Logo) VALUES ('$Sub_Profile_Id', '$User_Id', '$MyAccount_Type', '$MyAccount_Name', '$MyAccount_Address', '$MyHas_Logo')";
				mysqli_query($mysqli, $query1);
				$Sub_Profile_Id = mysqli_insert_id($mysqli);

				if ($MyHas_Logo == "Yes"){
					$Logo_Filename = "systemlogo_" .$Sub_Profile_Id."_" . basename($_FILES['logofile']['name']);
					$uploadfile = self::fullpath . "/" . $Logo_Filename;
					if (move_uploaded_file($_FILES['logofile']['tmp_name'], $uploadfile)) {
						$query1 = "UPDATE system_setup SET Logo_Filename = '$Logo_Filename' WHERE Sub_Profile_Id = '$Sub_Profile_Id'";
						mysqli_query($mysqli, $query1);
						Logs::WriteLog("UPDATE", "Successfully Updated Practice Logo to $Logo_Filename");
					}
				}
			}
				
		}
		Logs::WriteLog("ADD", "Successfully Saved Bill Sub Accounts QUERY: $query");
		return $Bill_Document_Id;
	}

	public static function Update_Organization_Details(){
		extract ($_POST);
		$mysqli = DB::myconn();

		$query0="SELECT * FROM system_setup";
		$stmt = mysqli_prepare($mysqli, $query0);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
	 	$list_count=mysqli_stmt_num_rows($stmt);
		if ($list_count <= 0){
			$query = "INSERT INTO system_setup (Organization_Name, Organization_Address, Has_Logo) VALUES ('$Organization_Name', '$Organization_Address', '$Has_Logo')";
			mysqli_query($mysqli, $query);
			Logs::WriteLog("ADD", "Successfully Saved Practice Details");
		} else {
			$query2 = "UPDATE system_setup SET Organization_Name = '$Organization_Name', Organization_Address = '$Organization_Address', Has_Logo = '$Has_Logo'";
			mysqli_query($mysqli, $query2);
			Logs::WriteLog("UPDATE", "Successfully Updated Practice Details");
		}

		if ($Has_Logo == "Yes"){
			$Logo_Filename = "systemlogo_" . basename($_FILES['logofile']['name']);
			$uploadfile = self::fullpath . "/" . $Logo_Filename;
			if (move_uploaded_file($_FILES['logofile']['tmp_name'], $uploadfile)) {
				$query1 = "UPDATE system_setup SET Logo_Filename = '$Logo_Filename'";
				mysqli_query($mysqli, $query1);
				Logs::WriteLog("UPDATE", "Successfully Updated Practice Logo to $Logo_Filename");
			}
		}
		
		return "TRUE";
		
		
	}

	public static function Count_System_Account_Types()
	{

		$Session_Array = Users::GetSession();
		$User_Id = $Session_Array['User_Id'];

		$mysqli = DB::myconn();		
		$query="SELECT Sub_Profile_Id FROM system_setup WHERE User_Id = '$User_Id'";
		$stmt = mysqli_prepare($mysqli, $query);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
	 	$list_count=mysqli_stmt_num_rows($stmt);
		return $list_count;
	
	} // end func

	public static function Get_System_Account_Types(){
		$mysqli = DB::myconn();

		$Session_Array = Users::GetSession();
		$User_Id = $Session_Array['User_Id'];
		
		$query="SELECT * FROM system_setup WHERE User_Id = '$User_Id'";			
		
		$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
		$MyArray = array();
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			extract ($row);
			
			$MyArray[$Sub_Profile_Id] = array($Account_Type, $Account_Name);			
		}

		return $MyArray;
	}
	   
}

?>