<?php
require_once(dirname(__FILE__) . '/../../config.php'); //obligatorio
require_once($CFG->dirroot.'/local/geo/forms.php');// IMPOORTANMTE
global $PAGE, $CFG, $OUTPUT, $DB, $USER;
$url = new moodle_url('/local/geo/index.php');
$context = context_system::instance();
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Titulo');
$PAGE->navbar->add('Titulo');
$PAGE->navbar->add('index');
echo $OUTPUT->header();
echo $OUTPUT->heading('heading');


echo "hola mundo";


$formulario = new comment();
if ($formulario->is_cancelled()) {
	echo 'Usted no ingreso comentarios';
} else if ($fromform = $formulario->get_data()) {
	$record = new stdClass();
	$record->comentario = $fromform->comment;
	$record->alumno_id = $USER->id;
	$DB->insert_record('local_geo', $record);

	redirect($CFG->dirroot.'/local/geo/confirmacion.php');

} else {
	$formulario->display();
}
echo $OUTPUT->footer();
?>