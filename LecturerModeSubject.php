<?php
$PageSecurity = 2;
include('includes/session.inc');
include('grades/ModeClass.php');
if(isset($_POST['period_id']) && isset($_POST['class_id']) && isset($_POST['PrintPDF']) && isset($_POST['exam_mode'])){
include('includes/PDFStarter.php');
include('grades/TemporaryMeanClass.php');
$FontSize=13;
function studentsRegisteredForSubject($subject,$period,$stream,$db)
{
$sql="SELECT COUNT(*) FROM registered_students WHERE subject_id='$subject' AND period_id='$period' AND class_id='$stream'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
return $myrow[0];
}
$_SESSION['class'] = $_POST['class_id'];
$_SESSION['period'] = $_POST['period_id'];		
$PageNumber=1;
$line_height=12;
NewPageHeader ();
$FontSize=13;
$FontSize=18;
$YPos= $Page_Height-$Top_Margin;
$XPos=0;
$FontSize=18;
$pdf->addJpegFromFile($_SESSION['LogoFile'] ,$XPos+260,$YPos-120,0,100);
$YPos-=(2*$line_height);
$pdf->SetFont('times', '', 18, '', 'false');

$LeftOvers = $pdf->addTextWrap(100,$YPos-($line_height*10),400,$FontSize,strtoupper($_SESSION['CompanyRecord']['coyname']));
$FontSize=12;
$LeftOvers = $pdf->addTextWrap(180,$YPos-($line_height*11),400,$FontSize,
$_SESSION['CompanyRecord']['regoffice3'].' - '.$_SESSION['CompanyRecord']['regoffice5'].' - '.('TEL :').' '.
$_SESSION['CompanyRecord']['regoffice4']);
$FontSize=10;
$LeftOvers = $pdf->addTextWrap(240,$YPos-($line_height*12),300,$FontSize,_('EMAIL :').' '.$_SESSION['CompanyRecord']['email']);
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

$sql = "SELECT c.id,c.class_name FROM classes c
WHERE c.id =  '". $_SESSION['class'] ."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_array($result);
$class_name=$myrow['class_name'];

$sql = "SELECT title FROM markingperiods
WHERE id =  '". $_POST['exam_mode'] ."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_array($result);
$exam_title=strtoupper($myrow['title']);
/*$LeftOvers = $pdf->addTextWrap(100,$YPos-($line_height*11),500,$FontSize, _('Reportcard For').': ' . $myrow[0].'    '._('Period').': ' .$myrow2[1].'-'.$myrow2[2]);*/	
$LeftOvers = $pdf->addTextWrap(260,$YPos-($line_height*12),400,$FontSize,$exam_title.' '._('EXAM'));
$LeftOvers = $pdf->addTextWrap(100,$YPos-($line_height*15),300,$FontSize, $title.' ' . $year);
$LeftOvers = $pdf->addTextWrap(300,$YPos-($line_height*15),300,$FontSize,_('Class')._(': '). $class_name);
	
$YPos +=20;
$YPos -=$line_height;
//Note, this is ok for multilang as this is the value of a Select, text in option is different

$YPos -=(12*$line_height);

$pdf->line(19, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);

$YPos -=50;
$YPos -=$line_height;
$pdf->line(19, $YPos+$line_height+2,$Page_Width-$Right_Margin, $YPos+$line_height+2);

$YPos -=(8*$line_height);
$line_width=40;
$XPos=130;
$YPos2=$YPos;
$count=0;
$i=0;
$bus_report_stream = new bus_report_stream_exam_mode($_POST['class_id'],$_POST['period_id'],$_POST['exam_mode'],$db);
$subjects_array = tep_get_subjects_stream_exam_mode($_POST['class_id'],$_POST['period_id'],$db);
$current_student='';
$pdf->starttransform();
$pdf->xy($XPos-20,332);
$pdf->rotate(90);
$LeftOvers = $pdf->addTextWrap($XPos-65,$YPos-5,300,$FontSize,_('GENDER'));
$pdf->stoptransform();
	foreach ($subjects_array as $r => $s) 
	{
		$pdf->starttransform();
		$pdf->xy($XPos,332);
		$pdf->rotate(90);
		$LeftOvers = $pdf->addTextWrap($XPos-45,$YPos,300,$FontSize,$s['subject_name']);
		$pdf->stoptransform();		
			$XPos +=(0.62*$line_width);
	}
	$LeftOvers = $pdf->addTextWrap($XPos+35,$YPos,300,$FontSize,_('Total'));
	$LeftOvers = $pdf->addTextWrap($XPos+60,$YPos,300,$FontSize,_('Grade'));
	$LeftOvers = $pdf->addTextWrap($XPos+110,$YPos,300,$FontSize,_('Rank'));	
	$YPos -=10;
	$rank =0;
	$totalEnglishPoints=0;
$totalKiswahiliPoints=0;
$totalMathPoints=0;
$totalGeographyPoints=0;
$totalHistoryPoints=0;
$totalPhysicsPoints=0;
$totalBusinessStudies=0;
$totalChemistryPoints=0;
$totalBiologyPoints=0;
$totalCREpoints=0;
$totalAgriculturePoints=0;
	$rank_ties=0;
	$previous_mean=0;
	$pdf->line(19, $YPos,$Page_Width-$Right_Margin, $YPos);	
	$YPos -=$line_height;
	foreach ($bus_report_stream->scheduled_students as $sa => $st) 
	{
		$no_of_students=$no_of_students+1;
		$total=0;
		if ($st['mean'] == $previous_mean)
		{
			$rank=$rank;
			$rank_ties=$rank_ties+1;
		}
		else
		{
			$rank=$rank_ties+$rank+1;
			$rank_ties=0;
		}
		$previous_mean=$st['mean'];
		$LeftOvers = $pdf->addTextWrap(145,$YPos+1,300,$FontSize,$st['initial']);
		$LeftOvers = $pdf->addTextWrap(21,$YPos+1,300,$FontSize,$st['name']);
		$pdf->line(19, $YPos,$Page_Width-$Right_Margin, $YPos);	
		$YPos -=(0.75*$line_height);			
		$scheduled = new scheduled_stream_exam_mode($st['student_id'],$db);
		$subjects_taken_by_student=0;
		$student_total=0;
		$student_total2=0;
		$scheduled->set_calendar_vars_stream_exam_mode($_POST['class_id'],$st['student_id'],$_POST['period_id'],$st['id'],
		$_POST['exam_mode'],$db);
		$XPos2=160;
		$subject_meangrade_array=0;		
		foreach ($scheduled->subject as $y=>$z) 
		{
		      
			  
			if($PageNumber <2)
			{
			$pdf->line($XPos2,$YPos+140,$XPos2, $YPos+10,$style);
			}
			if($PageNumber >1)
			{
			$pdf->line($XPos2, $YPos+32,$XPos2, $YPos-11,$style);
			}
			$LeftOvers = $pdf->addTextWrap($XPos2,$YPos+9,300,$FontSize,$z['tmarks']);
			if(isset($z['tmarks']))
		    {
			 $sql = "SELECT grade,title FROM reportcardgrades
		     WHERE range_from <=  '". $z['tmarks'] ."'
		     AND range_to >='". $z['tmarks']."'
		     AND grading LIKE '".$z['grading']."'";
		     $result=DB_query($sql,$db);
		     $myrow=DB_fetch_row($result);
		     $sub_grade=$myrow[0];
			 $sub_points=$myrow[1];  
			 $LeftOvers = $pdf->addTextWrap($XPos2+10,$YPos+10,300,$FontSize,$sub_grade);
		    }
			$XPos2 +=(0.62*$line_width);
			
			if($z['id']==4 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'OTHER'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalEnglishPoints=$totalEnglishPoints+$points;
		}
		if($z['id']==6 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'OTHER'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalKiswahiliPoints=$totalKiswahiliPoints+$points;
		}
		else if($z['id']==5 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'MATHS'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalMathPoints=$totalMathPoints+$points;
		}
		else if($z['id']==14 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'OTHER'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalGeographyPoints=$totalGeographyPoints+$points;
		}
		else if($z['id']==8 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'OTHER'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalHistoryPoints=$totalHistoryPoints+$points;
		}
		else if($z['id']==9 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'SCIENCE'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalPhysicsPoints=$totalPhysicsPoints+$points;
		}
		else if($z['id']==10 && isset($z['tmarks'])){		
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'OTHER'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$businessPoints=$myrowmean[0];
		$totalBusinessStudies=$totalBusinessStudies+$businessPoints;
		}
		else if($z['id']==11 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'SCIENCE'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalChemistryPoints=$totalChemistryPoints+$points;
		}
		else if($z['id']==12 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'SCIENCE'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalBiologyPoints=$totalBiologyPoints+$points;
		}
		else if($z['id']==13 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'OTHER'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalCREpoints=$totalCREpoints+$points;
		}
		else if($z['id']==15 && isset($z['tmarks'])){
		$sqlmean = "SELECT title FROM reportcardgrades
		WHERE range_from <=  '". $z['tmarks'] ."'
		AND range_to >='". $z['tmarks'] ."'
		AND grading LIKE 'OTHER'";
		$resultmean=DB_query($sqlmean,$db);
		$myrowmean=DB_fetch_row($resultmean);
		$points=$myrowmean[0];
		$totalAgriculturePoints=$totalAgriculturePoints+$points;
		}
		

		}//end of foreach ($scheduled->subject as $y=>$z)
		if($PageNumber ==1)
		{
		$pdf->line(19, $YPos+140,19, $YPos+10,$style);
		$pdf->line(140, $YPos+140,140, $YPos+10,$style);
		$pdf->line($XPos2+30,$YPos+140,$XPos2+30, $YPos+10,$style);
		$pdf->line($XPos2+60,$YPos+140,$XPos2+60, $YPos+10,$style);
		$pdf->line($XPos2+1,$YPos+140,$XPos2+1, $YPos+10,$style);
		$pdf->line(210, $YPos+140,210, $YPos+10,$style);	
		$pdf->line(566, $YPos+140,566, $YPos+10,$style);
		}
		if($PageNumber >1)
		{
		$pdf->line(19, 835,$Page_Width-$Right_Margin, 835,$style);
		$pdf->line(140, 832,140, $YPos-11,$style);
		$pdf->line(19, $YPos+31,19, $YPos-11,$style);
		$pdf->line($XPos2+1,$YPos+31,$XPos2+1, $YPos-11,$style);
		$pdf->line($XPos2+30,$YPos+31,$XPos2+30, $YPos-11,$style);
		$pdf->line($XPos2+60,$YPos+31,$XPos2+60, $YPos-11,$style);		
		$pdf->line(210, $YPos+31,210, $YPos-11,$style);	
		$pdf->line(566, $YPos+31,566, $YPos-11,$style);
		}	
		
		$sql = "SELECT grade FROM reportcardgrades
		WHERE title=  '". $st['meanScore']."'
		AND grading LIKE 'other'";
		$result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
		$grade=$myrow[0];
			
		$totalmarks_array =$bus_report_stream->total_marks_stream_exam_mode($st['student_id'],$_POST['period_id'],$db);
		$LeftOvers = $pdf->addTextWrap($XPos2+2,$YPos+9,300,$FontSize,number_format($st['mean']/$st['no_of_subjects'],3));
		$LeftOvers = $pdf->addTextWrap($XPos2+35,$YPos+9,300,$FontSize,$grade);	
		$LeftOvers = $pdf->addTextWrap($XPos2+65,$YPos+9,300,$FontSize,$rank);
		$grand_total=$grand_total+$st['mean'];	
		if ($YPos < ($Bottom_Margin + (2* $line_height)))
	{ 
		$PageNumber++;
		NewPageHeader ();
	}				
	}
	$XPos3=162;
foreach ($subjects_array as $r => $s) 
{	
	$count=0;
	$total_marks=0;
	$total_marks2=0;
	$count=studentsRegisteredForSubject($s['id'],$_POST['period_id'],$_POST['class_id'],$db);
		if($s['id']==4){
		$subject_mean=number_format($totalEnglishPoints/$count,3);
		}
		else if($s['id']==6){
		$subject_mean=number_format($totalKiswahiliPoints/$count,3);
		}
		else if($s['id']==5){
		$subject_mean=number_format($totalMathPoints/$count,3);
		}
		else if($s['id']==14){
		$subject_mean=number_format($totalGeographyPoints/$count,3);
		}
		else if($s['id']==8){
		$subject_mean=number_format($totalHistoryPoints/$count,3);
		}
		else if($s['id']==9){
		$subject_mean=number_format($totalPhysicsPoints/$count,3);
		}
		else if($s['id']==10){
		$subject_mean=number_format($totalBusinessStudies/$count,3);
		}
		else if($s['id']==11){
		$subject_mean=number_format($totalChemistryPoints/$count,3);
		}
		else if($s['id']==12){
		$subject_mean=number_format($totalBiologyPoints/$count,3);
		}
		else if($s['id']==13){
		$subject_mean=number_format($totalCREpoints/$count,3);
		}
		else if($s['id']==15){
		$subject_mean=number_format($totalAgriculturePoints/$count,3);
		}
	$checkMean=	number_format($subject_mean,0);	
	$sql = "SELECT grade FROM reportcardgrades
	WHERE title =  '". $checkMean ."'
	AND grading LIKE 'other'";
	$result=DB_query($sql,$db);
	$myrow=DB_fetch_row($result);
	$classSubjectGrade=$myrow[0];
	$LeftOvers = $pdf->addTextWrap($XPos3-2,$YPos+1,300,9,$subject_mean);
	$LeftOvers = $pdf->addTextWrap($XPos3,$YPos-10,300,9,$classSubjectGrade);
	$XPos3 +=(0.62*$line_width);
}//end of ssubjects array foreach
if($no_of_students>0){	
$mean_class=$grand_total/$no_of_students;	
}

$LeftOvers = $pdf->addTextWrap(21,$YPos+1,300,$FontSize,_('Subject Mean Score'));
//$LeftOvers = $pdf->addTextWrap($XPos3,$YPos+1,300,$FontSize,$mean_class);
$pdf->line(19, $YPos,$Page_Width-$Right_Margin, $YPos,$style);	
$LeftOvers = $pdf->addTextWrap(21,$YPos-10,300,$FontSize,_('Subject Mean Grade'));
//$LeftOvers = $pdf->addTextWrap($XPos3,$YPos-10,300,$FontSize,number_format($classSubjectGrade,1));	
$pdf->line(19, $YPos-11,$Page_Width-$Right_Margin, $YPos-11,$style);
$pdf->Output('Report-', 'I');
}

