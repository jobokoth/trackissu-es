<?
include 'inc-actions-header.php';

$OUTPUT = Users::Authenticate_User_Login($User_Name, $User_Pass);

if ($OUTPUT == "FALSE"){
	header("Location: ../index.php?error=true");
} else {
	if ($OUTPUT == "TRUE") {
		$Count = SystemSetup::Count_System_Account_Types();
		if ($Count <= 0) {
			header("Location: ../system_setup.php?NewInitSetup=True");	
		} elseif ($Count == 1) {
			$Result = SystemSetup::Get_System_Account_Types();
			foreach ($Result as $key => $val){
				$XX = $key;
				//exit;
			}
			//print "Location: actions-update.php?Sub_Profile_Id=$XX&action=update_current_account";
			//exit;
			header("Location: actions-update.php?Sub_Profile_Id=$XX&action=update_current_account");	
		} else {
			header("Location: ../select_account_types.php");	    
		}	    
	}
}
exit;
?>