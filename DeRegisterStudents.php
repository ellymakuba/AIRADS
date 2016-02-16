<?php
$PageSecurity = 3;
include('includes/session.inc');
$title = _('Manage Students');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
$msg='';
if(!isset($_SESSION['class_session']) || isset($_POST['clear_session'])  ){
if(!isset($_POST['view'])){
	echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class=enclosed><tr><td valign=top><table  class=enclosed>';
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

if(isset($_POST['view']) || isset($_SESSION['class_session']) ){
if(!isset($_SESSION['class_session'])){
$_SESSION['class_session']=$_POST['class_session'];	
}
$sql = "SELECT t.title,cl.current_term,cl.course_id,cl.class_name FROM classes cl
INNER JOIN terms t ON t.id=cl.current_term
WHERE cl.id='".$_SESSION['class_session']."'";
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
$query_data = DB_fetch_row($result);
$_SESSION['course'] = $query_data[2];
$_SESSION['class_name'] = $query_data[3];

echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<br><table class=enclosed>';
	
echo "<tr><td class=\"visible\">" . _('Class') . ":</td>
	<td class=\"visible\">".$_SESSION['class_name']."
	<input tabindex=21 type=submit name='clear_session' VALUE='" . _('Remove') . "'></td>
	</tr>";

	echo '<tr><td class="visible">' . _('Term') . ":</td>
			<td class=\"visible\"><select name='term'>";
			echo '<OPTION SELECTED VALUE=0>' . _('Select Term');
			$sql="SELECT id,title FROM  terms ";
			$result=DB_query($sql,$db);
			while ($myrow = DB_fetch_array($result)) {
			echo '<option value='. $myrow['id'].  '>'.' '.$myrow['title'];
			} //end while loop
			DB_data_seek($result,0);
			echo '</select></td></tr>';
			
	echo '<tr><td class="visible">' . _('Year') . ":</td>
			<td class=\"visible\"><select name='year'>";
			echo '<OPTION SELECTED VALUE=0>' . _('Select Year');
			$sql="SELECT id,year FROM  years ";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'].  '>'.' '.$myrow['year'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';		

echo '<tr><td class="visible">' . _('Subject') . ":</td>
<td class=\"visible\"><select name='subject_id'>";
echo '<OPTION SELECTED VALUE=0>' . _('Select Subject');
$sql="SELECT sub.id,sub.subject_name,sub.subject_code FROM subjects sub
		INNER JOIN registered_students rs ON rs.subject_id=sub.id
		INNER JOIN allowed_programs ap ON ap.subject_id=sub.id
		WHERE ap.program= '".$_SESSION['course']."'
		GROUP BY rs.subject_id
		ORDER BY sub.subject_name,sub.subject_code";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>'.$myrow['subject_name'].' - '.'('.$myrow['subject_code'].')';
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr></table>';			
			
echo "<br><div class='centre'><input  type='Submit' name='submit' value='" . _('Display Students') . "'></div><br>";
echo '</form>';
echo '<table class=enclosed>';
$sql = "SELECT year FROM collegeperiods
WHERE id='".$_SESSION['semester']."'";
$result = DB_query($sql,$db);
$myrow = DB_fetch_row($result);
$academic_year = $myrow[0];
$_SESSION['academic_year']=$academic_year;


$SearchString = '%' . str_replace(' ', '%', $_POST['searchval']) . '%';	
}//end of if isset view


if (isset($_POST['subject_id']) && $_POST['subject_id'] !=0
  && isset($_POST['term']) && $_POST['term'] !=0
  && isset($_SESSION['class_session']) && $_SESSION['class_session'] !=0
  && isset($_POST['year']) && $_POST['year'] !=0
  && isset($_POST['submit']))
 {
 $_SESSION['term_id'] = $_POST['term'];
$_SESSION['year_id'] = $_POST['year'];

 
 $sql="SELECT subject_name,id FROM subjects 
Where id= '".$_POST['subject_id']."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['subject']=$myrow[0];
$_SESSION['subject_id']=$myrow[1];

 $sql="SELECT title,id FROM terms
Where id= '".$_SESSION['term_id']."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['term']=$myrow[0];

 $sql="SELECT year,id FROM years
Where id= '".$_SESSION['year_id']."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['year']=$myrow[0];

echo '<br><table class=enclosed>';
echo "<form name='myform' method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';	
echo '<tr><th>' . _('Class') . '</th><th colspan=2>' . $_SESSION['class_name'] . '</th></tr>
	<tr><th>' . _('Subject') . ':</th><th colspan=2>' . $_SESSION['subject'] . '</th></tr>
	<tr><th>' . _('Period') . ':</th><th colspan=2>' . $_SESSION['term'].' '.$_SESSION['year'] . '</th></tr>';

$sql = "SELECT rs.id FROM registered_students rs
		INNER JOIN debtorsmaster dm ON dm.debtorno=rs.student_id
		WHERE rs.subject_id= '". $_SESSION['subject_id'] ."'
		AND rs.term='".$_SESSION['term_id']."'
		AND rs.year='".$_SESSION['year_id']."'
		AND rs.class_id='".$_SESSION['class_session']."'";
        $result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
		if ($myrow[0]>0 ){
echo '<tr><th>' . _('RegNo') . '</th>
		<th>' . _('Name') . ':</th>';
						
		$sql = "SELECT dm.*,rs.id as calendar_id FROM registered_students rs
		INNER JOIN debtorsmaster dm ON dm.debtorno=rs.student_id
		WHERE rs.subject_id= '". $_SESSION['subject_id'] ."'
		AND rs.term='".$_SESSION['term_id']."'
		AND rs.year='".$_SESSION['year_id']."'
		AND rs.class_id='".$_SESSION['class_session']."'
		ORDER BY dm.debtorno";
        $DbgMsg = _('The SQL that was used to retrieve the information was');
        $ErrMsg = _('Could not check whether the group is recursive because');
        $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	}		
			
		else{
		prnMsg( _('There are no Students to display for the chosen criteria'),'error');
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
		echo "<tr><td class=\"visible\"><Input type = 'Checkbox'  id='tick' name ='add_id[]' value='".$row['calendar_id']."'>".$row['debtorno']."</td>";
		  echo "<td class=\"visible\">".$row['name']."</td>";
		  
		    echo "</tr>";
		  $j++;
			}
		
echo "<tr><td><br><div class='centre'><input  type='Submit' name='remove' value='" . _('Deregister') . "'></div></td></tr>";
}
if (isset($_POST['remove'])){
$i=0;
if(isset($_POST['add_id'])){
foreach($_POST['add_id'] as $value){
$sql = "SELECT sm.marks,dm.name FROM studentsmarks sm
INNER JOIN debtorsmaster dm ON dm.debtorno=sm.student_id
		WHERE calendar_id='". $_POST['add_id'][$i] ."'
		GROUP BY sm.student_id";
		$result=DB_query($sql,$db);
		$count=DB_fetch_row($result);
		$marks=$count[0];
		$study=$count[1];
if($count>0 && $marks>0){
prnMsg(_($study._(' ').'Cannot be deregistered since there are marks under this subject'),'warn');
$i++;		
}
else{
$sql="DELETE FROM registered_students WHERE id='". $_POST['add_id'][$i] ."'";
	$result = DB_query($sql,$db);
	
$i++;
prnMsg(_('subjects deregistration successful'),'success');		
}//end of else

}//end of foreach

}//end of if $_POST['add_id']
include('includes/footer.inc');
			exit;
}//end of $_POST['register']

	
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