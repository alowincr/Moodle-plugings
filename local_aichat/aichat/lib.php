<?php
defined('MOODLE_INTERNAL') || die();

/**
 * 1lib-bloque-contexto-curso
 * Extrae contenido y archivos para el prompt.
 */
function local_aichat_get_course_context($courseid, $sectionnum) {
    global $DB;
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $modinfo = get_fast_modinfo($course);
    $fs = get_file_storage();
    
    $context_text = "Estás asistiendo en el curso '{$course->fullname}'.\n\n";
    $sections_to_include = ($sectionnum == 0) ? array_keys($modinfo->sections) : [0, $sectionnum];
    $inline_files = [];

    foreach ($sections_to_include as $sec) {
        if (!isset($modinfo->sections[$sec])) continue;
        foreach ($modinfo->sections[$sec] as $cmid) {
            $cm = $modinfo->cms[$cmid];
            if (!$cm->uservisible) continue;
            
            if ($cm->modname === 'page') {
                $page = $DB->get_record('page', ['id' => $cm->instance]);
                if ($page) $context_text .= strip_tags($page->content) . "\n";
            } else if ($cm->modname === 'resource') {
                $resource_context = context_module::instance($cm->id);
                $files = $fs->get_area_files($resource_context->id, 'mod_resource', 'content', 0);
                foreach ($files as $f) {
                    if ($f->get_mimetype() === 'application/pdf') {
                        $inline_files[] = ['mime_type' => 'application/pdf', 'data' => base64_encode($f->get_content())];
                    }
                }
            }
        }
    }
    return ['text' => $context_text, 'files' => $inline_files];
}

/**
 * 1lib-bloque-ejecucion-ia
 * Maneja la comunicación cURL con Gemini.
 */
function local_aichat_call_gemini($context_text, $message, $inline_files) {
    $api_key = get_config('local_ai_core', 'gemini_api_key');
    $model = get_config('local_ai_core', 'gemini_model') ?: 'gemini-1.5-flash';
    
    $parts = [['text' => $context_text . "\nPregunta: " . $message]];
    foreach ($inline_files as $file) {
        $parts[] = ['inline_data' => ['mime_type' => $file['mime_type'], 'data' => $file['data']]];
    }

    $payload = [
        'contents' => [['parts' => $parts]],
        'systemInstruction' => ['parts' => [['text' => 'Eres un tutor amigable. Responde usando SOLO los recursos proporcionados.']]],
        'generationConfig' => ['temperature' => 0.2]
    ];

    $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $api_key);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code >= 200 && $http_code < 300) {
        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sin respuesta.';
    }
    throw new Exception("Error de conexión con la IA (Código: $http_code).");
}