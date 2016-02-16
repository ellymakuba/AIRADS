<?php
$PageSecurity = 3;
include('includes/session.inc');
$title = _('Insert Marks');
include('includes/header.inc');

$current_year=date('Y');
//$current_year=2014;
$sql = "SELECT year,id FROM years
WHERE year LIKE  '$current_year'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['year']=$myrow[0]; 
$_SESSION['year_id']=$myrow[1]; 
echo '<p class="page_title_text">' . ' ' . _('Insert Marks') . '';
if(!isset($_SESSION['class_session']) || isset($_POST['clear_session'])  ){
if(!isset($_POST['view'])){
	echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table  class="enclosed"><tr><td valign=top><table  class=enclosed>';
echo '<tr><td>' . _('Class') . ': </td><td><select tabindex="5" name="class_session">';
$result = DB_query('SELECT * FROM classes WHERE status = 0 ORDER BY class_name',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['class_session']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['class_name'];
} //end while loop
	echo'</table></td></tr></table>';
	echo "<br><div class='centre'><input tabindex=20 type='Submit' name='view' value='" . _('Submit') . "'></div>";	
	echo '</form>';
				}
}	
if(isset($_POST['clear_session'])){
unset($_SESSION['class_session']);
}	
if( isset($_POST['view']) || isset($_SESSION['class_session']) ){
if(!isset($_SESSION['class_session'])){
$_SESSION['class_session']=$_POST['class_session'];	
}
$sql = "SELECT t.title,cl.current_term,cl.course_id,cl.class_name FROM classes cl
INNER JOIN terms t ON t.id=cl.current_term
WHERE cl.id='".$_SESSION['class_session']."'";
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
$query_data = DB_fetch_row($result);
$_SESSION['title'] = $query_data[0];
$_SESSION['current_term'] = $query_data[1];
$_SESSION['course'] = $query_data[2];
$_SESSION['class_name'] = $query_data[3];
	
echo '<br><table class=nclosed>';
echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo "<tr><td class=\"visible\">" . _('Class') . ":</td>
	<td>".$_SESSION['class_name']."
	<input tabindex=21 type=submit name='clear_session' VALUE='" . _('Remove') . "'></td>
	</tr>";

echo '<tr><td class="visible">' . _('Period') . ":</td>
		<td class=\"visible\">";
		echo $_SESSION['title'].' '.$_SESSION['year'];
		echo '</td></tr>';	
DB_data_seek($result,0);
echo '</select></td></tr>';
echo '<tr><td class="visible">' . _('Subject') . ":</td>
<td class=\"visible\"><select name='subject_id'>";
echo '<OPTION SELECTED VALUE=0>' . _('Select Subject');
	$sql="SELECT sub.id,sub.subject_name,sub.subject_code FROM subjects sub
		INNER JOIN allowed_programs ap ON ap.subject_id=sub.id
		AND ap.program= '".$_SESSION['course']."'
		ORDER BY sub.subject_name,sub.subject_code";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>'.$myrow['subject_name'].' - '.'('.$myrow['subject_code'].')';
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';	
echo '<tr><td class="visible">' . _('Exam Mode') . ":</td>
		<td class='visible'><select name='exam_mode_id'>";
		echo '<OPTION SELECTED VALUE=0>' . _('Select exam mode');
		$sql="SELECT id,title FROM markingperiods ORDER BY priority";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'].  '>'.' '.$myrow['title'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';
echo "<tr><td class='visible'>" . _('Out Of') . ":</td>
	<td class='visible'><input type='text' name='out_of' size=20></td>";
	echo'</table>';
	echo "<br><div class='centre'><input tabindex=20 type='Submit' name='submit' value='" . _('Display Students') . "'></div>";
}// end of if isset view

