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

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Planner/outcomes_add.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print __($guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Get action with highest precendence
	$highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print __($guid, "The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}
	else {
		if ($highestAction!="Manage Outcomes_viewEditAll" AND $highestAction!="Manage Outcomes_viewAllEditLearningArea") {
			print "<div class='error'>" ;
				print __($guid, "You do not have access to this action.") ;
			print "</div>" ;
		}
		else {
			//Proceed!
			print "<div class='trail'>" ;
			print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/outcomes.php'>" . __($guid, 'Manage Outcomes') . "</a> > </div><div class='trailEnd'>" . __($guid, 'Add Outcome') . "</div>" ;
			print "</div>" ;
			
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
				else if ($addReturn=="fail5") {
					$addReturnMessage=__($guid, "Your request was successful, but some data was not properly saved.") ;	
				}
				else if ($addReturn=="success0") {
					$addReturnMessage=__($guid, "Your request was completed successfully. You can now add another record if you wish.") ;	
					$class="success" ;
				}
				print "<div class='$class'>" ;
					print $addReturnMessage;
				print "</div>" ;
			} 
			
			$filter2="" ;
			if (isset($_GET["filter2"])) {
				$filter2=$_GET["filter2"] ;
			}
			
			if ($filter2!="") {
				print "<div class='linkTop'>" ;
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Planner/outcomes.php&filter2=" . $filter2 . "'>" . __($guid, 'Back to Search Results') . "</a>" ;
				print "</div>" ;
			}
			
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/outcomes_addProcess.php?filter2=" . $filter2 ?>">
				<table class='smallIntBorder fullWidth' cellspacing='0'>	
					<tr>
						<td style='width: 275px'> 
							<b><?php print __($guid, 'Scope') ?> *</b><br/>
							<span class="emphasis small"></span>
						</td>
						<td class="right">
							<?php
							if ($highestAction=="Manage Outcomes_viewEditAll") {
								?>
								<select name="scope" id="scope" class="standardWidth">
									<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
									<option value="School"><?php print __($guid, 'School') ?></option>
									<option value="Learning Area"><?php print __($guid, 'Learning Area') ?></option>
								</select>
								<script type="text/javascript">
									var scope=new LiveValidation('scope');
									scope.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
								</script>
								 <?php
							}
							else if ($highestAction=="Manage Outcomes_viewAllEditLearningArea") {
								?>
								<input readonly name="scope" id="scope" value="Learning Area" type="text" class="standardWidth">
								<?php
							}
							?>
						</td>
					</tr>
					
					
					<?php
					if ($highestAction=="Manage Outcomes_viewEditAll") {
						?>
						<script type="text/javascript">
							$(document).ready(function(){
								$("#learningAreaRow").css("display","none");
								
								$("#scope").change(function(){
									if ($('#scope option:selected').val()=="Learning Area" ) {
										$("#learningAreaRow").slideDown("fast", $("#learningAreaRow").css("display","table-row")); 
										gibbonDepartmentID.enable();
									}
									else {
										$("#learningAreaRow").css("display","none");
										gibbonDepartmentID.disable();
									}
								 });
							});
						</script>
						<?php
					}
					?>
					<tr id='learningAreaRow'>
						<td> 
							<b><?php print __($guid, 'Learning Area') ?> *</b><br/>
							<span class="emphasis small"></span>
						</td>
						<td class="right">
							<?php
							try {
								if ($highestAction=="Manage Outcomes_viewEditAll") {
									$dataSelect=array(); 
									$sqlSelect="SELECT * FROM gibbonDepartment WHERE type='Learning Area' ORDER BY name" ;
								}
								else if ($highestAction=="Manage Outcomes_viewAllEditLearningArea") {
									$dataSelect=array("gibbonPersonID"=>$_SESSION[$guid]["gibbonPersonID"]); 
									$sqlSelect="SELECT * FROM gibbonDepartment JOIN gibbonDepartmentStaff ON (gibbonDepartmentStaff.gibbonDepartmentID=gibbonDepartment.gibbonDepartmentID) WHERE gibbonPersonID=:gibbonPersonID AND (role='Coordinator' OR role='Teacher (Curriculum)') AND type='Learning Area'  ORDER BY name" ;
								}
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							?>
							<select name="gibbonDepartmentID" id="gibbonDepartmentID" class="standardWidth">
								<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
								<?php
								while ($rowSelect=$resultSelect->fetch()) {
									print "<option value='" . $rowSelect["gibbonDepartmentID"] . "'>" . $rowSelect["name"] . "</option>" ;
								}
								?>
							</select>
							<script type="text/javascript">
								var gibbonDepartmentID=new LiveValidation('gibbonDepartmentID');
								gibbonDepartmentID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
								<?php
								if ($highestAction=="Manage Outcomes_viewEditAll") {
									print "gibbonDepartmentID.disable();" ;
								}
								?>
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Name') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="name" id="name" maxlength=100 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var name2=new LiveValidation('name');
								name2.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Short Name') ?> *</b><br/>
						</td>
						<td class="right">
							<input name="nameShort" id="nameShort" maxlength=14 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								var nameShort=new LiveValidation('nameShort');
								nameShort.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Active') ?> *</b><br/>
							<span class="emphasis small"></span>
						</td>
						<td class="right">
							<select name="active" id="active" class="standardWidth">
								<option value="Y"><?php print __($guid, 'Yes') ?></option>
								<option value="N"><?php print __($guid, 'No') ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<td> 
							<b><?php print __($guid, 'Category') ?></b><br/>
						</td>
						<td class="right">
							<input name="category" id="category" maxlength=100 value="" type="text" class="standardWidth">
							<script type="text/javascript">
								$(function() {
									var availableTags=[
										<?php
										try {
											$dataAuto=array(); 
											$sqlAuto="SELECT DISTINCT category FROM gibbonOutcome ORDER BY category" ;
											$resultAuto=$connection2->prepare($sqlAuto);
											$resultAuto->execute($dataAuto);
										}
										catch(PDOException $e) { }
										while ($rowAuto=$resultAuto->fetch()) {
											print "\"" . $rowAuto["category"] . "\", " ;
										}
										?>
									];
									$( "#category" ).autocomplete({source: availableTags});
								});
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Description') ?></b><br/>
						</td>
						<td class="right">
							<textarea name='description' id='description' rows=5 style='width: 300px'></textarea>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print __($guid, 'Year Groups') ?></b><br/>
							<span class="emphasis small"><?php print __($guid, 'Relevant student year groups') ?><br/></span>
						</td>
						<td class="right">
							<?php
							print "<fieldset style='border: none'>" ;
							?>
							<script type="text/javascript">
								$(function () {
									$('.checkall').click(function () {
										$(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
									});
								});
							</script>
							<?php
							print __($guid, "All/None") . " <input type='checkbox' class='checkall'><br/>" ;
							$yearGroups=getYearGroups($connection2) ;
							if ($yearGroups=="") {
								print "<i>" . __($guid, 'No year groups available.') . "</i>" ;
							}
							else {
								for ($i=0; $i<count($yearGroups); $i=$i+2) {
									print __($guid, $yearGroups[($i+1)]) . " <input type='checkbox' name='gibbonYearGroupIDCheck" . ($i)/2 . "'><br/>" ; 
									print "<input type='hidden' name='gibbonYearGroupID" . ($i)/2 . "' value='" . $yearGroups[$i] . "'>" ;
								}
							}
							print "</fieldset>" ;
							?>
							<input type="hidden" name="count" value="<?php print (count($yearGroups))/2 ?>">
						</td>
					</tr>
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
		}
	}
}
?>