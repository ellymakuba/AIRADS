<?php
$PageSecurity = 6;
include('includes/session.inc');
if(isset($_POST['term']) && isset($_POST['year']) && isset($_POST['class_id']) ){
include('includes/PDFStarter.php');
include('grades/OveralClassReport.php');
//include('grades/ClassSubjectMean.php');


$_SESSION['class'] = $_POST['class_id'];
$_SESSION['term_id'] = $_POST['term'];
$_SESSION['year_id'] = $_POST['year'];		
$PageNumber=1;
$line_height=12;
NewPageHeader ();
$FontSize=18;
$pdf->addJpegFromFile($_SESSION['LogoFile'] ,$XPos+130,$YPos-120,0,100);
$YPos-=(2*$line_height);
$pdf->SetFont('times', '', 18, '', 'false');
$YPos-=(2*$line_height);
$pdf->SetFont('times', '', 12, '', 'false');
$FontSize=10;

$style = array('width' => 0.70, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(12, 12, 12));

$sql="SELECT cp.id,terms.title,years.year FROM collegeperiods cp
INNER JOIN terms ON terms.id=cp.term_id
INNER JOIN years ON years.id=cp.year 
WHERE cp.id =  '". $_SESSION['period'] ."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_array($result);
$title=$myrow['title'];
$year=$myrow['year'];

$sql = "SELECT id,class_name FROM classes
WHERE id =  '". $_SESSION['class'] ."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_array($result);
$class_name=$myrow['class_name'];
/*$LeftOvers = $pdf->addTextWrap(100,$YPos-($line_height*11),500,$FontSize, _('Reportcard For').': ' . $myrow[0].'    '._('Period').': ' .$myrow2[1].'-'.$myrow2[2]);*/	
$LeftOvers = $pdf->addTextWrap(200,$YPos-($line_height*12),400,$FontSize,_('OVERAL CLASS PERFORMANCE'));
 $LeftOvers = $pdf->addTextWrap(200,$YPos-($line_height*12.3),80,$FontSize,'______________________________________________________________________________');
$LeftOvers = $pdf->addTextWrap(100,$YPos-($line_height*15),300,$FontSize, $title.' ' . $year);
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*15),500,$FontSize,_('Class')._(': '). $class_name);
	
$YPos +=20;
$YPos -=$line_height;
//Note, this is ok for multilang as this is the value of a Select, text in option is different

$YPos -=(12*$line_height);

$pdf->line(19, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height,$style);

$YPos -=50;
$YPos -=$line_height;
$pdf->line(19, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height,$style);

$YPos -=(8*$line_height);
$line_width=40;
$XPos=180;
$YPos2=$YPos;
$count=0;
$i=0;
$bus_report = new bus_report($_POST['class_id'],$_POST['year'],$_POST['term'],$db);
$subjects_array =  tep_get_subjects($_POST['class_id'],$_POST['year'],$_POST['term'],$db);
$current_student='';
$pdf->starttransform();
$pdf->xy($XPos-20,332);
$pdf->rotate(90);
$pdf->stoptransform();
$FontSize=5;
foreach ($subjects_array as $r => $s)
{
	$pdf->starttransform();
	$pdf->xy($XPos,332);
	$pdf->rotate(90);
	$LeftOvers = $pdf->addTextWrap($XPos-45,$YPos,300,$FontSize,$s['subject_name']);
	$pdf->stoptransform();		
	$XPos +=(0.5*$line_width);
}
$FontSize=10;
$LeftOvers = $pdf->addTextWrap($XPos+35,$YPos,300,$FontSize,_('Total'));
$LeftOvers = $pdf->addTextWrap($XPos+70,$YPos,300,$FontSize,_('Grade'));
//$LeftOvers = $pdf->addTextWrap($XPos+110,$YPos,300,$FontSize,_('Rank'));		
$YPos -=10;
$rank =0;
$pdf->line(19, $YPos,$Page_Width-$Right_Margin, $YPos,$style);	
$YPos -=$line_height;