if(isset($_POST['submit'])){
if($_POST['exam_mode_id']==0){
prnMsg(_(' You must select the exam mode'),'warn');
exit();
}	
if(empty($_POST['out_of'])){
prnMsg(_(' Please fill the out of field'),'warn');
exit();
}
$sql="SELECT subject_name,id FROM subjects 
Where id= '".$_POST['subject_id']."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['subject']=$myrow[0];
$_SESSION['subject_id']=$myrow[1]; 

$sql="SELECT title,id FROM markingperiods
Where id= '".$_POST['exam_mode_id']."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['mode']=$myrow[0];
$_SESSION['mode_id']=$myrow[1]; 

$_SESSION['out_of']=$_POST['out_of']; 

echo '<br><table class=enclosed>';
echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';	
echo '<tr><th>' . _('Class') . '</th><th colspan=2>' . $_SESSION['class_name'] . '</th></tr>
	<tr><th>' . _('Subject') . ':</th><th colspan=2>' . $_SESSION['subject'] . '</th></tr>
	<tr><th>' . _('Period') . ':</th><th colspan=2>' . $_SESSION['title'].' '.$_SESSION['year'] . '</th></tr>
	<tr><th>' . _('Mode') . ':</th><th colspan=2>' . $_SESSION['mode'] . '</th></tr>
	<tr><th>' . _('Out Of') . ':</th><th colspan=2>' . $_SESSION['out_of'] . '</th></tr>';
	
	echo '<tr><th>' . _('AdmNo') . '</th>
	<th>' . _('Name') . ':</th>
	<th>' . _('Marks') . ':</th>';
		
$sql = "SELECT COUNT(*) FROM registered_students
		WHERE subject_id =  '". $_POST['subject_id'] ."'
		AND term =  '". $_SESSION['current_term'] ."'
		AND class_id='". $_SESSION['class_session'] ."'
		AND year =  '". $_SESSION['year_id'] ."'";
		$result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
		if ($myrow[0]>0 ){
         $sql = "SELECT rs.*,dm.name,dm.debtorno FROM registered_students rs
		 INNER JOIN debtorsmaster dm ON dm.debtorno=rs.student_id
		WHERE subject_id =  '". $_POST['subject_id'] ."'
		AND rs.term =  '". $_SESSION['current_term'] ."'
		AND rs.class_id='". $_SESSION['class_session'] ."'
		AND year =  '". $_SESSION['year_id'] ."'
		ORDER BY dm.debtorno";
         $DbgMsg = _('The SQL that was used to retrieve the information was');
         $ErrMsg = _('Could not check whether the group is recursive because');
         $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);	
	while ($row = DB_fetch_array($result))
			{
	echo "<tr>";
	echo "<input type='hidden' name='calendar_id[]' value='".$row['id']."'>";
	echo "<input type='hidden' name='student_id[]' value='".$row['debtorno']."' >";
	echo "<tr><td class=\"visible\">".$row['debtorno']."</td>";
	echo "<td class=\"visible\">".$row['name']."</td>";
	echo "<td class=\"visible\"><input type='text' name='marks[]' size=20 ></td>";
	echo "</tr>";
	 $j++;
	}
	}
echo "<tr><td></td><td></td><td><input type=submit name='add_marks' value='"._('Post Marks')."'></td></tr>";
}
if (isset($_POST['add_marks'])){
$i=0;
foreach($_POST['marks'] as $value){
if($_POST['marks'][$i]>0){
$sql = "SELECT id FROM studentsmarks
		WHERE student_id='". $_POST['student_id'][$i] ."'
		AND exam_mode='". $_SESSION['mode_id'] ."'
		AND calendar_id =  '".$_POST['calendar_id'][$i] ."'";
		$result=DB_query($sql,$db);
	if(DB_fetch_row($result)>0){
prnMsg(_($_POST['student_id'][$i]._(' ').'s marks has already been entered for this subject'),'warn');
	
}
else{
$sql = "SELECT exam_type_id FROM markingperiods
		WHERE id='". $_SESSION['mode_id'] ."'";
		$result=DB_query($sql,$db);
		$row = DB_fetch_array($result);
		if($row['exam_type_id']==1){
		if($_POST['marks'][$i]>$_SESSION['out_of']){
			prnMsg(_(' Student marks cannot be greater than the out of mark'),'warn');
			}
			else{	
	$sql = "INSERT INTO studentsmarks 
			(student_id,term,marks,out_of,exam_mode,calendar_id,year) 
			VALUES ('" .$_POST['student_id'][$i] ."','" .$_SESSION['current_term']."',
			'" .$_POST['marks'][$i]/$_SESSION['out_of']*30 ."','" .$_SESSION['out_of']."','" .$_SESSION['mode_id']."','" .
			$_POST['calendar_id'][$i]."','" .$_SESSION['year_id']."') ";
			$ErrMsg = _('This marks could not be added because');
				$result = DB_query($sql,$db,$ErrMsg);
				prnMsg( _('Marks Added'),'success');
				}
		}
	elseif($row['exam_type_id']==2){
	if($_POST['marks'][$i]>$_SESSION['out_of']){
			prnMsg(_(' Student marks cannot be greater than the out of mark'),'warn');
			}
			else{
	$sql = "INSERT INTO studentsmarks 
			(student_id,term,marks,out_of,exam_mode,calendar_id,year) 
			VALUES ('" .$_POST['student_id'][$i] ."','" .$_SESSION['current_term']."','" .$_POST['marks'][$i]/$_SESSION['out_of']*70 ."',
			'" .$_SESSION['out_of']."','" .$_SESSION['mode_id']."','" .$_POST['calendar_id'][$i]."','" .$_SESSION['year_id']."') ";
			$ErrMsg = _('This marks could not be added because');
			$result = DB_query($sql,$db,$ErrMsg);
			prnMsg( _('Marks Added'),'success');
			}
	}
	elseif($row['exam_type_id']==3){
	if($_POST['marks'][$i]>$_SESSION['out_of']){
			prnMsg(_(' Student marks cannot be greater than the out of mark'),'warn');
			}
			else{
	$sql = "INSERT INTO studentsmarks 
			(student_id,term,marks,out_of,exam_mode,calendar_id,year) 
			VALUES ('" .$_POST['student_id'][$i] ."','" .$_SESSION['current_term']."','" .$_POST['marks'][$i]/$_SESSION['out_of']*100 ."',
			'" .$_SESSION['out_of']."','" .$_SESSION['mode_id']."','" .$_POST['calendar_id'][$i]."','" .$_SESSION['year_id']."') ";
			$ErrMsg = _('This marks could not be added because');
			$result = DB_query($sql,$db,$ErrMsg);
			prnMsg( _('Marks Added'),'success');
			}
	}
}
}
$i++;

}
echo '</form>';			
}	
include('includes/footer.inc');
?>


