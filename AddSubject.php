<?php
$PageSecurity = 3;
include('includes/session.inc');
$title = _('Course Maintenance');
include('includes/header.inc');
echo '<p class="page_title_text">' . ' ' . _('Subject Maintenance') . '';       
$sql = "SELECT fullaccess FROM www_users
WHERE userid=  '" . trim($_SESSION['UserID']) . "'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$administrator_rights=$myrow[0];
if(isset($_GET['Remove']))
{
        $sujectPDFNotes=$_POST['subjectPDFNotes'];
   	$target_path = "PDFNotes/$subjectPDFNotes";
	echo $_POST['subjectPDFNotes'];
	unlink($target_path);
	unset($_POST['subjectPDFNotes']);
	$null='';
	$sql = "DELETE FROM subjectpdfnotes WHERE id = '".$_GET['Remove']."'";
	$result = DB_query($sql, $db);
	prnMsg(_('Record') . ' ' . $id . ' ' . _('has been updated'),'success');
}		
if (isset($_GET['SelectedSubject'])) {
	$SelectedSubject=$_GET['SelectedSubject'];
} elseif (isset($_POST['SelectedSubject'])) {
	$SelectedSubject=$_POST['SelectedSubject'];
}

if (isset($Errors)) {
	unset($Errors);
}

$_POST['subject_name'] = ucwords($_POST['subject_name'] );

if(isset($_REQUEST['programs_arr']))
{
$mp_array=implode(', ', $_REQUEST['programs_arr']);
}
$Errors = array();

