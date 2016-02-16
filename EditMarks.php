<?php
$PageSecurity = 3;
include('includes/session.inc');
$title = _('Edit Marks');
include('includes/header.inc');
echo '<p class="page_title_text">' . ' ' . _('Edit Marks') . '';
if(!isset($_SESSION['class_session']) || isset($_POST['clear_session'])  ){
if(!isset($_POST['view'])){
	echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table  class=enclosed><tr><td valign=top><table  class=enclosed>';
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
		echo '</select></td></tr></table>';	
		
	echo "<br><div class='centre'><input tabindex=20 type='Submit' name='submit' value='" . _('Display Students') . "'></div>";
	echo '</form>';
}//end of isset view
if (isset($_POST['submit'])) {
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

$sql="SELECT title,id FROM markingperiods
Where id= '".$_POST['exam_mode_id']."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['mode']=$myrow[0];
$_SESSION['mode_id']=$myrow[1]; 

 $sql="SELECT year,id FROM years
Where id= '".$_SESSION['year_id']."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['year']=$myrow[0];


$sql = "SELECT sm.out_of FROM registered_students rs
INNER JOIN studentsmarks sm ON rs.id=sm.calendar_id
WHERE rs.subject_id =  '". $_SESSION['subject_id'] ."'
AND rs.term =  '". $_SESSION['term_id'] ."'
AND sm.exam_mode='". $_SESSION['mode_id'] ."'
AND rs.year='". $_SESSION['year_id'] ."'
AND rs.class_id='". $_SESSION['class_session'] ."'
GROUP BY rs.class_id";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
//$_SESSION['out_of']=$myrow[0];
$sql = "SELECT exam_type_id FROM markingperiods
		WHERE id='". $_SESSION['mode_id'] ."'";
		$result=DB_query($sql,$db);
		$row = DB_fetch_array($result);
		if($row['exam_type_id']==1){
		$_SESSION['out_of']=30;
		}
		elseif($row['exam_type_id']==2){
		$_SESSION['out_of']=70;
		}
		elseif($row['exam_type_id']==3){
		$_SESSION['out_of']=100;
		}


echo '<br><table class=enclosed>';
echo "<form name='myform' method='post' action=" . $_SERVER['PHP_SELF'] . '>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';	
echo '<tr><th>' . _('Class') . '</th><th colspan=2>' . $_SESSION['class_name'] . '</th></tr>
	<tr><th>' . _('Subject') . ':</th><th colspan=2>' . $_SESSION['subject'] . '</th></tr>
	<tr><th>' . _('Period') . ':</th><th colspan=2>' . $_SESSION['term'].' '.$_SESSION['year'] . '</th></tr>
	<tr><th>' . _('Mode') . ':</th><th colspan=2>' . $_SESSION['mode'] . '</th></tr>
	<tr><th>' . _('Out Of') . ':</th><th colspan=2>' . $_SESSION['out_of'] . '</th></tr>';
	
	echo '<tr><th>' . _('AdmNo') . '</th>
	<th>' . _('Name') . ':</th>
	<th>' . _('Marks') . ':</th>';
	
	echo '<tr><td>
	 <input type="button" name="Check_All" value="Check All"
onClick="Check(document.myform.tick)">
	  </td></tr>';
	
	$sql = "SELECT exam_type_id FROM markingperiods
		WHERE id='". $_POST['exam_mode'] ."'";
		$result=DB_query($sql,$db);
		$row = DB_fetch_array($result);
		$real_type=$row['exam_type_id'];
		
         $sql = "SELECT rs.id as ids,rs.subject_id,rs.student_id,sm.marks,dm.debtorno,dm.name 
		 FROM registered_students rs
		INNER JOIN studentsmarks sm ON rs.id=sm.calendar_id
		INNER JOIN debtorsmaster dm ON dm.debtorno=rs.student_id
		WHERE rs.subject_id =  '". $_SESSION['subject_id'] ."'
		AND rs.term =  '". $_SESSION['term_id'] ."'
		AND sm.exam_mode='". $_SESSION['mode_id'] ."'
		AND rs.year='". $_SESSION['year_id'] ."'
		AND rs.class_id='". $_SESSION['class_session'] ."'
		ORDER BY dm.debtorno";
         $DbgMsg = _('The SQL that was used to retrieve the information was');
         $ErrMsg = _('Could not check whether the group is recursive because');
         $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		 $count=DB_num_rows($result);	
	while ($row = DB_fetch_array($result))
			{
			 if (($j%2)==1)
		    echo "<tr>";
		  
	echo "<tr><td class=\"visible\"><input type='checkbox' name='calendar_id[]' id='tick' value='".$row['ids']."'>".$row['debtorno']."</td>";
echo "<input type='hidden' name='student_id[]' value='".$row['debtorno']."' readonly=''>"; 
echo "<td class=\"visible\">".$row['name']."</td>"; 
echo "<td class=\"visible\">"; ?><input type="text" name='marks<?php echo $row['ids']; ?>' id='marks' value='<?php echo $row['marks']; ?>' size='20' > <?php "</td>";
	echo "</tr>";
	 $j++;
	}
echo "<tr><td></td><td><input type=submit name='edit_marks' value='"._('Edit Marks')."'></td><td><input  type=submit name='delete_marks' VALUE='" . _('Delete Row(s)') . "'></td></tr>";
echo '</table><br></form>';		
}
if (isset($_POST['edit_marks'])){
$i=0;
foreach($_POST['calendar_id'] as $id){		
$sql = "SELECT exam_type_id FROM markingperiods
		WHERE id='". $_SESSION['mode_id'] ."'";
		$result=DB_query($sql,$db);
		$row = DB_fetch_array($result);
	if($_POST['marks'.$id] > $_SESSION['out_of']){
	 prnMsg(_('Marks cannot exceed Out of field'),'warn'); 
	
	}
	else{
		if($row['exam_type_id']==1){
		$sql = "UPDATE studentsmarks  SET marks='" .($_POST['marks'.$id]/$_SESSION['out_of'])*30 ."'
		WHERE calendar_id='" .$id ."'
		AND exam_mode='" .$_SESSION['mode_id'] ."'";
		$ErrMsg = _('This marks could not be updated because');
		$result = DB_query($sql,$db,$ErrMsg);
		prnMsg( _('Marks updated'),'success');
		}
		elseif($row['exam_type_id']==2){
		$sql = "UPDATE studentsmarks  SET marks='" .($_POST['marks'.$id]/$_SESSION['out_of'])*70 ."'
		WHERE calendar_id='" .$id."'
		AND exam_mode='" .$_SESSION['mode_id'] ."'";
		$ErrMsg = _('This marks could not be updated because');
		$result = DB_query($sql,$db,$ErrMsg);
		prnMsg( _('Marks updated'),'success');
		}
		elseif($row['exam_type_id']==3){
		$sql = "UPDATE studentsmarks  SET marks='" .($_POST['marks'.$id]/$_SESSION['out_of'])*100 ."'
		WHERE calendar_id='" .$id."'
		AND exam_mode='" .$_SESSION['mode_id'] ."'";
		$ErrMsg = _('This marks could not be updated because');
		$result = DB_query($sql,$db,$ErrMsg);
		prnMsg( _('Marks updated'),'success');
		}
	}	
}
$i++;
}

if (isset($_POST['delete_marks'])){
	foreach($_POST['calendar_id'] as $id){
	$sql = "DELETE FROM studentsmarks 
		WHERE calendar_id='" .$id ."'
		AND exam_mode='" .$_SESSION['mode_id'] ."'";
		$ErrMsg = _('This marks could not be updated because');
			$result = DB_query($sql,$db,$ErrMsg);
			prnMsg( _('Marks deleted'),'success');
	
	}
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

