<?
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

session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/School Admin/externalAssessments_manage_edit_field_add.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	//Proceed!
	$gibbonExternalAssessmentID=$_GET["gibbonExternalAssessmentID"] ;
	
	if ($gibbonExternalAssessmentID=="") {
		print "<div class='error'>" ;
			print "You have not specified a column." ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("gibbonExternalAssessmentID"=>$gibbonExternalAssessmentID); 
			$sql="SELECT name FROM gibbonExternalAssessment WHERE gibbonExternalAssessmentID=:gibbonExternalAssessmentID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}

		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print "The specified grade scale does not exist." ;
			print "</div>" ;
		}
		else {
			$row=$result->fetch() ;
			
			print "<div class='trail'>" ;
			print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/externalAssessments_manage.php'>Manage External Assessments</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/externalAssessments_manage_edit.php&gibbonExternalAssessmentID=$gibbonExternalAssessmentID'>Edit External Assessment</a> > </div><div class='trailEnd'>Add Field</div>" ;
			print "</div>" ;
			
			$addReturn = $_GET["addReturn"] ;
			$addReturnMessage ="" ;
			$class="error" ;
			if (!($addReturn=="")) {
				if ($addReturn=="fail0") {
					$addReturnMessage ="Add failed because you do not have access to this action." ;	
				}
				else if ($addReturn=="fail2") {
					$addReturnMessage ="Add failed due to a database error." ;	
				}
				else if ($addReturn=="fail3") {
					$addReturnMessage ="Add failed because your inputs were invalid." ;	
				}
				else if ($addReturn=="fail4") {
					$addReturnMessage ="Update failed some values need to be unique but were not." ;	
				}
				else if ($addReturn=="fail5") {
					$addReturnMessage ="Update failed some values need to be unique but were not." ;	
				}
				else if ($addReturn=="success0") {
					$addReturnMessage ="Add was successful." ;	
					$class="success" ;
				}
				print "<div class='$class'>" ;
					print $addReturnMessage;
				print "</div>" ;
			} 
			?>
			<form method="post" action="<? print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/externalAssessments_manage_edit_field_addProcess.php" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr>
						<td> 
							<b>Grade Scale *</b><br/>
							<span style="font-size: 90%"><i>This value cannot be changed.</i></span>
						</td>
						<td class="right">
							<input readonly name="name" id="name" value="<? print $row["name"] ?>" type="text" style="width: 300px">
						</td>
					</tr>
					<tr>
						<td> 
							<b>Name *</b><br/>
							<span style="font-size: 90%"><i></i></span>
						</td>
						<td class="right">
							<input name="name" id="name" maxlength=50 value="" type="text" style="width: 300px">
							<script type="text/javascript">
								var name = new LiveValidation('name');
								name.add(Validate.Presence);
							 </script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Category *</b><br/>
						</td>
						<td class="right">
							<input name="category" id="category" maxlength=10 value="<? print $row["category"] ?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var category = new LiveValidation('category');
								category.add(Validate.Presence);
							 </script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Order *</b><br/>
							<span style="font-size: 90%"><i>Order in which fields appear within category<br/>Should be unique for this category.<br/></i></span>
						</td>
						<td class="right">
							<input name="order" id="order" maxlength=4 value="<? print $row["order"] ?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var order = new LiveValidation('order');
								order.add(Validate.Presence);
							 </script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Grade Scale *</b><br/>
							<span style="font-size: 90%"><i>Grade scale used to control values that can be assigned.</i></span>
						</td>
						<td class="right">
							<select name="gibbonScaleID" id="gibbonScaleID" style="width: 302px">
								<?
								try {
									$dataSelect=array(); 
									$sqlSelect="SELECT * FROM gibbonScale WHERE (active='Y') ORDER BY name" ;
									$resultSelect=$connection2->prepare($sqlSelect);
									$resultSelect->execute($dataSelect);
								}
								catch(PDOException $e) { }
								print "<option value='Please select...'>Please select...</option>" ;
								while ($rowSelect=$resultSelect->fetch()) {
									if ($row2["gibbonScaleIDAttainment"]==$rowSelect["gibbonScaleID"]) {
										print "<option selected value='" . $rowSelect["gibbonScaleID"] . "'>" . htmlPrep($rowSelect["name"]) . "</option>" ;
									}
									else {
										print "<option value='" . $rowSelect["gibbonScaleID"] . "'>" . htmlPrep($rowSelect["name"]) . "</option>" ;
									}
								}
								?>				
							</select>
							<script type="text/javascript">
								var gibbonScaleID = new LiveValidation('gibbonScaleID');
								gibbonScaleID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "Select something!"});
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Year Groups</b><br/>
							<span style="font-size: 90%"><i>Year groups to which this field is relevant.</i></span>
						</td>
						<td class="right">
							<? 
							print "<fieldset style='border: none'>" ;
							?>
							<script type="text/javascript">
								$(function () { // this line makes sure this code runs on page load
									$('.checkall').click(function () {
										$(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
									});
								});
							</script>
							<?
							print "All / None <input type='checkbox' class='checkall'><br/>" ;
							$yearGroups=getYearGroups($connection2) ;
							if ($yearGroups=="") {
								print "<i>No year groups available.</i>" ;
							}
							else {
								for ($i=0; $i<count($yearGroups); $i=$i+2) {
									print $yearGroups[($i+1)] . " <input type='checkbox' name='gibbonYearGroupIDCheck" . ($i)/2 . "'><br/>" ; 
									print "<input type='hidden' name='gibbonYearGroupID" . ($i)/2 . "' value='" . $yearGroups[$i] . "'>" ;
								}
							}
							print "</fieldset>" ;
							?>
							<input type="hidden" name="count" value="<? print (count($yearGroups))/2 ?>">
						</td>
					</tr>
					
					<tr>
						<td>
							<span style="font-size: 90%"><i>* denotes a required field</i></span>
						</td>
						<td class="right">
							<input name="gibbonExternalAssessmentID" id="gibbonExternalAssessmentID" value="<? print $gibbonExternalAssessmentID ?>" type="hidden">
							<input type="hidden" name="address" value="<? print $_SESSION[$guid]["address"] ?>">
							<input type="reset" value="Reset"> <input type="submit" value="Submit">
						</td>
					</tr>
				</table>
			</form>
			<?
		}	
	}
}
?>