foreach ($bus_report->scheduled_students as $sa => $st) 
{
	$total=0;
	$no_of_students=$no_of_students+1;
	if ($YPos < ($Bottom_Margin + (2* $line_height)))
	{ 
		$PageNumber++;
		NewPageHeader ();
	}
	$rank=$rank+1;
	$LeftOvers = $pdf->addTextWrap(21,$YPos+1,300,$FontSize,$st['name']);
	$pdf->line(19, $YPos,$Page_Width-$Right_Margin, $YPos,$style);	
	$YPos -=(0.8*$line_height);
	$scheduled = new scheduled($st['student_id'],$db);
    $subjects_taken_by_student=0;
	$student_total=0;
	$student_total2=0;
//$scheduled->set_primary_vars_class($_POST['class_id'],$st['student_id'],$_POST['year'],$_POST['term'],$st['id'],$db);
$XPos2=230;
$subject_meangrade_array=0;
foreach ($subjects_array as $subjects => $subject) 
{
$marks=primary_get_subjects_marks_class($subject['id'],$st['student_id'],$_POST['class_id'],$_POST['year'],$_POST['term'],$st['id'],$db);
$student_total2=$student_total2+$marks;
$LeftOvers = $pdf->addTextWrap($XPos2-20,$YPos+10,300,$FontSize,$marks);
	if($PageNumber <2)
	{
	$pdf->line($XPos2-1,$YPos+140,$XPos2-1, $YPos-11,$style);
	}
	if($PageNumber >1)
	{
	$pdf->line($XPos2, $YPos+32,$XPos2, $YPos-11,$style);
	$pdf->line($XPos2-1,$YPos+140,$XPos2-1, $YPos-11,$style);	
	}

	$XPos2 +=(0.5*$line_width);	

	}//end of scheduled subject
if($PageNumber ==1){
$pdf->line(19, $YPos+140,19, $YPos-11,$style);
$pdf->line(195, $YPos+140,195, $YPos-11,$style);
$pdf->line($XPos2+50,$YPos+140,$XPos2+50, $YPos-11,$style);
$pdf->line($XPos2+14,$YPos+140,$XPos2+14, $YPos-11,$style);
$pdf->line(210, $YPos+140,210, $YPos-11,$style);	
$pdf->line(566, $YPos+140,566, $YPos-11,$style);
}
if($PageNumber >1){
$pdf->line(19, 832,$Page_Width-$Right_Margin, 832,$style);
$pdf->line(195, 832,195, $YPos-11,$style);
$pdf->line(19, $YPos+31,19, $YPos-24,$style);
$pdf->line($XPos2+50,$YPos+31,$XPos2+50, $YPos-11,$style);
$pdf->line($XPos2+14,$YPos+31,$XPos2+14, $YPos-11,$style);
$pdf->line(210, $YPos+31,210, $YPos-11,$style);	
$pdf->line(566, $YPos+31,566, $YPos-11,$style);
}
		
$subjects_taken_by_student=students_subjects_class($st['student_id'],$_POST['year'],$_POST['term'],$db);
if($subjects_taken_by_student >0)
{
		$subjects_count=$student_total2/$subjects_taken_by_student;
		$sql = "SELECT grade FROM reportcardgrades
		WHERE range_from <=  '". $subjects_count ."'
		AND range_to >='". $subjects_count."'
		AND grading LIKE 'other'";
		$result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
		$grade=$myrow[0];
}			
$totalmarks_array =$bus_report->total_marks($st['student_id'],$_POST['year'],$_POST['term'],$db);
$LeftOvers = $pdf->addTextWrap($XPos2-10,$YPos+10,300,$FontSize,$student_total2);
$LeftOvers = $pdf->addTextWrap($XPos2+30,$YPos+10,300,$FontSize,$grade);	
//$LeftOvers = $pdf->addTextWrap($XPos2+65,$YPos+10,300,$FontSize,$rank);	
$grand_total=$grand_total+$student_total2;							
	}
