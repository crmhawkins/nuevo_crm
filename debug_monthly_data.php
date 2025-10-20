<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$autoseo = \App\Models\Autoseo\Autoseo::find(13);
$jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];

echo "Total JSONs: " . count($jsonStorage) . "\n\n";

$monthlyData = [];

foreach ($jsonStorage as $jsonInfo) {
    $path = $jsonInfo['path'] ?? null;
    if (!$path) continue;

    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) continue;

    $jsonContent = file_get_contents($fullPath);
    $data = json_decode($jsonContent, true);

    if ($data) {
        $date = $data['uploaded_at'] ?? 'Unknown';
        $month = date('Y-m', strtotime($date));
        
        if (!isset($monthlyData[$month])) {
            $monthlyData[$month] = [];
        }
        $monthlyData[$month][] = $date;
        
        echo "JSON: " . basename($path) . "\n";
        echo "  Fecha: $date\n";
        echo "  Mes: $month\n";
        echo "  Keywords: " . count($data['detalles_keywords'] ?? []) . "\n\n";
    }
}

echo "\n=== RESUMEN POR MES ===\n";
foreach ($monthlyData as $month => $dates) {
    echo "$month: " . count($dates) . " JSONs\n";
    foreach ($dates as $date) {
        echo "  - $date\n";
    }
}


