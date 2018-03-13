<?php
class Logs {

	const myKEYjob = Var_CONFIG_Mykeyjob;
	const fullpath = Var_CONFIG_Basepath_Logs;



	public static function WriteLog($Action, $Action_Details, $User_Id = 0){
		$Session_Array = Users::GetSession();
		if ($User_Id == 0){
			$User_Id = $Session_Array['User_Id'];
		}
		$date_time = date("r");
		$filename = date("d_m_Y");
		$path = self::fullpath.'/';
		$filename = $path.$filename.".log";
		$mydata = $User_Id."#~#".$date_time."#~#".$Action."#~#".$Action_Details."\n";
		if (file_put_contents($filename,$mydata,FILE_APPEND|LOCK_EX)){
			return "LOG_TRUE";	
		} else {
			return "LOG_FALSE";			
		}

	}
   
}

?>