$XPos3=212;
/*
foreach ($subjects_array as $r => $s) {
$bus_report_class= new bus_report_class($_POST['class_id'],$_POST['period_id'],$s['id'],$db);
$count=0;
$total_marks=0;
$total_marks2=0;
foreach ($bus_report_class->scheduled_students as $a => $b) {
$total_marks=total_marks_class($b['student_id'],$_POST['period_id'],$s['id'],$db);
$total_marks2=$total_marks2+$total_marks;	
$count=$count+1;
}
if($count > 0){
$subject_mean=$total_marks2/$count;
}
else{
$subject_mean=0;
}

$LeftOvers = $pdf->addTextWrap($XPos3-1,$YPos+1,300,9,$total_marks2);
$LeftOvers = $pdf->addTextWrap($XPos3,$YPos-10,300,9,number_format($subject_mean,1));
$XPos3 +=(0.5*$line_width);
}//end of ssubjects array foreach

if($no_of_students>0){	
$mean_class=$grand_total/$no_of_students;	
}	
$LeftOvers = $pdf->addTextWrap(21,$YPos+1,300,$FontSize,_('Total'));
$LeftOvers = $pdf->addTextWrap($XPos3,$YPos+1,300,$FontSize,$grand_total);
$pdf->line(19, $YPos,$Page_Width-$Right_Margin, $YPos,$style);	
$LeftOvers = $pdf->addTextWrap(21,$YPos-10,300,$FontSize,_('Mean Score'));
$LeftOvers = $pdf->addTextWrap($XPos3,$YPos-10,300,$FontSize,number_format($mean_class,1));	*/
$pdf->line(19, $YPos-11,$Page_Width-$Right_Margin, $YPos-11,$style);	
$pdf->Output('Report-', 'I');


}
else { /*The option to print PDF was not hit */

	//include('includes/session.inc');
	$title = _('Manage Students');

include('includes/header.inc');

echo '<FORM METHOD="POST" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<TABLE class=enclosed><TR><TD>' . _('Class:') . '</TD><TD><SELECT Name="class_id">';
		DB_data_seek($result, 0);
		$sql = 'SELECT cl.id,cl.class_name FROM classes cl 
		ORDER BY cl.class_name';
		$result = DB_query($sql, $db);
		while ($myrow = DB_fetch_array($result)) {
			if ($myrow['id'] == $_POST['class_id']) {  
				echo '<OPTION SELECTED VALUE=';
			} else {
				echo '<OPTION VALUE=';
			}
echo $myrow['id'] . '>' . $myrow['class_name'];
		} //end while loop
	echo '</SELECT></TD></TR>';
echo '<TR><TD class="visible">' . _('Term:') . '</TD><TD class="visible"><SELECT Name="term">';
		DB_data_seek($result, 0);
		$sql="SELECT id,title FROM terms ";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
			if ($myrow['id'] == $_POST['term']) {  
				echo '<OPTION SELECTED VALUE=';
			} else {
				echo '<OPTION VALUE=';
			}
			echo $myrow['id'] . '>'.' '.$myrow['title'];
		} //end while loop
	echo '</SELECT></TD></TR>';
	echo '<TR><TD class="visible">' . _('Year:') . '</TD><TD class="visible"><SELECT Name="year">';
		DB_data_seek($result, 0);
		$sql="SELECT id,year FROM years ";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
			if ($myrow['id'] == $_POST['year']) {  
				echo '<OPTION SELECTED VALUE=';
			} else {
				echo '<OPTION VALUE=';
			}
			echo $myrow['id'] . '>'.' '.$myrow['year'];
		} //end while loop
	echo '</SELECT></TD></TR>';
	echo "</TABLE>";
	echo "<P><CENTER><INPUT TYPE='Submit' NAME='PrintPDF' VALUE='" . _('PrintPDF') . "'>";

	include('includes/footer.inc');;
} /*end of else not PrintPDF */
function NewPageHeader () {
	global $PageNumber,
				$pdf,
				$YPos,
				$YPos2,
				$YPos4,
				$Page_Height,
				$Page_Width,
				$Top_Margin,
				$FontSize,
				$Left_Margin,
				$XPos,
				$XPos2,
				$Right_Margin,
				$line_height;
				$line_width;

	/*PDF page header for GL Account report */

	if ($PageNumber > 1){
		$pdf->newPage();
	}
$YPos= $Page_Height-$Top_Margin;


	


}
?>