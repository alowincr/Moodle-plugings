<?php
define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once('locallib.php');

$courseid = required_param('courseid', PARAM_INT);
$sectionnum = optional_param('section', 0, PARAM_INT);
$message = required_param('message', PARAM_RAW);

require_login($courseid);

try {
    // 1lib-bloque-preparacion-contexto
    $data = local_aichat_get_course_context($courseid, $sectionnum);
    
    // 1lib-bloque-ejecucion-IA
    $response = local_aichat_call_gemini($data['text'], $message, $data['files']);
    
    echo json_encode(['success' => true, 'response' => $response]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}