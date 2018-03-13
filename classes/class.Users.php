<?php
class Users {
	
	const myKEYjob = Var_CONFIG_Mykeyjob;


	public static function AddSession($row){
		extract ($row);
		session_start();
		$_SESSION['User_Id'] = $User_Id;
		$_SESSION['User_Name'] = $User_Name;
		$_SESSION['User_Group_Id'] = $User_Group_Id;
		return $User_Id;
	}

	public static function GetSession(){
		session_start();
		return $_SESSION;
	}

	public static function UpdateSession($My_Sub_Account_Id = 0){
		extract ($_POST);
		extract ($_GET);
		session_start();
		if ($My_Sub_Account_Id > 0) {
			$_SESSION['Sub_Profile_Id'] = $My_Sub_Account_Id;		    
		} else {
			$_SESSION['Sub_Profile_Id'] = $Sub_Profile_Id;		    
		}
	}

	public static function DestroySession(){
		session_start();
		$User_Id = $_SESSION['User_Id'];
		unset($_SESSION['User_Id']);
		unset($_SESSION);
		session_destroy();
		session_unset();
		$_SESSION = array();
		$f = Logs::WriteLog("LOGOUT", "User Log Out", $User_Id);
		return $f;
	}

	public static function Authenticate_User_Login($User_Name, $User_Pass) {

		$mysqli = DB::myconn();
		
		$query="SELECT User_Id, User_Type, Sub_Profile_Id, DECODE(User_Name,'". self::myKEYjob ."') AS User_Name, User_Group_Id FROM system_users WHERE User_Name=ENCODE('$User_Name','".self::myKEYjob."') AND User_Pass=ENCODE('$User_Pass','".self::myKEYjob."')  LIMIT 1";

		//exit;

		$stmt = mysqli_prepare($mysqli, $query);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_store_result($stmt);
		
		$list_count=mysqli_stmt_num_rows($stmt);
		if($list_count > 0) {
			$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$User_Id = Users::AddSession($row);
			Logs::WriteLog("LOGIN", "Successfully Logged In", $User_Id);
			$User_Group_Id = $row['User_Group_Id'];
			$User_Type = $row['User_Type'];
			if ($User_Type == "Normal_User") {
				$RetArray = array($User_Group_Id);
				return "TRUE";			    
			} else {
				$Sub_Profile_Id = $row['Sub_Profile_Id'];
			    return $Sub_Profile_Id;
			}
		} else {
			$User = $User_Name."+".$User_Pass;
			Logs::WriteLog("LOGIN", "Login NOT Successful $query", $User);
			return "FALSE";
		}
    }

	public static function Add_New_User() {
		
		extract ($_POST);

		$Session_Array = Users::GetSession();
		$User_Id = $Session_Array['User_Id'];
		$Sub_Profile_Id = $Session_Array['Sub_Profile_Id'];
	
		$query = "INSERT INTO system_users (User_Name, User_Pass, User_Group_Id, Title_Id, First_Name, Last_Name, Email, Mobile_Number, Signup_Date, MPESA_Transaction_Code) VALUES (ENCODE('$User_Name','".self::myKEYjob."'), ENCODE('$User_Pass','".self::myKEYjob."'), '$User_Group_Id', '$Title_Id', '$First_Name', '$Last_Name', '$Email', '$Mobile_Number', NOW(), '$MPESA_Transaction_Code')";
		
		$mysqli = DB::myconn();

		if (mysqli_query($mysqli, $query)){
			$User_Id = mysqli_insert_id($mysqli);
			Logs::WriteLog("ADD", "Successfully Saved User $User_Id");
			return $User_Id;
		} else {
			$err = mysqli_error($mysqli);
			Logs::WriteLog("ERROR", "Unable to create new User $User_Id CLASS: Users >>> FUNCTION: Add_New_User >>> QUERY: $query >>> ERROR $err");
			return -1;
		}
    }

	//Add_New_Sub_User
	public static function Add_New_Sub_User() {
		
		extract ($_POST);

		$Session_Array = Users::GetSession();
		$User_Id = $Session_Array['User_Id'];
		//$Sub_Profile_Id = $Session_Array['Sub_Profile_Id'];
	
		$query = "INSERT INTO system_users (User_Type, Main_User_Id, Sub_Profile_Id, User_Name, User_Pass, User_Group_Id, Title_Id, First_Name, Last_Name, Email, Mobile_Number) VALUES ('Sub_User', '$User_Id', '$Access_To', ENCODE('$User_Name','".self::myKEYjob."'), ENCODE('$User_Pass','".self::myKEYjob."'), '$User_Group_Id', '$Title_Id', '$First_Name', '$Last_Name', '$Email', '$Mobile_Number')";
		
		$mysqli = DB::myconn();

		if (mysqli_query($mysqli, $query)){
			$User_Id = mysqli_insert_id($mysqli);
			Logs::WriteLog("ADD", "Successfully Saved User $User_Id");
			return $User_Id;
		} else {
			$err = mysqli_error($mysqli);
			Logs::WriteLog("ERROR", "Unable to create new User $User_Id CLASS: Users >>> FUNCTION: Add_New_User >>> QUERY: $query >>> ERROR $err");
			return -1;
		}
    }

