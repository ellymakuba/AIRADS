<?php
$PageSecurity = 6;
if(isset($_POST['period_id']) && isset($_POST['class_id']) && isset($_POST['subject_id'])){
include('includes/session.inc');
include('includes/PDFStarter.php');
require('grades/LecturerSubjectClass.php');
$FontSize=13;
$pdf->addinfo('Title', _('Sales Receipt') );

$_SESSION['class'] = $_POST['class_id'];
$_SESSION['period'] = $_POST['period_id'];
$_SESSION['subject'] = $_POST['subject_id'];			
$PageNumber=1;
$line_height=12;
if ($PageNumber>1){
	$pdf->newPage();
}
$FontSize=13;
$YPos= $Page_Height-$Top_Margin;
$XPos=0;
$pdf->addJpegFromFile($_SESSION['LogoFile'] ,$XPos+150,$YPos-120,0,80);

$DebtorNo=$_POST['debtorno'];

$FontSize=24;
$LeftOvers = $pdf->addTextWrap(150,$YPos-($line_height),300,$FontSize, _('AFRICAN INSTITUTE '));
$FontSize=10;
$LeftOvers = $pdf->addTextWrap(200,$YPos-($line_height*2.5),300,$FontSize, _('Of Research and Development Studies '));

$FontSize=8;
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*5),300,$FontSize,_('Institute Plaza'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*6),300,$FontSize,_('Next to kenya Power'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*7),300,$FontSize,_('emergency office'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*8),300,$FontSize,$_SESSION['CompanyRecord']['regoffice3']);
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*9),300,$FontSize,$_SESSION['CompanyRecord']['regoffice4']);
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*10),300,$FontSize,$_SESSION['CompanyRecord']['regoffice6']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos-($line_height*5),140,$FontSize, _('Tel').': ' . $_SESSION['CompanyRecord']['regoffice2']);

$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos-($line_height*6),300,$FontSize, _('website').': ' . $_SESSION['CompanyRecord']['regoffice1']);


$sql = "SELECT usr.realname,sub.subject_name FROM www_users usr
INNER JOIN subjects sub ON sub.lecturer_id=usr.userid
WHERE sub.id =  '". $_SESSION['subject'] ."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_array($result);
$lecturer=$myrow['realname'];
$subject=$myrow['subject_name'];

/*$LeftOvers = $pdf->addTextWrap(100,$YPos-($line_height*11),500,$FontSize, _('Reportcard For').': ' . $myrow[0].'    '._('Period').': ' .$myrow2[1].'-'.$myrow2[2]);*/	
$LeftOvers = $pdf->addTextWrap(200,$YPos-($line_height*12),400,$FontSize,_('LECTURER SUBJECT PERFORMANCE'));
 $LeftOvers = $pdf->addTextWrap(200,$YPos-($line_height*12.3),75,$FontSize,'______________________________________________________________________________');

$LeftOvers = $pdf->addTextWrap(120,$YPos-($line_height*15),300,$FontSize, _('Subject').': ' . $subject);
$LeftOvers = $pdf->addTextWrap(300,$YPos-($line_height*15),300,$FontSize, _('Lecturer').': ' . $lecturer);	
$YPos +=20;
$YPos -=$line_height;
//Note, this is ok for multilang as this is the value of a Select, text in option is different

$YPos -=(12*$line_height);

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height);

$YPos -=50;
$YPos -=$line_height;
$Left_Margin2=100;
$pdf->line($Left_Margin2, $YPos+$line_height,500, $YPos+$line_height);

$line_width=40;
$XPos=160;
$YPos2=$YPos;
$count=0;
$i=0;
$bus_report2 = new bus_report2($_POST['class_id'],$_POST['period_id'],$_POST['subject_id'],$db);
$subjects_array = tep_get_exam_mode2($db);
$current_student='';
foreach ($subjects_array as $r => $s) {
$LeftOvers = $pdf->addTextWrap($XPos+70,$YPos,300,$FontSize,$s['title']);
$XPos +=(1.5*$line_width);
		}
