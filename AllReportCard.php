<?php
$PageSecurity = 6;
if(isset($_POST['term']) && isset($_POST['year']) && isset($_POST['PrintPDF'])){
include('includes/session.inc');
include('includes/PDFStarter.php');
require('grades/ReportCardClass.php');
$Right_Margin= $Right_Margin+20;	
$FontSize=13;

$style = array('width' => 0.70, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'phase' => 10, 'color' => array(12, 12, 12));
$_SESSION['period'] = $_POST['period_id'];

$sqlstream = "SELECT * FROM classes ";
		$resultstream = DB_query($sqlstream,$db);
			
while ($myrowstream= DB_fetch_array($resultstream))
{	

$sqlclass = "SELECT DISTINCT(student_id) FROM registered_students 
 WHERE class_id='" .$myrowstream['id']. "'
 AND year='" .$_POST['year']. "'
 AND term='" .$_POST['term']. "'";
	$resultclass = DB_query($sqlclass,$db);
	if(DB_num_rows($resultclass)>0)
	{
while ($myrowclass=DB_fetch_array($resultclass)) {
$FontSize=13;
$YPos= $Page_Height-$Top_Margin;
$XPos=0;
$pdf->addJpegFromFile($_SESSION['LogoFile'] ,60,$YPos-210,0,150);

$FontSize=8;
$LeftOvers = $pdf->addTextWrap(50,$YPos-($line_height*7),300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$LeftOvers = $pdf->addTextWrap(50,$YPos-($line_height*8),300,$FontSize,$_SESSION['CompanyRecord']['regoffice1']);
$LeftOvers = $pdf->addTextWrap(50,$YPos-($line_height*9),300,$FontSize,$_SESSION['CompanyRecord']['regoffice2']);
$LeftOvers = $pdf->addTextWrap(50,$YPos-($line_height*10),300,$FontSize,$_SESSION['CompanyRecord']['regoffice3']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos-($line_height*7),300,$FontSize,$_SESSION['CompanyRecord']['regoffice4']);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos-($line_height*8),300,$FontSize, _('Email').': ' . _('airads2006@yahoo.com'));
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos-($line_height*9),300,$FontSize, _('website').': ' . _('www.africaninstitutekenya.com'));



$sql = "SELECT dtr.name,dtr.debtorno,dp.department_name,cs.course_name,t.title,cl.class_name
 FROM debtorsmaster dtr
INNER JOIN classes cl ON cl.id=dtr.class_id 
INNER JOIN courses cs ON cs.id=cl.course_id
INNER JOIN departments dp ON dp.id=cs.department_id
INNER JOIN terms t ON t.id=cl.current_term
WHERE debtorno =  '". $myrowclass['student_id'] ."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
		

/*$LeftOvers = $pdf->addTextWrap(100,$YPos-($line_height*11),500,$FontSize, _('Reportcard For').': ' . $myrow[0].'    '._('Period').': ' .$myrow2[1].'-'.$myrow2[2]);*/	

$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*22),300,$FontSize,_('Name').':'.$myrow[0]);
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*23),300,$FontSize,_('Department').':'.$myrow[2]);	
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*24),300,$FontSize,_('Class').':'.$myrow[5]);
$LeftOvers = $pdf->addTextWrap(40,$YPos-($line_height*25),300,$FontSize,_('Date Of Issue').':'. Date($_SESSION['DefaultDateFormat']) );	
	
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos-($line_height*22),300,$FontSize, _('Admn No').': ' . $myrow[1]);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos-($line_height*23),300,6, _('Course').': ' . $myrow[3]);
$LeftOvers = $pdf->addTextWrap($Page_Width-$Right_Margin-150,$YPos-($line_height*24),300,$FontSize, _('Term').': ' . $myrow[4]);
	
