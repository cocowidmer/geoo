<?php
require_once(dirname(__FILE__) . '/../../config.php');

global $PAGE, $CFG, $OUTPUT, $DB;

require_login();
// Variables enviadas por URL desde el bloque, esto depende del boton donde se hizo blick
// courseid es course module id, es decir, el curso donde el usuario estaba
$courseid = required_param ( 'id', PARAM_INT ); //brings the right course id
// action es que boton apreto el usuario en el bloque, este puede ser action = {assign, quiz, resource}
// por defecto es "empty", es decir que si no se llega desde el bloque el plugin no despliega información
//$action = optional_param('action','empty',PARAM_TEXT);
// Construcción de la pagina en formato moodle
$url = new moodle_url('/local/geoo/index.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$title = "Ranking";
$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();


$context = get_context_instance(CONTEXT_COURSE,$courseid);
$esProfesor = false;
if ($roles = get_user_roles($context, $USER->id)) //created an if, to separate information between teacher and student USEFUL INFO(teacher->can see all grades, student->only see his/her grade(s))
 {foreach ($roles as $role) {
		if($role->shortname == 'editingteacher'){
			$esProfesor = true;}}}
			
$buttonback = new moodle_url('../../course/view.php', array('id'=>$courseid)); //to get back to the course

			
			

$ranking = required_param ( 'ranking', PARAM_INT );

if ($ranking == 1) // ASSIGNMENT->TAREAS, SHOWS AVERAGE OF ASSIGNMENT OF THE COURSE OF EVERY STUDENT
{
		
$items = $DB->get_records_sql ( "SELECT firstname, lastname, ROUND(AVG(ag.grade),1) as average, u.id as ui, fullname
			FROM mdl_assign_grades as ag INNER JOIN
			mdl_assign as a ON (a.id = ag.assignment)
			INNER JOIN mdl_user as u ON (ag.userid = u.id)
            INNER JOIN mdl_course as c ON (a.course= c.id)
			WHERE a.course = $courseid
			GROUP BY firstname, lastname
			ORDER BY average  DESC" );
// new table that shows position and grades by student

$table = new html_table ();
$table->head = array (
		'Position',
		'Average of Course',
		'Firstname',
		'Lastname'
);

$position = 0;
foreach ( $items as $item ) {
	$position++;
	if($esProfesor){
		$ui= $item->ui;
    	$firstname = $item->firstname;
		$lastname = $item->lastname;
		$average = $item->average;
		$fullname= $item->fullname;
		$table->data[] = array($position, $average, $firstname, $lastname);
	}else{
		if($USER->id == $item->ui){
			$firstname = $item->firstname;
			$lastname = $item->lastname;
			$average = $item->average;
			$fullname= $item->fullname;
			$table->data[] = array($position, $average, $firstname, $lastname);
		}
	}
}

	 if (!empty($average)) { //if there are no grades
	echo "Estas viendo el promedio de tareas del curso-> $fullname";
	echo html_writer::table($table);
	echo $OUTPUT->single_button($buttonback, 'Return');

    }
    
    else {
    
    	echo "There are no grades yet";
    	echo $OUTPUT->single_button($buttonback, 'Return');
    		}

} 





else if ($ranking == 2) // GRADES OF THE COURSE->NOTAS DEL CURSO, SHOWS AVERAGE OF THE COURSE OF EVERY STUDENT
{
$items = $DB->get_records_sql ("SELECT
			firstname, lastname, ROUND(finalgrade,1) as finalgrade, u.id as ui, fullname
			FROM
			mdl_grade_grades as gg INNER JOIN
			(mdl_grade_items as gi JOIN mdl_course as c JOIN mdl_user as u)
			ON (gg.itemid = gi.id AND gi.courseid= $courseid AND gg.userid = u.id AND gi.courseid = c.id)
			GROUP BY firstname, lastname
			ORDER BY finalgrade DESC");
	
	
	// new table that shows position and grades by student
	
	$table = new html_table ();
	$table->head = array (
		'Position',
		'Average of Course',
		'Firstname',
		'Lastname'
	);
	$position = 0;
	foreach ( $items as $item ) {
		$position++;
		if($esProfesor){
			$ui= $item->ui;
			$firstname = $item->firstname;
			$lastname = $item->lastname;
			$finalgrade = $item->finalgrade;
			$fullname= $item->fullname;
			$table->data[] = array($position, $finalgrade, $firstname, $lastname);
		}else{
			if($USER->id == $item->ui){
				$firstname = $item->firstname;
				$lastname = $item->lastname;
				$finalgrade = $item->finalgrade;
				$fullname= $item->fullname;
				$table->data[] = array($position, $finalgrade, $firstname, $lastname);
			}
		}
	}
	
	if (!empty($finalgrade)) { //if there are no grades
		echo "Estas viendo el promedio de notas del curso-> $fullname";
		echo html_writer::table($table);
		echo $OUTPUT->single_button($buttonback, 'Return');
	}
	
	else {
	
		echo "There are no grades yet";
		echo $OUTPUT->single_button($buttonback, 'Return');}
	
	}
	
	

	

else if ($ranking == 3) // ACTIVITIES -> ACTIVIDADES, SHOWS THE TOTAL OF FORUMS WRITEN AND FILES DOWNLOADED OF EVERY STUDENT OF THE COURSE
{
	
	$items = $DB->get_records_sql ("SELECT firstname, lastname, ROUND(COUNT(lsl.objectid)/5,0) as sumaarchivos,
		IFNULL (T.sumaforos,0) as sumaforos, 
		(ROUND(COUNT(lsl.objectid)/5,0) + IFNULL (T.sumaforos,0)) as suma, u.id as ui, fullname
		FROM mdl_logstore_standard_log as lsl 
		INNER JOIN mdl_user as u ON (lsl.userid = u.id) 
		INNER JOIN (mdl_modules as m JOIN mdl_course_modules as cm JOIN mdl_resource as r JOIN mdl_course as c) 
		ON (cm.module = m.id AND r.course = cm.course AND r.id=lsl.objectid AND r.course=c.id) 
		LEFT JOIN
		(SELECT COUNT(fd.id) as sumaforos, u2.id 
			FROM mdl_forum_discussions as fd 
			INNER JOIN mdl_user as u2
		ON (fd.userid = u2.id and fd.course = $courseid)
		GROUP BY u2.id) as T ON (T.id = u.id) 
			WHERE lsl.action='viewed'
		AND lsl.courseid= $courseid AND lsl.objecttable='resource' GROUP BY firstname, lastname ORDER BY suma DESC");
	
	// new table that shows position and grades by student
	
	$table = new html_table ();
	$table->head = array (
			'Position',
			'Total',
			'Downloaded Files',
			'Forums Done',
			'Firstname',
			'Lastname'
			
	);
	
		$position = 0;
	foreach ( $items as $item ) {
		$position++;
		if($esProfesor){
		$ui= $item->ui;
		$firstname = $item->firstname;
		$lastname = $item->lastname;
		$sumaarchivos= $item->sumaarchivos;
		$sumaforos= $item->sumaforos;
		$suma= $item->suma;
		$fullname= $item->fullname;
	    $table->data[] = array($position, $suma, $sumaarchivos, $sumaforos, $firstname, $lastname);
		} else{
			if($USER->id == $item->ui){
			$firstname = $item->firstname;
			$lastname = $item->lastname;
			$sumaarchivos= $item->sumaarchivos;
			$sumaforos= $item->sumaforos;
			$suma= $item->suma;
			$fullname= $item->fullname;
	   		$table->data[] = array($position, $suma, $sumaarchivos, $sumaforos, $firstname, $lastname);
			}
		}
	}
	
	
	
	
	
    if (!empty($sumaarchivos && $sumaforos)) { //if there are no grades
    echo "Estas viendo el total de foros escritos y archivos descargados del curso-> $fullname";
	echo html_writer::table($table);
	echo $OUTPUT->single_button($buttonback, 'Return');
    }
    
    else {
    	echo "There are no grades yet";
    	echo $OUTPUT->single_button($buttonback, 'Return');
    	}
	}
	
	


echo $OUTPUT->footer();
?>