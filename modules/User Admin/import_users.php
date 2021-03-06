<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/User Admin/import_users.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Import Users') . "</div>" ;
	print "</div>" ;
	
	$step=NULL ;
	if (isset($_GET["step"])) {
		$step=$_GET["step"] ;
	}
	if ($step=="") {
		$step=1 ;
	}
	else if (($step!=1) AND ($step!=2)) {
		$step=1 ;
	}
	
	//STEP 1, SELECT TERM
	if ($step==1) {
		?>
		<h2>
			<?php print _('Step 1 - Select CSV Files') ?>
		</h2>
		<p>
			<?php print _('This page allows you to import user data from a CSV file, in one of two modes: 1) Sync - the import file includes all users, whether they be students, staff, parents or other. The system will take the import and set any existing users not present in the file to "Left", whilst importing new users into the system, or 2) Import - the import file includes only users you wish to add to the system. New users will be assigned a random password, unless a default is set or the Password field is not blank. Select the CSV file you wish to use for the synchronise operation.') ?><br/>
		</p>
		<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/import_users.php&step=2" ?>" enctype="multipart/form-data">
			<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
				<tr>
					<td> 
						<b>Mode *</b><br/>
						<span style="font-size: 90%"><i></i></span>
					</td>
					<td class="right">
						<select name="mode" id="mode" style="width: 302px">
							<option value="sync"><?php print _('Sync') ?></option>
							<option value="import"><?php print _('Import') ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td style='width: 275px'> 
						<b><?php print _('CSV File') ?> *</b><br/>
						<span style="font-size: 90%"><i><?php print _('See Notes below for specification.') ?></i></span>
					</td>
					<td class="right">
						<input type="file" name="file" id="file" size="chars">
						<script type="text/javascript">
							var file=new LiveValidation('file');
							file.add(Validate.Presence);
						</script>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Field Delimiter') ?> *</b><br/>
					</td>
					<td class="right">
						<input type="text" style="width: 300px" name="fieldDelimiter" value="," maxlength=1>
						<script type="text/javascript">
							var fieldDelimiter=new LiveValidation('fieldDelimiter');
							fieldDelimiter.add(Validate.Presence);
						</script>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('String Enclosure') ?> *</b><br/>
						<span style="font-size: 90%"><i></i></span>
					</td>
					<td class="right">
						<input type="text" style="width: 300px" name="stringEnclosure" value='"' maxlength=1>
						<script type="text/javascript">
							var stringEnclosure=new LiveValidation('stringEnclosure');
							stringEnclosure.add(Validate.Presence);
						</script>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Default Password') ?></b><br/>
						<span style="font-size: 90%"><i><?php print _('If not set, and Password field is empty, random passwords will be used.') ?></i></span>
					</td>
					<td class="right">
						<input type="text" style="width: 300px" name="defaultPassword" value='' maxlength=20>
					</td>
				</tr>
				<tr>
					<td>
						<span style="font-size: 90%"><i>* <?php print _("denotes a required field") ; ?></i></span>
					</td>
					<td class="right">
						<input name="gibbonSchoolYearID" id="gibbonSchoolYearID" value="<?php print $gibbonSchoolYearID ?>" type="hidden">
						<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
						<input type="submit" value="<?php print _("Submit") ; ?>">
					</td>
				</tr>
			</table>
		</form>
		
		
		
		<h4>
			<?php print _('Notes') ?>
		</h4>
		<ol>
			<li style='color: #c00; font-weight: bold'><?php print _('THE SYSTEM WILL NOT PROMPT YOU TO PROCEED, IT WILL JUST DO THE IMPORT. BACKUP YOUR DATA.') ?></li>
			<li><?php print _('You may only submit CSV files.') ?></li>
			<li><?php print _('Imports cannot be run concurrently (e.g. make sure you are the only person importing at any one time).') ?></li>
			<li><?php print _('Your import should only include those users whose status is set "Full" (e.g. current users).') ?></li>
			<li><?php print _('The submitted file must have the following fields in the following order (* denotes required field):') ?></li> 
				<ol>
					<li><b><?php print _('Title') ?></b> - <?php print _('e.g. Mr, Mrs, Dr') ?></li>
					<li><b><?php print _('Surname') ?> *</b> - <?php print _('Family name') ?></li>
					<li><b><?php print _('First Name') ?> *</b> - <?php print _('Given name') ?></li>
					<li><b><?php print _('Preferred Name') ?> *</b> - <?php print _('Most common name, alias, nickname, handle, etc') ?></li>
					<li><b><?php print _('Official Name') ?> *</b> - <?php print _('Full name as shown in ID documents.') ?></li>
					<li><b><?php print _('Name In Characters') ?></b> - <?php print _('e.g. Chinese name') ?></li>
					<li><b><?php print _('Gender') ?> *</b> - <?php print _('F or M') ?></li>
					<li><b><?php print _('Username') ?> *</b> - <?php print _('Must be unique') ?></li>
					<li><b><?php print _('Password') ?></b> - <?php print _('If blank, default password or random password will be used.') ?></li>
					<li><b><?php print _('House') ?></b> - <?php print _('House short name, as set in School Admin. Must already exist).') ?></li>
					<li><b><?php print _('DOB') ?></b> - <?php print _('Date of birth') ?> (yyyy-mm-dd)</li>
					<li><b><?php print _('Role') ?> *</b> - <?php print _('Teacher, Support Staff, Student or Parent') ?></li>
					<li><b><?php print _('Email') ?></b></li>
					<li><b><?php print _('Image (240)') ?></b> - <?php print _('path from /uploads/ to medium portrait image (240px by 320px)') ?></li>
					<li><b><?php print _('Address 1') ?></b> - <?php print _('Unit, Building, Street') ?></li>
					<li><b><?php print _('Address 1 (District)') ?></b> - <?php print _('County, State, District') ?></li>
					<li><b><?php print _('Address 1 (Country)') ?></b></li>
					<li><b><?php print _('Address 2') ?></b> - <?php print _('Unit, Building, Street') ?></li>
					<li><b><?php print _('Address 2 (District)') ?></b> - <?php print _('County, State, District') ?></li>
					<li><b><?php print _('Address 2 (Country)') ?></b></li>
					<li><b><?php print _('Phone 1 (Type)') ?></b> - <?php print _('Mobile, Home, Work, Fax, Pager, Other') ?></li>
					<li><b><?php print _('Phone 1 (Country Code)') ?></b> - <?php print _('IDD code, without 00 or +') ?></li>
					<li><b><?php print _('Phone 1') ?></b> - <?php print _('No spaces or punctuation, just numbers') ?></li>
					<li><b><?php print _('Phone 2 (Type)') ?></b> - <?php print _('Mobile, Home, Work, Fax, Pager, Other') ?></li>
					<li><b><?php print _('Phone 2 (Country Code)') ?></b> - <?php print _('IDD code, without 00 or +') ?></li>
					<li><b><?php print _('Phone 2') ?></b> - <?php print _('No spaces or punctuation, just numbers') ?></li>
					<li><b><?php print _('Phone 3 (Type)') ?></b> - <?php print _('Mobile, Home, Work, Fax, Pager, Other') ?></li>
					<li><b><?php print _('Phone 3 (Country Code)') ?></b> - <?php print _('IDD code, without 00 or +') ?></li>
					<li><b><?php print _('Phone 3') ?></b> - <?php print _('No spaces or punctuation, just numbers') ?></li>
					<li><b><?php print _('Phone 4 (Type)') ?></b> - <?php print _('Mobile, Home, Work, Fax, Pager, Other') ?></li>
					<li><b><?php print _('Phone 4 (Country Code)') ?></b> - <?php print _('IDD code, without 00 or +') ?></li>
					<li><b><?php print _('Phone 4') ?></b> - <?php print _('No spaces or punctuation, just numbers') ?></li>
					<li><b><?php print _('Website') ?></b> - <?php print _('Must start with http:// or https://') ?></li>
					<li><b><?php print _('First Language') ?></b></li>
					<li><b><?php print _('Second Language') ?></b></li>
					<li><b><?php print _('Profession') ?></b> - <?php print _('For parents only') ?></li>
					<li><b><?php print _('Employer') ?></b> - <?php print _('For parents only') ?></li>
					<li><b><?php print _('Job Title') ?></b> - <?php print _('For parents only') ?></li>
					<li><b><?php print _('Emergency 1 Name') ?></b> - <?php print _('For students and staff only') ?></li>
					<li><b><?php print _('Emergency 1 Number 1') ?></b> - <?php print _('For students and staff only') ?></li>
					<li><b><?php print _('Emergency 1 Number 2') ?></b> - <?php print _('For students and staff only') ?></li>
					<li><b><?php print _('Emergency 1  Relationship') ?></b> - <?php print _('For students and staff only') ?></li>
					<li><b><?php print _('Emergency 2 Name') ?></b> - <?php print _('For students and staff only') ?></li>
					<li><b><?php print _('Emergency 2 Number 1') ?></b> - <?php print _('For students and staff only') ?></li>
					<li><b><?php print _('Emergency 2 Number 2') ?></b> - <?php print _('For students and staff only') ?></li>
					<li><b><?php print _('Emergency 2  Relationship') ?></b> - <?php print _('For students and staff only') ?></li>
					<li><b><?php print _('Start Date') ?></b> - yyyy-mm-dd</li>
				</ol>
			</li>
			<li><?php print _('Do not include a header row in the CSV files.') ?></li>
		</ol>
	<?php
	}
	else if ($step==2) {
		?>
		<h2>
			<?php print _('Step 2 - Data Check & Confirm') ?>
		</h2>
		<?php
		
		//Check file type
		if (($_FILES['file']['type']!="text/csv") AND ($_FILES['file']['type']!="text/comma-separated-values") AND ($_FILES['file']['type']!="text/x-comma-separated-values") AND ($_FILES['file']['type']!="application/vnd.ms-excel")) {
			?>
			<div class='error'>
				<?php print sprintf(_('Import cannot proceed, as the submitted file has a MIME-TYPE of %1$s, and as such does not appear to be a CSV file.'), $_FILES['file']['type']) ?><br/>
			</div>
			<?php
		}
		else if (($_POST["fieldDelimiter"]=="") OR ($_POST["stringEnclosure"]=="")) {
			?>
			<div class='error'>
				<?php print _('Import cannot proceed, as the "Field Delimiter" and/or "String Enclosure" fields have been left blank.') ?><br/>
			</div>
			<?php
		}
		else if ($_POST["mode"]!="sync" AND $_POST["mode"]!="import") {
			?>
			<div class='error'>
				<?php print _('Import cannot proceed, as the "Mode" field have been left blank.') ?><br/>
			</div>
			<?php
		}
		else {
			$proceed=true ;
			$mode=$_POST["mode"] ;
			
			if ($mode=="sync") { //SYNC
				//PREPARE TABLES
				print "<h4>" ;
					print _("Prepare Database Tables") ;
				print "</h4>" ;
				//Lock tables
				$lockFail=false ;
				try {
					$sql="LOCK TABLES gibbonPerson WRITE, gibbonHouse WRITE" ;
					$result=$connection2->query($sql);   
				}
				catch(PDOException $e) {
					$lockFail=true ; 
					$proceed=false ;
				}
				if ($lockFail==true) {
					print "<div class='error'>" ;
						print _("The database could not be locked for use.") ;
					print "</div>" ;	
				}
				else if ($lockFail==false) {
					print "<div class='success'>" ;
						print _("The database was successfully locked.") ;
					print "</div>" ;	
				}	
			
				if ($lockFail==FALSE) {	
					//READ IN DATA
					if ($proceed==true) {
						print "<h4>" ;
							print _("File Import") ;
						print "</h4>" ;
						$importFail=false ;
						$csvFile=$_FILES['file']['tmp_name'] ;
						$handle=fopen($csvFile, "r");
						$users=array() ;
						$userCount=0 ;
						$userSuccessCount=0 ;
						while (($data=fgetcsv($handle, 100000, stripslashes($_POST["fieldDelimiter"]), stripslashes($_POST["stringEnclosure"]))) !==FALSE) {
							if ($data[1]!="" AND $data[2]!="" AND $data[3]!="" AND $data[4]!="" AND $data[6]!="" AND $data[7]!="" AND $data[11]!="") {
								$users[$userSuccessCount]["title"]="" ; if (isset($data[0])) { $users[$userSuccessCount]["title"]=$data[0] ; }
								$users[$userSuccessCount]["surname"]="" ; if (isset($data[1])) { $users[$userSuccessCount]["surname"]=$data[1] ;  }
								$users[$userSuccessCount]["firstName"]="" ; if (isset($data[2])) { $users[$userSuccessCount]["firstName"]=$data[2] ; }
								$users[$userSuccessCount]["preferredName"]="" ; if (isset($data[3])) { $users[$userSuccessCount]["preferredName"]=$data[3] ; }
								$users[$userSuccessCount]["officialName"]="" ; if (isset($data[4])) { $users[$userSuccessCount]["officialName"]=$data[4] ; }
								$users[$userSuccessCount]["nameInCharacters"]="" ; if (isset($data[5])) { $users[$userSuccessCount]["nameInCharacters"]=$data[5] ; }
								$users[$userSuccessCount]["gender"]="" ; if (isset($data[6])) { $users[$userSuccessCount]["gender"]=$data[6] ; }
								$users[$userSuccessCount]["username"]="" ; if (isset($data[7])) { $users[$userSuccessCount]["username"]=$data[7] ; }
								$users[$userSuccessCount]["password"]="" ; if (isset($data[8])) { $users[$userSuccessCount]["password"]=$data[8] ; }
								$users[$userSuccessCount]["house"]="" ; if (isset($data[9])) { $users[$userSuccessCount]["house"]=$data[9] ; }
								$users[$userSuccessCount]["dob"]="" ; if (isset($data[10])) { $users[$userSuccessCount]["dob"]=$data[10] ; }
								$users[$userSuccessCount]["role"]="" ; if (isset($data[11])) { $users[$userSuccessCount]["role"]=$data[11] ; }
								$users[$userSuccessCount]["email"]="" ; if (isset($data[12])) { $users[$userSuccessCount]["email"]=$data[12] ; }
								$users[$userSuccessCount]["image_240"]="" ; if (isset($data[13])) { $users[$userSuccessCount]["image_240"]=$data[13] ; }
								$users[$userSuccessCount]["address1"]="" ; if (isset($data[14])) { $users[$userSuccessCount]["address1"]=$data[14] ; }
								$users[$userSuccessCount]["address1District"]="" ; if (isset($data[15])) { $users[$userSuccessCount]["address1District"]=$data[15] ; }
								$users[$userSuccessCount]["address1Country"]="" ; if (isset($data[16])) { $users[$userSuccessCount]["address1Country"]=$data[16] ; }
								$users[$userSuccessCount]["address2"]="" ; if (isset($data[17])) { $users[$userSuccessCount]["address2"]=$data[17] ; }
								$users[$userSuccessCount]["address2District"]="" ; if (isset($data[18])) { $users[$userSuccessCount]["address2District"]=$data[18] ; }
								$users[$userSuccessCount]["address2Country"]="" ; if (isset($data[19])) { $users[$userSuccessCount]["address2Country"]=$data[19] ; }
								$users[$userSuccessCount]["phone1Type"]="" ; if (isset($data[20])) { $users[$userSuccessCount]["phone1Type"]=$data[20] ; }
								$users[$userSuccessCount]["phone1CountryCode"]="" ; if (isset($data[21])) { $users[$userSuccessCount]["phone1CountryCode"]=$data[21] ; }
								$users[$userSuccessCount]["phone1"]="" ; if (isset($data[22])) { $users[$userSuccessCount]["phone1"]=preg_replace('/[^0-9+]/', '', $data[22]) ; }
								$users[$userSuccessCount]["phone2Type"]="" ; if (isset($data[23])) { $users[$userSuccessCount]["phone2Type"]=$data[23] ; }
								$users[$userSuccessCount]["phone2CountryCode"]="" ; if (isset($data[24])) { $users[$userSuccessCount]["phone2CountryCode"]=$data[24] ; }
								$users[$userSuccessCount]["phone2"]="" ; if (isset($data[25])) { $users[$userSuccessCount]["phone2"]=preg_replace('/[^0-9+]/', '', $data[25]) ; }
								$users[$userSuccessCount]["phone3Type"]="" ; if (isset($data[26])) { $users[$userSuccessCount]["phone3Type"]=$data[26] ; }
								$users[$userSuccessCount]["phone3CountryCode"]="" ; if (isset($data[27])) { $users[$userSuccessCount]["phone3CountryCode"]=$data[27] ; }
								$users[$userSuccessCount]["phone3"]="" ; if (isset($data[28])) { $users[$userSuccessCount]["phone3"]=preg_replace('/[^0-9+]/', '', $data[28]) ; }
								$users[$userSuccessCount]["phone4Type"]="" ; if (isset($data[29])) { $users[$userSuccessCount]["phone4Type"]=$data[29] ; }
								$users[$userSuccessCount]["phone4CountryCode"]="" ; if (isset($data[30])) { $users[$userSuccessCount]["phone4CountryCode"]=$data[30] ; }
								$users[$userSuccessCount]["phone4"]="" ; if (isset($data[31])) { $users[$userSuccessCount]["phone4"]=preg_replace('/[^0-9+]/', '', $data[31]) ; }
								$users[$userSuccessCount]["website"]="" ; if (isset($data[32])) { $users[$userSuccessCount]["website"]=$data[32] ; }
								$users[$userSuccessCount]["languageFirst"]="" ; if (isset($data[33])) { $users[$userSuccessCount]["languageFirst"]=$data[33] ; }
								$users[$userSuccessCount]["languageSecond"]="" ; if (isset($data[34])) { $users[$userSuccessCount]["languageSecond"]=$data[34] ; }
								$users[$userSuccessCount]["profession"]="" ; if (isset($data[35])) { $users[$userSuccessCount]["profession"]=$data[35] ; }
								$users[$userSuccessCount]["employer"]="" ; if (isset($data[36])) { $users[$userSuccessCount]["employer"]=$data[36] ; }
								$users[$userSuccessCount]["jobTitle"]="" ; if (isset($data[37])) { $users[$userSuccessCount]["jobTitle"]=$data[37] ; }
								$users[$userSuccessCount]["emergency1Name"]="" ; if (isset($data[38])) { $users[$userSuccessCount]["emergency1Name"]=$data[38] ; }
								$users[$userSuccessCount]["emergency1Number1"]="" ; if (isset($data[39])) { $users[$userSuccessCount]["emergency1Number1"]=$data[39] ; }
								$users[$userSuccessCount]["emergency1Number2"]="" ; if (isset($data[40])) { $users[$userSuccessCount]["emergency1Number2"]=$data[40] ; }
								$users[$userSuccessCount]["emergency1Relationship"]="" ; if (isset($data[41])) { $users[$userSuccessCount]["emergency1Relationship"]=$data[41] ; }
								$users[$userSuccessCount]["emergency2Name"]="" ; if (isset($data[42])) { $users[$userSuccessCount]["emergency2Name"]=$data[42] ; }
								$users[$userSuccessCount]["emergency2Number1"]="" ; if (isset($data[43])) { $users[$userSuccessCount]["emergency2Number1"]=$data[43] ; }
								$users[$userSuccessCount]["emergency2Number2"]="" ; if (isset($data[44])) { $users[$userSuccessCount]["emergency2Number2"]=$data[44] ; }
								$users[$userSuccessCount]["emergency2Relationship"]="" ; if (isset($data[45])) { $users[$userSuccessCount]["emergency2Relationship"]=$data[45] ; }
								$users[$userSuccessCount]["dateStart"]="" ; if (isset($data[46])) { $users[$userSuccessCount]["dateStart"]=$data[46] ; }
								
								$userSuccessCount++ ;
							}
							else {
								print "<div class='error'>" ;
									print sprintf(_('User with username %1$s had some information malformations.'), $data[7]) ;
								print "</div>" ;
							}
							$userCount++ ;
						}
						fclose($handle);
						if ($userSuccessCount==0) {
							print "<div class='error'>" ;
								print _("No useful users were detected in the import file (perhaps they did not meet minimum requirements), so the import will be aborted.") ;
							print "</div>" ;
							$proceed=false ;
						}
						else if ($userSuccessCount<$userCount) {
							print "<div class='error'>" ;
								print _("Some users could not be successfully read or used, so the import will be aborted.") ;
							print "</div>" ;
							$proceed=false ;
						}
						else if ($userSuccessCount==$userCount) {
							print "<div class='success'>" ;
								print _("All users could be read and used, so the import will proceed.") ;
							print "</div>" ;
						}
						else {
							print "<div class='error'>" ;
								print _("An unknown error occured, so the import will be aborted.") ;
							print "</div>" ;
							$proceed=false ;
						}
					}
				
				
					if ($proceed==TRUE) {
						//SET USERS NOT IN IMPORT TO LEFT
						print "<h4>" ;
							print _("Set To Left") ;
						print "</h4>" ;
						$setLeftFail=FALSE ;
						$usernameWhere="(" ; 
						foreach ($users AS $user) {
							$usernameWhere.="'" . $user["username"] . "'," ;
						}
						$usernameWhere=substr($usernameWhere,0,-1) ; 
						$usernameWhere.=")" ; 
					
						try {
							$data=array(); 
							$sql="UPDATE gibbonPerson SET status='Left' WHERE username NOT IN $usernameWhere AND username <> '" . $_SESSION[$guid]["username"] . "'" ;
							$result=$connection2->prepare($sql);
							$result->execute($data);
						}
						catch(PDOException $e) { 
							$setLeftFail=TRUE ;
						}
					
						if ($setLeftFail==TRUE) {
							print "<div class='error'>" ;
								print _("An error was encountered in setting users not in the import to Left") ;
							print "</div>" ;
						}
						else {
							print "<div class='success'>" ;
								print _("All users not in the import (except you) have been set to left.") ;
							print "</div>" ;
						}
			
						//CHECK USERS IN IMPORT FOR EXISTENCE, IF NOT EXIST, ADD THEM, IF THEY ARE UPDATE THEM
						print "<h4>" ;
							print _("Update & Insert") ;
						print "</h4>" ;
						foreach ($users AS $user) {
							$userProceed=TRUE ;
							try {
								$data=array("username"=>$user["username"]); 
								$sql="SELECT * FROM gibbonPerson WHERE username=:username" ;
								$result=$connection2->prepare($sql);
								$result->execute($data);
							}
							catch(PDOException $e) { 
								$userProceed=FALSE ;
							}
						
							if ($userProceed==FALSE) {
								print "<div class='error'>" ;
									print _("There was an error locating user:") . " " . $user["username"] . "." ;
								print "</div>" ;
							}
							else {
								if ($result->rowCount()==1) {
									$row=$result->fetch() ;
									//UPDATE USER
									$updateUserFail=FALSE ;
									$role="" ;
									$roleAll=$row["gibbonRoleIDAll"] ;
									if ($user["role"]=="Student") {
										$role="003" ;
									}
									if ($user["role"]=="Teacher") {
										$role="002" ;
									}
									if ($user["role"]=="Support Staff") {
										$role="006" ;
									}
									if ($user["role"]=="Parent") {
										$role="004" ;
									}
									if (strpos($role, $row["gibbonRoleIDAll"])===0) {
										$roleAll=$row["gibbonRoleIDAll"]. "," . $role ;
									}
								
									try {
										$data=array("title"=>$user["title"], "surname"=>$user["surname"], "firstName"=>$user["firstName"], "preferredName"=>$user["preferredName"], "officialName"=>$user["officialName"], "gender"=>$user["gender"], "house"=>$user["house"], "dob"=>$user["dob"], "gibbonRoleIDPrimary"=>$role, "gibbonRoleIDAll"=>$roleAll, "email"=>$user["email"], "image_240"=>$user["image_240"], "address1"=>$user["address1"], "address1District"=>$user["address1District"], "address1Country"=>$user["address1Country"], "address2"=>$user["address2"], "address2District"=>$user["address2District"], "address2Country"=>$user["address2Country"], "phone1Type"=>$user["phone1Type"], "phone1CountryCode"=>$user["phone1CountryCode"], "phone1"=>$user["phone1"], "phone2Type"=>$user["phone2Type"], "phone2CountryCode"=>$user["phone2CountryCode"], "phone2"=>$user["phone2"], "phone3Type"=>$user["phone3Type"], "phone3CountryCode"=>$user["phone3CountryCode"], "phone3"=>$user["phone3"], "phone4Type"=>$user["phone4Type"], "phone4CountryCode"=>$user["phone4CountryCode"], "phone4"=>$user["phone4"], "website"=>$user["website"], "languageFirst"=>$user["languageFirst"], "languageSecond"=>$user["languageSecond"], "profession"=>$user["profession"], "employer"=>$user["employer"], "jobTitle"=>$user["jobTitle"], "emergency1Name"=>$user["emergency1Name"], "emergency1Number1"=>$user["emergency1Number1"], "emergency1Number2"=>$user["emergency1Number2"], "emergency1Relationship"=>$user["emergency1Relationship"], "emergency2Name"=>$user["emergency2Name"], "emergency2Number1"=>$user["emergency2Number1"], "emergency2Number2"=>$user["emergency2Number2"], "emergency2Relationship"=>$user["emergency2Relationship"], "dateStart"=>$user["dateStart"], "nameInCharacters"=>$user["nameInCharacters"], "username"=>$user["username"]); 
										$sql="UPDATE gibbonPerson SET title=:title, surname=:surname, firstName=:firstName, preferredName=:preferredName, officialName=:officialName, gender=:gender, gibbonHouseID=(SELECT gibbonHouseID FROM gibbonHouse WHERE nameShort=:house), dob=:dob, gibbonRoleIDPrimary=:gibbonRoleIDPrimary, gibbonRoleIDAll=:gibbonRoleIDAll, status='Full', email=:email, image_240=:image_240, address1=:address1, address1District=:address1District, address1Country=:address1Country, address2=:address2, address2District=:address2District, address2Country=:address2Country, phone1Type=:phone1Type, phone1CountryCode=:phone1CountryCode, phone1=:phone1, phone2Type=:phone2Type, phone2CountryCode=:phone2CountryCode, phone2=:phone2, phone3Type=:phone3Type, phone3CountryCode=:phone3CountryCode, phone3=:phone3, phone4Type=:phone4Type, phone4CountryCode=:phone4CountryCode, phone4=:phone4, website=:website, languageFirst=:languageFirst, languageSecond=:languageSecond, profession=:profession, employer=:employer, jobTitle=:jobTitle, emergency1Name=:emergency1Name, emergency1Number1=:emergency1Number1, emergency1Number2=:emergency1Number2, emergency1Relationship=:emergency1Relationship, emergency2Name=:emergency2Name, emergency2Number1=:emergency2Number1, emergency2Number2=:emergency2Number2, emergency2Relationship=:emergency2Relationship, dateStart=:dateStart, dateEnd=NULL, nameInCharacters=:nameInCharacters WHERE username=:username" ;
										$result=$connection2->prepare($sql);
										$result->execute($data);
									}
									catch(PDOException $e) { 
										print $e->getMessage() ;
										$updateUserFail=TRUE ;
									}
								
									//Spit out results
									if ($updateUserFail==TRUE) {
										print "<div class='error'>" ;
											print _("There was an error updating user:") . " " . $user["username"] . "." ;
										print "</div>" ;
									}
									else {
										print "<div class='success'>" ;
											print sprintf(_('User %1$s was successfully updated.'), $user["username"]) ;
										print "</div>" ;
									}
								}
								else if ($result->rowCount()==0) {
									//ADD USER
									$addUserFail=FALSE ;
									$salt=getSalt() ;
									if ($user["password"]!="") {
										$password=$user["password"];
									}
									else if ($_POST["defaultPassword"]!="") {
										$password=$_POST["defaultPassword"];
									}
									else {
										$password=randomPassword(8);
									}
									$passwordStrong=hash("sha256", $salt.$password) ;
									$role="" ;
									if ($user["role"]=="Student") {
										$role="003" ;
									}
									if ($user["role"]=="Teacher") {
										$role="002" ;
									}
									if ($user["role"]=="Support Staff") {
										$role="006" ;
									}
									if ($user["role"]=="Parent") {
										$role="004" ;
									}
									$roleAll=$role ;
								
									if ($role=="") {
										print "<div class='error'>" ;
											print _("There was an error with the role of user:") . " " . $user["username"] . "." ;
										print "</div>" ;
									}
									else {
										try {
											$data=array("title"=>$user["title"], "surname"=>$user["surname"], "firstName"=>$user["firstName"], "preferredName"=>$user["preferredName"], "officialName"=>$user["officialName"], "gender"=>$user["gender"], "house"=>$user["house"], "dob"=>$user["dob"], "username"=>$user["username"], "passwordStrongSalt"=>$salt, "passwordStrong"=>$passwordStrong, "gibbonRoleIDPrimary"=>$role, "gibbonRoleIDAll"=>$roleAll, "email"=>$user["email"], "image_240"=>$user["image_240"], "address1"=>$user["address1"], "address1District"=>$user["address1District"], "address1Country"=>$user["address1Country"], "address2"=>$user["address2"], "address2District"=>$user["address2District"], "address2Country"=>$user["address2Country"], "phone1Type"=>$user["phone1Type"], "phone1CountryCode"=>$user["phone1CountryCode"], "phone1"=>$user["phone1"], "phone2Type"=>$user["phone2Type"], "phone2CountryCode"=>$user["phone2CountryCode"], "phone2"=>$user["phone2"], "phone3Type"=>$user["phone3Type"], "phone3CountryCode"=>$user["phone3CountryCode"], "phone3"=>$user["phone3"], "phone4Type"=>$user["phone4Type"], "phone4CountryCode"=>$user["phone4CountryCode"], "phone4"=>$user["phone4"], "website"=>$user["website"], "languageFirst"=>$user["languageFirst"], "languageSecond"=>$user["languageSecond"], "profession"=>$user["profession"], "employer"=>$user["employer"], "jobTitle"=>$user["jobTitle"], "emergency1Name"=>$user["emergency1Name"], "emergency1Number1"=>$user["emergency1Number1"], "emergency1Number2"=>$user["emergency1Number2"], "emergency1Relationship"=>$user["emergency1Relationship"], "emergency2Name"=>$user["emergency2Name"], "emergency2Number1"=>$user["emergency2Number1"], "emergency2Number2"=>$user["emergency2Number2"], "emergency2Relationship"=>$user["emergency2Relationship"], "dateStart"=>$user["dateStart"], "nameInCharacters"=>$user["nameInCharacters"]); 
											$sql="INSERT INTO gibbonPerson SET title=:title, surname=:surname, firstName=:firstName, preferredName=:preferredName, officialName=:officialName, gender=:gender, gibbonHouseID=(SELECT gibbonHouseID FROM gibbonHouse WHERE nameShort=:house), dob=:dob, status='Full', username=:username, passwordStrongSalt=:passwordStrongSalt, passwordStrong=:passwordStrong, gibbonRoleIDPrimary=:gibbonRoleIDPrimary, gibbonRoleIDAll=:gibbonRoleIDAll, passwordForceReset='Y', email=:email, image_240=:image_240, address1=:address1, address1District=:address1District, address1Country=:address1Country, address2=:address2, address2District=:address2District, address2Country=:address2Country, phone1Type=:phone1Type, phone1CountryCode=:phone1CountryCode, phone1=:phone1, phone2Type=:phone2Type, phone2CountryCode=:phone2CountryCode, phone2=:phone2, phone3Type=:phone3Type, phone3CountryCode=:phone3CountryCode, phone3=:phone3, phone4Type=:phone4Type, phone4CountryCode=:phone4CountryCode, phone4=:phone4, website=:website, languageFirst=:languageFirst, languageSecond=:languageSecond, profession=:profession, employer=:employer, jobTitle=:jobTitle, emergency1Name=:emergency1Name, emergency1Number1=:emergency1Number1, emergency1Number2=:emergency1Number2, emergency1Relationship=:emergency1Relationship, emergency2Name=:emergency2Name, emergency2Number1=:emergency2Number1, emergency2Number2=:emergency2Number2, emergency2Relationship=:emergency2Relationship, dateStart=:dateStart, dateEnd=NULL, nameInCharacters=:nameInCharacters" ;
											$result=$connection2->prepare($sql);
											$result->execute($data);
										}
										catch(PDOException $e) { 
											$addUserFail=TRUE ;
											print $e->getMessage() ;
										}
									
										//Spit out results
										if ($addUserFail==TRUE) {
											print "<div class='error'>" ;
												print _("There was an error creating user:") . " " . $user["username"] . "." ;
											print "</div>" ;
										}
										else {
											print "<div class='success'>" ;
												print sprintf(_('User %1$s was successfully created with password %2$s.'), $user["username"], $password) ;
											print "</div>" ;
										}
									}
								}
								else {
									print "<div class='error'>" ;
										print _("There was an error locating user:") . " " . $user["username"] . "." ;
									print "</div>" ;
								}	
							}
						}
					}
				
					//UNLOCK TABLES
					try {
						$sql="UNLOCK TABLES" ;
						$result=$connection2->query($sql);   
					}
					catch(PDOException $e) { }	
				}	
			}
			else if ($mode=="import") { //IMPORT
				//PREPARE TABLES
				print "<h4>" ;
					print _("Prepare Database Tables") ;
				print "</h4>" ;
				//Lock tables
				$lockFail=false ;
				try {
					$sql="LOCK TABLES gibbonPerson WRITE, gibbonHouse WRITE" ;
					$result=$connection2->query($sql);   
				}
				catch(PDOException $e) {
					$lockFail=true ; 
					$proceed=false ;
				}
				if ($lockFail==true) {
					print "<div class='error'>" ;
						print _("The database could not be locked for use.") ;
					print "</div>" ;	
				}
				else if ($lockFail==false) {
					print "<div class='success'>" ;
						print _("The database was successfully locked.") ;
					print "</div>" ;	
				}	
			
				if ($lockFail==FALSE) {	
					//READ IN DATA
					if ($proceed==true) {
						print "<h4>" ;
							print _("File Import") ;
						print "</h4>" ;
						$importFail=false ;
						$csvFile=$_FILES['file']['tmp_name'] ;
						$handle=fopen($csvFile, "r");
						$users=array() ;
						$userCount=0 ;
						$userSuccessCount=0 ;
						while (($data=fgetcsv($handle, 100000, stripslashes($_POST["fieldDelimiter"]), stripslashes($_POST["stringEnclosure"]))) !==FALSE) {
							if ($data[1]!="" AND $data[2]!="" AND $data[3]!="" AND $data[4]!="" AND $data[6]!="" AND $data[7]!="" AND $data[11]!="") {
								$users[$userSuccessCount]["title"]="" ; if (isset($data[0])) { $users[$userSuccessCount]["title"]=$data[0] ; }
								$users[$userSuccessCount]["surname"]="" ; if (isset($data[1])) { $users[$userSuccessCount]["surname"]=$data[1] ;  }
								$users[$userSuccessCount]["firstName"]="" ; if (isset($data[2])) { $users[$userSuccessCount]["firstName"]=$data[2] ; }
								$users[$userSuccessCount]["preferredName"]="" ; if (isset($data[3])) { $users[$userSuccessCount]["preferredName"]=$data[3] ; }
								$users[$userSuccessCount]["officialName"]="" ; if (isset($data[4])) { $users[$userSuccessCount]["officialName"]=$data[4] ; }
								$users[$userSuccessCount]["nameInCharacters"]="" ; if (isset($data[5])) { $users[$userSuccessCount]["nameInCharacters"]=$data[5] ; }
								$users[$userSuccessCount]["gender"]="" ; if (isset($data[6])) { $users[$userSuccessCount]["gender"]=$data[6] ; }
								$users[$userSuccessCount]["username"]="" ; if (isset($data[7])) { $users[$userSuccessCount]["username"]=$data[7] ; }
								$users[$userSuccessCount]["password"]="" ; if (isset($data[8])) { $users[$userSuccessCount]["password"]=$data[8] ; }
								$users[$userSuccessCount]["house"]="" ; if (isset($data[9])) { $users[$userSuccessCount]["house"]=$data[9] ; }
								$users[$userSuccessCount]["dob"]="" ; if (isset($data[10])) { $users[$userSuccessCount]["dob"]=$data[10] ; }
								$users[$userSuccessCount]["role"]="" ; if (isset($data[11])) { $users[$userSuccessCount]["role"]=$data[11] ; }
								$users[$userSuccessCount]["email"]="" ; if (isset($data[12])) { $users[$userSuccessCount]["email"]=$data[12] ; }
								$users[$userSuccessCount]["image_240"]="" ; if (isset($data[13])) { $users[$userSuccessCount]["image_240"]=$data[13] ; }
								$users[$userSuccessCount]["address1"]="" ; if (isset($data[14])) { $users[$userSuccessCount]["address1"]=$data[14] ; }
								$users[$userSuccessCount]["address1District"]="" ; if (isset($data[15])) { $users[$userSuccessCount]["address1District"]=$data[15] ; }
								$users[$userSuccessCount]["address1Country"]="" ; if (isset($data[16])) { $users[$userSuccessCount]["address1Country"]=$data[16] ; }
								$users[$userSuccessCount]["address2"]="" ; if (isset($data[17])) { $users[$userSuccessCount]["address2"]=$data[17] ; }
								$users[$userSuccessCount]["address2District"]="" ; if (isset($data[18])) { $users[$userSuccessCount]["address2District"]=$data[18] ; }
								$users[$userSuccessCount]["address2Country"]="" ; if (isset($data[19])) { $users[$userSuccessCount]["address2Country"]=$data[19] ; }
								$users[$userSuccessCount]["phone1Type"]="" ; if (isset($data[20])) { $users[$userSuccessCount]["phone1Type"]=$data[20] ; }
								$users[$userSuccessCount]["phone1CountryCode"]="" ; if (isset($data[21])) { $users[$userSuccessCount]["phone1CountryCode"]=$data[21] ; }
								$users[$userSuccessCount]["phone1"]="" ; if (isset($data[22])) { $users[$userSuccessCount]["phone1"]=preg_replace('/[^0-9+]/', '', $data[22]) ; }
								$users[$userSuccessCount]["phone2Type"]="" ; if (isset($data[23])) { $users[$userSuccessCount]["phone2Type"]=$data[23] ; }
								$users[$userSuccessCount]["phone2CountryCode"]="" ; if (isset($data[24])) { $users[$userSuccessCount]["phone2CountryCode"]=$data[24] ; }
								$users[$userSuccessCount]["phone2"]="" ; if (isset($data[25])) { $users[$userSuccessCount]["phone2"]=preg_replace('/[^0-9+]/', '', $data[25]) ; }
								$users[$userSuccessCount]["phone3Type"]="" ; if (isset($data[26])) { $users[$userSuccessCount]["phone3Type"]=$data[26] ; }
								$users[$userSuccessCount]["phone3CountryCode"]="" ; if (isset($data[27])) { $users[$userSuccessCount]["phone3CountryCode"]=$data[27] ; }
								$users[$userSuccessCount]["phone3"]="" ; if (isset($data[28])) { $users[$userSuccessCount]["phone3"]=preg_replace('/[^0-9+]/', '', $data[28]) ; }
								$users[$userSuccessCount]["phone4Type"]="" ; if (isset($data[29])) { $users[$userSuccessCount]["phone4Type"]=$data[29] ; }
								$users[$userSuccessCount]["phone4CountryCode"]="" ; if (isset($data[30])) { $users[$userSuccessCount]["phone4CountryCode"]=$data[30] ; }
								$users[$userSuccessCount]["phone4"]="" ; if (isset($data[31])) { $users[$userSuccessCount]["phone4"]=preg_replace('/[^0-9+]/', '', $data[31]) ; }
								$users[$userSuccessCount]["website"]="" ; if (isset($data[32])) { $users[$userSuccessCount]["website"]=$data[32] ; }
								$users[$userSuccessCount]["languageFirst"]="" ; if (isset($data[33])) { $users[$userSuccessCount]["languageFirst"]=$data[33] ; }
								$users[$userSuccessCount]["languageSecond"]="" ; if (isset($data[34])) { $users[$userSuccessCount]["languageSecond"]=$data[34] ; }
								$users[$userSuccessCount]["profession"]="" ; if (isset($data[35])) { $users[$userSuccessCount]["profession"]=$data[35] ; }
								$users[$userSuccessCount]["employer"]="" ; if (isset($data[36])) { $users[$userSuccessCount]["employer"]=$data[36] ; }
								$users[$userSuccessCount]["jobTitle"]="" ; if (isset($data[37])) { $users[$userSuccessCount]["jobTitle"]=$data[37] ; }
								$users[$userSuccessCount]["emergency1Name"]="" ; if (isset($data[38])) { $users[$userSuccessCount]["emergency1Name"]=$data[38] ; }
								$users[$userSuccessCount]["emergency1Number1"]="" ; if (isset($data[39])) { $users[$userSuccessCount]["emergency1Number1"]=$data[39] ; }
								$users[$userSuccessCount]["emergency1Number2"]="" ; if (isset($data[40])) { $users[$userSuccessCount]["emergency1Number2"]=$data[40] ; }
								$users[$userSuccessCount]["emergency1Relationship"]="" ; if (isset($data[41])) { $users[$userSuccessCount]["emergency1Relationship"]=$data[41] ; }
								$users[$userSuccessCount]["emergency2Name"]="" ; if (isset($data[42])) { $users[$userSuccessCount]["emergency2Name"]=$data[42] ; }
								$users[$userSuccessCount]["emergency2Number1"]="" ; if (isset($data[43])) { $users[$userSuccessCount]["emergency2Number1"]=$data[43] ; }
								$users[$userSuccessCount]["emergency2Number2"]="" ; if (isset($data[44])) { $users[$userSuccessCount]["emergency2Number2"]=$data[44] ; }
								$users[$userSuccessCount]["emergency2Relationship"]="" ; if (isset($data[45])) { $users[$userSuccessCount]["emergency2Relationship"]=$data[45] ; }
								$users[$userSuccessCount]["dateStart"]="" ; if (isset($data[46])) { $users[$userSuccessCount]["dateStart"]=$data[46] ; }
								
								$userSuccessCount++ ;
							}
							else {
								print "<div class='error'>" ;
									print sprintf(_('User with username %1$s had some information malformations.'), $data[7]) ;
								print "</div>" ;
							}
							$userCount++ ;
						}
						fclose($handle);
						if ($userSuccessCount==0) {
							print "<div class='error'>" ;
								print _("No useful users were detected in the import file (perhaps they did not meet minimum requirements), so the import will be aborted.") ;
							print "</div>" ;
							$proceed=false ;
						}
						else if ($userSuccessCount<$userCount) {
							print "<div class='error'>" ;
								print _("Some users could not be successfully read or used, so the import will be aborted.") ;
							print "</div>" ;
							$proceed=false ;
						}
						else if ($userSuccessCount==$userCount) {
							print "<div class='success'>" ;
								print _("All users could be read and used, so the import will proceed.") ;
							print "</div>" ;
						}
						else {
							print "<div class='error'>" ;
								print _("An unknown error occured, so the import will be aborted.") ;
							print "</div>" ;
							$proceed=false ;
						}
					}
				
				
					if ($proceed==TRUE) {
						//CHECK USERS IN IMPORT FOR EXISTENCE, IF NOT EXIST, ADD THEM, IF THEY ARE UPDATE THEM
						print "<h4>" ;
							print _("Check & Insert") ;
						print "</h4>" ;
						foreach ($users AS $user) {
							$userProceed=TRUE ;
							try {
								$data=array("username"=>$user["username"]); 
								$sql="SELECT * FROM gibbonPerson WHERE username=:username" ;
								$result=$connection2->prepare($sql);
								$result->execute($data);
							}
							catch(PDOException $e) { 
								$userProceed=FALSE ;
							}
						
							if ($userProceed==FALSE) {
								print "<div class='error'>" ;
									print _("There was an error locating user:") . " " . $user["username"] . "." ;
								print "</div>" ;
							}
							else {
								if ($result->rowCount()==1) {
									$row=$result->fetch() ;
									//USER ALREADY EXISTS
									print "<div class='error'>" ;
										print _("There was an error inserting user:") . " " . $user["username"] . "." ;
									print "</div>" ;
								}
								else if ($result->rowCount()==0) {
									//ADD USER
									$addUserFail=FALSE ;
									$salt=getSalt() ;
									if ($user["password"]!="") {
										$password=$user["password"];
									}
									else if ($_POST["defaultPassword"]!="") {
										$password=$_POST["defaultPassword"];
									}
									else {
										$password=randomPassword(8);
									}
									$passwordStrong=hash("sha256", $salt.$password) ;
									$role="" ;
									if ($user["role"]=="Student") {
										$role="003" ;
									}
									if ($user["role"]=="Teacher") {
										$role="002" ;
									}
									if ($user["role"]=="Support Staff") {
										$role="006" ;
									}
									if ($user["role"]=="Parent") {
										$role="004" ;
									}
									$roleAll=$role ;
								
									if ($role=="") {
										print "<div class='error'>" ;
											print _("There was an error with the role of user:") . " " . $user["username"] . "." ;
										print "</div>" ;
									}
									else {
										try {
											$data=array("title"=>$user["title"], "surname"=>$user["surname"], "firstName"=>$user["firstName"], "preferredName"=>$user["preferredName"], "officialName"=>$user["officialName"], "gender"=>$user["gender"], "house"=>$user["house"], "dob"=>$user["dob"], "username"=>$user["username"], "passwordStrongSalt"=>$salt, "passwordStrong"=>$passwordStrong, "gibbonRoleIDPrimary"=>$role, "gibbonRoleIDAll"=>$roleAll, "email"=>$user["email"], "image_240"=>$user["image_240"], "address1"=>$user["address1"], "address1District"=>$user["address1District"], "address1Country"=>$user["address1Country"], "address2"=>$user["address2"], "address2District"=>$user["address2District"], "address2Country"=>$user["address2Country"], "phone1Type"=>$user["phone1Type"], "phone1CountryCode"=>$user["phone1CountryCode"], "phone1"=>$user["phone1"], "phone2Type"=>$user["phone2Type"], "phone2CountryCode"=>$user["phone2CountryCode"], "phone2"=>$user["phone2"], "phone3Type"=>$user["phone3Type"], "phone3CountryCode"=>$user["phone3CountryCode"], "phone3"=>$user["phone3"], "phone4Type"=>$user["phone4Type"], "phone4CountryCode"=>$user["phone4CountryCode"], "phone4"=>$user["phone4"], "website"=>$user["website"], "languageFirst"=>$user["languageFirst"], "languageSecond"=>$user["languageSecond"], "profession"=>$user["profession"], "employer"=>$user["employer"], "jobTitle"=>$user["jobTitle"], "emergency1Name"=>$user["emergency1Name"], "emergency1Number1"=>$user["emergency1Number1"], "emergency1Number2"=>$user["emergency1Number2"], "emergency1Relationship"=>$user["emergency1Relationship"], "emergency2Name"=>$user["emergency2Name"], "emergency2Number1"=>$user["emergency2Number1"], "emergency2Number2"=>$user["emergency2Number2"], "emergency2Relationship"=>$user["emergency2Relationship"], "dateStart"=>$user["dateStart"], "nameInCharacters"=>$user["nameInCharacters"]); 
											$sql="INSERT INTO gibbonPerson SET title=:title, surname=:surname, firstName=:firstName, preferredName=:preferredName, officialName=:officialName, gender=:gender, gibbonHouseID=(SELECT gibbonHouseID FROM gibbonHouse WHERE nameShort=:house), dob=:dob, status='Full', username=:username, passwordStrongSalt=:passwordStrongSalt, passwordStrong=:passwordStrong, gibbonRoleIDPrimary=:gibbonRoleIDPrimary, gibbonRoleIDAll=:gibbonRoleIDAll, passwordForceReset='Y', email=:email, image_240=:image_240, address1=:address1, address1District=:address1District, address1Country=:address1Country, address2=:address2, address2District=:address2District, address2Country=:address2Country, phone1Type=:phone1Type, phone1CountryCode=:phone1CountryCode, phone1=:phone1, phone2Type=:phone2Type, phone2CountryCode=:phone2CountryCode, phone2=:phone2, phone3Type=:phone3Type, phone3CountryCode=:phone3CountryCode, phone3=:phone3, phone4Type=:phone4Type, phone4CountryCode=:phone4CountryCode, phone4=:phone4, website=:website, languageFirst=:languageFirst, languageSecond=:languageSecond, profession=:profession, employer=:employer, jobTitle=:jobTitle, emergency1Name=:emergency1Name, emergency1Number1=:emergency1Number1, emergency1Number2=:emergency1Number2, emergency1Relationship=:emergency1Relationship, emergency2Name=:emergency2Name, emergency2Number1=:emergency2Number1, emergency2Number2=:emergency2Number2, emergency2Relationship=:emergency2Relationship, dateStart=:dateStart, dateEnd=NULL, nameInCharacters=:nameInCharacters" ;
											$result=$connection2->prepare($sql);
											$result->execute($data);
										}
										catch(PDOException $e) { 
											$addUserFail=TRUE ;
											print $e->getMessage() ;
										}
									
										//Spit out results
										if ($addUserFail==TRUE) {
											print "<div class='error'>" ;
												print _("There was an error creating user:") . " " . $user["username"] . "." ;
											print "</div>" ;
										}
										else {
											print "<div class='success'>" ;
												print sprintf(_('User %1$s was successfully created with password %2$s.'), $user["username"], $password) ;
											print "</div>" ;
										}
									}
								}
								else {
									print "<div class='error'>" ;
										print _("There was an error locating user:") . " " . $user["username"] . "." ;
									print "</div>" ;
								}	
							}
						}
					}
				
					//UNLOCK TABLES
					try {
						$sql="UNLOCK TABLES" ;
						$result=$connection2->query($sql);   
					}
					catch(PDOException $e) { }	
				}	
			}		
		}
	}
}
?>