if (isset($_POST['submit'])) {
$target_path = "pdfnotes/";
if(!empty($_FILES['subjectpdfnotes']['tmp_name'])){
$target_path_subjectPDF = $target_path . basename( $_FILES['subjectpdfnotes']['name']);
if (($_FILES["subjectpdfnotes"]["type"] == "application/pdf")&& ($_FILES["subjectpdfnotes"]["size"] < 90000000))
	 {
	 	 if ($_FILES["subjectpdfnotes"]["error"] > 0) 
	 {
		echo "<ul><li>".$_FILES["subjectpdfnotes"]["error"]."</li></ul>";
		  $InputError=1;
	  } 
	  else
	   {
		if (file_exists("pdfnotes/" . $_FILES["subjectpdfnotes"]["name"])) 
		{
		  prnMsg( _('File already exists'),'warn');
		  $InputError=1;
		} 
		else 
		{
		  move_uploaded_file($_FILES['subjectpdfnotes']['tmp_name'],$target_path_subjectPDF);
		  $sql="INSERT INTO subjectpdfnotes (filename,subject_Id) VALUES ('".$_FILES['subjectpdfnotes']['name']."','$SelectedSubject')";
		  $result = DB_query($sql,$db);
		}
	  }
	 }//end of if
	 else {
	  prnMsg( _($_FILES["subjectpdfnotes"]["type"].' '.$_FILES["subjectpdfnotes"]["size"].' file does not meet required criteria, check file extension.(NB: size should be less than 10mb)'),'warn');
	  $InputError=1;
	}
}

	$_SESSION['course_code']=$SelectedSubject;
	$InputError = 0;
	$i=1;

	$sql="SELECT count(subject_code)
	FROM subjects WHERE subject_code='".$_POST['subject_code']."'";
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_row($result);

	if ($myrow[0]>0 and !isset($SelectedSubject)) {
		$InputError = 1;
		prnMsg( _('Course code already exists in the database'),'error');
		$Errors[$i] = 'course_code';
		$i++;
	}
	
	$sql="SELECT count(subject_code)
	FROM subjects WHERE subject_code='".$_POST['subject_code']."'
	AND id !='".$SelectedSubject."'";
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_row($result);

	if ($myrow[0]>0) {
		$InputError = 1;
		prnMsg( _('Course code already exists in the database'),'error');
		$Errors[$i] = 'subject_code';
		$i++;
	}
	if(sizeof($_REQUEST['programs_arr'])==0){
	$InputError = 1;
	prnMsg( _('Choose at least one program'),'error');
	$i++;
	}
	
	if (isset($SelectedSubject) AND $InputError !=1) {
			$subject=$_POST['subject_name'];
			$subject= strtoupper($subject);
			
			$subject_code=$_POST['subject_code'];
			$subject_code= strtoupper($subject_code);
			
			$sql = "UPDATE subjects
			SET subject_name='" . strtoupper($_POST['subject_name']) . "',
			subject_code='" . $subject_code . "'
			WHERE subjects.id = '" . $SelectedSubject . "'";
		$msg = _('The subject details have been updated');
		$ErrMsg = _('The subject could not be inserted or modified because');
		$DbgMsg = _('The SQL used to insert/modify the subject details was');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		

$sqldel="DELETE FROM allowed_programs WHERE subject_id ='" . $SelectedSubject . "'";
$delete= DB_query($sqldel,$db);
foreach ($_REQUEST['programs_arr'] as $progy){
if(isset($_REQUEST['programs_arr']))
{
$sql2="INSERT INTO allowed_programs(subject_id,program)
VALUES('" . $SelectedSubject . "','" . $progy . "')";
$result2 = DB_query($sql2,$db);
}
}
}
 elseif ($InputError !=1) {
	$subject=$_POST['subject_name'];
	$subject= strtoupper($subject);	
	$subject_code=$_POST['subject_code'];
	$subject_code= strtoupper($subject_code);
	$sql = "INSERT INTO subjects (subject_name,subject_code)
	VALUES ('" . strtoupper($_POST['subject_name']) . "','" . $subject_code . "')";
	$msg = _('The new subject has been entered');
	$ErrMsg = _('The subject could not be inserted or modified because');
	$DbgMsg = _('The SQL used to insert/modify the subject details was');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	prnMsg($msg,'success');
		
$sqllast="SELECT LAST_INSERT_ID()";
$resultlast = DB_query($sqllast,$db);
$myrowlast = DB_fetch_row($resultlast);
$id = $myrowlast[0];		
foreach ($_REQUEST['programs_arr'] as $progy)
{
if(isset($_REQUEST['programs_arr']))
{
$sql2="INSERT INTO allowed_programs(subject_id,program)
VALUES('" . $id . "','" . $progy . "')";
$result2 = DB_query($sql2,$db);
}
}
}

	if( $InputError !=1 ) 
	{		
		echo '<br>';
		unset($_POST['subject_code']);
		unset($_POST['subject_name']);
		unset($SelectedSubject);
	}


} elseif (isset($_GET['delete'])) {
	$sql= "SELECT COUNT(subject_id) FROM registered_students WHERE subject_id='$SelectedSubject'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0)
	 {
		$CancelDelete = 1;
		prnMsg(_('Cannot delete this subject since some students have registered'),'warn');
		echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('subject registration(s)');

	}
	$CancelDelete = 0;
	if (!$CancelDelete) {
		$sql="DELETE FROM subjects WHERE id='$SelectedSubject'";
		$result = DB_query($sql,$db);
		prnMsg(_('Course deleted'),'success');
	} //end if Delete bank account

	unset($_GET['delete']);
	unset($SelectedSubject);
}
/* Always show the list of accounts */
If (!isset($SelectedSubject)) {
echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';	
echo '<table  class=enclosed>';
echo '<tr><td>' . _('Enter Course Name/Code') . ':<input type="Text" name="searchval"   size=50   maxlength=70></td>
<td><input  type="submit" name="form1" value="Search"></td></tr>';	
echo "<tr><th>" . _('Subject Code') . "</th>
<th>" . _('Subject Title') . "</th>
</tr>";
if (isset($_GET['pageno']))
 {
   $pageno = $_GET['pageno'];
}
else 
{
   $pageno = 1;
}
$sql = "SELECT count(*) FROM subjects";
$result = DB_query($sql,$db);
$query_data = DB_fetch_row($result);
$numrows = $query_data[0];
			
$targetpage = "AddCourse.php";
$rows_per_page = 10;
$lastpage      = ceil($numrows/$rows_per_page);
$pageno = (int)$pageno;
if ($pageno > $lastpage) {
   $pageno = $lastpage;
} // if
$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
$SearchString = '%' . str_replace(' ', '%', $_POST['searchval']) . '%';
if (isset($_POST['form1'])){
$sql = "SELECT * FROM subjects
	WHERE subject_code LIKE  '". $SearchString."'
		OR subject_name LIKE  '". $SearchString."'";
	$result = DB_query($sql,$db);
}
else{	
	$sql = "SELECT * FROM subjects ORDER BY subject_code $limit";
	$result = DB_query($sql,$db);
}	
	$k=0; 
	while ($myrow = DB_fetch_array($result)) {		
	echo '<tr>';
	if($administrator_rights==8){
	printf("<td>%s</td>
			<td>%s</td>
			<td><a href=\"%s&SelectedSubject=%s\">" . _('Edit') . "</a></td>
			<td><a href=\"%s&SelectedSubject=%s&delete=1&subject_code=%s\">" . _('Delete') . "</a></td>
			</tr>",
			$myrow['subject_code'],
			$myrow['subject_name'],
			$_SERVER['PHP_SELF']  . "?" . SID,
			$myrow['id'],
			$_SERVER['PHP_SELF'] . "?" . SID,
			$myrow['id'],
			urlencode($myrow['subject_code']));
	}
	else{
		printf("<td>%s</td>
			<td>%s</td>
			<td><a href=\"%s&SelectedSubject=%s\">" . _('Edit') . "</a></td>
			</tr>",
			$myrow['subject_code'],
			$myrow['subject_name'],
			$_SERVER['PHP_SELF']  . "?" . SID,
			$myrow['id'],
			urlencode($myrow['subject_code']));
		}

	} //END WHILE LIST LOOP
	echo "<tr>";
if ($pageno == 1) {
   echo "<td>"." FIRST PREV"." </td>";
} else {
   echo " <td><a href='{$_SERVER['PHP_SELF']}?pageno=1'>FIRST</a> ";
   $prevpage = $pageno-1;
   echo " <a href='{$_SERVER['PHP_SELF']}?pageno=$prevpage'>PREV</a> "." </td>";
}
echo " <td>( Page $pageno of $lastpage ) "." </td>";
if ($pageno == $lastpage) {
   echo "<td> NEXT LAST "."</td>";
} else {
   $nextpage = $pageno+1;
   echo "<td> <a href='{$_SERVER['PHP_SELF']}?pageno=$nextpage'>NEXT</a> ";
   echo " <a href='{$_SERVER['PHP_SELF']}?pageno=$lastpage'>LAST</a> "." </td>";
}		
	echo '<tr></table><p>';
}

