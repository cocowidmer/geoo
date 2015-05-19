<?php
/**
 * This file keeps track of upgrades to the evaluaciones block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @since 2.0
 * @package blocks
 * @copyright 2012 Jorge Villalon
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 *
 * @param int $oldversion
 * @param object $block
 */
function xmldb_local_geo_upgrade($oldversion) {
	global $CFG, $DB;
	$dbman = $DB->get_manager();
	
	if ($oldversion < 2015051201) {
	
		// Define table local_geo to be created.
		$table = new xmldb_table('local_geo');
	
		// Adding fields to table local_geo.
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('alumno_id', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
		$table->add_field('comentario', XMLDB_TYPE_TEXT, null, null, null, null, null);
	
		// Adding keys to table local_geo.
		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
	
		// Conditionally launch create table for local_geo.
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}
	
		// Geo savepoint reached.
		upgrade_plugin_savepoint(true, 2015051201, 'local', 'geo');
	}
	
	return true;
	}