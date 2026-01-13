<?php
declare(strict_types=1);

use App\Core\Helpers;

$data = $data ?? [];

$k = $data['kpis'] ?? ['total_co2'=>0,'total_waste'=>0,'total_kg'=>0,'completed_count'=>0];
$s = $data['series'] ?? ['labels'=>[],'kg'=>[],'count'=>[],'co2'=>[],'waste'=>[]];

$labelsJson = json_encode($s['labels'], JSON_UNESCAPED_UNICODE);
$co2Json    = json_encode($s['co2']);
$wasteJson  = json_encode($s['waste']);
$kgJson     = json_encode($s['kg']);
$countJson  = json_encode($s['count']);
?>

<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
  <div>
    <div class="h3 mb-1">Platform Impact</div>
    <div class="app-muted">Totals computed from <b>COMPLETED</b> transactions only (dummy coefficients × total kg by category).</div>
  </div>
  <a class="btn app-btn-soft" href="<?= Helpers::route('browse') ?>">Browse items</a>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-3">
    <div class="app-card p-4 h-100">
      <div class="fw-semibold mb-1">Total CO₂ saved</div>
      <div class="display-6 fw-semibold"><?= Helpers::e((string)$k['total_co2']) ?> <span class="fs-6 app-muted">kg</span></div>
      <div class="small app-muted mt-2">Across all completed exchanges.</div>
    </div>
  </div>
  <div class="col-12 col-lg-3">
    <div class="app-card p-4 h-100">
      <div class="fw-semibold mb-1">Total waste diverted</div>
      <div class="display-6 fw-semibold"><?= Helpers::e((string)$k['total_waste']) ?> <span class="fs-6 app-muted">kg</span></div>
      <div class="small app-muted mt-2">Estimate using placeholders.</div>
    </div>
  </div>
  <div class="col-12 col-lg-3">
    <div class="app-card p-4 h-100">
      <div class="fw-semibold mb-1">Total kg reused</div>
      <div class="display-6 fw-semibold"><?= Helpers::e((string)$k['total_kg']) ?> <span class="fs-6 app-muted">kg</span></div>
      <div class="small app-muted mt-2">Sum of item weights in completed deals.</div>
    </div>
  </div>
  <div class="col-12 col-lg-3">
    <div class="app-card p-4 h-100">
      <div class="fw-semibold mb-1">Completed deals</div>
      <div class="display-6 fw-semibold"><?= (int)$k['completed_count'] ?></div>
      <div class="small app-muted mt-2">More completions → larger totals.</div>
    </div>
  </div>
</div>

<?php if (empty($s['labels'])): ?>
  <div class="app-card p-4">
    <div class="h5 mb-1">No completed transactions yet</div>
    <div class="app-muted">Complete a swap/sale/donation to generate impact statistics and charts.</div>
  </div>
  <?php return; ?>
<?php endif; ?>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="app-card p-3 p-md-4 h-100">
      <div class="d-flex justify-content-between align-items-center">
        <div class="fw-semibold">CO₂ saved by category</div>
        <span class="badge badge-soft">kg CO₂</span>
      </div>
      <div class="mt-3">
        <canvas id="co2Bar" height="130"></canvas>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="app-card p-3 p-md-4 h-100">
      <div class="d-flex justify-content-between align-items-center">
        <div class="fw-semibold">Share of CO₂ saved</div>
        <span class="badge badge-soft">%</span>
      </div>
      <div class="mt-3">
        <canvas id="co2Donut" height="180"></canvas>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="app-card p-3 p-md-4 h-100">
      <div class="d-flex justify-content-between align-items-center">
        <div class="fw-semibold">Waste diverted by category</div>
        <span class="badge badge-soft">kg waste</span>
      </div>
      <div class="mt-3">
        <canvas id="wasteBar" height="130"></canvas>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="app-card p-3 p-md-4 h-100">
      <div class="fw-semibold mb-2">Category table (totals)</div>

      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr class="text-white-50">
              <th>Category</th>
              <th class="text-end">Kg</th>
              <th class="text-end">CO₂</th>
              <th class="text-end">Waste</th>
              <th class="text-end">Deals</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($i=0; $i<count($s['labels']); $i++): ?>
              <tr>
                <td class="text-white"><?= Helpers::e((string)$s['labels'][$i]) ?></td>
                <td class="text-end"><?= Helpers::e((string)$s['kg'][$i]) ?></td>
                <td class="text-end"><?= Helpers::e((string)$s['co2'][$i]) ?></td>
                <td class="text-end"><?= Helpers::e((string)$s['waste'][$i]) ?></td>
                <td class="text-end"><?= Helpers::e((string)$s['count'][$i]) ?></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <div class="small app-muted mt-3">
        *All values are estimates (dummy coefficients × weight_kg).
      </div>
    </div>
  </div>
</div>

<!-- Chart.js (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
$(function () {
  const labels = <?= $labelsJson ?>;
  const co2 = <?= $co2Json ?>;
  const waste = <?= $wasteJson ?>;

  // CO2 bar
  new Chart(document.getElementById('co2Bar'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'CO₂ saved (kg)',
        data: co2
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });

  // Waste bar
  new Chart(document.getElementById('wasteBar'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Waste diverted (kg)',
        data: waste
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });

  // CO2 donut
  new Chart(document.getElementById('co2Donut'), {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        label: 'CO₂ share',
        data: co2
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });
});
</script>
