<?php
$PageSecurity = 2;
include('includes/session.inc');
$title = _('Student Management');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
echo '<p class="page_title_text">'. ' ' . _('Student Management') . '';
if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();

if (isset($_POST['submit'])) {
	$InputError = 0;
	$i=1;
	$_POST['DebtorNo'] = strtoupper($_POST['DebtorNo']);
	$sql="SELECT COUNT(debtorno) FROM debtorsmaster WHERE debtorno='".$_POST['DebtorNo']."'";
	$result=DB_query($sql,$db);
	$myrow=DB_fetch_row($result);
	if ($myrow[0]>0 and isset($_POST['New'])) {
		$InputError = 1;
		prnMsg( _('The student registration number already exists in the database'),'error');
		$Errors[$i] = 'DebtorNo';
		$i++;
	} elseif (strlen($_POST['CustName']) > 40 OR strlen($_POST['CustName'])==0) {
		$InputError = 1;
		prnMsg( _('The Student Name must be entered and be forty characters or less long'),'error');
		$Errors[$i] = 'CustName';
		$i++;
		
	} elseif ($_SESSION['AutoDebtorNo']==0 AND strlen($_POST['DebtorNo']) ==0) {
		$InputError = 1;
		prnMsg( _('The debtor code cannot be empty'),'error');
		$Errors[$i] = 'DebtorNo';
		$i++;
	}
	elseif ($_POST['student_class']==0) {
		$InputError = 1;
		prnMsg( _('You must assign the student to a class'),'error');
		$Errors[$i] = 'student_class';
		$i++;
	}
	 elseif ($_SESSION['AutoDebtorNo']==0 AND ContainsIllegalCharacters($_POST['DebtorNo'])) {
		$InputError = 1;
		prnMsg( _('The Student RegNo cannot contain any of the following characters') . " . - ' & + \" " . _('or a space'),'error');
		$Errors[$i] = 'DebtorNo';
		$i++;
//	} elseif (ContainsIllegalCharacters($_POST['Address1']) OR ContainsIllegalCharacters($_POST['Address2'])) {
//		$InputError = 1;
//		prnMsg( _('Lines of the address  must not contain illegal characters'),'error');
	} elseif (strlen($_POST['Address1']) >40) {
		$InputError = 1;
		prnMsg( _('The Line 1 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'Address1';
		$i++;
	} elseif (strlen($_POST['Address2']) >40) {
		$InputError = 1;
		prnMsg( _('The Line 2 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'Address2';
		$i++;
	} elseif (strlen($_POST['Address3']) >40) {
		$InputError = 1;
		prnMsg( _('The Line 3 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'Address3';
		$i++;
	} elseif (strlen($_POST['Address4']) >50) {
		$InputError = 1;
		prnMsg( _('The Line 4 of the address must be fifty characters or less long'),'error');
		$Errors[$i] = 'Address4';
		$i++;
	} elseif (strlen($_POST['Address5']) >20) {
		$InputError = 1;
		prnMsg( _('The Line 5 of the address must be twenty characters or less long'),'error');
		$Errors[$i] = 'Address5';
		$i++;
	} elseif (strlen($_POST['Address6']) >15) {
		$InputError = 1;
		prnMsg( _('The Line 6 of the address must be fifteen characters or less long'),'error');
		$Errors[$i] = 'Address6';
		$i++;
	}
	
	elseif (strlen($_POST['Fax']) >25) {
		$InputError = 1;
		prnMsg(_('The fax number must be 25 characters or less long'),'error');
		$Errors[$i] = 'Fax';
		$i++;
	}

	if ($InputError !=1){

		$SQL_ClientSince = FormatDateForSQL($_POST['ClientSince']);

		if (!isset($_POST['New'])) {

			$sql = "SELECT count(id)
					  FROM debtortrans
					where debtorno = '" . $_POST['DebtorNo'] . "'";
			$result = DB_query($sql,$db);
			$myrow = DB_fetch_array($result);

			if ($myrow[0] == 0) {
			
			  $sql = "UPDATE debtorsmaster SET
			  		name='" . $_POST['CustName'] . "',
					age='" . $_POST['age'] . "',
					gender='" . $_POST['gender'] . "',
					grade_level_id='" . $_POST['grade_level'] . "',
					course_id='" . $_POST['course'] . "',
					class_id='" . $_POST['student_class'] . "',
					boxno='" . $_POST['boxno'] . "',
					town='" . $_POST['town'] . "',
					zip='" . $_POST['zip'] . "',
					state='" . $_POST['state'] . "',
					mobileno='" . $_POST['mobileno'] . "',
					relationship='" . $_POST['relationship'] . "',
					gname='" . $_POST['gname'] . "',
					gboxno='" . $_POST['gboxno'] . "',
					gtown='" . $_POST['gtown'] . "',
					gstate='" . $_POST['gstate'] . "',
					gmobileno='" . $_POST['gmobileno'] . "',
					currcode='" . $_POST['CurrCode'] . "',
					email='" . $_POST['Email'] . "',
					status='" . $_POST['Blocked'] . "'
				  WHERE debtorno = '" . $_POST['DebtorNo'] . "'";
			} else {

			  $currsql = "SELECT currcode
					  		FROM debtorsmaster
							where debtorno = '" . $_POST['DebtorNo'] . "'";
			  $currresult = DB_query($currsql,$db);
			  $currrow = DB_fetch_array($currresult);
			  $OldCurrency = $currrow[0];

			  $sql = "UPDATE debtorsmaster SET
			  		name='" . $_POST['CustName'] . "',
					age='" . $_POST['age'] . "',
					gender='" . $_POST['gender'] . "',
					grade_level_id='" . $_POST['grade_level'] . "',
					course_id='" . $_POST['course'] . "',
					class_id='" . $_POST['student_class'] . "',
					boxno='" . $_POST['boxno'] . "',
					town='" . $_POST['town'] . "',
					zip='" . $_POST['zip'] . "',
					state='" . $_POST['state'] . "',
					mobileno='" . $_POST['mobileno'] . "',
					relationship='" . $_POST['relationship'] . "',
					gname='" . $_POST['gname'] . "',
					gboxno='" . $_POST['gboxno'] . "',
					gtown='" . $_POST['gtown'] . "',
					gstate='" . $_POST['gstate'] . "',
					gmobileno='" . $_POST['gmobileno'] . "',
					currcode='" . $_POST['CurrCode'] . "',
					email='" . $_POST['Email'] . "',
					status='" . $_POST['Blocked'] . "'
				  WHERE debtorno = '" . $_POST['DebtorNo'] . "'";

			  if ($OldCurrency != $_POST['CurrCode']) {
			  	prnMsg( _('The currency code cannot be updated as there are already transactions for this student'),'info');
			  }
			}

			$ErrMsg = _('The student could not be updated because');
			$result = DB_query($sql,$db,$ErrMsg);
			prnMsg( _('Student updated'),'success');
			echo '<br>';

		} 
		else { //it is a new customer
			/* set the DebtorNo if $AutoDebtorNo in config.php has been set to
			something greater 0 */
			if ($_SESSION['AutoDebtorNo'] > 0) {
				/* system assigned, sequential, numeric */
				if ($_SESSION['AutoDebtorNo']== 1) {
					$_POST['DebtorNo'] = GetNextTransNo(500, $db);
				}
			}
		

	$sql = "INSERT INTO debtorsmaster (debtorno,name,age,gender,boxno,town,
	zip,state,mobileno,grade_level_id,class_id,course_id,relationship,gname,gboxno,gstate,gmobileno,email)
	VALUES ('" . $_POST['DebtorNo'] ."','" . $_POST['CustName'] ."','" . $_POST['age'] ."',
'" . $_POST['gender'] ."','" . $_POST['boxno'] . "','" . $_POST['town'] . "','" . $_POST['zip'] . "',
'" . $_POST['state'] . "','" . $_POST['mobileno'] . "','" . $_POST['grade_level'] . "','" . $_POST['student_class'] . "','" . $_POST['course'] . "','" . ($_POST['relationship']). "','" . $_POST['gname'] . "','" . ($_POST['gboxno']). "','" . $_POST['gstate'] . "','" . $_POST['gmobileno'] . "','" . $_POST['Email'] . "')";

			$ErrMsg = _('This student could not be added because');
			$result = DB_query($sql,$db,$ErrMsg);
			prnMsg( _('Student Added'),'success');
			
		
	include('includes/footer.inc');
	exit;
		}
	} else {
		prnMsg( _('Validation failed') . '. ' . _('No updates or deletes took place'),'error');
	}

} elseif (isset($_POST['delete'])) {

//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorTrans'

	$sql= "SELECT COUNT(*) FROM debtortrans WHERE debtorno='" . $_POST['DebtorNo'] . "'";
	$result = DB_query($sql,$db);

	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		prnMsg( _('This student cannot be deleted because there are transactions that refer to it'),'warn');
		echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('transactions against this student');

	} else {
		$sql= "SELECT COUNT(*) FROM salesorders WHERE debtorno='" . $_POST['DebtorNo'] . "'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			$CancelDelete = 1;
			prnMsg( _('Cannot delete the student record because orders have been created against it'),'warn');
			echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('invoices against this student');
		} else {
			$sql= "SELECT COUNT(*) FROM salesanalysis WHERE cust='" . $_POST['DebtorNo'] . "'";
			$result = DB_query($sql,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]>0) {
				$CancelDelete = 1;
				prnMsg( _('Cannot delete this student record because sales analysis records exist for it'),'warn');
				echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('sales analysis records against this student');
			} else {
				$sql= "SELECT COUNT(*) FROM custbranch WHERE debtorno='" . $_POST['DebtorNo'] . "'";
				$result = DB_query($sql,$db);
				$myrow = DB_fetch_row($result);
				if ($myrow[0]>0) {
					$CancelDelete = 1;
					prnMsg(_('Cannot delete this student because there are branch records set up against it'),'warn');
					echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('branch records relating to this student');
				}
			}
		}

	}
	if ($CancelDelete==0) { //ie not cancelled the delete as a result of above tests
		
		$sql="DELETE FROM debtorsmaster WHERE debtorno='" . $_POST['DebtorNo'] . "'";
		$result = DB_query($sql,$db);
		prnMsg( _('Student') . ' ' . $_POST['DebtorNo'] . ' ' . _('has been deleted - together with all the associated contacts') . ' !','success');
		include('includes/footer.inc');
		unset($_SESSION['CustomerID']);
		exit;
	} //end if Delete Customer
}

if(isset($reset)){
	unset($_POST['CustName']);
	unset($_POST['Address1']);
	unset($_POST['Address2']);
	unset($_POST['Address3']);
	unset($_POST['Address4']);
	unset($_POST['Address5']);
	unset($_POST['Address6']);
	unset($_POST['Phone']);
	unset($_POST['Fax']);
	unset($_POST['Email']);
	unset($_POST['HoldReason']);
	unset($_POST['PaymentTerms']);
	unset($_POST['Discount']);
	unset($_POST['DiscountCode']);
	unset($_POST['PymtDiscount']);
	unset($_POST['CreditLimit']);
// Leave Sales Type set so as to faciltate fast customer setup
//	unset($_POST['SalesType']);
	unset($_POST['DebtorNo']);
	unset($_POST['InvAddrBranch']);
	unset($_POST['TaxRef']);
	unset($_POST['CustomerPOLine']);
// Leave Type ID set so as to faciltate fast customer setup
//	unset($_POST['typeid']);
}

/*DebtorNo could be set from a post or a get when passed as a parameter to this page */

if (isset($_POST['DebtorNo'])){
	$DebtorNo = $_POST['DebtorNo'];
} elseif (isset($_GET['DebtorNo'])){
	$DebtorNo = $_GET['DebtorNo'];
}
if (isset($_POST['ID'])){
	$ID = $_POST['ID'];
} elseif (isset($_GET['ID'])){
	$ID = $_GET['ID'];
} else {
	$ID='';
}
if (isset($_POST['ws'])){
	$ws = $_POST['ws'];
} elseif (isset($_GET['ws'])){
	$ws = $_GET['ws'];
}
if (isset($_POST['Edit'])){
	$Edit = $_POST['Edit'];
} elseif (isset($_GET['Edit'])){
	$Edit = $_GET['Edit'];
} else {
	$Edit='';
}

if (isset($_POST['Add'])){
	$Add = $_POST['Add'];
} elseif (isset($_GET['Add'])){
	$Add = $_GET['Add'];
}


if (!isset($DebtorNo)) {
	if ($SetupErrors>0) {
		echo '<br /><div class=centre><a href="'.$_SERVER['PHP_SELF'] .'" >'._('Click here to continue').'</a></div>';
		include('includes/footer.inc');
		exit;
	}

	echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

	echo "<input type='Hidden' name='New' value='Yes'>";

	$DataError =0;

	echo '<table class=enclosed><tr><td class="visible"><table class=enclosed>';

	echo '<tr><td colspan="2"><h3>General Information</h3></td></tr>';
	if ($_SESSION['AutoDebtorNo']==0)  {
		echo '<tr><td class="visible">' . _('Student RegNo') . ":</td><td class=\"visible\"><input tabindex=1 type='Text' name='DebtorNo' size=30 maxlength=30></td></tr>";
	}

	echo '<tr><td class="visible">' . _('Student Name') . ':</td>
		<td class="visible"><input tabindex=2 type="Text" name="CustName" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('Program') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='course'>";
$sql2="SELECT course_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_course_id=$myrow2['course_id'];
		
if(!isset($DebtorNo) || $current_course_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Program');
		$sql="SELECT id,course_name FROM courses ORDER BY course_name";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['course_name'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';
}
else{	
$sql2="SELECT cs.id,cs.course_name 
FROM debtorsmaster dm
INNER JOIN courses cs ON cs.id=dm.course_id
WHERE dm.debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$selected_course=$myrow2['grade_level'];
$selected_course_id=$myrow2['id'];


$sql3="SELECT id,course_name FROM courses
ORDER BY course_name";
$result3=DB_query($sql3,$db);
while(list($coursid, $selected_course) = DB_fetch_row($result3))
                {
        if ($coursid==$selected_course_id)
         {
          echo '<option selected value="' . $coursid . '">' . $selected_course . '</option>';
          }
		
         else
       {
        echo '<option value="' . $coursid . '">' . $selected_course. '</option>';
    }
   }
DB_data_seek($result3,0);
		echo '</select></td></tr>';
}		
$sql2="SELECT gl.id,gl.grade_level 
FROM debtorsmaster dm
INNER JOIN gradelevels gl ON gl.id=dm.grade_level_id
WHERE dm.debtorno='$Debtorno'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$selected_grade=$myrow2['grade_level'];
$selected_id=$myrow2['id'];

echo '<td class="visible">' . _('Grade Level') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='grade_level'>";
		
$sql2="SELECT grade_level_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_grade_id=$myrow2['grade_level_id'];
		
if(!isset($DebtorNo) || $current_grade_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Grade Level');
		$sql="SELECT id,grade_level FROM gradelevels";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['grade_level'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td>';
}
else{		
		$sql="SELECT id,grade_level FROM gradelevels ";
		$result=DB_query($sql,$db);
		while(list($gradeid, $grade_level) = DB_fetch_row($result)){
		if ($gradeid==$selected_id)
         {
          echo '<option selected value="' . $gradeid . '">' . $grade_level . '</option>';
          }
         else
       {
	   
        echo '<option value="' . $gradeid . '">' . $grade_level . '</option>';
    }
   }
DB_data_seek($result,0);
		echo '</select></td></tr>';
	}	
echo '<tr><td class="visible">' . _('Class') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='student_class'>";
		
$sql2="SELECT class_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_class_id=$myrow2['class_id'];
		
if(!isset($DebtorNo) || $current_class_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Class');
		$sql="SELECT id,class_name FROM classes 
		WHERE status=0
		ORDER BY class_name";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['class_name'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td>';
}
else{
		
$sql2="SELECT cl.id,cl.class_name 
FROM debtorsmaster dm
INNER JOIN classes cl ON cl.id=dm.class_id
WHERE dm.debtorno='$DebtorNo'
ORDER BY class_name";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$selected_class=$myrow2['grade_level'];
$selected_class_id=$myrow2['id'];

$sql3="SELECT id,class_name FROM classes ORDER BY class_name";
$result3=DB_query($sql3,$db);
while(list($classid, $selected_class) = DB_fetch_row($result3))
                {
        if ($classid==$selected_class_id)
         {
          echo '<option selected value="' . $classid . '">' . $selected_class . '</option>';
          }
         else
       {
        echo '<option value="' . $classid . '">' . $selected_class. '</option>';
    }
   }
DB_data_seek($result3,0);
		echo '</select></td>';
	}
	
	'</SELECT></TD></TR>';
echo '<TR><td class="visible">' . _('Status') . ":</TD><td class=\"visible\"><SELECT name='Blocked'>";
if ($_POST['Blocked']==0){
	echo '<OPTION SELECTED VALUE=0>' . _('Open');
	echo '<OPTION VALUE=1>' . _('Blocked');
} else {
 	echo '<OPTION SELECTED VALUE=1>' . _('Blocked');
	echo '<OPTION VALUE=0>' . _('Open');
}
		$result=DB_query('SELECT loccode, locationname FROM locations',$db);
$result=DB_query('SELECT currency, currabrev FROM currencies',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<tr><td colspan=2>' . prnMsg(_('There are no currencies currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</td></tr>';
	} else {
		if (!isset($_POST['CurrCode'])){
			$CurrResult = DB_query('SELECT currencydefault FROM companies WHERE coycode=1',$db);
			$myrow = DB_fetch_row($CurrResult);
			$_POST['CurrCode'] = $myrow[0];
		}
		echo '<tr><td class="visible">' . _('Student Currency') . ':</td><td class="visible"><select tabindex=17 name="CurrCode">';
		while ($myrow = DB_fetch_array($result)) {
			if ($_POST['CurrCode']==$myrow['currabrev']){
				echo '<option selected value='. $myrow['currabrev'] . '>' . $myrow['currency'] . '</option>';
			} else {
				echo '<option value='. $myrow['currabrev'] . '>' . $myrow['currency'] . '</option>';
			}
		} //end while loop
		DB_data_seek($result,0);

		echo '</select></td></tr>';
		
	}
  echo '</table></td><td ><table class=enclosed>';
echo '<tr><td colspan="2"><h3>Student Contact</h3></td></tr>';
	
echo '<tr><td class="visible">' . _('P.O BOX') . ':</td>
		<td class="visible"><input  type="Text" name="boxno" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('Town') . ':</td>
		<td class="visible"><input  type="Text" name="town" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('Postal Code') . ':</td>
		<td class="visible"><input tabindex=4 type="Text" name="zip" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('State') . ':</td>
		<td class="visible"><input tabindex=5 type="Text" name="state" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('Mobile No') . ':</td>
		<td class="visible"><input tabindex=6 type="Text" name="mobileno" size=30 maxlength=40></td></tr>';
echo '<tr><td class="visible">' . _('Email Address') . ':</td>
		<td class="visible"><input tabindex=2 type="Text" name="Email" size=30 maxlength=40></td></tr>';
		
 echo '</table></td><td><table class=enclosed>';
 echo '<tr><td colspan="2"><h3>Guardian Contact</h3></td></tr>';

 echo '<tr><td class="visible">' . _('Relationship To Student') . ':</td>
		<td class="visible"><input tabindex=2 type="Text" name="relationship" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('Full Name') . ':</td>
		<td class="visible"><input tabindex=3 type="Text" name="gname" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('Mobile No') . ':</td>
		<td class="visible"><input tabindex=5 type="Text" name="gmobileno" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('P.O BOX') . ':</td>
		<td class="visible"><input tabindex=4 type="Text" name="gboxno" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('Town') . ':</td>
		<td class="visible"><input tabindex=6 type="Text" name="gtown" size=30 maxlength=40></td></tr>';
	echo '<tr><td class="visible">' . _('Postal Code') . ':</td>
		<td class="visible"><input tabindex=6 type="Text" name="gstate" size=30 maxlength=40></td></tr>';
	echo'</table></td></tr></table>';
	if ($DataError ==0){
		echo "<br><div class='centre'><input tabindex=20 type='Submit' name='submit' value='" . _('Add New Student') . "'>&nbsp;<input tabindex=21 type=submit action=RESET VALUE='" . _('Reset') . "'></div>";
	}
	echo '</form>';

} else {

//DebtorNo exists - either passed when calling the form or from the form itself

	echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . '?' . SID ."'>";
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class=enclosed><tr><td valign=top><table class=enclosed>';

	if (!isset($_POST['New'])) {
		$sql = "SELECT debtorsmaster.debtorno,
				name,
				age,
				gender,
				course_id,
				grade_level_id,
				class_id,
				status,
				currcode,
				boxno,
				town,
				zip,
				state,
				mobileno,
				relationship,
				gname,
				gboxno,
				gtown,
				gstate,
				gmobileno,
				email
				FROM debtorsmaster
			WHERE debtorsmaster.debtorno = '" . $DebtorNo . "'";

		$ErrMsg = _('The student details could not be retrieved because');
		$result = DB_query($sql,$db,$ErrMsg);

		$myrow = DB_fetch_array($result);
		/* if $AutoDebtorNo in config.php has not been set or if it has been set to a number less than one,
		then display the DebtorNo */
		echo '<tr><td colspan="2"><h3>General Information</h3></td></tr>';
		if ($_SESSION['AutoDebtorNo']== 0 )  {
			echo '<tr><td class="visible">' . _('Student RegNo') . ":</td>
				<td class=\"visible\">" . $DebtorNo. "</td></tr>";
		}
		$_POST['CustName'] = $myrow['name'];
		$_POST['age']  = $myrow['age'];
		$_POST['gender']  = $myrow['gender'];
		$_POST['grade_level']  = $myrow['grade_level_id'];
		$_POST['course']  = $myrow['course_id'];
		$_POST['student_class'] = $myrow['class_id'];
		$_POST['boxno'] = $myrow['boxno'];
		$_POST['town'] = $myrow['town'];
		$_POST['zip'] = $myrow['zip'];
		$_POST['state']  = $myrow['state'];
		$_POST['mobileno']  = $myrow['mobileno'];
		$_POST['relationship']  = $myrow['relationship'];
		$_POST['gname']  = $myrow['gname'];
		$_POST['gboxno']  = $myrow['gboxno'];
		$_POST['gtown']	= $myrow['gtown'];
		$_POST['gstate'] = $myrow['gstate'];
		$_POST['Email'] = $myrow['email'];
		$_POST['gmobileno'] = $myrow['gmobileno'];
		echo '<input type=hidden name="DebtorNo" value="' . $DebtorNo . '">';

	} else {
	// its a new customer being added
		echo '<input type=hidden name="New" value="Yes">';

		/* if $AutoDebtorNo in config.php has not been set or if it has been set to a number less than one,
		then provide an input box for the DebtorNo to manually assigned */
		echo '<tr><td colspan="2"><h3>General Information</h3></td></tr>';
		if ($_SESSION['AutoDebtorNo']== 0 )  {
			echo '<tr><td class="visible">' . _('Student RegNo') . ':</td>
				<td class="visible"><input ' . (in_array('DebtorNo',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="DebtorNo" value="' . $DebtorNo . '" size=20 maxlength=30></td></tr>';
		}
	}
	if (isset($_GET['Modify'])) {
	$sql = "SELECT fullaccess FROM www_users
WHERE userid=  '" . trim($_SESSION['UserID']) . "'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$administrator_rights=$myrow[0];
if($administrator_rights ==8){
		echo '<tr><td class="visible">' . _('Student Name') . ':</td><td class="visible">' . $_POST['CustName'] . '</td></tr>';
}	
else{
echo '<tr><td class="visible">' . _('Student Name') . ':</td>
			<td class="visible"><input ' . (in_array('CustName',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="CustName" value="' . $_POST['CustName'] . '" size=30 maxlength=40 readonly=""></td></tr>';	
	}	
echo '<tr><td class="visible">' . _('Program') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='course'>";
$sql2="SELECT course_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_course_id=$myrow2['course_id'];
		
if(!isset($DebtorNo) || $current_course_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Program');
		$sql="SELECT id,course_name FROM courses ORDER BY course_name";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['course_name'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';
}
else{	
$sql2="SELECT cs.id,cs.course_name 
FROM debtorsmaster dm
INNER JOIN courses cs ON cs.id=dm.course_id
WHERE dm.debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$selected_course=$myrow2['grade_level'];
$selected_course_id=$myrow2['id'];


$sql3="SELECT id,course_name FROM courses
ORDER BY course_name";
$result3=DB_query($sql3,$db);
while(list($coursid, $selected_course) = DB_fetch_row($result3))
                {
        if ($coursid==$selected_course_id)
         {
          echo '<option selected value="' . $coursid . '">' . $selected_course . '</option>';
          }
		
         else
       {
        echo '<option value="' . $coursid . '">' . $selected_course. '</option>';
    }
   }
DB_data_seek($result3,0);
		echo '</select></td></tr>';
}		
$sql2="SELECT gl.id,gl.grade_level 
FROM debtorsmaster dm
INNER JOIN gradelevels gl ON gl.id=dm.grade_level_id
WHERE dm.debtorno='$Debtorno'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$selected_grade=$myrow2['grade_level'];
$selected_id=$myrow2['id'];

echo '<td class="visible">' . _('Grade Level') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='grade_level'>";
		
$sql2="SELECT grade_level_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_grade_id=$myrow2['grade_level_id'];
		
if(!isset($DebtorNo) || $current_grade_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Grade Level');
		$sql="SELECT id,grade_level FROM gradelevels";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['grade_level'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td>';
}
else{		
		$sql="SELECT id,grade_level FROM gradelevels ";
		$result=DB_query($sql,$db);
		while(list($gradeid, $grade_level) = DB_fetch_row($result)){
		if ($gradeid==$selected_id)
         {
          echo '<option selected value="' . $gradeid . '">' . $grade_level . '</option>';
          }
         else
       {
	   
        echo '<option value="' . $gradeid . '">' . $grade_level . '</option>';
    }
   }
DB_data_seek($result,0);
		echo '</select></td></tr>';
	}	
echo '<tr><td class="visible">' . _('Class') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='student_class'>";
		
$sql2="SELECT class_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_class_id=$myrow2['class_id'];
		
if(!isset($DebtorNo) || $current_class_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Class');
		$sql="SELECT id,class_name FROM classes  WHERE status=0
		ORDER BY class_name";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['class_name'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td>';
}
else{
		
$sql2="SELECT cl.id,cl.class_name 
FROM debtorsmaster dm
INNER JOIN classes cl ON cl.id=dm.class_id
WHERE dm.debtorno='$DebtorNo'
ORDER BY class_name";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$selected_class=$myrow2['grade_level'];
$selected_class_id=$myrow2['id'];

$sql3="SELECT id,class_name FROM classes ORDER BY class_name";
$result3=DB_query($sql3,$db);
while(list($classid, $selected_class) = DB_fetch_row($result3))
                {
        if ($classid==$selected_class_id)
         {
          echo '<option selected value="' . $classid . '">' . $selected_class . '</option>';
          }
         else
       {
        echo '<option value="' . $classid . '">' . $selected_class. '</option>';
    }
   }
DB_data_seek($result3,0);
		echo '</select></td>';
	}
		
		echo '</table></td><td class="visible"><table class=enclosed>';
	} else {
	$sql = "SELECT fullaccess FROM www_users
WHERE userid=  '" . trim($_SESSION['UserID']) . "'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$administrator_rights=$myrow[0];
if($administrator_rights ==8){
		echo '<tr><td class="visible">' . _('Student Name') . ':</td>
			<td class="visible"><input ' . (in_array('CustName',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="CustName" value="' . $_POST['CustName'] . '" size=30 maxlength=40></td></tr>';
	}	
	else{
echo '<tr><td class="visible">' . _('Student Name') . ':</td>
			<td class="visible"><input ' . (in_array('CustName',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="CustName" value="' . $_POST['CustName'] . '" size=30 maxlength=40 readonly=""></td></tr>';	
	}
		echo '<tr><td class="visible">' . _('Program') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='course'>";
$sql2="SELECT course_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_course_id=$myrow2['course_id'];
		
if(!isset($DebtorNo) || $current_course_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Program');
		$sql="SELECT id,course_name FROM courses ORDER BY course_name";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['course_name'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';
}
else{	
$sql2="SELECT cs.id,cs.course_name 
FROM debtorsmaster dm
INNER JOIN courses cs ON cs.id=dm.course_id
WHERE dm.debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$selected_course=$myrow2['grade_level'];
$selected_course_id=$myrow2['id'];


$sql3="SELECT id,course_name FROM courses
ORDER BY course_name";
$result3=DB_query($sql3,$db);
while(list($coursid, $selected_course) = DB_fetch_row($result3))
                {
        if ($coursid==$selected_course_id)
         {
          echo '<option selected value="' . $coursid . '">' . $selected_course . '</option>';
          }
		
         else
       {
        echo '<option value="' . $coursid . '">' . $selected_course. '</option>';
    }
   }
DB_data_seek($result3,0);
		echo '</select></td></tr>';
}		

echo '<td class="visible">' . _('Grade Level3') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='grade_level'>";
		
$sql2="SELECT grade_level_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_grade_id=$myrow2['grade_level_id'];
		
if(!isset($DebtorNo) || $current_grade_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Grade Level');
		$sql="SELECT id,grade_level FROM gradelevels";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['grade_level'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td>';
}
else{	
	
		$sql="SELECT id,grade_level FROM gradelevels ";
		$result=DB_query($sql,$db);
		while(list($gradeid, $grade_level2) = DB_fetch_row($result)){
		if ($gradeid==$current_grade_id)
         {
          echo '<option selected value="' . $gradeid . '">' . $grade_level2 . '</option>';
          }
         else
       {
	   
        echo '<option value="' . $gradeid . '">' . $grade_level2 . '</option>';
    }
   }
DB_data_seek($result,0);
		echo '</select></td></tr>';
	}	
echo '<tr><td class="visible">' . _('Class') . ":</td>
		<td colspan=\"2\" class=\"visible\"><select name='student_class'>";
		
$sql2="SELECT class_id FROM debtorsmaster WHERE debtorno='$DebtorNo'";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$current_class_id=$myrow2['class_id'];
		
if(!isset($DebtorNo) || $current_class_id==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Select Class');
		$sql="SELECT id,class_name FROM classes 
		WHERE status=0
		ORDER BY class_name";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
		echo '<option value='. $myrow['id'] . '>' . $myrow['class_name'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td>';
}
else{
		
$sql2="SELECT cl.id,cl.class_name 
FROM debtorsmaster dm
INNER JOIN classes cl ON cl.id=dm.class_id
WHERE dm.debtorno='$DebtorNo'
ORDER BY class_name";
$result2=DB_query($sql2,$db);
$myrow2 = DB_fetch_array($result2);
$selected_class=$myrow2['grade_level'];
$selected_class_id=$myrow2['id'];

$sql3="SELECT id,class_name FROM classes ORDER BY class_name";
$result3=DB_query($sql3,$db);
while(list($classid, $selected_class) = DB_fetch_row($result3))
                {
        if ($classid==$selected_class_id)
         {
          echo '<option selected value="' . $classid . '">' . $selected_class . '</option>';
          }
         else
       {
        echo '<option value="' . $classid . '">' . $selected_class. '</option>';
    }
   }
DB_data_seek($result3,0);
		echo '</select></td>';
	}
		
		echo '</SELECT></TD></TR>';
echo '<TR><td class="visible">' . _('Status') . ":</TD><td class=\"visible\"><SELECT name='Blocked'>";
if ($_POST['Blocked']==0){
	echo '<OPTION SELECTED VALUE=0>' . _('Open');
	echo '<OPTION VALUE=1>' . _('Blocked');
} else {
 	echo '<OPTION SELECTED VALUE=1>' . _('Blocked');
	echo '<OPTION VALUE=0>' . _('Open');
}
			$result=DB_query('SELECT loccode, locationname FROM locations',$db);
			
	if (isset($_GET['Modify'])) {
		$result=DB_query('SELECT currency FROM currencies WHERE currabrev="'.$_POST['CurrCode'].'"',$db);
		$myrow=DB_fetch_array($result);
		echo '<tr><td class="visible">' . _('Credit Status') . ":</td><td class=\"visible\">".$myrow['currency']."</td></tr>";
	} else {
		$result=DB_query('SELECT currency, currabrev FROM currencies',$db);
		echo '<tr><td class="visible">' . _('Student Currency') . ":</td>
			<td class=\"visible\"><select name='CurrCode'>";
		while ($myrow = DB_fetch_array($result)) {
			if ($_POST['CurrCode']==$myrow['currabrev']){
				echo '<option selected value='. $myrow['currabrev'] . '>' . $myrow['currency'];
			} else {
				echo '<option value='. $myrow['currabrev'] . '>' . $myrow['currency'];
			}
		} //end while loop
		DB_data_seek($result,0);
		echo '</select></td></tr>';
	}
		echo '</table></td><td><table class=enclosed>';
		echo '<tr><td colspan="2"><h3>Student Contact</h3></td></tr>';
		echo '<tr><td class="visible">' . _('P.O BOX') . ':</td>
			<td class="visible"><input ' . (in_array('boxno',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="boxno" size=30 maxlength=40 value="' . $_POST['boxno'] . '"></td></tr>';
		echo '<tr><td class="visible">' . _('Town') . ':</td>
			<td class="visible"><input ' . (in_array('town',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address2" size=30 maxlength=40 value="' . $_POST['town'] . '"></td></tr>';
		echo '<tr><td class="visible">' . _('Postal Code') . ':</td>
			<td class="visible"><input ' . (in_array('zip',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="zip" size=20 maxlength=40 value="' . $_POST['zip'] . '"></td></tr>';
		echo '<tr><td class="visible">' . _('State') . ':</td>
			<td class="visible"><input ' . (in_array('state',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="state" size=30 maxlength=40 value="' . $_POST['state'] . '"></td></tr>';
		echo '<tr><td class="visible">' . _('Mobile No') . ':</td>
			<td class="visible"><input ' . (in_array('mobileno',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="mobileno" size=30 maxlength=40 value="' . $_POST['mobileno'] . '"></td></tr>';
echo '<tr><td class="visible">' . _('Email Address') . ':</td>
			<td class="visible"><input ' . (in_array('Email',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Email" value="' . $_POST['Email'] . '" size=42 maxlength=40></td></tr>';			
	echo '</table></td><td><table class=enclosed>';
echo '<tr><td colspan="2"><h3>Guardian Contact</h3></td></tr>';
echo '<tr><td class="visible">' . _('Relationship to Student') . ':</td>
			<td class="visible"><input ' . (in_array('relationship',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="relationship" size=30 maxlength=40 value="' . $_POST['relationship'] . '"></td></tr>';	
	echo '<tr><td class="visible">' . _('Full Name') . ':</td>
			<td class="visible"><input ' . (in_array('gname',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="gname" size=30 maxlength=40 value="' . $_POST['gname'] . '"></td></tr>';
echo '<tr><td class="visible">' . _('Mobile No') . ':</td>
			<td class="visible"><input ' . (in_array('gmobileno',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="gmobileno" size=30 maxlength=40 value="' . $_POST['gmobileno'] . '"></td></tr>';
	echo '<tr><td class="visible">' . _('P.O BOX') . ':</td>
			<td class="visible"><input ' . (in_array('gboxno',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="gboxno" size=30 maxlength=40 value="' . $_POST['gboxno'] . '"></td></tr>';
		echo '<tr><td class="visible">' . _('Town') . ':</td>
			<td class="visible"><input ' . (in_array('gtown',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="gtown" size=30 maxlength=40 value="' . $_POST['gtown'] . '"></td></tr>';
		echo '<tr><td class="visible">' . _('Postal Code') . ':</td>
			<td class="visible"><input ' . (in_array('gstate',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="gstate" size=30 maxlength=40 value="' . $_POST['gstate'] . '"></td></tr>';
				
			
	}

	echo '</select></td></tr></table></td></tr>';
	echo '<tr><td colspan=2>';

		//	echo "<input type='Submit' name='addcontact' VALUE='" . _('ADD Contact') . "'>";
	echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '?'.SID.'&DebtorNo="'.$DebtorNo.'"&ID='.$ID.'&Edit'.$Edit.'>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo'</td></tr></table>';

	if (isset($_POST['New']) and $_POST['New']) {
		echo "<div class='centre'><input type='Submit' name='submit' VALUE='" . _('Add New Student') .
			"'>&nbsp;<input type=submit name='reset' VALUE='" . _('Reset') . "'></div></form>";
	} else if (!isset($_GET['Modify'])){
		echo "<br><div class='centre'><input type='Submit' name='submit' VALUE='" . _('Update Student') . "'>";
		echo '&nbsp;<input type="Submit" name="delete" VALUE="' . _('Delete Student') . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');">';
	}
	if(isset($_POST['addcontact']) AND (isset($_POST['addcontact'])!=''))
	{
		echo '<meta http-equiv="Refresh" content="0; url=' . $rootpath . '/AddCustomerContacts.php?' . SID . '&DebtorNo=' .$DebtorNo.'">';
	}
	echo '</div>';
} // end of main ifs

include('includes/footer.inc');
?>