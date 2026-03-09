<?php
$path = 'd:\\xampp\\htdocs\\tkb_school\\app\\Providers\\Filament\\AdminPanelProvider.php';
$content = file_get_contents($path);
$content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
file_put_contents($path, $content);
echo "BOM fixed.\n";
