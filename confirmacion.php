<?php
require_once(dirname(__FILE__) . '/../../config.php'); //obligatorio
/*require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/user/renderer.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/grader/lib.php'); */
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


echo 'hola mudno';


function get_activity_finalgrade($activity, $activityid, $userid) {
	global $DB;

	$sql = "SELECT
                gg.itemid, gg.userid, gg.rawscaleid, gg.finalgrade, gi.scaleid
            FROM
                {grade_grades} gg
            INNER JOIN {grade_items} gi ON gi.id = gg.itemid
            WHERE gi.itemmodule = :activity AND gi.iteminstance = :iteminstance AND gg.userid = :userid";
	$params['activity'] = $activity;
	$params['iteminstance'] = $activityid;
	$params['userid'] = $userid;

	$gradeitem = $DB->get_records_sql($sql, $params);

	$finalgrade = 0;
	if (!empty($gradeitem)) {
		$gradeitem = current($gradeitem);

		// Grade without scale -- grademax 100.
		if (empty($gradeitem->scaleid)) {
			$finalgrade = $gradeitem->finalgrade / 10;
		} else {
			$finalgrade = get_finalgrade_by_scale($gradeitem->finalgrade, $gradeitem->scaleid);
		}
	}

	return $finalgrade;

}

var_dump($finalgrade);




echo $OUTPUT->footer();
?>