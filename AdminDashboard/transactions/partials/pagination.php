<?php
/** @var array $pagination */
$page          = $pagination['page'];
$total_pages   = $pagination['total_pages'];
$total_records = $pagination['total_records'];
$start_record  = $pagination['start_record'];
$end_record    = $pagination['end_record'];
?>
<div class="pagination-container">
  <div>
    Showing <?= (int)$start_record ?> to <?= (int)$end_record ?> of <?= (int)$total_records ?> entries
  </div>
  <ul class="pagination">
    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Previous</a>
    </li>

    <?php
      $start_page = max(1, $page - 2);
      $end_page   = min($total_pages, $page + 2);
      if ($start_page > 1) {
        echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
        if ($start_page > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
      }
      for ($i = $start_page; $i <= $end_page; $i++):
    ?>
      <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor;

      if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
          echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
      }
    ?>

    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>">Next</a>
    </li>
  </ul>
</div>
