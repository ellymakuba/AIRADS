<?php
function tep_get_students($class,$year,$term,$db) {
	$sql = "select DISTINCT(rs.student_id),dm.*,SUM(sm.marks) as totalmarks from registered_students rs
	INNER JOIN debtorsmaster dm ON dm.id=rs.student_id
	INNER JOIN studentsmarks sm ON sm.student_id=rs.student_id
	INNER JOIN classes cl ON cl.id=rs.class_id
	WHERE cl.id='$class'
	AND rs.year='$year'
	AND rs.term='$term'
	GROUP BY sm.student_id
	order by totalmarks DESC";
	$result = DB_query($sql,$db);
	while ($row = DB_fetch_array($result)) {
		$students_array[] = array("id" => $row['id'],
				     "name" => $row['name']);
	}
	return $students_array;
}
function tep_set_students($debtorno,$year,$term,$db) {
	$sql = "select id,student_id from registered_students 
	WHERE student_id='$debtorno'
	AND year='$year'
	AND term='$term'";
	$result = DB_query($sql,$db);
	while ($row = DB_fetch_array($result)) {
		$ids[] = array("id" => $row['id'],
				     "student_id" => $row['student_id']);
	}
	
	return $ids;
}
function students_subjects_class($student,$year,$term,$db) {
	
		$sql = "select COUNT(rs.subject_id) from registered_students rs
		INNER JOIN subjects sub ON sub.id=rs.subject_id
		WHERE rs.student_id='$student'
		AND rs.year='$year'
		AND rs.term='$term'";
	$result = DB_query($sql,$db);
	$row = DB_fetch_row($result);
	$num_of_subjects=$row[0];
	
	return $num_of_subjects;
}
function students_subjects($student,$year,$term,$db) {
	$sql = "select COUNT(subject_id) from registered_students 
	WHERE student_id='$student'
	AND year='$year'
	AND term='$term'";
	$result = DB_query($sql,$db);
	$row = DB_fetch_row($result);
	$num_of_subjects=$row[0];
	
	return $num_of_subjects;
}
function tep_get_subjects($class,$year,$term,$db) {
		$sql = "select sub.* from subjects sub 
		INNER JOIN registered_students rs ON rs.subject_id=sub.id
		INNER JOIN classes cl ON cl.id=rs.class_id
		WHERE cl.id='$class'
		AND rs.year='$year'
		AND rs.term='$term'
		GROUP BY rs.subject_id";
	$result = DB_query($sql,$db);
	while ($row = DB_fetch_array($result)) {
		$subjects_array[] = array("id" => $row['id'],
				     "subject_code" => $row['subject_code'],
					 "subject_name" => $row['subject_name'],
					 "department_id" => $row['department_id']);
	}
	return $subjects_array;
}
function primary_get_subjects_marks_class($subject_id,$student_id,$class,$year,$term,$calendar_id,$db) 
{	
	$sql = "select COUNT(mp.exam_type_id) as no_of_cats from studentsmarks sm
	INNER JOIN markingperiods mp ON mp.id=sm.exam_mode
	INNER JOIN registered_students rs ON rs.id=sm.calendar_id
	WHERE  mp.exam_type_id=1
	AND sm.student_id='$student_id'
	AND rs.subject_id='$subject_id'
	AND sm.year='$year'
	AND sm.term='$term'";
	$result = DB_query($sql,$db);
	$row = DB_fetch_row($result);
	$num_of_cats=$row[0];
	
	$sql = "select SUM(sm.marks) as cat_marks from studentsmarks sm
	INNER JOIN markingperiods mp ON mp.id=sm.exam_mode
	INNER JOIN registered_students rs ON rs.id=sm.calendar_id
	WHERE  mp.exam_type_id=1
	AND sm.student_id='$student_id'
	AND rs.subject_id='$subject_id'
	AND sm.year='$year'
	AND sm.term='$term'";
	$result = DB_query($sql,$db);
	$row = DB_fetch_row($result);
	$cat_marks=$row[0];
	
	if($num_of_cats > 0){
	$average_marks=number_format($cat_marks/$num_of_cats,0);
	}
	else{
	$average_marks='';
	}
	
	$sql = "select SUM(sm.marks) as exam_marks from studentsmarks sm
	INNER JOIN markingperiods mp ON mp.id=sm.exam_mode
	INNER JOIN registered_students rs ON rs.id=sm.calendar_id
	WHERE  mp.exam_type_id !=1
	AND sm.student_id='$student_id'
	AND rs.subject_id='$subject_id'
	AND sm.year='$year'
	AND sm.term='$term'";
	$result = DB_query($sql,$db);
	$row = DB_fetch_row($result);
	$exam_marks=$row[0];
	$real_marks=$exam_marks+$average_marks;
	
	if($real_marks > 0){
	$real_marks=number_format($real_marks,0);
	}
	else{
	$real_marks='';
	}
	return $real_marks;
}
function tep_get_subjects_marks($subject_id,$student_id,$class,$year,$term,$calendar_id,$db) {
	
	$sql = "select COUNT(mp.exam_type_id) as no_of_cats from studentsmarks sm
	INNER JOIN markingperiods mp ON mp.id=sm.exam_mode
	INNER JOIN registered_students rs ON rs.id=sm.calendar_id
	WHERE sm.student_id='$student_id'
	AND rs.subject_id='$subject_id'
	AND rs.year='$year'
	AND rs.term='$term'";
	$result = DB_query($sql,$db);
	$row = DB_fetch_row($result);
	$num_of_exams_mode=$row[0];
	
	$sql = "select SUM(sm.marks) as cat_marks from studentsmarks sm
	INNER JOIN markingperiods mp ON mp.id=sm.exam_mode
	INNER JOIN registered_students rs ON rs.id=sm.calendar_id
	INNER JOIN subjects sub ON sub.id=rs.subject_id
	WHERE  sm.student_id='$student_id'
	AND rs.subject_id='$subject_id'
	AND rs.year='$year'
	AND rs.term='$term'";
	$result = DB_query($sql,$db);
	$row = DB_fetch_row($result);
	$marks=$row[0];
	
	if($num_of_exams_mode > 0){
	$average_marks=$marks/$num_of_exams_mode;
	}
	else{
	$average_marks='';
	}
	
	
	return $average_marks;
	
	
}


