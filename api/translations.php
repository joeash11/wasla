<?php
// ============================================
// Translation API
// GET /api/translations.php?lang=ar
// Returns a JSON dictionary of translations
// ============================================
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$lang = isset($_GET['lang']) ? preg_replace('/[^a-z]/', '', strtolower($_GET['lang'])) : 'en';

$translations_dir = __DIR__ . '/../lang/';
$file = $translations_dir . $lang . '.json';

if (!file_exists($file)) {
    http_response_code(404);
    echo json_encode(['error' => "Language '$lang' not found", 'available' => ['en', 'ar']]);
    exit;
}

$data = file_get_contents($file);
echo $data;
?>
