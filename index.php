<?php
$PageSecurity = 7;
include('includes/session.inc');
$title = _('Index page');
include('includes/header.inc');
if (isset($StudentLogin) AND $StudentLogin==1){
	echo '<table class="table_index">
			<tr>
			<td class="menu_group_item">
			<p>&bull; <a href="' . $rootpath . '/ViewSubjectsPDFNotes.php">' . _('View Subject Notes') . '</a></p>
			</td>
			</tr>			
		</table>';
	include('includes/footer.inc');
	exit;
}
else{
echo '<table style="background-color:white; color:blue;"><td><h2>'._('Webafriq College Management System').'</h2></td></table>';
}
ini_set('error_reporting', E_ALL);
include('includes/footer.inc');
?>
