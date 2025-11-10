<?php
/** @var array $tableData */
$transactions = $tableData['transactions'] ?? [];
?>
<div class="table-responsive">
  <table class="custom-table">
    <thead>
      <tr>
        <th>Transaction ID</th>
        <!-- <th>Transaction No.</th> -->
        <th>Requesting Party</th>
        <th>Contact Number</th>
        <th>Relationship</th>
        <th>Document Type</th>
        <th>Mode of Payment</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($transactions)): ?>
        <tr>
          <td colspan="8" class="text-center" style="padding: 40px;">
            <i class='bx bx-info-circle' style="font-size: 48px; color: #ddd;"></i>
            <div style="margin-top: 10px; color: #6c757d;">No transactions found</div>
          </td>
        </tr>
      <?php else: foreach ($transactions as $t): 
        $status = strtolower($t['status'] ?? 'pending');
        $badge  = $status === 'confirmed' ? 'status-confirmed'
               : ($status === 'cancelled' ? 'status-cancelled' : 'status-pending');
      ?>
        <tr>
          <td>
            <span class="transaction-id"
                  data-bs-toggle="modal" data-bs-target="#transactionModal"
                  data-id="<?= htmlspecialchars($t['id']) ?>"
                  data-transaction='<?= htmlspecialchars(json_encode($t), ENT_QUOTES, "UTF-8") ?>'>
              <?= htmlspecialchars($t['id']) ?>
            </span>
          </td>
          <!-- <td><?= htmlspecialchars($t['transaction_no']) ?></td> -->
          <td><?= htmlspecialchars($t['requesting_party']) ?></td>
          <td><?= htmlspecialchars($t['contact_number']) ?></td>
          <td><?= htmlspecialchars($t['relationship']) ?></td>
          <td><?= htmlspecialchars($t['document_type']) ?></td>
          <td><?= htmlspecialchars($t['payment_mode']) ?></td>
          <td><span class="<?= $badge ?>"><?= ucfirst($status) ?></span></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
