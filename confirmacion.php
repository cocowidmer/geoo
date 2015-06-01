<?php
require_once(dirname(__FILE__) . '/../../config.php'); //obligatorio
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/user/renderer.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/grader/lib.php');
global $PAGE, $CFG, $OUTPUT, $DB, $USER;
$url = new moodle_url('/local/geoo/confirmacion.php');
$context = context_system::instance();
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Titulo');
$PAGE->navbar->add('Titulo');
$PAGE->navbar->add('index');
echo $OUTPUT->header();
echo $OUTPUT->heading('heading');

$grades=grade_regrade_final_grades('2');
var_dump($grades);

echo $OUTPUT->footer();
?>