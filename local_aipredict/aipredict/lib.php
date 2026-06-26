<?php
/**
 * Biblioteca local para el bloque aipredict.
 *
 * @package    local_aipredict
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * 1lib-bloque-dataset-algoritmo
 * Propósito: Recopilar, procesar y filtrar datos académicos (logs y notas)
 * para el modelo predictivo de IA.
 */
function aipredict_gather_course_data($courseid) {
    global $DB, $CFG;
    
    require_once($CFG->dirroot.'/course/lib.php');
    
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $context = context_course::instance($courseid);
    
    // 1lib-bloque-extraccion-estudiantes
    $all_enrolled = get_enrolled_users($context, '', 0, 'u.id, u.firstname, u.lastname');
    $students = [];
    foreach ($all_enrolled as $u) {
        if (!has_capability('moodle/course:update', $context, $u->id)) {
            $students[] = $u;
        }
    }
    
    $dataset = [];
    
    // 1lib-bloque-calculo-actividades
    $modinfo = get_fast_modinfo($course);
    $total_activities = 0;
    foreach ($modinfo->cms as $cm) {
        if ($cm->completion != COMPLETION_TRACKING_NONE) {
            $total_activities++;
        }
    }

    foreach ($students as $student) {
        // [Lógica de recopilación de notas y logs...]
        
        // 1lib-bloque-filtro-eficiencia
        // Propósito: Optimización de tokens evitando enviar datos de usuarios sin interacción.
        if ($student_data['last_access_days_ago'] === 'Never' && $student_data['total_actions'] == 0) {
            continue; 
        }
        
        $dataset[] = $student_data;
    }
    
    return $dataset;
}

/**
 * 1lib-bloque-navegacion-extendida
 * Propósito: Integrar el acceso al panel de IA en el menú global de Moodle.
 */
function local_aipredict_extend_navigation(global_navigation $navigation) {
    global $USER;

    if (!isloggedin() || isguestuser()) {
        return;
    }

    $url = new moodle_url('/local/aipredict/index.php');
    $node = $navigation->add(
        get_string('pluginname', 'local_aipredict'),
        $url,
        navigation_node::TYPE_CUSTOM,
        null,
        'aipredict'
    );
    
    $node->showinflatnavigation = true;
}