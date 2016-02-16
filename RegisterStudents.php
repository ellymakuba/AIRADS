<?php
$PageSecurity = 3;
include('includes/session.inc');
$title = _('Manage Students');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
$msg='';
$current_year=date('Y');
//$current_year=2014;
$sql = "SELECT year,id FROM years
WHERE year LIKE  '$current_year'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['year']=$myrow[0]; 
$_SESSION['year_id']=$myrow[1]; 
echo "<form name='myform' method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table border="1" width="40%">';

echo '<tr><td>' . _('Class') . ': </td><td><select tabindex="5" name="student_class">';
$result = DB_query('SELECT * FROM classes WHERE status = 0 ORDER BY class_name',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_SESSION['class_session'] || $myrow['id']==$_POST['student_class']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['class_name'];
} //end while loop
	echo '</select></td></tr></table>';
		
echo "<br><div class='centre'><input  type='Submit' name='submit' value='" . _('Display Students') . "'>&nbsp;<input  type=submit action=RESET VALUE='" . _('Reset') . "'></div><br>";	

if (isset($_POST['submit'])) {
$_SESSION['class'] = $_POST['student_class'];
$sql = "SELECT t.title,cl.current_term,cl.course_id FROM classes cl
INNER JOIN terms t ON t.id=cl.current_term
WHERE cl.id='".$_SESSION['class']."'";
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
$query_data = DB_fetch_row($result);
$_SESSION['title'] = $query_data[0];
$_SESSION['current_term'] = $query_data[1];
$_SESSION['course'] = $query_data[2];
$SearchString = '%' . str_replace(' ', '%', $_POST['searchval']) . '%';

echo '<table class=selection width="60%">';
	
if (isset($_POST['student_class']) && $_POST['student_class'] !=0) {
$sql = "SELECT class_name FROM classes
		WHERE id =  '". $_SESSION['class']."'";
		$result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);	
echo "<tr><td class=\"visible\">" . _('Class') . ":</td>
	<td>".$myrow[0]."</td></tr>";

echo '<tr><td class="visible">' . _('Term') . ":</td>
		<td class=\"visible\">";
		echo $_SESSION['title'];
		echo '</td></tr>';	
			
echo '<tr><td class="visible">' . _('Year') . ":</td>
		<td class=\"visible\">";
		echo $_SESSION['year'];
		echo '</td></tr>';		
	
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
	echo '<tr><td class="visible">' . _('Lecturer') . ":</td>
		<td class=\"visible\"><select name='lecturer'>";
		echo '<OPTION SELECTED VALUE=0>' . _('Select Lecturer');
		$sql="SELECT userid,realname FROM www_users ";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['userid'].  '>'.' '.$myrow['realname'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';		

	
echo '<tr><th>' . _('Adm No') . '</th>
		<th>' . _('Name') . ':</th>';
		
		
$sql = "SELECT COUNT(*) FROM debtorsmaster
		WHERE  class_id= '". $_SESSION['class'] ."'";
        $result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
		if ($myrow[0]>0 ){		
		$sql = "SELECT * FROM debtorsmaster
		WHERE  class_id= '". $_SESSION['class'] ."'";
        $DbgMsg = _('The SQL that was used to retrieve the information was');
        $ErrMsg = _('Could not check whether the group is recursive because');
        $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		}
		else{
		prnMsg( _('There are no records to display Currently'),'error');
exit();
}		


}		
else{
prnMsg( _('Please choose the search criteria'),'error');
exit();
}
echo '<tr><td>
	 <input type="button" name="Check_All" value="Check All"
onClick="Check(document.myform.tick)">
	  </td></tr>';				
			while ($row = DB_fetch_array($result))
			{
			 if (($j%2)==1)
		    echo "<tr bgcolor=\"F0F0F0\">";
		  else
		    echo "<tr bgcolor=\"FFFFFF\">";
			$ovamount=-$row['ovamount']; ?>
			<?php 
		echo "<tr><td class=\"visible\"><Input type = 'Checkbox' name ='add_id[]' id='tick' value='".$row['debtorno']."'>".$row['debtorno']."</td>";
			?><?php
		  echo "<td class=\"visible\">".$row['name']."</td>";
		  
		    echo "</tr>";
		  $j++;
			}
			

echo "<td><br><div class='centre'><input  type='Submit' name='register' value='" . _('Register') . "'></div></td></tr>";
}
if (isset($_POST['register'])){	
$sql = "SELECT year FROM collegeperiods
		WHERE id =  '". $_POST['period_id'] ."'";
		$result=DB_query($sql,$db);
		$row=DB_fetch_row($result);
		$academic_year=$row[0];
		$_SESSION['year']=$academic_year;
		
$i=0;
if(isset($_POST['add_id'])){
	foreach($_POST['add_id'] as $value){
	$sql = "SELECT id FROM registered_students
			WHERE student_id='". $_POST['add_id'][$i] ."'
			AND subject_id='". $_POST['subject_id'] ."'
			AND term =  '". $_SESSION['current_term'] ."'
			AND year =  '". $_SESSION['year_id'] ."'";
			$result=DB_query($sql,$db);
		if(DB_fetch_row($result)>0){
		prnMsg(_($_POST['add_id'][$i]._(' ').'has already been registered for this subject'),'warn');
		$i++;		
		}
		else{
		$sql = "INSERT INTO registered_students (student_id,subject_id,term,class_id,year,lecturer) 
		VALUES ('" .$_POST['add_id'][$i] ."','" .$_POST['subject_id'] ."','" .$_SESSION['current_term'] ."','" .$_SESSION['class'] ."',
		'" .$_SESSION['year_id'] ."','" .$_POST['lecturer'] ."') ";
		$ErrMsg = _('The student could not be updated because');
		$result = DB_query($sql,$db,$ErrMsg);
		$i++;	
		}
			
	}
	prnMsg( _('student registration successful'),'success');
}
include('includes/footer.inc');
			exit;
}	
include('includes/footer.inc');
?>
<SCRIPT LANGUAGE="JavaScript">
<!--

<!-- Begin
function Check(chk)
{
if(document.myform.Check_All.value=="Check All"){
for (i = 0; i < chk.length; i++)
chk[i].checked = true ;
document.myform.Check_All.value="UnCheck All";
}else{

for (i = 0; i < chk.length; i++)
chk[i].checked = false ;
document.myform.Check_All.value="Check All";
}
}

// End -->
</script>
