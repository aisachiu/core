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

//Module includes from User Admin (for custom fields)
include "./modules/User Admin/moduleFunctions.php" ;

$proceed=FALSE ;
$public=FALSE ;

if (isset($_SESSION[$guid]["username"])==FALSE) {
	$public=TRUE ;

	//Get public access
	$publicApplications=getSettingByScope($connection2, 'Application Form', 'publicApplications') ;
	if ($publicApplications=="Y") {
		$proceed=TRUE ;
	}
}
else {
	if (isActionAccessible($guid, $connection2, "/modules/Students/applicationForm.php")!=FALSE) {
		$proceed=TRUE ;
	}
}

//Set gibbonPersonID of the person completing the application
$gibbonPersonID=NULL ;
if (isset($_SESSION[$guid]["gibbonPersonID"])) {
	$gibbonPersonID=$_SESSION[$guid]["gibbonPersonID"] ;
}


if ($proceed==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print __($guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	if (isset($_SESSION[$guid]["username"])) {
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . $_SESSION[$guid]["organisationNameShort"] . " " . __($guid, 'Application Form') . "</div>" ;
	}
	else {
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > </div><div class='trailEnd'>" . $_SESSION[$guid]["organisationNameShort"] . " " . __($guid, 'Application Form') . "</div>" ;
	}
	print "</div>" ;

	//Get intro
	$intro=getSettingByScope($connection2, 'Application Form', 'introduction') ;
	if ($intro!="") {
		print "<p>" ;
			print $intro ;
		print "</p>" ;
	}

	if (isset($_SESSION[$guid]["username"])==false) {
		print "<div class='warning' style='font-weight: bold'>" . sprintf(__($guid, 'If you already have an account for %1$s %2$s, please log in now to prevent creation of duplicate data about you! Once logged in, you can find the form under People > Students in the main menu.'), $_SESSION[$guid]["organisationNameShort"], $_SESSION[$guid]["systemName"]) . " " . sprintf(__($guid, 'If you do not have an account for %1$s %2$s, please use the form below.'), $_SESSION[$guid]["organisationNameShort"], $_SESSION[$guid]["systemName"]) . "</div>" ;
	}

	if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
	$addReturnMessage="" ;
	$class="error" ;
	if (!($addReturn=="")) {
		if ($addReturn=="fail0") {
			$addReturnMessage=__($guid, "Your request failed because you do not have access to this action.") ;
		}
		else if ($addReturn=="fail2") {
			$addReturnMessage=__($guid, "Your request failed due to a database error.") ;
		}
		else if ($addReturn=="fail3") {
			$addReturnMessage=__($guid, "Your request failed because your inputs were invalid.") ;
		}
		else if ($addReturn=="fail4") {
			$addReturnMessage=__($guid, "Your request failed because your inputs were invalid.") ;
		}
		else if ($addReturn=="success0" OR $addReturn=="success1" OR $addReturn=="success2"  OR $addReturn=="success4") {
			print "<script type='text/javascript'>" ;
				print "$(document).ready(function(){" ;
					print "alert('Your application was successfully submitted. Please read the information in the green box above the application form for additional information.') ;" ;
				print "});" ;
			print "</script>" ;
			if ($addReturn=="success0") {
				$addReturnMessage=__($guid, "Your application was successfully submitted. Our admissions team will review your application and be in touch in due course.") ;
			}
			else if ($addReturn=="success1") {
				$addReturnMessage=__($guid, "Your application was successfully submitted and payment has been made to your credit card. Our admissions team will review your application and be in touch in due course.") ;
			}
			else if ($addReturn=="success2") {
				$addReturnMessage=__($guid, "Your application was successfully submitted, but payment could not be made to your credit card. Our admissions team will review your application and be in touch in due course.") ;
			}
			else if ($addReturn=="success3") {
				$addReturnMessage=__($guid, "Your application was successfully submitted, payment has been made to your credit card, but there has been an error recording your payment. Please print this screen and contact the school ASAP. Our admissions team will review your application and be in touch in due course.") ;
			}
			else if ($addReturn=="success4") {
				$addReturnMessage=__($guid, "Your application was successfully submitted, but payment could not be made as the payment gateway does not support the system's currency. Our admissions team will review your application and be in touch in due course.") ;
			}
			if (isset($_GET["id"])) {
				if ($_GET["id"]!="") {
					$addReturnMessage=$addReturnMessage . "<br/><br/>" . __($guid, 'If you need to contact the school in reference to this application, please quote the following number:') . " <b><u>" . $_GET["id"] . "</b></u>." ;
				}
			}
			if ($_SESSION[$guid]["organisationAdmissionsName"]!="" AND $_SESSION[$guid]["organisationAdmissionsEmail"]!="") {
				$addReturnMessage=$addReturnMessage . "<br/><br/>Please contact <a href='mailto:" . $_SESSION[$guid]["organisationAdmissionsEmail"] . "'>" . $_SESSION[$guid]["organisationAdmissionsName"] . "</a> if you have any questions, comments or complaints." ;
			}

			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $addReturnMessage;
		print "</div>" ;
	}

	$currency=getSettingByScope($connection2, "System", "currency") ;
	$applicationFee=getSettingByScope($connection2, "Application Form", "applicationFee") ;
	$enablePayments=getSettingByScope($connection2, "System", "enablePayments") ;
	$paypalAPIUsername=getSettingByScope($connection2, "System", "paypalAPIUsername") ;
	$paypalAPIPassword=getSettingByScope($connection2, "System", "paypalAPIPassword") ;
	$paypalAPISignature=getSettingByScope($connection2, "System", "paypalAPISignature") ;

	if ($applicationFee>0 AND is_numeric($applicationFee)) {
		print "<div class='warning'>" ;
			print __($guid, "Please note that there is an application fee of:") . " <b><u>" . $currency . $applicationFee . "</u></b>." ;
			if ($enablePayments=="Y" AND $paypalAPIUsername!="" AND $paypalAPIPassword!="" AND $paypalAPISignature!="") {
				print " " . __($guid, 'Payment must be made by credit card, using our secure PayPal payment gateway. When you press Submit at the end of this form, you will be directed to PayPal in order to make payment. During this process we do not see or store your credit card details.') ;
			}
		print "</div>" ;
	}

	?>

	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/applicationFormProcess.php" ?>" enctype="multipart/form-data">
		<table class='smallIntBorder fullWidth' cellspacing='0'>
			<tr class='break'>
				<td colspan=2>
					<h3><?php print __($guid, 'Student') ?></h3>
				</td>
			</tr>

			<tr>
				<td colspan=2>
					<h4><?php print __($guid, 'Student Personal Data') ?></h4>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b><?php print __($guid, 'Surname') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Family name as shown in ID documents.') ?></span>
				</td>
				<td class="right">
					<input name="surname" id="surname" maxlength=30 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var surname=new LiveValidation('surname');
						surname.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'First Name') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'First name as shown in ID documents.') ?></span>
				</td>
				<td class="right">
					<input name="firstName" id="firstName" maxlength=30 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var firstName=new LiveValidation('firstName');
						firstName.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Preferred Name') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Most common name, alias, nickname, etc.') ?></span>
				</td>
				<td class="right">
					<input name="preferredName" id="preferredName" maxlength=30 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var preferredName=new LiveValidation('preferredName');
						preferredName.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Official Name') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Full name as shown in ID documents.') ?></span>
				</td>
				<td class="right">
					<input title='Please enter full name as shown in ID documents' name="officialName" id="officialName" maxlength=150 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var officialName=new LiveValidation('officialName');
						officialName.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Name In Characters') ?></b><br/>
					<span class="emphasis small"><?php print __($guid, 'Chinese or other character-based name.') ?></span>
				</td>
				<td class="right">
					<input name="nameInCharacters" id="nameInCharacters" maxlength=20 value="" type="text" class="standardWidth">
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Gender') ?> *</b><br/>
				</td>
				<td class="right">
					<select name="gender" id="gender" class="standardWidth">
						<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
						<option value="F"><?php print __($guid, 'Female') ?></option>
						<option value="M"><?php print __($guid, 'Male') ?></option>
					</select>
					<script type="text/javascript">
						var gender=new LiveValidation('gender');
						gender.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Date of Birth') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Format:') . " " . $_SESSION[$guid]["i18n"]["dateFormat"]  ?></span>
				</td>
				<td class="right">
					<input name="dob" id="dob" maxlength=10 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var dob=new LiveValidation('dob');
						dob.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } );
					 	dob.add(Validate.Presence);
					</script>
					 <script type="text/javascript">
						$(function() {
							$( "#dob" ).datepicker();
						});
					</script>
				</td>
			</tr>


			<tr>
				<td colspan=2>
					<h4><?php print __($guid, 'Student Background') ?></h4>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Home Language - Primary') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'The primary language used in the student\'s home.') ?></span>
				</td>
				<td class="right">
					<select name="languageHomePrimary" id="languageHomePrimary" class="standardWidth">
						<?php
						print "<option value='Please select...'>Please select...</option>" ;
						try {
							$dataSelect=array();
							$sqlSelect="SELECT name FROM gibbonLanguage ORDER BY name" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) { }
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["name"] . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
						}
						?>
					</select>
					<script type="text/javascript">
						var languageHomePrimary=new LiveValidation('languageHomePrimary');
						languageHomePrimary.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Home Language - Secondary') ?></b><br/>
				</td>
				<td class="right">
					<select name="languageHomeSecondary" id="languageHomeSecondary" class="standardWidth">
						<?php
						print "<option value=''></option>" ;
						try {
							$dataSelect=array();
							$sqlSelect="SELECT name FROM gibbonLanguage ORDER BY name" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) { }
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["name"] . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'First Language') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Student\'s native/first/mother language.') ?></span>
				</td>
				<td class="right">
					<select name="languageFirst" id="languageFirst" class="standardWidth">
						<?php
						print "<option value='Please select...'>Please select...</option>" ;
						try {
							$dataSelect=array();
							$sqlSelect="SELECT name FROM gibbonLanguage ORDER BY name" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) { }
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["name"] . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
						}
						?>
					</select>
					<script type="text/javascript">
						var languageFirst=new LiveValidation('languageFirst');
						languageFirst.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Second Language') ?></b><br/>
				</td>
				<td class="right">
					<select name="languageSecond" id="languageSecond" class="standardWidth">
						<?php
						print "<option value=''></option>" ;
						try {
							$dataSelect=array();
							$sqlSelect="SELECT name FROM gibbonLanguage ORDER BY name" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) { }
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["name"] . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Third Language') ?></b><br/>
				</td>
				<td class="right">
					<select name="languageThird" id="languageThird" class="standardWidth">
						<?php
						print "<option value=''></option>" ;
						try {
							$dataSelect=array();
							$sqlSelect="SELECT name FROM gibbonLanguage ORDER BY name" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) { }
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["name"] . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Country of Birth') ?> *</b><br/>
				</td>
				<td class="right">
					<select name="countryOfBirth" id="countryOfBirth" class="standardWidth">
						<?php
						try {
							$dataSelect=array();
							$sqlSelect="SELECT printable_name FROM gibbonCountry ORDER BY printable_name" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) { }
						print "<option value='Please select...'>" . _("Please select...") . "</option>" ;
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(__($guid, $rowSelect["printable_name"])) . "</option>" ;
						}
						?>
					</select>
					<script type="text/javascript">
						var countryOfBirth=new LiveValidation('countryOfBirth');
						countryOfBirth.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Citizenship') ?> *</b><br/>
				</td>
				<td class="right">
					<select name="citizenship1" id="citizenship1" class="standardWidth">
						<?php
						print "<option value='Please select...'>" . _("Please select...") . "</option>" ;
						$nationalityList=getSettingByScope($connection2, "User Admin", "nationality") ;
						if ($nationalityList=="") {
							try {
								$dataSelect=array();
								$sqlSelect="SELECT printable_name FROM gibbonCountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								print "<option value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(__($guid, $rowSelect["printable_name"])) . "</option>" ;
							}
						}
						else {
							$nationalities=explode(",", $nationalityList) ;
							foreach ($nationalities as $nationality) {
								print "<option value='" . trim($nationality) . "'>" . trim($nationality) . "</option>" ;
							}
						}
						?>
					</select>
					<script type="text/javascript">
						var citizenship1=new LiveValidation('citizenship1');
						citizenship1.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Citizenship Passport Number') ?></b><br/>
				</td>
				<td class="right">
					<input name="citizenship1Passport" id="citizenship1Passport" maxlength=30 value="" type="text" class="standardWidth">
				</td>
			</tr>
			<tr>
				<td>
					<?php
					if ($_SESSION[$guid]["country"]=="") {
						print "<b>" . __($guid, 'National ID Card Number') . "</b><br/>" ;
					}
					else {
						print "<b>" . $_SESSION[$guid]["country"] . " " . __($guid, 'ID Card Number') . "</b><br/>" ;
					}
					?>
				</td>
				<td class="right">
					<input name="nationalIDCardNumber" id="nationalIDCardNumber" maxlength=30 value="" type="text" class="standardWidth">
				</td>
			</tr>
			<tr>
				<td>
					<?php
					if ($_SESSION[$guid]["country"]=="") {
						print "<b>" . __($guid, 'Residency/Visa Type') . "</b><br/>" ;
					}
					else {
						print "<b>" . $_SESSION[$guid]["country"] . " " . __($guid, 'Residency/Visa Type') . "</b><br/>" ;
					}
					?>
				</td>
				<td class="right">
					<?php
					$residencyStatusList=getSettingByScope($connection2, "User Admin", "residencyStatus") ;
					if ($residencyStatusList=="") {
						print "<input name='residencyStatus' id='residencyStatus' maxlength=30 value='' type='text' style='width: 300px'>" ;
					}
					else {
						print "<select name='residencyStatus' id='residencyStatus' style='width: 302px'>" ;
							print "<option value=''></option>" ;
							$residencyStatuses=explode(",", $residencyStatusList) ;
							foreach ($residencyStatuses as $residencyStatus) {
								print "<option value='" . trim($residencyStatus) . "'>" . trim($residencyStatus) . "</option>" ;
							}
						print "</select>" ;
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php
					if ($_SESSION[$guid]["country"]=="") {
						print "<b>" . __($guid, 'Visa Expiry Date') . "</b><br/>" ;
					}
					else {
						print "<b>" . $_SESSION[$guid]["country"] . " " . __($guid, 'Visa Expiry Date') . "</b><br/>" ;
					}
					print "<span style='font-size: 90%'><i>Format: " ; if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; } print ". " . __($guid, 'If relevant.') . "</span>" ;
					?>
				</td>
				<td class="right">
					<input name="visaExpiryDate" id="visaExpiryDate" maxlength=10 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var visaExpiryDate=new LiveValidation('visaExpiryDate');
						visaExpiryDate.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } );
					</script>
					 <script type="text/javascript">
						$(function() {
							$( "#visaExpiryDate" ).datepicker();
						});
					</script>
				</td>
			</tr>


			<tr>
				<td colspan=2>
					<h4><?php print __($guid, 'Student Contact') ?></h4>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Email') ?></b><br/>
				</td>
				<td class="right">
					<input name="email" id="email" maxlength=50 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var email=new LiveValidation('email');
						email.add(Validate.Email);
					</script>
				</td>
			</tr>
			<?php
			for ($i=1; $i<3; $i++) {
				?>
				<tr>
					<td>
						<b><?php print __($guid, 'Phone') ?> <?php print $i ?></b><br/>
						<span class="emphasis small"><?php print __($guid, 'Type, country code, number.') ?></span>
					</td>
					<td class="right">
						<input name="phone<?php print $i ?>" id="phone<?php print $i ?>" maxlength=20 value="" type="text" style="width: 160px">
						<select name="phone<?php print $i ?>CountryCode" id="phone<?php print $i ?>CountryCode" style="width: 60px">
							<?php
							print "<option value=''></option>" ;
							try {
								$dataSelect=array();
								$sqlSelect="SELECT * FROM gibbonCountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								print "<option value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(__($guid, $rowSelect["printable_name"])) . "</option>" ;
							}
							?>
						</select>
						<select style="width: 70px" name="phone<?php print $i ?>Type">
							<option value=""></option>
							<option value="Mobile"><?php print __($guid, 'Mobile') ?></option>
							<option value="Home"><?php print __($guid, 'Home') ?></option>
							<option value="Work"><?php print __($guid, 'Work') ?></option>
							<option value="Fax"><?php print __($guid, 'Fax') ?></option>
							<option value="Pager"><?php print __($guid, 'Pager') ?></option>
							<option value="Other"><?php print __($guid, 'Other') ?></option>
						</select>
					</td>
				</tr>
				<?php
			}
			?>


			<tr>
				<td colspan=2>
					<h4><?php print __($guid, 'Special Educational Needs & Medical') ?></h4>
					<?php
					$applicationFormSENText=getSettingByScope($connection2, 'Students', 'applicationFormSENText') ;
					if ($applicationFormSENText!="") {
						print "<p>" ;
							print $applicationFormSENText ;
						print "</p>" ;
					}
					?>
				</td>
			</tr>
			<script type="text/javascript">
				$(document).ready(function(){
					$(".sen").change(function(){
						if ($('select.sen option:selected').val()=="Y" ) {
							$("#senDetailsRow").slideDown("fast", $("#senDetailsRow").css("display","table-row"));
						} else {
							$("#senDetailsRow").css("display","none");
						}
					 });
				});
			</script>
			<tr>
				<td>
					<b><?php print __($guid, 'Special Educational Needs (SEN)') ?></b><br/>
					<span class="emphasis small"><?php print __($guid, 'Are there any known or suspected SEN concerns, or previous SEN assessments?') ?></span><br/>
				</td>
				<td class="right">
					<select name="sen" id="sen" class='sen standardWidth'>
						<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
						<option value="Y" /> <?php print ynExpander($guid, 'Y') ?>
						<option value="N" /> <?php print ynExpander($guid, 'N') ?>
					</select>
					<script type="text/javascript">
						var sen=new LiveValidation('sen');
						sen.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr id='senDetailsRow' style='display: none'>
				<td colspan=2 style='padding-top: 15px'>
					<b><?php print __($guid, 'SEN Details') ?></b><br/>
					<span class="emphasis small"><?php print __($guid, 'Provide any comments or information concerning your child\'s development and SEN history.') ?></span><br/>
					<textarea name="senDetails" id="senDetails" rows=5 style="width:738px; margin: 5px 0px 0px 0px"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan=2 style='padding-top: 15px'>
					<b><?php print __($guid, 'Medical Information') ?></b><br/>
					<span class="emphasis small"><?php print __($guid, 'Please indicate any medical conditions.') ?></span><br/>
					<textarea name="medicalInformation" id="medicalInformation" rows=5 style="width:738px; margin: 5px 0px 0px 0px"></textarea>
				</td>
			</tr>



			<tr>
				<td colspan=2>
					<h4><?php print __($guid, 'Student Education') ?></h4>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Anticipated Year of Entry') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'What school year will the student join in?') ?></span>
				</td>
				<td class="right">
					<select name="gibbonSchoolYearIDEntry" id="gibbonSchoolYearIDEntry" class="standardWidth">
						<?php
						print "<option value='Please select...'>" . __($guid, 'Please select...') . "</option>" ;
						try {
							$dataSelect=array();
							$sqlSelect="SELECT * FROM gibbonSchoolYear WHERE (status='Current' OR status='Upcoming') ORDER BY sequenceNumber" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) {
							print "<div class='error'>" . $e->getMessage() . "</div>" ;
						}
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["gibbonSchoolYearID"] . "'>" . htmlPrep($rowSelect["name"]) . "</option>" ;
						}
						?>
					</select>
					<script type="text/javascript">
						var gibbonSchoolYearIDEntry=new LiveValidation('gibbonSchoolYearIDEntry');
						gibbonSchoolYearIDEntry.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Intended Start Date') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Student\'s intended first day at school.') ?><br/><?php print __($guid, 'Format:') ?> <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?></span>
				</td>
				<td class="right">
					<input name="dateStart" id="dateStart" maxlength=10 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var dateStart=new LiveValidation('dateStart');
						dateStart.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } );
					 	dateStart.add(Validate.Presence);
					</script>
					 <script type="text/javascript">
						$(function() {
							$( "#dateStart" ).datepicker();
						});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Year Group at Entry') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Which year level will student enter.') ?></span>
				</td>
				<td class="right">
					<select name="gibbonYearGroupIDEntry" id="gibbonYearGroupIDEntry" class="standardWidth">
						<?php
						print "<option value='Please select...'>" . __($guid, 'Please select...') . "</option>" ;
						try {
							$dataSelect=array();
							$sqlSelect="SELECT * FROM gibbonYearGroup ORDER BY sequenceNumber" ;
							$resultSelect=$connection2->prepare($sqlSelect);
							$resultSelect->execute($dataSelect);
						}
						catch(PDOException $e) {
							print "<div class='error'>" . $e->getMessage() . "</div>" ;
						}
						while ($rowSelect=$resultSelect->fetch()) {
							print "<option value='" . $rowSelect["gibbonYearGroupID"] . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
						}
						?>
					</select>
					<script type="text/javascript">
						var gibbonYearGroupIDEntry=new LiveValidation('gibbonYearGroupIDEntry');
						gibbonYearGroupIDEntry.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>

			<?php
			$dayTypeOptions=getSettingByScope($connection2, 'User Admin', 'dayTypeOptions') ;
			if ($dayTypeOptions!="") {
				?>
				<tr>
					<td>
						<b><?php print __($guid, 'Day Type') ?></b><br/>
						<span class="emphasis small"><?php print getSettingByScope($connection2, 'User Admin', 'dayTypeText') ; ?></span>
					</td>
					<td class="right">
						<select name="dayType" id="dayType" class="standardWidth">
							<?php
							$dayTypes=explode(",", $dayTypeOptions) ;
							foreach ($dayTypes as $dayType) {
								print "<option value='" . trim($dayType) . "'>" . trim($dayType) . "</option>" ;
							}
							?>
						</select>
					</td>
				</tr>
				<?php
			}
			$applicationFormRefereeLink=getSettingByScope($connection2, 'Students', 'applicationFormRefereeLink') ;
			if ($applicationFormRefereeLink!="") {
				?>
				<tr>
					<td>
						<b><?php print __($guid, 'Current School Reference Email') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'An email address for a referee at the applicant\'s current school.') ?></span>
					</td>
					<td class="right">
						<input name="referenceEmail" id="referenceEmail" maxlength=100 value="" type="text" class="standardWidth">
						<script type="text/javascript">
							var referenceEmail=new LiveValidation('referenceEmail');
							referenceEmail.add(Validate.Presence);
							referenceEmail.add(Validate.Email);
						</script>
					</td>
				</tr>
				<?php
			}
			?>



			<tr>
				<td colspan=2 style='padding-top: 15px'>
					<b><?php print __($guid, 'Previous Schools') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Please give information on the last two schools attended by the applicant.') ?></span>
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<?php
					print "<table cellspacing='0' style='width: 100%'>" ;
						print "<tr class='head'>" ;
							print "<th>" ;
								print __($guid, "School Name") ;
							print "</th>" ;
							print "<th>" ;
								print __($guid, "Address") ;
							print "</th>" ;
							print "<th>" ;
								print sprintf(__($guid, 'Grades%1$sAttended'), "<br/>") ;
							print "</th>" ;
							print "<th>" ;
								print sprintf(__($guid, 'Language of%1$sInstruction'), "<br/>") ;
							print "</th>" ;
							print "<th>" ;
								print __($guid, "Joining Date") . "<br/><span style='font-size: 80%'>" ; if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; } print "</span>" ;
							print "</th>" ;
						print "</tr>" ;


						for ($i=1; $i<3; $i++) {
							if ((($i%2)-1)==0) {
								$rowNum="even" ;
							}
							else {
								$rowNum="odd" ;
							}

							print "<tr class=$rowNum>" ;
								print "<td>" ;
									print "<input name='schoolName$i' id='schoolName$i' maxlength=50 value='' type='text' style='width:120px; float: left'>" ;
								print "</td>" ;
								print "<td>" ;
									print "<input name='schoolAddress$i' id='schoolAddress$i' maxlength=255 value='' type='text' style='width:120px; float: left'>" ;
								print "</td>" ;
								print "<td>" ;
									print "<input name='schoolGrades$i' id='schoolGrades$i' maxlength=20 value='' type='text' style='width:70px; float: left'>" ;
								print "</td>" ;
								print "<td>" ;
									print "<input name='schoolLanguage$i' id='schoolLanguage$i' maxlength=50 value='' type='text' style='width:100px; float: left'>" ;
									?>
									<script type="text/javascript">
										$(function() {
											var availableTags=[
												<?php
												try {
													$dataAuto=array();
													$sqlAuto="SELECT DISTINCT schoolLanguage" . $i . " FROM gibbonApplicationForm ORDER BY schoolLanguage" . $i ;
													$resultAuto=$connection2->prepare($sqlAuto);
													$resultAuto->execute($dataAuto);
												}
												catch(PDOException $e) { }
												while ($rowAuto=$resultAuto->fetch()) {
													print "\"" . $rowAuto["schoolLanguage" . $i] . "\", " ;
												}
												?>
											];
											$( "#schoolLanguage<?php print $i ?>" ).autocomplete({source: availableTags});
										});
									</script>
									<?php
								print "</td>" ;
								print "<td>" ;
									?>
									<input name="<?php print "schoolDate$i" ?>" id="<?php print "schoolDate$i" ?>" maxlength=10 value="" type="text" style="width:90px; float: left">
									<script type="text/javascript">
										$(function() {
											$( "#<?php print "schoolDate$i" ?>" ).datepicker();
										});
									</script>
									<?php
								print "</td>" ;
							print "</tr>" ;
						}
					print "</table>" ;
					?>
				</td>
			</tr>



			<?php
			//CUSTOM FIELDS FOR STUDENT
			$resultFields=getCustomFields($connection2, $guid, TRUE, FALSE, FALSE, FALSE, TRUE, NULL) ;
			if ($resultFields->rowCount()>0) {
				?>
				<tr>
					<td colspan=2>
						<h4><?php print __($guid, 'Other Information') ?></h4>
					</td>
				</tr>
				<?php
				while ($rowFields=$resultFields->fetch()) {
					print renderCustomFieldRow($connection2, $guid, $rowFields) ;
				}
			}

			//FAMILY
			try {
				$dataSelect=array("gibbonPersonID"=>$gibbonPersonID);
				$sqlSelect="SELECT * FROM gibbonFamily JOIN gibbonFamilyAdult ON (gibbonFamily.gibbonFamilyID=gibbonFamilyAdult.gibbonFamilyID) WHERE gibbonFamilyAdult.gibbonPersonID=:gibbonPersonID ORDER BY name" ;
				$resultSelect=$connection2->prepare($sqlSelect);
				$resultSelect->execute($dataSelect);
			}
			catch(PDOException $e) { }

			if ($public==TRUE OR $resultSelect->rowCount()<1) {
				?>
				<input type="hidden" name="gibbonFamily" value="FALSE">

				<tr class='break'>
					<td colspan=2>
						<h3>
							<?php print __($guid, 'Home Address') ?>
						</h3>
						<p>
							<?php print __($guid, 'This address will be used for all members of the family. If an individual within the family needs a different address, this can be set through Data Updater after admission.') ?>
						</p>
					</td>
				</tr>
				<tr>
					<td>
						<b><?php print __($guid, 'Home Address') ?> *</b><br/>
						<span class="emphasis small"><?php print __($guid, 'Unit, Building, Street') ?></span>
					</td>
					<td class="right">
						<input name="homeAddress" id="homeAddress" maxlength=255 value="" type="text" class="standardWidth">
						<script type="text/javascript">
							var homeAddress=new LiveValidation('homeAddress');
							homeAddress.add(Validate.Presence);
						</script>
					</td>
				</tr>
				<tr>
					<td>
						<b><?php print __($guid, 'Home Address (District)') ?> *</b><br/>
						<span class="emphasis small"><?php print __($guid, 'County, State, District') ?></span>
					</td>
					<td class="right">
						<input name="homeAddressDistrict" id="homeAddressDistrict" maxlength=30 value="" type="text" class="standardWidth">
					</td>
					<script type="text/javascript">
						$(function() {
							var availableTags=[
								<?php
								try {
									$dataAuto=array();
									$sqlAuto="SELECT DISTINCT name FROM gibbonDistrict ORDER BY name" ;
									$resultAuto=$connection2->prepare($sqlAuto);
									$resultAuto->execute($dataAuto);
								}
								catch(PDOException $e) { }
								while ($rowAuto=$resultAuto->fetch()) {
									print "\"" . $rowAuto["name"] . "\", " ;
								}
								?>
							];
							$( "#homeAddressDistrict" ).autocomplete({source: availableTags});
						});
					</script>
					<script type="text/javascript">
						var homeAddressDistrict=new LiveValidation('homeAddressDistrict');
						homeAddressDistrict.add(Validate.Presence);
					</script>
				</tr>
				<tr>
					<td>
						<b><?php print __($guid, 'Home Address (Country)') ?> *</b><br/>
					</td>
					<td class="right">
						<select name="homeAddressCountry" id="homeAddressCountry" class="standardWidth">
							<?php
							try {
								$dataSelect=array();
								$sqlSelect="SELECT printable_name FROM gibbonCountry ORDER BY printable_name" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							print "<option value='Please select...'>" . __($guid, 'Please select...') . "</option>" ;
							while ($rowSelect=$resultSelect->fetch()) {
								print "<option value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(__($guid, $rowSelect["printable_name"])) . "</option>" ;
							}
							?>
						</select>
						<script type="text/javascript">
							var homeAddressCountry=new LiveValidation('homeAddressCountry');
							homeAddressCountry.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
						</script>
					</td>
				</tr>
				<?php

				if (isset($_SESSION[$guid]["username"])) {
					$start=2 ;
					?>
					<tr class='break'>
						<td colspan=2>
							<h3>
								<?php print __($guid, 'Parent/Guardian 1') ?>
								<?php
								if ($i==1) {
									print "<span style='font-size: 75%'></span>" ;
								}
								?>
							</h3>
						</td>
					</tr>
					<tr>
						<td>
							<b><?php print __($guid, 'Username') ?></b><br/>
							<span class="emphasis small"><?php print __($guid, 'System login ID.') ?></span>
						</td>
						<td class="right">
							<input readonly name='parent1username' maxlength=30 value="<?php print $_SESSION[$guid]["username"] ?>" type="text" class="standardWidth">
						</td>
					</tr>

					<tr>
						<td>
							<b><?php print __($guid, 'Surname') ?></b><br/>
							<span class="emphasis small"><?php print __($guid, 'Family name as shown in ID documents.') ?></span>
						</td>
						<td class="right">
							<input readonly name='parent1surname' maxlength=30 value="<?php print $_SESSION[$guid]["surname"] ?>" type="text" class="standardWidth">
						</td>
					</tr>
					<tr>
						<td>
							<b><?php print __($guid, 'Preferred Name') ?></b><br/>
							<span class="emphasis small"><?php print __($guid, 'Most common name, alias, nickname, etc.') ?></span>
						</td>
						<td class="right">
							<input readonly name='parent1preferredName' maxlength=30 value="<?php print $_SESSION[$guid]["preferredName"] ?>" type="text" class="standardWidth">
						</td>
					</tr>
					<tr>
						<td>
							<b><?php print __($guid, 'Relationship') ?> *</b><br/>
						</td>
						<td class="right">
							<select name="parent1relationship" id="parent1relationship" class="standardWidth">
								<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
								<option value="Mother"><?php print __($guid, 'Mother') ?></option>
								<option value="Father"><?php print __($guid, 'Father') ?></option>
								<option value="Step-Mother"><?php print __($guid, 'Step-Mother') ?></option>
								<option value="Step-Father"><?php print __($guid, 'Step-Father') ?></option>
								<option value="Adoptive Parent"><?php print __($guid, 'Adoptive Parent') ?></option>
								<option value="Guardian"><?php print __($guid, 'Guardian') ?></option>
								<option value="Grandmother"><?php print __($guid, 'Grandmother') ?></option>
								<option value="Grandfather"><?php print __($guid, 'Grandfather') ?></option>
								<option value="Aunt"><?php print __($guid, 'Aunt') ?></option>
								<option value="Uncle"><?php print __($guid, 'Uncle') ?></option>
								<option value="Nanny/Helper"><?php print __($guid, 'Nanny/Helper') ?></option>
								<option value="Other"><?php print __($guid, 'Other') ?></option>
							</select>
							<script type="text/javascript">
								var parent1relationship=new LiveValidation('parent1relationship');
								parent1relationship.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
							</script>
						</td>
					</tr>
					<?php
						//CUSTOM FIELDS FOR PARENT 1 WITH FAMILY
						$resultFields=getCustomFields($connection2, $guid, FALSE, FALSE, TRUE, FALSE, TRUE, NULL) ;
						if ($resultFields->rowCount()>0) {
							while ($rowFields=$resultFields->fetch()) {
								print renderCustomFieldRow($connection2, $guid, $rowFields, "", "parent1") ;
							}
						}
					?>

					<input name='parent1gibbonPersonID' value="<?php print $gibbonPersonID ?>" type="hidden">
					<?php
				}
				else {
					$start=1 ;
				}
				for ($i=$start;$i<3;$i++) {
					?>
					<tr class='break'>
						<td colspan=2>
							<h3>
								<?php print __($guid, 'Parent/Guardian') ?> <?php print $i ?>
								<?php
								if ($i==1) {
									print "<span style='font-size: 75%'> " . __($guid, '(e.g. mother)') . "</span>" ;
								}
								else if ($i==2 AND $gibbonPersonID=="") {
									print "<span style='font-size: 75%'> " . __($guid, '(e.g. father)') . "</span>" ;
								}
								?>
							</h3>
						</td>
					</tr>
					<?php
					if ($i==2) {
						?>
						<tr>
							<td class='right' colspan=2>
								<script type="text/javascript">
									/* Advanced Options Control */
									$(document).ready(function(){
										$("#secondParent").click(function(){
											if ($('input[name=secondParent]:checked').val()=="No" ) {
												$(".secondParent").slideUp("fast");
												$("#parent2title").attr("disabled", "disabled");
												$("#parent2surname").attr("disabled", "disabled");
												$("#parent2firstName").attr("disabled", "disabled");
												$("#parent2preferredName").attr("disabled", "disabled");
												$("#parent2officialName").attr("disabled", "disabled");
												$("#parent2nameInCharacters").attr("disabled", "disabled");
												$("#parent2gender").attr("disabled", "disabled");
												$("#parent2relationship").attr("disabled", "disabled");
												$("#parent2languageFirst").attr("disabled", "disabled");
												$("#parent2languageSecond").attr("disabled", "disabled");
												$("#parent2citizenship1").attr("disabled", "disabled");
												$("#parent2nationalIDCardNumber").attr("disabled", "disabled");
												$("#parent2residencyStatus").attr("disabled", "disabled");
												$("#parent2visaExpiryDate").attr("disabled", "disabled");
												$("#parent2email").attr("disabled", "disabled");
												$("#parent2phone1Type").attr("disabled", "disabled");
												$("#parent2phone1CountryCode").attr("disabled", "disabled");
												$("#parent2phone1").attr("disabled", "disabled");
												$("#parent2phone2Type").attr("disabled", "disabled");
												$("#parent2phone2CountryCode").attr("disabled", "disabled");
												$("#parent2phone2").attr("disabled", "disabled");
												$("#parent2profession").attr("disabled", "disabled");
												$("#parent2employer").attr("disabled", "disabled");
											}
											else {
												$(".secondParent").slideDown("fast", $(".secondParent").css("display","table-row"));
												$("#parent2title").removeAttr("disabled");
												$("#parent2surname").removeAttr("disabled");
												$("#parent2firstName").removeAttr("disabled");
												$("#parent2preferredName").removeAttr("disabled");
												$("#parent2officialName").removeAttr("disabled");
												$("#parent2nameInCharacters").removeAttr("disabled");
												$("#parent2gender").removeAttr("disabled");
												$("#parent2relationship").removeAttr("disabled");
												$("#parent2languageFirst").removeAttr("disabled");
												$("#parent2languageSecond").removeAttr("disabled");
												$("#parent2citizenship1").removeAttr("disabled");
												$("#parent2nationalIDCardNumber").removeAttr("disabled");
												$("#parent2residencyStatus").removeAttr("disabled");
												$("#parent2visaExpiryDate").removeAttr("disabled");
												$("#parent2email").removeAttr("disabled");
												$("#parent2phone1Type").removeAttr("disabled");
												$("#parent2phone1CountryCode").removeAttr("disabled");
												$("#parent2phone1").removeAttr("disabled");
												$("#parent2phone2Type").removeAttr("disabled");
												$("#parent2phone2CountryCode").removeAttr("disabled");
												$("#parent2phone2").removeAttr("disabled");
												$("#parent2profession").removeAttr("disabled");
												$("#parent2employer").removeAttr("disabled");
											}
										 });
									});
								</script>
								<span style='font-weight: bold; font-style: italic'><?php print __($guid, 'Do not include a second parent/guardian') ?> <input id='secondParent' name='secondParent' type='checkbox' value='No'/></span>
							</td>
						</tr>
						<?php
					}
					?>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td colspan=2>
							<h4><?php print __($guid, 'Parent/Guardian') ?> <?php print $i ?> <?php print __($guid, 'Personal Data') ?></h4>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Title') ?> *</b><br/>
							<span class="emphasis small"></span>
						</td>
						<td class="right">
							<select class="standardWidth" id="<?php print "parent$i" ?>title" name="<?php print "parent$i" ?>title">
								<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
								<option value="Ms."><?php print __($guid, 'Ms.') ?></option>
								<option value="Miss"><?php print __($guid, 'Miss.') ?></option>
								<option value="Mr."><?php print __($guid, 'Mr.') ?></option>
								<option value="Mrs."><?php print __($guid, 'Mrs.') ?></option>
								<option value="Dr."><?php print __($guid, 'Dr.') ?></option>
							</select>
							<script type="text/javascript">
								var <?php print "parent$i" ?>title=new LiveValidation('<?php print "parent$i" ?>title');
								<?php print "parent$i" ?>title.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
							</script>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Surname') ?> *</b><br/>
							<span class="emphasis small"><?php print __($guid, 'Family name as shown in ID documents.') ?></span>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>surname" id="<?php print "parent$i" ?>surname" maxlength=30 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var <?php print "parent$i" ?>surname=new LiveValidation('<?php print "parent$i" ?>surname');
								<?php print "parent$i" ?>surname.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'First Name') ?> *</b><br/>
							<span class="emphasis small"><?php print __($guid, 'First name as shown in ID documents.') ?></span>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>firstName" id="<?php print "parent$i" ?>firstName" maxlength=30 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var <?php print "parent$i" ?>firstName=new LiveValidation('<?php print "parent$i" ?>firstName');
								<?php print "parent$i" ?>firstName.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Preferred Name') ?> *</b><br/>
							<span class="emphasis small"><?php print __($guid, 'Most common name, alias, nickname, etc.') ?></span>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>preferredName" id="<?php print "parent$i" ?>preferredName" maxlength=30 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var <?php print "parent$i" ?>preferredName=new LiveValidation('<?php print "parent$i" ?>preferredName');
								<?php print "parent$i" ?>preferredName.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Official Name') ?> *</b><br/>
							<span class="emphasis small"><?php print __($guid, 'Full name as shown in ID documents.') ?></span>
						</td>
						<td class="right">
							<input title='Please enter full name as shown in ID documents' name="<?php print "parent$i" ?>officialName" id="<?php print "parent$i" ?>officialName" maxlength=150 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var <?php print "parent$i" ?>officialName=new LiveValidation('<?php print "parent$i" ?>officialName');
								<?php print "parent$i" ?>officialName.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Name In Characters') ?></b><br/>
							<span class="emphasis small"><?php print __($guid, 'Chinese or other character-based name.') ?></span>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>nameInCharacters" id="<?php print "parent$i" ?>nameInCharacters" maxlength=20 value="" type="text" class="standardWidth">
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Gender') ?> *</b><br/>
						</td>
						<td class="right">
							<select name="<?php print "parent$i" ?>gender" id="<?php print "parent$i" ?>gender" class="standardWidth">
								<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
								<option value="F"><?php print __($guid, 'Female') ?></option>
								<option value="M"><?php print __($guid, 'Male') ?></option>
							</select>
							<script type="text/javascript">
								var <?php print "parent$i" ?>gender=new LiveValidation('<?php print "parent$i" ?>gender');
								<?php print "parent$i" ?>gender.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
							</script>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Relationship') ?> *</b><br/>
						</td>
						<td class="right">
							<select name="<?php print "parent$i" ?>relationship" id="<?php print "parent$i" ?>relationship" class="standardWidth">
								<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
								<option value="Mother"><?php print __($guid, 'Mother') ?></option>
								<option value="Father"><?php print __($guid, 'Father') ?></option>
								<option value="Step-Mother"><?php print __($guid, 'Step-Mother') ?></option>
								<option value="Step-Father"><?php print __($guid, 'Step-Father') ?></option>
								<option value="Adoptive Parent"><?php print __($guid, 'Adoptive Parent') ?></option>
								<option value="Guardian"><?php print __($guid, 'Guardian') ?></option>
								<option value="Grandmother"><?php print __($guid, 'Grandmother') ?></option>
								<option value="Grandfather"><?php print __($guid, 'Grandfather') ?></option>
								<option value="Aunt"><?php print __($guid, 'Aunt') ?></option>
								<option value="Uncle"><?php print __($guid, 'Uncle') ?></option>
								<option value="Nanny/Helper"><?php print __($guid, 'Nanny/Helper') ?></option>
								<option value="Other"><?php print __($guid, 'Other') ?></option>
							</select>
							<script type="text/javascript">
								var <?php print "parent$i" ?>relationship=new LiveValidation('<?php print "parent$i" ?>relationship');
								<?php print "parent$i" ?>relationship.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
							</script>
						</td>
					</tr>

					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td colspan=2>
							<h4><?php print __($guid, 'Parent/Guardian') ?> <?php print $i ?> <?php print __($guid, 'Personal Background') ?></h4>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'First Language') ?></b><br/>
						</td>
						<td class="right">
							<select name="<?php print "parent$i" ?>languageFirst" id="<?php print "parent$i" ?>languageFirst" class="standardWidth">
								<?php
								print "<option value=''></option>" ;
								try {
									$dataSelect=array();
									$sqlSelect="SELECT name FROM gibbonLanguage ORDER BY name" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									print "<option value='" . $rowSelect["name"] . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
								}
								?>
							</select>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Second Language') ?></b><br/>
						</td>
						<td class="right">
							<select name="<?php print "parent$i" ?>languageSecond" id="<?php print "parent$i" ?>languageSecond" class="standardWidth">
								<?php
								print "<option value=''></option>" ;
								try {
									$dataSelect=array();
									$sqlSelect="SELECT name FROM gibbonLanguage ORDER BY name" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								while ($rowSelect=$resultSelect->fetch()) {
									print "<option value='" . $rowSelect["name"] . "'>" . htmlPrep(__($guid, $rowSelect["name"])) . "</option>" ;
								}
								?>
							</select>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Citizenship') ?></b><br/>
						</td>
						<td class="right">
							<select name="<?php print "parent$i" ?>citizenship1" id="<?php print "parent$i" ?>citizenship1" class="standardWidth">
								<?php
								print "<option value=''></option>" ;
								$nationalityList=getSettingByScope($connection2, "User Admin", "nationality") ;
								if ($nationalityList=="") {
									try {
										$dataSelect=array();
										$sqlSelect="SELECT printable_name FROM gibbonCountry ORDER BY printable_name" ;
										$resultSelect=$connection2->prepare($sqlSelect);
										$resultSelect->execute($dataSelect);
									}
									catch(PDOException $e) { }
									while ($rowSelect=$resultSelect->fetch()) {
										print "<option value='" . $rowSelect["printable_name"] . "'>" . htmlPrep(__($guid, $rowSelect["printable_name"])) . "</option>" ;
									}
								}
								else {
									$nationalities=explode(",", $nationalityList) ;
									foreach ($nationalities as $nationality) {
										print "<option value='" . trim($nationality) . "'>" . trim($nationality) . "</option>" ;
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<?php
							if ($_SESSION[$guid]["country"]=="") {
								print "<b>" . __($guid, 'National ID Card Number') . "</b><br/>" ;
							}
							else {
								print "<b>" . $_SESSION[$guid]["country"] . " " . __($guid, 'ID Card Number') . "</b><br/>" ;
							}
							?>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>nationalIDCardNumber" id="<?php print "parent$i" ?>nationalIDCardNumber" maxlength=30 value="" type="text" class="standardWidth">
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<?php
							if ($_SESSION[$guid]["country"]=="") {
								print "<b>" . __($guid, 'Residency/Visa Type') . "</b><br/>" ;
							}
							else {
								print "<b>" . $_SESSION[$guid]["country"] . " " . __($guid, 'Residency/Visa Type') . "</b><br/>" ;
							}
							?>
						</td>
						<td class="right">
							<?php
							$residencyStatusList=getSettingByScope($connection2, "User Admin", "residencyStatus") ;
							if ($residencyStatusList=="") {
								print "<input name='parent" . $i . "residencyStatus' id='parent" . $i . "residencyStatus' maxlength=30 type='text' style='width: 300px'>" ;
							}
							else {
								print "<select name='parent" . $i . "residencyStatus' id='parent" . $i . "residencyStatus' style='width: 302px'>" ;
									print "<option value=''></option>" ;
									$residencyStatuses=explode(",", $residencyStatusList) ;
									foreach ($residencyStatuses as $residencyStatus) {
										print "<option value='" . trim($residencyStatus) . "'>" . trim($residencyStatus) . "</option>" ;
									}
								print "</select>" ;
							}
							?>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<?php
							if ($_SESSION[$guid]["country"]=="") {
								print "<b>" . __($guid, 'Visa Expiry Date') . "</b><br/>" ;
							}
							else {
								print "<b>" . $_SESSION[$guid]["country"] . " " . __($guid, 'Visa Expiry Date') . "</b><br/>" ;
							}
							print "<span style='font-size: 90%'><i>" . __($guid, 'Format:') . " " ; if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; } print ". " . __($guid, 'If relevant.') . "</span>" ;
							?>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>visaExpiryDate" id="<?php print "parent$i" ?>visaExpiryDate" maxlength=10 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var <?php print "parent$i" ?>visaExpiryDate=new LiveValidation('<?php print "parent$i" ?>visaExpiryDate');
								<?php print "parent$i" ?>visaExpiryDate.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } ?>, failureMessage: "Use <?php if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { print "dd/mm/yyyy" ; } else { print $_SESSION[$guid]["i18n"]["dateFormat"] ; }?>." } );
							</script>
							 <script type="text/javascript">
								$(function() {
									$( "#<?php print "parent$i" ?>visaExpiryDate" ).datepicker();
								});
							</script>
						</td>
					</tr>


					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td colspan=2>
							<h4><?php print __($guid, 'Parent/Guardian') ?> <?php print $i ?> <?php print __($guid, 'Contact') ?></h4>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Email') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>email" id="<?php print "parent$i" ?>email" maxlength=50 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var <?php print "parent$i" ?>email=new LiveValidation('<?php print "parent$i" ?>email');
								<?php
								print "parent$i" . "email.add(Validate.Email);";
								print "parent$i" . "email.add(Validate.Presence);" ;
								?>
							</script>
						</td>
					</tr>

					<?php
					for ($y=1; $y<3; $y++) {
						?>
						<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
							<td>
								<b><?php print __($guid, 'Phone') ?> <?php print $y ; if ($y==1) { print " *" ;}?></b><br/>
								<span class="emphasis small"><?php print __($guid, 'Type, country code, number.') ?></span>
							</td>
							<td class="right">
								<input name="<?php print "parent$i" ?>phone<?php print $y ?>" id="<?php print "parent$i" ?>phone<?php print $y ?>" maxlength=20 value="" type="text" style="width: 160px">
								<?php
								if ($y==1) {
									?>
									<script type="text/javascript">
										var <?php print "parent$i" ?>phone<?php print $y ?>=new LiveValidation('<?php print "parent$i" ?>phone<?php print $y ?>');
										<?php print "parent$i" ?>phone<?php print $y ?>.add(Validate.Presence);
									</script>
									<?php
								}
								?>
								<select name="<?php print "parent$i" ?>phone<?php print $y ?>CountryCode" id="<?php print "parent$i" ?>phone<?php print $y ?>CountryCode" style="width: 60px">
									<?php
									print "<option value=''></option>" ;
									try {
										$dataSelect=array();
										$sqlSelect="SELECT * FROM gibbonCountry ORDER BY printable_name" ;
										$resultSelect=$connection2->prepare($sqlSelect);
										$resultSelect->execute($dataSelect);
									}
									catch(PDOException $e) { }
									while ($rowSelect=$resultSelect->fetch()) {
										print "<option value='" . $rowSelect["iddCountryCode"] . "'>" . htmlPrep($rowSelect["iddCountryCode"]) . " - " .  htmlPrep(__($guid, $rowSelect["printable_name"])) . "</option>" ;
									}
									?>
								</select>
								<select style="width: 70px" name="<?php print "parent$i" ?>phone<?php print $y ?>Type">
									<option value=""></option>
									<option value="Mobile"><?php print __($guid, 'Mobile') ?></option>
									<option value="Home"><?php print __($guid, 'Home') ?></option>
									<option value="Work"><?php print __($guid, 'Work') ?></option>
									<option value="Fax"><?php print __($guid, 'Fax') ?></option>
									<option value="Pager"><?php print __($guid, 'Pager') ?></option>
									<option value="Other"><?php print __($guid, 'Other') ?></option>
								</select>
							</td>
						</tr>
						<?php
					}
					?>

					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td colspan=2>
							<h4><?php print __($guid, 'Parent/Guardian') ?> <?php print $i ?> <?php print __($guid, 'Employment') ?></h4>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Profession') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>profession" id="<?php print "parent$i" ?>profession" maxlength=30 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var <?php print "parent$i" ?>profession=new LiveValidation('<?php print "parent$i" ?>profession');
								<?php print "parent$i" ?>profession.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
						<td>
							<b><?php print __($guid, 'Employer') ?></b><br/>
						</td>
						<td class="right">
							<input name="<?php print "parent$i" ?>employer" id="<?php print "parent$i" ?>employer" maxlength=30 value="" type="text" class="standardWidth">
						</td>
					</tr>
					<?php


					//CUSTOM FIELDS FOR PARENTS, WITH FAMILY
					$resultFields=getCustomFields($connection2, $guid, FALSE, FALSE, TRUE, FALSE, TRUE, NULL) ;
					if ($resultFields->rowCount()>0) {
						?>
						<tr <?php if ($i==2) { print "class='secondParent'" ; }?>>
							<td colspan=2>
								<h4><?php print __($guid, 'Parent/Guardian') ?> <?php print $i ?> <?php print __($guid, 'Other Fields') ?></h4>
							</td>
						</tr>
						<?php
						while ($rowFields=$resultFields->fetch()) {
							if ($i==2) {
								print renderCustomFieldRow($connection2, $guid, $rowFields, "", "parent2", "secondParent") ;
								?>
								<script type="text/javascript">
									/* Advanced Options Control */
									$(document).ready(function(){
										$("#secondParent").click(function(){
											if ($('input[name=secondParent]:checked').val()=="No" ) {
												$("#parent<?php print $i ?>custom<?php print $rowFields["gibbonPersonFieldID"] ?>").attr("disabled", "disabled");
											}
											else {
												$("#parent<?php print $i ?>custom<?php print $rowFields["gibbonPersonFieldID"] ?>").removeAttr("disabled");
											}
										 });
									});
								</script>
								<?php
							}
							else {
								print renderCustomFieldRow($connection2, $guid, $rowFields, "", "parent1") ;
							}
						}
					}
				}
			}
			else {
				?>
				<input type="hidden" name="gibbonFamily" value="TRUE">
				<tr class='break'>
					<td colspan=2>
						<h3><?php print __($guid, 'Family') ?></h3>
						<p><?php print __($guid, 'Choose the family you wish to associate this application with.') ?></p>
						<?php
						print "<table cellspacing='0' style='width: 100%'>" ;
							print "<tr class='head'>" ;
								print "<th>" ;
									print __($guid, "Family Name") ;
								print "</th>" ;
								print "<th>" ;
									print __($guid, "Selected") ;
								print "</th>" ;
								print "<th>" ;
									print __($guid, "Relationships") ;
								print "</th>" ;
							print "</tr>" ;

							$rowCount=1 ;
							while ($rowSelect=$resultSelect->fetch()) {
								if (($rowCount%2)==0) {
									$rowNum="odd" ;
								}
								else {
									$rowNum="even" ;
								}

								print "<tr class=$rowNum>" ;
									print "<td>" ;
										print "<b>" . $rowSelect["name"] . "</b><br/>" ;
									print "</td>" ;
									print "<td>" ;
										$checked="" ;
										if ($rowCount==1) {
											$checked="checked" ;
										}
										print "<input $checked value='" . $rowSelect["gibbonFamilyID"] . "' name='gibbonFamilyID' type='radio'/>" ;
									print "</td>" ;
									print "<td>" ;
										try {
											$dataRelationships=array("gibbonFamilyID"=>$rowSelect["gibbonFamilyID"]);
											$sqlRelationships="SELECT surname, preferredName, title, gender, gibbonFamilyAdult.gibbonPersonID FROM gibbonFamilyAdult JOIN gibbonPerson ON (gibbonFamilyAdult.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonFamilyID=:gibbonFamilyID" ;
											$resultRelationships=$connection2->prepare($sqlRelationships);
											$resultRelationships->execute($dataRelationships);
										}
										catch(PDOException $e) {
											print "<div class='error'>" . $e->getMessage() . "</div>" ;
										}
										while ($rowRelationships=$resultRelationships->fetch()) {
											print "<div style='width: 100%; height: 20px; vertical-align: middle'>" ;
												print formatName($rowRelationships["title"], $rowRelationships["preferredName"], $rowRelationships["surname"], "Parent") ;
												?>
												<select name="<?php print $rowSelect["gibbonFamilyID"] ?>-relationships[]" id="relationships[]" style="width: 200px">
													<option <?php if ($rowRelationships["gender"]=="F") { print "selected" ; } ?> value="Mother"><?php print __($guid, 'Mother') ?></option>
													<option <?php if ($rowRelationships["gender"]=="M") { print "selected" ; } ?> value="Father"><?php print __($guid, 'Father') ?></option>
													<option value="Step-Mother"><?php print __($guid, 'Step-Mother') ?></option>
													<option value="Step-Father"><?php print __($guid, 'Step-Father') ?></option>
													<option value="Adoptive Parent"><?php print __($guid, 'Adoptive Parent') ?></option>
													<option value="Guardian"><?php print __($guid, 'Guardian') ?></option>
													<option value="Grandmother"><?php print __($guid, 'Grandmother') ?></option>
													<option value="Grandfather"><?php print __($guid, 'Grandfather') ?></option>
													<option value="Aunt"><?php print __($guid, 'Aunt') ?></option>
													<option value="Uncle"><?php print __($guid, 'Uncle') ?></option>
													<option value="Nanny/Helper"><?php print __($guid, 'Nanny/Helper') ?></option>
													<option value="Other"><?php print __($guid, 'Other') ?></option>
												</select>
												<input type="hidden" name="<?php print $rowSelect["gibbonFamilyID"] ?>-relationshipsGibbonPersonID[]" value="<?php print $rowRelationships["gibbonPersonID"] ?>">
												<?php
											print "</div>" ;
											print "<br/>" ;
										}
									print "</td>" ;
								print "</tr>" ;
								$rowCount++ ;
							}
						print "</table>" ;
						?>
					</td>
				</tr>
				<?php
			}
			?>
			<tr class='break'>
				<td colspan=2>
					<h3><?php print __($guid, 'Siblings') ?></h3>
				</td>
			</tr>
			<tr>
				<td colspan=2 style='padding-top: 0px'>
					<p><?php print __($guid, 'Please give information on the applicants\'s siblings.') ?></p>
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<?php
					print "<table cellspacing='0' style='width: 100%'>" ;
						print "<tr class='head'>" ;
							print "<th>" ;
								print __($guid, "Sibling Name") ;
							print "</th>" ;
							print "<th>" ;
								print __($guid, "Date of Birth") . "<br/><span style='font-size: 80%'>" . $_SESSION[$guid]["i18n"]["dateFormat"] . "</span>" ;
							print "</th>" ;
							print "<th>" ;
								print __($guid, "School Attending") ;
							print "</th>" ;
							print "<th>" ;
								print __($guid, "Joining Date") . "<br/><span style='font-size: 80%'>" . $_SESSION[$guid]["i18n"]["dateFormat"] . "</span>" ;
							print "</th>" ;
						print "</tr>" ;

						$rowCount=1 ;

						//List siblings who have been to or are at the school
						if (isset($gibbonFamilyID)) {
							try {
								$dataSibling=array("gibbonFamilyID"=>$gibbonFamilyID);
								$sqlSibling="SELECT surname, preferredName, dob, dateStart FROM gibbonFamilyChild JOIN gibbonPerson ON (gibbonFamilyChild.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonFamilyID=:gibbonFamilyID ORDER BY dob ASC, surname, preferredName" ;
								$resultSibling=$connection2->prepare($sqlSibling);
								$resultSibling->execute($dataSibling);
							}
							catch(PDOException $e) {
								print "<div class='error'>" . $e->getMessage() . "</div>" ;
							}

							while ($rowSibling=$resultSibling->fetch()) {
								if (($rowCount%2)==0) {
									$rowNum="odd" ;
								}
								else {
									$rowNum="even" ;
								}

								print "<tr class=$rowNum>" ;
									print "<td>" ;
										print "<input name='siblingName$rowCount' id='siblingName$rowCount' maxlength=50 value='" . formatName("", $rowSibling["preferredName"], $rowSibling["surname"], "Student") . "' type='text' style='width:120px; float: left'>" ;
									print "</td>" ;
									print "<td>" ;
										?>
										<input name="<?php print "siblingDOB$rowCount" ?>" id="<?php print "siblingDOB$rowCount" ?>" maxlength=10 value="<?php print dateConvertBack($guid, $rowSibling["dob"]) ?>" type="text" style="width:90px; float: left"><br/>
										<script type="text/javascript">
											$(function() {
												$( "#<?php print "siblingDOB$rowCount" ?>" ).datepicker();
											});
										</script>
										<?php
									print "</td>" ;
									print "<td>" ;
										print "<input name='siblingSchool$rowCount' id='siblingSchool$rowCount' maxlength=50 value='" . $_SESSION[$guid]["organisationName"] . "' type='text' style='width:200px; float: left'>" ;
									print "</td>" ;
									print "<td>" ;
										?>
										<input name="<?php print "siblingSchoolJoiningDate$rowCount" ?>" id="<?php print "siblingSchoolJoiningDate$rowCount" ?>" maxlength=10 value="<?php print dateConvertBack($guid, $rowSibling["dateStart"]) ?>" type="text" style="width:90px; float: left">
										<script type="text/javascript">
											$(function() {
												$( "#<?php print "siblingSchoolJoiningDate$rowCount" ?>" ).datepicker();
											});
										</script>
										<?php
									print "</td>" ;
								print "</tr>" ;

								$rowCount++ ;
							}
						}

						//Space for other siblings
						for ($i=$rowCount; $i<4; $i++) {
							if (($i%2)==0) {
								$rowNum="even" ;
							}
							else {
								$rowNum="odd" ;
							}

							print "<tr class=$rowNum>" ;
								print "<td>" ;
									print "<input name='siblingName$i' id='siblingName$i' maxlength=50 value='' type='text' style='width:120px; float: left'>" ;
								print "</td>" ;
								print "<td>" ;
									?>
									<input name="<?php print "siblingDOB$i" ?>" id="<?php print "siblingDOB$i" ?>" maxlength=10 value="" type="text" style="width:90px; float: left"><br/>
									<script type="text/javascript">
										$(function() {
											$( "#<?php print "siblingDOB$i" ?>" ).datepicker();
										});
									</script>
									<?php
								print "</td>" ;
								print "<td>" ;
									print "<input name='siblingSchool$i' id='siblingSchool$i' maxlength=50 value='' type='text' style='width:200px; float: left'>" ;
								print "</td>" ;
								print "<td>" ;
									?>
									<input name="<?php print "siblingSchoolJoiningDate$i" ?>" id="<?php print "siblingSchoolJoiningDate$i" ?>" maxlength=10 value="" type="text" style="width:120px; float: left">
									<script type="text/javascript">
										$(function() {
											$( "#<?php print "siblingSchoolJoiningDate$i" ?>" ).datepicker();
										});
									</script>
									<?php
								print "</td>" ;
							print "</tr>" ;
						}
					print "</table>" ;
					?>
				</td>
			</tr>

			<?php
			$languageOptionsActive=getSettingByScope($connection2, 'Application Form', 'languageOptionsActive') ;
			if ($languageOptionsActive=="Y") {
				?>
				<tr class='break'>
					<td colspan=2>
						<h3><?php print __($guid, 'Language Selection') ?></h3>
						<?php
						$languageOptionsBlurb=getSettingByScope($connection2, 'Application Form', 'languageOptionsBlurb') ;
						if ($languageOptionsBlurb!="") {
							print "<p>" ;
								print $languageOptionsBlurb ;
							print "</p>" ;
						}
						?>
					</td>
				</tr>
				<tr>
					<td>
						<b><?php print __($guid, 'Language Choice') ?> *</b><br/>
						<span class="emphasis small"><?php  print __($guid, 'Please choose preferred additional language to study.') ?></span>
					</td>
					<td class="right">
						<select name="languageChoice" id="languageChoice" class="standardWidth">
							<?php
							print "<option value='Please select...'>" . __($guid, 'Please select...') . "</option>" ;
							$languageOptionsLanguageList=getSettingByScope($connection2, "Application Form", "languageOptionsLanguageList") ;
							$languages=explode(",", $languageOptionsLanguageList) ;
							foreach ($languages as $language) {
								print "<option value='" . trim($language) . "'>" . trim($language) . "</option>" ;
							}
							?>
						</select>
						<script type="text/javascript">
							var languageChoice=new LiveValidation('languageChoice');
							languageChoice.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
						</script>
					</td>
				</tr>
				<tr>
					<td colspan=2 style='padding-top: 15px'>
						<b><?php print __($guid, 'Language Choice Experience') ?> *</b><br/>
						<span class="emphasis small"><?php print __($guid, 'Has the applicant studied the selected language before? If so, please describe the level and type of experience.') ?></span><br/>
						<textarea name="languageChoiceExperience" id="languageChoiceExperience" rows=5 style="width:738px; margin: 5px 0px 0px 0px"></textarea>
						<script type="text/javascript">
							var languageChoiceExperience=new LiveValidation('languageChoiceExperience');
							languageChoiceExperience.add(Validate.Presence);
						</script>
					</td>
				</tr>
				<?php
			}
			?>



			<tr class='break'>
				<td colspan=2>
					<h3><?php print __($guid, 'Scholarships') ?></h3>
					<?php
					//Get scholarships info
					$scholarship=getSettingByScope($connection2, 'Application Form', 'scholarships') ;
					if ($scholarship!="") {
						print "<p>" ;
							print $scholarship ;
						print "</p>" ;
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Interest') ?></b><br/>
					<span class="emphasis small"><?php print __($guid, 'Indicate if you are interested in a scholarship.') ?></span><br/>
				</td>
				<td class="right">
					<input type="radio" id="scholarshipInterest" name="scholarshipInterest" class="type" value="Y" /> <?php print ynExpander($guid, 'Y') ?>
					<input checked type="radio" id="scholarshipInterest" name="scholarshipInterest" class="type" value="N" /> <?php print ynExpander($guid, 'N') ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Required?') ?></b><br/>
					<span class="emphasis small"><?php print __($guid, 'Is a scholarship required for you to take up a place at the school?') ?></span><br/>
				</td>
				<td class="right">
					<input type="radio" id="scholarshipRequired" name="scholarshipRequired" class="type" value="Y" /> <?php print ynExpander($guid, 'Y') ?>
					<input checked type="radio" id="scholarshipRequired" name="scholarshipRequired" class="type" value="N" /> <?php print ynExpander($guid, 'N') ?>
				</td>
			</tr>


			<tr class='break'>
				<td colspan=2>
					<h3><?php print __($guid, 'Payment') ?></h3>
				</td>
			</tr>
			<script type="text/javascript">
				/* Resource 1 Option Control */
				$(document).ready(function(){
					$("#companyNameRow").css("display","none");
					$("#companyContactRow").css("display","none");
					$("#companyAddressRow").css("display","none");
					$("#companyEmailRow").css("display","none");
					$("#companyCCFamilyRow").css("display","none");
					$("#companyPhoneRow").css("display","none");
					$("#companyAllRow").css("display","none");
					$("#companyCategoriesRow").css("display","none");
					companyEmail.disable() ;
					companyAddress.disable() ;
					companyContact.disable() ;
					companyName.disable() ;

					$(".payment").click(function(){
						if ($('input[name=payment]:checked').val()=="Family" ) {
							$("#companyNameRow").css("display","none");
							$("#companyContactRow").css("display","none");
							$("#companyAddressRow").css("display","none");
							$("#companyEmailRow").css("display","none");
							$("#companyCCFamilyRow").css("display","none");
							$("#companyPhoneRow").css("display","none");
							$("#companyAllRow").css("display","none");
							$("#companyCategoriesRow").css("display","none");
							companyEmail.disable() ;
							companyAddress.disable() ;
							companyContact.disable() ;
							companyName.disable() ;
						} else {
							$("#companyNameRow").slideDown("fast", $("#companyNameRow").css("display","table-row"));
							$("#companyContactRow").slideDown("fast", $("#companyContactRow").css("display","table-row"));
							$("#companyAddressRow").slideDown("fast", $("#companyAddressRow").css("display","table-row"));
							$("#companyEmailRow").slideDown("fast", $("#companyEmailRow").css("display","table-row"));
							$("#companyCCFamilyRow").slideDown("fast", $("#companyCCFamilyRow").css("display","table-row"));
							$("#companyPhoneRow").slideDown("fast", $("#companyPhoneRow").css("display","table-row"));
							$("#companyAllRow").slideDown("fast", $("#companyAllRow").css("display","table-row"));
							if ($('input[name=companyAll]:checked').val()=="Y" ) {
								$("#companyCategoriesRow").css("display","none");
							} else {
								$("#companyCategoriesRow").slideDown("fast", $("#companyCategoriesRow").css("display","table-row"));
							}
							companyEmail.enable() ;
							companyAddress.enable() ;
							companyContact.enable() ;
							companyName.enable() ;
						}
					 });

					 $(".companyAll").click(function(){
						if ($('input[name=companyAll]:checked').val()=="Y" ) {
							$("#companyCategoriesRow").css("display","none");
						} else {
							$("#companyCategoriesRow").slideDown("fast", $("#companyCategoriesRow").css("display","table-row"));
						}
					 });
				});
			</script>
			<tr id="familyRow">
				<td colspan=2>
					<p><?php print __($guid, 'If you choose family, future invoices will be sent according to your family\'s contact preferences, which can be changed at a later date by contacting the school. For example you may wish both parents to receive the invoice, or only one. Alternatively, if you choose Company, you can choose for all or only some fees to be covered by the specified company.') ?></p>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'Send Future Invoices To') ?></b><br/>
				</td>
				<td class="right">
					<input type="radio" name="payment" value="Family" class="payment" checked /> <?php print __($guid, 'Family') ?>
					<input type="radio" name="payment" value="Company" class="payment" /> <?php print __($guid, 'Company') ?>
				</td>
			</tr>
			<tr id="companyNameRow">
				<td>
					<b><?php print __($guid, 'Company Name') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="companyName" id="companyName" maxlength=100 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var companyName=new LiveValidation('companyName');
						companyName.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr id="companyContactRow">
				<td>
					<b><?php print __($guid, 'Company Contact Person') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="companyContact" id="companyContact" maxlength=100 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var companyContact=new LiveValidation('companyContact');
						companyContact.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr id="companyAddressRow">
				<td>
					<b><?php print __($guid, 'Company Address') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="companyAddress" id="companyAddress" maxlength=255 value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var companyAddress=new LiveValidation('companyAddress');
						companyAddress.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr id="companyEmailRow">
				<td>
					<b><?php print __($guid, 'Company Emails') ?> *</b><br/>
					<span class="emphasis small"><?php print __($guid, 'Comma-separated list of email address.') ?></span>
				</td>
				<td class="right">
					<input name="companyEmail" id="companyEmail" value="" type="text" class="standardWidth">
					<script type="text/javascript">
						var companyEmail=new LiveValidation('companyEmail');
						companyEmail.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr id="companyCCFamilyRow">
				<td>
					<b><?php print __($guid, 'CC Family?') ?></b><br/>
					<span class="emphasis small"><?php print __($guid, 'Should the family be sent a copy of billing emails?') ?></span>
				</td>
				<td class="right">
					<select name="companyCCFamily" id="companyCCFamily" class="standardWidth">
						<option value="N" /> <?php print __($guid, 'No') ?>
						<option value="Y" /> <?php print __($guid, 'Yes') ?>
					</select>
				</td>
			</tr>
			<tr id="companyPhoneRow">
				<td>
					<b><?php print __($guid, 'Company Phone') ?></b><br/>
				</td>
				<td class="right">
					<input name="companyPhone" id="companyPhone" maxlength=20 value="" type="text" class="standardWidth">
				</td>
			</tr>
			<?php
			try {
				$dataCat=array();
				$sqlCat="SELECT * FROM gibbonFinanceFeeCategory WHERE active='Y' AND NOT gibbonFinanceFeeCategoryID=1 ORDER BY name" ;
				$resultCat=$connection2->prepare($sqlCat);
				$resultCat->execute($dataCat);
			}
			catch(PDOException $e) { }
			if ($resultCat->rowCount()<1) {
				print "<input type=\"hidden\" name=\"companyAll\" value=\"Y\" class=\"companyAll\"/>" ;
			}
			else {
				?>
				<tr id="companyAllRow">
					<td>
						<b><?php print __($guid, 'Company All?') ?></b><br/>
						<span class="emphasis small"><?php print __($guid, 'Should all items be billed to the specified company, or just some?') ?></span>
					</td>
					<td class="right">
						<input type="radio" name="companyAll" value="Y" class="companyAll" checked /> <?php print __($guid, 'All') ?>
						<input type="radio" name="companyAll" value="N" class="companyAll" /> <?php print __($guid, 'Selected') ?>
					</td>
				</tr>
				<tr id="companyCategoriesRow">
					<td>
						<b><?php print __($guid, 'Company Fee Categories') ?></b><br/>
						<span class="emphasis small"><?php print __($guid, 'If the specified company is not paying all fees, which categories are they paying?') ?></span>
					</td>
					<td class="right">
						<?php
						while ($rowCat=$resultCat->fetch()) {
							print __($guid, $rowCat["name"]) . " <input type='checkbox' name='gibbonFinanceFeeCategoryIDList[]' value='" . $rowCat["gibbonFinanceFeeCategoryID"] . "'/><br/>" ;
						}
						print __($guid, "Other") . " <input type='checkbox' name='gibbonFinanceFeeCategoryIDList[]' value='0001'/><br/>" ;
						?>
					</td>
				</tr>
			<?php
			}

			$requiredDocuments=getSettingByScope($connection2, "Application Form", "requiredDocuments") ;
			$requiredDocumentsText=getSettingByScope($connection2, "Application Form", "requiredDocumentsText") ;
			$requiredDocumentsCompulsory=getSettingByScope($connection2, "Application Form", "requiredDocumentsCompulsory") ;
			if ($requiredDocuments!="" AND $requiredDocuments!=FALSE) {
				?>
				<tr class='break'>
					<td colspan=2>
						<h3><?php print __($guid, 'Supporting Documents') ?></h3>
						<?php
						if ($requiredDocumentsText!="" OR $requiredDocumentsCompulsory!="") {
							print "<p>" ;
								print $requiredDocumentsText . " " ;
								if ($requiredDocumentsCompulsory=="Y") {
									print __($guid, "All documents must all be included before the application can be submitted.") ;
								}
								else {
									print __($guid, "These documents are all required, but can be submitted separately to this form if preferred. Please note, however, that your application will be processed faster if the documents are included here.") ;
								}
							print "</p>" ;
						}
						?>
					</td>
				</tr>
				<?php

				//Get list of acceptable file extensions
				try {
					$dataExt=array();
					$sqlExt="SELECT * FROM gibbonFileExtension" ;
					$resultExt=$connection2->prepare($sqlExt);
					$resultExt->execute($dataExt);
				}
				catch(PDOException $e) { }
				$ext="" ;
				while ($rowExt=$resultExt->fetch()) {
					$ext=$ext . "'." . $rowExt["extension"] . "'," ;
				}

				$requiredDocumentsList=explode(",", $requiredDocuments) ;
				$count=0 ;
				foreach ($requiredDocumentsList AS $document) {
					?>
					<tr>
						<td>
							<b><?php print $document ; if ($requiredDocumentsCompulsory=="Y") { print " *" ; } ?></b><br/>
						</td>
						<td class="right">
							<?php
							print "<input type='file' name='file$count' id='file$count'><br/>" ;
							print "<input type='hidden' name='fileName$count' id='filefileName$count' value='$document'>" ;
							if ($requiredDocumentsCompulsory=="Y") {
								print "<script type='text/javascript'>" ;
									print "var file$count=new LiveValidation('file$count');" ;
									print "file$count.add( Validate.Inclusion, { within: [" . $ext . "], failureMessage: 'Illegal file type!', partialMatch: true, caseSensitive: false } );" ;
									print "file$count.add(Validate.Presence);" ;
								print "</script>" ;
							}
							$count++ ;
							?>
						</td>
					</tr>
					<?php
				}
				?>
				<tr>
					<td colspan=2>
						<?php print getMaxUpload($guid) ; ?>
						<input type="hidden" name="fileCount" value="<?php print $count ?>">
					</td>
				</tr>
				<?php
			}
			?>

			<tr class='break'>
				<td colspan=2>
					<h3><?php print __($guid, 'Miscellaneous') ?></h3>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php print __($guid, 'How Did You Hear About Us?') ?> *</b><br/>
				</td>
				<td class="right">
					<?php
					$howDidYouHearList=getSettingByScope($connection2, "Application Form", "howDidYouHear") ;
					if ($howDidYouHearList=="") {
						print "<input name='howDidYouHear' id='howDidYouHear' maxlength=30 value='" . $row["howDidYouHear"] . "' type='text' style='width: 300px'>" ;
					}
					else {
						print "<select name='howDidYouHear' id='howDidYouHear' style='width: 302px'>" ;
							print "<option value='Please select...'>" . __($guid, 'Please select...') . "</option>" ;
							$howDidYouHears=explode(",", $howDidYouHearList) ;
							foreach ($howDidYouHears as $howDidYouHear) {
								print "<option value='" . trim($howDidYouHear) . "'>" . __($guid, trim($howDidYouHear)) . "</option>" ;
							}
						print "</select>" ;
						?>
						<script type="text/javascript">
							var howDidYouHear=new LiveValidation('howDidYouHear');
							howDidYouHear.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
						</script>
						<?php
					}
					?>
				</td>
			</tr>
			<script type="text/javascript">
				$(document).ready(function(){
					$("#howDidYouHear").change(function(){
						if ($('#howDidYouHear option:selected').val()=="Please select..." ) {
							$("#tellUsMoreRow").css("display","none");
						}
						else {
							$("#tellUsMoreRow").slideDown("fast", $("#tellUsMoreRow").css("display","table-row"));
						}
					 });
				});
			</script>
			<tr id="tellUsMoreRow" style='display: none'>
				<td>
					<b><?php print __($guid, 'Tell Us More') ?> </b><br/>
					<span class="emphasis small"><?php print __($guid, 'The name of a person or link to a website, etc.') ?></span>
				</td>
				<td class="right">
					<input name="howDidYouHearMore" id="howDidYouHearMore" maxlength=255 value="" type="text" class="standardWidth">
				</td>
			</tr>
			<?php
			$privacySetting=getSettingByScope( $connection2, "User Admin", "privacy" ) ;
			$privacyBlurb=getSettingByScope( $connection2, "User Admin", "privacyBlurb" ) ;
			$privacyOptions=getSettingByScope( $connection2, "User Admin", "privacyOptions" ) ;
			if ($privacySetting=="Y" AND $privacyBlurb!="" AND $privacyOptions!="") {
				?>
				<tr>
					<td>
						<b><?php print __($guid, 'Privacy') ?> *</b><br/>
						<span class="emphasis small"><?php print htmlPrep($privacyBlurb) ?><br/>
						</span>
					</td>
					<td class="right">
						<?php
						$options=explode(",",$privacyOptions) ;
						foreach ($options AS $option) {
							print $option . " <input type='checkbox' name='privacyOptions[]' value='" . htmlPrep($option) . "'/><br/>" ;
						}
						?>

					</td>
				</tr>
				<?php
			}

			//Get agreement
			$agreement=getSettingByScope($connection2, 'Application Form', 'agreement') ;
			if ($agreement!="") {
				print "<tr class='break'>" ;
					print "<td colspan=2>" ;
						print "<h3>" ;
							print __($guid, "Agreement") ;
						print "</h3>" ;
						print "<p>" ;
							print $agreement ;
						print "</p>" ;
					print "</td>" ;
				print "</tr>" ;
				print "<tr>" ;
					print "<td>" ;
						print "<b>" . __($guid, 'Do you agree to the above?') . "</b><br/>" ;
					print "</td>" ;
					print "<td class='right'>" ;
						print "Yes <input type='checkbox' name='agreement' id='agreement'>" ;
						?>
						<script type="text/javascript">
							var agreement=new LiveValidation('agreement');
							agreement.add( Validate.Acceptance );
						</script>
						 <?php
					print "</td>" ;
				print "</tr>" ;
			}
			?>


			<tr>
				<td>
					<span class="emphasis small">* <?php print __($guid, "denotes a required field") ; ?></span>
				</td>
				<td class="right">
					<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
					<input type="submit" value="<?php print __($guid, "Submit") ; ?>">
				</td>
			</tr>
		</table>
	</form>

	<?php
	//Get postscrript
	$postscript=getSettingByScope($connection2, 'Application Form', 'postscript') ;
	if ($postscript!="") {
		print "<h2>" ;
			print __($guid, "Further Information") ;
		print "</h2>" ;
		print "<p style='padding-bottom: 15px'>" ;
			print $postscript ;
		print "</p>" ;
	}
}
?>