if (isset($SelectedSubject)) {
	echo '<p>';
	echo "<br /><div class='centre'><a href='" . $_SERVER['PHP_SELF'] ."?" . SID . "'>" . _('Show all Subjects') . '</a></div>';
	echo '<p>';
}

echo "<form method='post' enctype='multipart/form-data' action=" . $_SERVER['PHP_SELF'] . "?" . SID . ">";
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if (isset($SelectedSubject) AND !isset($_GET['delete'])) {
	$sql = "SELECT * FROM subjects WHERE id='$SelectedSubject'";
	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);

	$_POST['subject_name'] = $myrow['subject_name'];
	$_POST['subject_code']  = $myrow['subject_code'];
	
	echo '<input type=hidden name=SelectedSubject VALUE=' . $SelectedSubject . '>';
	echo '<input type=hidden name=subject_code VALUE=' . $_POST['subject_code'] . '>';
	echo '<table class=enclosed>';
} 
else 
{ 
	echo '<table class=enclosed><tr>';	
}
if (!isset($_POST['subject_name'])) {
	$_POST['subject_name']='';
}
if (!isset($_POST['subject_code'])) {
	$_POST['subject_code']='';
}
if (!isset($_POST['lecturer_id'])) {
	$_POST['lecturer_id']='';
}

	
$sql ="SELECT id,course_code,course_name FROM courses ORDER BY course_code";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		$programs_RET[] = array("id" => $myrow['id'],
				     "course_code" => $myrow['course_code'],"course_name" => $myrow['course_name']);
		}

echo '</br><tr><TD class="visible">' . _('Program(s):') . '</TD>';
echo '<TD class="visible">';
foreach($programs_RET as $programs=>$program)
{
$sql = "SELECT program
		FROM allowed_programs
		WHERE program='". $program['id']."'
		AND subject_id='" . $SelectedSubject . "'";
	$result = DB_query($sql, $db);
	$myrow = DB_fetch_row($result);
if($myrow[0]==$program['id']){
echo '<INPUT type=checkbox name=programs_arr[] checked=checked value='.$program['id'].'>'.$program['course_code'];
}else{
echo '<INPUT type=checkbox name=programs_arr[]  value='.$program['id'].'>'.$program['course_code'];
}
}
echo '</TD></TR>';

echo '<td class="visible">' . _('Subject Code') . ': </td>
<td class="visible"><input tabindex="2" ' . (in_array('subject_code',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="subject_code" value="' . $_POST['subject_code'] . '" size=40 maxlength=50></td></tr>
<tr><td class="visible">' . _('Subject Title') . ': </td>
<td class="visible"><input tabindex="3" ' . (in_array('subject_name',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="subject_name" value="' . $_POST['subject_name'] . '" size=70 maxlength=100></td></tr>';
if (isset($_GET['SelectedSubject']) and ($administrator_rights==8 || $administrator_rights==13)) {
echo '<TR><td class="visible">Attach PDF notes</br>(150px * 110px)</td><TD class="visible"><input type="file" name="subjectpdfnotes"></TD></TR>';
}
echo '</tr></table><br>
<div class="centre"><input tabindex="7" type="Submit" name="submit" value="'. _('Submit') .'"></div>';

if (isset($_GET['SelectedSubject'])){
echo '<table class=enclosed>';
$sql = "SELECT * FROM subjectpdfnotes WHERE subject_id='".$_GET['SelectedSubject']."' ORDER BY fileName";
$result = DB_query($sql,$db);
while ($row = DB_fetch_array($result))
{		
		 echo "<tr >";
		 echo '<td class="visible"><a href="' . $rootpath .'/pdfnotes/'. $row['filename'] . '">' . $row['filename'] . '</a></td>';
		 echo "<td><a href='" . $_SERVER['PHP_SELF'] . "?" . SID . "&Remove=" . $row['id']. "'>" . _('Remove File') . "</a></td>";
		 echo '</tr>';
}
echo '</table>';
}
echo '</form>';
include('includes/footer.inc');
?>