$FontSize=20;	
$YPos -=230;
$LeftOvers = $pdf->addTextWrap(180,$YPos,300,$FontSize,$_SESSION['CompanyRecord']['coyname']);
$YPos -=(0.5*$line_height);
$pdf->line($Left_Margin, $YPos,$Page_Width-$Right_Margin, $YPos,$style);
$YPos -=(1*$line_height);
$FontSize=9;	
$LeftOvers = $pdf->addTextWrap(200,$YPos,400,$FontSize,_('STUDENT ACADEMIC PROGRESS REPORT'));
$YPos -=(0.5*$line_height);
 $LeftOvers = $pdf->addTextWrap(200,$YPos,100,$FontSize,'______________________________________________________________________________');

	
$FontSize=8;
$YPos -=55;
$XPos=170;
$line_width=50;
$pdf->line($Left_Margin, $YPos,$Page_Width-$Right_Margin, $YPos,$style);
$YPos -=(1*$line_height);
$YPos2=$YPos+$line_height;
$count=0;
$i=0;
$bus_report = new bus_report($myrowclass['student_id'],$_POST['term'],$_POST['year'],$db);
$status_array = tep_get_status($db);
$YPos =430;
foreach ($status_array as $r => $s) {
	$LeftOvers = $pdf->addTextWrap(45,$YPos+20,300,$FontSize,_('NO'));		
	$LeftOvers = $pdf->addTextWrap(70,$YPos+20,300,$FontSize,_('COURSE DESCRIPTION'));
$pdf->starttransform();
$pdf->xy($XPos,332);
$pdf->rotate(90);	
	$LeftOvers = $pdf->addTextWrap($XPos-65,$YPos-15,300,$FontSize,$s['title']);
$pdf->stoptransform();	
	$XPos +=(0.5*$line_width);
		}
		
foreach ($bus_report->scheduled_subjects as $a => $b) {

	$count=$count+1;
	$scheduled = new scheduled($b['subject_id'],$db);
	$scheduled->set_calendar_vars($b['id'],$db);
	$LeftOvers = $pdf->addTextWrap(45,$YPos,300,$FontSize,$count);
	$FontSize=6;
	$LeftOvers = $pdf->addTextWrap(70,$YPos,300,$FontSize,$scheduled->subject_name);
	$FontSize=8;
	$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height,$style);
	$status_array = tep_get_status($db);
	$XPos2=240;
	$YPos -=(1.2*$line_height);

foreach ($scheduled->status as $y=>$z) {
$i++;
	$LeftOvers = $pdf->addTextWrap($XPos2+20,$YPos+15,300,$FontSize,$z['marks']);
	$pdf->line($XPos2+10,$YPos2,$XPos2+10, $YPos+($line_height*1),$style);
	$XPos2 +=(0.5*$line_width);
	
				}
	$totalmarks_array =$bus_report->total_marks($myrowclass['student_id'],$b['id'],$b['subject_id'],$_POST['term'],$_POST['year'],$db);
$sql = "SELECT cs.course_code FROM courses cs
INNER JOIN debtorsmaster dm ON dm.course_id=cs.id
WHERE dm.debtorno=  '". $myrowclass['student_id'] ."'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$course_code=$myrow[0];

if($course_code==2429){
$sql = "SELECT title,comment FROM reportcardgrades
		WHERE range_from <=  '". $totalmarks_array ."'
		AND range_to >='". $totalmarks_array ."'
		AND grading LIKE 'pharmacy'";
        $result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
}
else{
$sql = "SELECT title,comment FROM reportcardgrades
		WHERE range_from <=  '". $totalmarks_array ."'
		AND range_to >='". $totalmarks_array ."'
		AND grading LIKE 'default'";
        $result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
}		
	$LeftOvers = $pdf->addTextWrap($XPos2+30,$YPos+15,300,$FontSize,$totalmarks_array);					
	$LeftOvers = $pdf->addTextWrap($XPos2+70,$YPos+15,300,$FontSize,$myrow[0]);
	$LeftOvers = $pdf->addTextWrap($XPos2+120,$YPos+15,300,$FontSize,$myrow[1]);			
	$totalmarks_array2=$totalmarks_array2+$totalmarks_array;					
			}
					