class student {

var $debtorno;
var $name;
var $class_id;
var $course_id;
var $grade_level_id;

function student($debtorno,$db) {
	$sql = "select * from debtorsmaster where debtorno = '$debtorno' limit 1";
	$result = DB_query($sql,$db);
	$row = DB_fetch_array($result);
	$this->debtorno = $debtorno;
	$this->name = $row['name'];
	$this->class_id = $row['class_id'];
	$this->course_id = $row['course_id'];
}


function set_calendar_vars($calendar_id,$db) {
	$sql = "select * from registered_students where id='$calendar_id' LIMIT 1";
	$result = DB_query($sql,$db);
	$row = DB_fetch_array($result);
	$this->calendar_id = $calendar_id;
}


}

 class scheduled extends student {
	var $calendar_id;
	var $start_date;
	var $subject;			//array containing the number of users in different status.
	var $total_users;
	var $cancelled;

	function scheduled($debtorno,$db) {
		$this->student($debtorno,$db);
	}

	function set_primary_vars_class($class,$debtorno,$year,$term,$calendar_id,$db) {
		$subjects_array = tep_get_subjects($class,$year,term,$db);
		foreach ($subjects_array as $r=>$s) {
			$this->subject[] = array("id" => $s['id'],
									"department_id" => $s['department_id'],
								   "tmarks" => primary_get_subjects_marks_class($s['id'],$debtorno,$class,$year,$term, $this->calendar_id,$db));
		}
	}

}
class bus_report {
	var $student;			//array of courses that are eligible for report
	var $start_date;
	var $end_date;

	var $scheduled_students;			//courses included in $course that was scheduled within the given time


	function bus_report($class,$year,$term, $db) {
		$this->student = $this->get_student($db);

		$this->scheduled_students = $this->get_scheduled_students($class,$year,$term,$db);
	}

	function get_student($db) {
		$student_array = array();
		// build where clause to exclude courses by previous choices.
		
		$sql = "select debtorno from debtorsmaster ";
		//echo $query;
		$result = DB_query($sql,$db);
		while ($row = DB_fetch_array($result)) {
			$student_array[] = $row['debtorno'];
		}
		return $student_array;
	}


	function get_scheduled_students($class,$year,$term,$db) {
		
		$scheduled_students_array = array();
		$sql = "select SUM(sm.marks) as smarks,rs.id,dm.name, rs.student_id 
		FROM registered_students rs,studentsmarks sm,debtorsmaster dm
		WHERE dm.debtorno=rs.student_id
		AND sm.student_id=rs.student_id
		AND rs.year='$year'
		AND rs.term=sm.term
		AND dm.class_id='$class'
		AND sm.year=rs.year
		GROUP BY rs.student_id
		ORDER BY smarks DESC";
		//echo $query;
		$result = DB_query($sql,$db);
		if (DB_num_rows($result) > 0) {
			while ($row = DB_fetch_array($result)) {
				$scheduled_students_array[] = array('id' => $row['id'],
												'student_id' => $row['student_id'],
												'name' => $row['name']);
			}
			return $scheduled_students_array;
		}
		else
		{
			
			return $scheduled_students_array;
		}
	}
	
function total_marks($student_id,$year,$term,$db) {
		$sql = "select SUM(sm.marks) as smarks from studentsmarks sm
		INNER JOIN markingperiods mp ON mp.id=sm.exam_mode
		WHERE sm.student_id='$student_id'
		AND mp.title NOT LIKE 'Transcript'
		AND sm.year='$year'
		AND sm.term='$term'";
		//echo $query; 
		$result = DB_query($sql,$db);
		$row = DB_fetch_array($result);
		return $row['smarks'];
		}
	
function subject_meangrade($subject_id,$year,$term,$class,$db) {
		$sql = "select SUM(sm.marks) as submarks from studentsmarks sm
		INNER JOIN registered_students rs ON rs.id=sm.calendar_id
		INNER JOIN markingperiods mp ON mp.id=sm.exam_mode
		INNER JOIN classes cl.id=rs.class_id
		INNER JOIN gradelevels gl ON gl.id=rs.yos
		WHERE rs.subject_id='$subject_id'
		AND mp.title NOT LIKE 'Transcript'
		AND rs.year='$year'
		AND rs.term='$term'
		AND gl.id='$class'";
		//echo $query; 
		$result = DB_query($sql,$db);
		$row = DB_fetch_array($result);
		return $row['submarks'];
		}

 }

?>
