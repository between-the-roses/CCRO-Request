<?php /** @var array $cert */ ?>
<div class="certificate-section">
  <h3 class="section-title"><i class='bx bx-file'></i> Certificate Requests by Type</h3>
  <div class="row g-3">
    <?php
      $cards = [
        ['key'=>'birth','title'=>'Birth Certificates','icon'=>'bx-child','count'=>$cert['birthCount'],'done'=>$cert['birthCompleted']],
        ['key'=>'marriage','title'=>'Marriage Certificates','icon'=>'bx-heart','count'=>$cert['marriageCount'],'done'=>$cert['marriageCompleted']],
        ['key'=>'death','title'=>'Death Certificates','icon'=>'bx-cross','count'=>$cert['deathCount'],'done'=>$cert['deathCompleted']],
      ];
      foreach ($cards as $c):
        $pct = $c['count'] > 0 ? ($c['done'] / $c['count']) * 100 : 0;
    ?>
    <div class="col-lg-4 col-md-6">
      <div class="certificate-col">
        <div class="card-box <?= $c['key'] ?> loading-animation">
          <div>
            <i class='bx <?= $c['icon'] ?>'></i>
            <h4><?= $c['title'] ?></h4>
            <h2><?= number_format($c['count']) ?></h2>
            <p><?= number_format($c['done']) ?> Completed</p>
          </div>
          <div class="progress-bar-custom">
            <div class="progress-fill <?= $c['key'] ?>" style="width: <?= $pct ?>%;"></div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