else { /*The option to print PDF was not hit */
	$title = _('Manage Students');
	include('includes/header.inc');
	echo '<p class="page_title_text">' . ' ' . _('Stream Single Exam Marksheet') . '';
	echo '<FORM METHOD="POST" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '">';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<CENTER><TABLE class="enclosed"><TR><TD>' . _('Class:') . '</TD><TD><SELECT Name="class_id">';
	DB_data_seek($result, 0);
	$result = DB_query('SELECT * FROM classes ORDER BY class_name',$db);
	while ($myrow = DB_fetch_array($result)) 
	{
		if ($myrow['id']==$_POST['class_id']) 
		{
			echo '<option selected VALUE=';
		} 
		else
		 {
			echo '<option VALUE=';
		}
		echo $myrow['id'] . '>' . $myrow['class_name'];
	} //end while loop
	echo '</SELECT></TD></TR>';
	echo '<CENTER><TR><TD>' . _('Period:') . '</TD><TD><SELECT Name="period_id">';
		DB_data_seek($result, 0);
		$sql="SELECT cp.id,terms.title,years.year FROM collegeperiods cp
		INNER JOIN terms ON terms.id=cp.term_id
		INNER JOIN years ON years.id=cp.year ";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result))
		 {
			if ($myrow['id'] == $_POST['id'])
			 {  
				echo '<OPTION SELECTED VALUE=';
			}
			 else 
			 {
				echo '<OPTION VALUE=';
			}
			echo $myrow['id'] . '>'.' '.$myrow['title'].' '.$myrow['year'];
		} //end while loop
	echo '</SELECT></TD></TR>';
echo '<CENTER><TR><TD class="visible">' . _('Exam Mode:') . '</TD><TD class="visible"><SELECT Name="exam_mode">';
		DB_data_seek($result, 0);
		$sql="SELECT * FROM markingperiods ";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) 
		{
			if ($myrow['id'] == $_POST['id'])
			 {  
				echo '<OPTION SELECTED VALUE=';
			} 
			else 
			{
				echo '<OPTION VALUE=';
			}
			echo $myrow['id'] . '>'.' '.$myrow['title'];
		} //end while loop
	echo '</SELECT></TD></TR>';		
	echo "</TABLE>";
	echo "<P><CENTER><INPUT TYPE='Submit' NAME='PrintPDF' VALUE='" . _('View') . "'>";
	include('includes/footer.inc');	
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
	$pdf->line(19, $YPos,$Page_Width-$Right_Margin, $YPos);	
		$pdf->newPage();
		
	}
$YPos= $Page_Height-$Top_Margin;


	


}
?>