$LeftOvers = $pdf->addTextWrap($XPos2+25,$YPos2-55,300,$FontSize,_('Total(%)'));
$pdf->line($XPos2+20,$YPos2,$XPos2+20, $YPos+($line_height*1),$style);
$LeftOvers = $pdf->addTextWrap($XPos2+60,$YPos2-55,300,$FontSize,_('Grade'));
$pdf->line($XPos2+55,$YPos2,$XPos2+55, $YPos+($line_height*1),$style);
$LeftOvers = $pdf->addTextWrap($XPos2+110,$YPos2-55,300,$FontSize,_('Comment'));
$pdf->line($XPos2+100,$YPos2,$XPos2+100, $YPos+($line_height*1),$style);
$pdf->line(40, $YPos2,40, $YPos+($line_height*1),$style);
$pdf->line(60,$YPos2,60, $YPos+($line_height*1),$style);
$pdf->line(546,$YPos2,546, $YPos+($line_height*1),$style);

$pdf->line($Left_Margin, $YPos+$line_height,$Page_Width-$Right_Margin, $YPos+$line_height,$style);
$LeftOvers = $pdf->addTextWrap(40,$YPos-10,300,$FontSize,_('Total Subjects').' :'.$count);
$LeftOvers = $pdf->addTextWrap(250,$YPos-10,300,$FontSize,_('Total Marks').' :'.$totalmarks_array2);
$out_of=100*$count;
$LeftOvers = $pdf->addTextWrap(410,$YPos-10,300,$FontSize,_('Out of').' :'.$out_of);

