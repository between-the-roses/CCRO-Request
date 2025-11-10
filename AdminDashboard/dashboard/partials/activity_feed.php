<?php /** @var array $recent */ ?>
<div class="activity-feed loading-animation">
  <h3 class="section-title"><i class='bx bx-activity'></i> Recent Activity</h3>

  <?php if (!empty($recent)): foreach ($recent as $r):
      $certType = strtolower($r['certificate_type']);
      $iconClass = (strpos($certType,'marriage')!==false) ? 'marriage' :
                   ((strpos($certType,'death')!==false) ? 'death' : 'birth');
      $icon = $iconClass==='birth' ? 'bx-child' : ($iconClass==='marriage' ? 'bx-heart' : 'bx-cross');
  ?>
  <div class="activity-item">
    <div class="activity-icon <?= $iconClass ?>"><i class='bx <?= $icon ?>'></i></div>
    <div class="activity-details">
      <h6><?= htmlspecialchars($r['fullname']) ?></h6>
      <p>Requested <?= htmlspecialchars($r['certificate_type']) ?> • <?= date('M j, Y g:i A', strtotime($r['createdat'])) ?></p>
    </div>
    <div class="activity-status status-<?= htmlspecialchars($r['status']) ?>"><?= ucfirst($r['status']) ?></div>
  </div>
  <?php endforeach; else: ?>
    <div class="text-center text-muted py-4">
      <i class='bx bx-activity' style="font-size:3rem;opacity:.3"></i>
      <p>No recent activity</p>
    </div>
  <?php endif; ?>
</div>
