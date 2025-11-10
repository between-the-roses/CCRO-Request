<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../repository/stats_repo.php';
header('Content-Type: application/json');
echo json_encode([
  'cert'        => certCounts($conn),
  'status'      => statusCounts($conn),
  'time'        => timeCounts($conn),
  'avgHours'    => avgProcessingHours($conn),
  'recent'      => recentRequests($conn, 10),
], JSON_UNESCAPED_SLASHES);