if($course_code==2429){
$mean_grade=$totalmarks_array2/$count;
$sql = "SELECT title,comment FROM reportcardgrades
		WHERE range_from <=  '". $mean_grade ."'
		AND range_to >='". $mean_grade."'
		AND grading LIKE 'pharmacy'";
		$result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
}
else{
if($count>0){
$mean_grade=$totalmarks_array2/$count;
}
$sql = "SELECT title,comment FROM reportcardgrades
		WHERE range_from <=  '". $mean_grade ."'
		AND range_to >='". $mean_grade."'
		AND grading LIKE 'default'";
		$result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
}	
if($course_code==2429){
$LeftOvers = $pdf->addTextWrap(40,$YPos-20,300,$FontSize,_('Mean Grade').' :'.$myrow[1]);	
$LeftOvers = $pdf->addTextWrap(40,$YPos-40,300,$FontSize,_('KEY TO GRADING SYSTEM'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-50,300,$FontSize,_('100-80  1A Distinction'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-60,300,$FontSize,_('79-75   2A Distiction'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-70,300,$FontSize,_('70-74   3B Credit'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-80,300,$FontSize,_('69-65   3B Credit'));

$LeftOvers = $pdf->addTextWrap(200,$YPos-50,300,$FontSize,_('64-60  4B Credit'));
$LeftOvers = $pdf->addTextWrap(200,$YPos-60,300,$FontSize,_('55-59  5C Pass'));
$LeftOvers = $pdf->addTextWrap(200,$YPos-70,300,$FontSize,_('50-54  5C Pass'));
$LeftOvers = $pdf->addTextWrap(200,$YPos-80,300,$FontSize,_('49-0   Referred'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-100,300,$FontSize,_('* Pass after supplementary'));
}
else{	
$LeftOvers = $pdf->addTextWrap(40,$YPos-20,300,$FontSize,_('Mean Grade').' :'.$myrow[1]);	
$LeftOvers = $pdf->addTextWrap(40,$YPos-40,300,$FontSize,_('KEY TO GRADING SYSTEM'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-50,300,$FontSize,_('100-90  1 Distinction'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-60,300,$FontSize,_('89-80   2 Distiction'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-70,300,$FontSize,_('79-70   3 Credit'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-80,300,$FontSize,_('69-60   4 Credit'));

$LeftOvers = $pdf->addTextWrap(200,$YPos-50,300,$FontSize,_('59-50  5 Pass'));
$LeftOvers = $pdf->addTextWrap(200,$YPos-60,300,$FontSize,_('49-40  6 Pass'));
$LeftOvers = $pdf->addTextWrap(200,$YPos-70,300,$FontSize,_('39-30  7 Reffered'));
$LeftOvers = $pdf->addTextWrap(200,$YPos-80,300,$FontSize,_('29-0   8 Fail'));
$LeftOvers = $pdf->addTextWrap(40,$YPos-100,300,$FontSize,_('* Pass after supplementary'));
}
$LeftOvers = $pdf->addTextWrap(200,$YPos-100,300,$FontSize,_('Registrar'.':'));
$LeftOvers = $pdf->addTextWrap(230,$YPos-100,80,$FontSize,'______________________________________________________________________________');
$LeftOvers = $pdf->addTextWrap(40,$YPos-120,300,$FontSize,_('Principal'));
$LeftOvers = $pdf->addTextWrap(70,$YPos-120,80,$FontSize,'______________________________________________________________________________');
$LeftOvers = $pdf->addTextWrap(40,$YPos-140,300,$FontSize,_('Chief Examination Officer'));
$LeftOvers = $pdf->addTextWrap(125,$YPos-140,80,$FontSize,'______________________________________________________________________________');
$LeftOvers = $pdf->addTextWrap(40,$YPos-160,300,$FontSize,_('This transcript is not valid without the principals rubber stamp'));

$FontSize=9;
$LeftOvers = $pdf->addTextWrap(200,92,300,$FontSize,_('AIRADS "Where quality is nurtured"'));
$pdf->line($Left_Margin, 91,$Page_Width-$Right_Margin, 91,$style);
$LeftOvers = $pdf->addTextWrap(40,80,300,$FontSize,_('TRAINING'));
$LeftOvers = $pdf->addTextWrap(100,85,30,$FontSize,'______________________________________________________________________________');
$LeftOvers = $pdf->addTextWrap(250,80,300,$FontSize,_('RESEARCH'));
$LeftOvers = $pdf->addTextWrap(320,85,30,$FontSize,'______________________________________________________________________________');
$LeftOvers = $pdf->addTextWrap(410,80,300,$FontSize,_('DEVELOPMENT'));
$FontSize=7.5;
$LeftOvers = $pdf->addTextWrap(70,70,600,$FontSize,_('The institute is approved by ministry of Science and Technology Reg no MOST/PC/1049/07, Ministry of education Reg no P/TC/155/2007'));
$LeftOvers = $pdf->addTextWrap(40,60,600,$FontSize,_('Ministry of Health Pharmacy and Poisons Board Reg no PPB/COL/013/07 and kenya national Examination Council with centre number 509113(technical) and 50903(ECDE) '));
	
	$PageNumber++;			
	if ($PageNumber>1){
	$pdf->newPage();
		}	
				}
			}	
			
		}	
$pdf->Output('Receipt-'.$_GET['ReceiptNumber'], 'I');
unset($_SESSION['allowed']);	
}

else { /*The option to print PDF was not hit */

	include('includes/session.inc');
	$title = _('Manage Students');

include('includes/header.inc');


echo '<FORM METHOD="POST" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '">';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
echo '<table class=enclosed>';
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
	$sql = "SELECT sr.secrolename FROM www_users us
	INNER JOIN securityroles sr ON sr.secroleid=us.fullaccess
		WHERE us.userid=  '" . trim($_SESSION['UserID']) . "'";
		$result=DB_query($sql,$db);
		$myrow=DB_fetch_row($result);
	if($myrow[0]==_('System Administrator') || $myrow[0]==_('Academic Officer')){	
	echo "<P><INPUT TYPE='Submit' NAME='PrintPDF' VALUE='" . _('PrintPDF') . "'>";
	$_SESSION['allowed']=1;
	}
	else{
	$_SESSION['allowed']=0;
	}

	include('includes/footer.inc');
} /*end of else not PrintPDF */

?>