$YPos -=10;
$rank =0;
$YPos -=$line_height;
$count=0;
foreach ($bus_report2->scheduled_students as $a => $b) {	
$count=$count+1;	
$scheduled2 = new scheduled2($b['student_id'],$db);
$scheduled2->set_calendar_vars2($b['id'],$db);
$LeftOvers = $pdf->addTextWrap($Left_Margin2+3,$YPos,300,$FontSize,$scheduled2->debtorno);
$pdf->line($Left_Margin2, $YPos+$line_height,500, $YPos+$line_height);
$YPos -=(2*$line_height);
$XPos2=220;
$totalmarks_array =$bus_report2->total_marks($b['student_id'],$_POST['period_id'],$_POST['subject_id'],$db);
foreach ($scheduled2->exam_mode as $y=>$z) {
$i++;

	$LeftOvers = $pdf->addTextWrap($XPos2+20,$YPos+25,300,$FontSize,$z['tmarks']);
	$XPos2 +=(1.5*$line_width);
	$pdf->line($XPos2-10,625,$XPos2-10, $YPos+$line_height);
}
$LeftOvers = $pdf->addTextWrap($XPos2+10,$YPos+25,300,$FontSize,$totalmarks_array);
		}
$subject_mean=$bus_report2->subject_meangrade2($_POST['subject_id'],$_POST['period_id'],$_POST['class_id'],$db);		
$pdf->line($Left_Margin2, 625,$Left_Margin2, $YPos+($line_height*1));	
$pdf->line(220, 625,220, $YPos+($line_height*1));
$pdf->line(450, 625,450, $YPos+($line_height*1));		
$pdf->line($Left_Margin2, $YPos+$line_height,500, $YPos+$line_height);
if($subject_mean > 0){
$mean=$subject_mean/$count;
}
else{
$mean=0;
}
$LeftOvers = $pdf->addTextWrap($Left_Margin2+150,$YPos-10,300,$FontSize,_('Mean')._(': ').number_format($mean,2));			
$LeftOvers = $pdf->addTextWrap($XPos2+10,613,300,$FontSize,_('Total'));
$pdf->line(500, 625,500, $YPos+($line_height*1));
$pdf->Output('Receipt-'.$_GET['ReceiptNumber'], 'I');


}
else { /*The option to print PDF was not hit */

	include('includes/session.inc');
	$title = _('Manage Students2');

include('includes/header.inc');

echo '<FORM METHOD="POST" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<TABLE class=enclosed><TR><TD>' . _('Class:') . '</TD><TD><SELECT Name="class_id">';
		DB_data_seek($result, 0);
		$sql = 'SELECT cl.id,cl.class_name,c.course_name,gl.grade_level FROM classes cl 
		INNER JOIN courses c ON c.id=cl.course_id
		INNER JOIN gradelevels gl ON gl.id=cl.grade_level_id
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
echo '<TABLE class=enclosed><TR><TD>' . _('Period:') . '</TD><TD><SELECT Name="period_id">';
		DB_data_seek($result, 0);
		$sql="SELECT cp.id,terms.title,years.year FROM collegeperiods cp
		INNER JOIN terms ON terms.id=cp.term_id
		INNER JOIN years ON years.id=cp.year ";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
			if ($myrow['id'] == $_POST['id']) {  
				echo '<OPTION SELECTED VALUE=';
			} else {
				echo '<OPTION VALUE=';
			}
			echo $myrow['id'] . '>'.' '.$myrow['title'].' '.$myrow['year'];
		} //end while loop
	echo '</SELECT></TD></TR>';
echo '<tr><td>' . _('Subject') . ":</td>
		<td><select name='subject_id'>";
		echo '<OPTION SELECTED VALUE=0>' . _('Select Subject');
		$sql="SELECT id,subject_name FROM subjects ORDER BY subject_name";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['subject_name'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';	
	echo "</TABLE>";
	echo "<P><CENTER><INPUT TYPE='Submit' NAME='PrintPDF' VALUE='" . _('PrintPDF') . "'>";

	include('includes/footer.inc');;
} /*end of else not PrintPDF */

?>