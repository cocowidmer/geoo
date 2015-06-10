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
if ($roles = get_user_roles($context, $USER->id)) {
	foreach ($roles as $role) {
		if($role->shortname == 'editingteacher'){
			$esProfesor = true;}}}


$ranking = required_param ( 'ranking', PARAM_INT );

if ($ranking == 1) // TAREAS, SHOWS AVERAGE OF ASSIGNMENT OF THE COURSE OF EVERY STUDENT
{
	

	if($esProfesor){ 	
$items = $DB->get_records_sql ( "SELECT firstname, lastname, AVG(ag.grade) as average
			FROM mdl_assign_grades as ag INNER JOIN
			mdl_assign as a ON (a.id = ag.assignment)
			INNER JOIN mdl_user as u ON (ag.userid = u.id)
			WHERE a.course = $courseid
			GROUP BY firstname, lastname
			ORDER BY average  DESC" );
// new table that shows position and grades by student

$table = new html_table ();
$table->head = array (
		'Position',
		'Average',
		'Firstname',
		'Lastname'
);

$position = 0;
foreach ( $items as $item ) {
	$position++;
	$firstname = $item->firstname;
	$lastname = $item->lastname;
	$average = $item->average;
	$table->data[] = array($position, $average, $firstname, $lastname);
}

echo html_writer::table($table);
	}

else
{echo "hola no puedes ver esto!";}


} 





else if ($ranking == 2) // NOTAS, SHOWS AVERAGE OF THE COURSE OF EVERY STUDENT
{
	if($esProfesor){
      $items = $DB->get_records_sql ("SELECT
			firstname, lastname, finalgrade
			FROM
			mdl_grade_grades as gg INNER JOIN
			(mdl_grade_items as gi JOIN mdl_course as c JOIN mdl_user as u)
			ON (gg.itemid = gi.id AND gi.courseid= $courseid AND gg.userid = u.id)
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
	$firstname = $item->firstname;
	$lastname = $item->lastname;
	$finalgrade = $item->finalgrade;
	$table->data[] = array($position, $finalgrade, $firstname, $lastname);
	}
	
	echo html_writer::table($table);


}

else
{echo "hola no puedes ver esto!";}

}
	

else if ($ranking == 3) // ACTIVIDADES, SHOWS THE TOTAL OF FORUMS WRITEN AND FILES DOWNLOADED OF EVERY STUDENT OF THE COURSE
{
	if($esProfesor){
	$items = $DB->get_records_sql ("SELECT firstname, lastname, COUNT(lsl.objectid) as sumaarchivos,
		IFNULL (T.sumaforos,0) as sumaforos, 
		(COUNT(lsl.objectid) + IFNULL (T.sumaforos,0)) as suma
		FROM mdl_logstore_standard_log as lsl 
		INNER JOIN mdl_user as u ON (lsl.userid = u.id) 
		INNER JOIN (mdl_modules as m JOIN mdl_course_modules as cm JOIN mdl_resource as r) 
		ON (cm.module = m.id AND r.course = cm.course AND r.id=lsl.objectid) 
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
			'Firstname',
			'Lastname',
			'Downloaded Files',
			'Forums Done',
			'Total'
			
	);
	
	
	foreach ( $items as $item ) {
		$firstname = $item->firstname;
		$lastname = $item->lastname;
		$sumaarchivos= $item->sumaarchivos;
		$sumaforos= $item->sumaforos;
		$suma= $item->suma;
	
		$table->data[] = array($firstname, $lastname, $sumaarchivos, $sumaforos, $suma);
	}
	
	echo html_writer::table($table);
	
	
	
	
}

else
{echo "hola no puedes ver esto!";}

}


echo $OUTPUT->footer();
?>