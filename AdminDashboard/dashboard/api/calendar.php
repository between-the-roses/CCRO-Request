<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../repository/stats_repo.php';
header('Content-Type: application/json');
echo json_encode(calendarEvents($conn), JSON_UNESCAPED_SLASHES);