	public static function Update_User_Profile(){
		extract ($_POST);
		$mysqli = DB::myconn();

		$query2 = "UPDATE system_users SET Title_Id = '$Title_Id', First_Name = '$First_Name', Last_Name = '$Last_Name', Email = '$Email', Mobile_Number = '$Mobile_Number'";
		if (($User_Pass) and ($New_Pass) and ($New_Pass_Confirm)){
			//Logs::WriteLog("TEST", "PART 1 SUCCESS");
			if ($New_Pass == $New_Pass_Confirm){
				//Logs::WriteLog("TEST", "PART 2 SUCCESS");
				$queryOLD = "SELECT First_Name FROM system_users WHERE User_Id = '$User_Id' AND User_Pass=ENCODE('$User_Pass','".self::myKEYjob."') LIMIT 1";
				$stmt = mysqli_prepare($mysqli, $queryOLD);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_store_result($stmt);
				$list_count=mysqli_stmt_num_rows($stmt);
				if($list_count > 0) {
					//Logs::WriteLog("TEST", "PART 3 SUCCESS");
					$query2 .= ", User_Pass = ENCODE('$New_Pass','".self::myKEYjob."')"; 
				}
			}
		}

		$query2 .= " WHERE User_Id = '$User_Id'";
		mysqli_query($mysqli, $query2);
		
		Logs::WriteLog("UPDATE", "Successfully Updated User Profile $User_Id");
	}


	public static function Get_System_Users($User_Group_Id = 0){
		$mysqli = DB::myconn();
		
		if ($User_Group_Id == 0){
			$query="SELECT User_Id, DECODE(User_Name,'".self::myKEYjob."') AS User_Name, User_Group_Id, Title_Id, First_Name, Last_Name, Status FROM system_users WHERE Visibility = 'Visible'";
		} else {
			$query="SELECT User_Id, DECODE(User_Name,'".self::myKEYjob."') AS User_Name, User_Group_Id, Title_Id, First_Name, Last_Name, Status FROM system_users WHERE User_Group_Id = '$User_Group_Id'";
		}
		

		$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
		$MyArray = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			extract ($row);
			$User_Group = UserGroups::Get_User_Group($User_Group_Id);
			$MyArray[$User_Id] = array($User_Name, $First_Name, $Last_Name, $Status, $User_Group);
		}
		return $MyArray;
	}

	public static function Get_Sub_Users(){

		$Session_Array = Users::GetSession();
		$User_Id = $Session_Array['User_Id'];
		
		$mysqli = DB::myconn();
		
		$query="SELECT User_Id, DECODE(User_Name,'".self::myKEYjob."') AS User_Name, User_Group_Id, Title_Id, First_Name, Last_Name, Status FROM system_users WHERE Visibility = 'Visible' AND User_Type = 'Sub_User' AND Main_User_Id = '$User_Id'";
		

		$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
		$MyArray = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			extract ($row);
			$User_Group = UserGroups::Get_User_Group($User_Group_Id);
			$MyArray[$User_Id] = array($User_Name, $First_Name, $Last_Name, $Status, $User_Group);
		}
		return $MyArray;
	}

	public static function Get_Single_User($User_Id){
		$mysqli = DB::myconn();
		
		$query="SELECT Title_Id, First_Name, Last_Name, Email, Mobile_Number, User_Pass FROM system_users WHERE User_Id = '$User_Id'";
		
		$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
		$MyArray = array();
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row;
	}


	public static function Get_User_Titles(){
		$mysqli = DB::myconn();
		
		$query="SELECT * FROM user_titles";
		
		$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
		$MyArray = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			extract ($row);
			$MyArray[$Title_Id] = $Title;
		}
		return $MyArray;
	}

	public static function Get_Single_Title($Title_Id){
		$mysqli = DB::myconn();
		
		$query="SELECT Title FROM user_titles WHERE Title_Id = '$Title_Id'";
		
		$result = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
		$MyArray = array();
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['Title'];
	}
   
}

?>