<?php
$capabilities = array(
		'local/geoo:putgrade' => array(
				// Capability type (write, read, etc.)
				'captype' => 'read',
				// Context in which the capability can be set (course, category, etc.)
				'contextlevel' => CONTEXT_SYSTEM,
				// Default values for different roles (only teachers and managers can modify)
				'legacy' => array(
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW,
						'student'=>CAP_PROHIBIT,
				)),
		);

/*
 * 	$notas = $DB->get_records_sql('algo');
		var_dump($notas);
		foreach($notas as $nota){
		
		echo $nota;
		
		}
 */
		?>
		
		
	