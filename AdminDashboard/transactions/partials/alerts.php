<?php
$success = $_GET['success'] ?? null;
$action  = $_GET['action']  ?? '';
$error   = $_GET['error']   ?? '';

if ($success === 'true'):
  $actionText = $action === 'confirm' ? 'confirmed' : ($action === 'cancel' ? 'cancelled' : 'updated');
?>
  <div class="success-message">
    <strong>Success!</strong> Transaction has been <?= htmlspecialchars($actionText) ?> successfully.
  </div>
<?php elseif ($success === 'false'): ?>
  <div class="error-message">
    <strong>Error:</strong>
    <?= htmlspecialchars($error ?: 'Something went wrong while saving.') ?>
  </div>
<?php endif; ?>
