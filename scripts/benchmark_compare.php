<?php
/**
 * Compare benchmark_results_main.json vs benchmark_results_V2.json
 * Writes scripts/benchmark_comparison.json and scripts/benchmark_report.html
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden: this script must be run from the command line.');
}

$root = dirname(__DIR__);
$mainFile = $root . '/scripts/benchmark_results_main.json';
$v2File = $root . '/scripts/benchmark_results_V2.json';

if (!is_file($mainFile) || !is_file($v2File)) {
    fwrite(STDERR, "Missing benchmark result files. Run run_benchmark_comparison.sh first.\n");
    exit(1);
}

$main = json_decode(file_get_contents($mainFile), true, 512, JSON_THROW_ON_ERROR);
$v2 = json_decode(file_get_contents($v2File), true, 512, JSON_THROW_ON_ERROR);

$index = static function (array $data): array {
    $map = [];
    foreach ($data['benchmarks'] as $b) {
        $map[$b['name']] = $b;
    }
    foreach ($data['mysql_benchmarks'] ?? [] as $b) {
        $map[$b['name']] = $b;
    }
    return $map;
};

$mainIdx = $index($main);
$v2Idx = $index($v2);

$allNames = array_unique(array_merge(array_keys($mainIdx), array_keys($v2Idx)));
sort($allNames);

$comparisons = [];
foreach ($allNames as $name) {
    $m = $mainIdx[$name] ?? null;
    $v = $v2Idx[$name] ?? null;
    if (!$m || !$v) {
        continue;
    }
    $mainMean = (float)$m['mean_ms'];
    $v2Mean = (float)$v['mean_ms'];
    $delta = round($v2Mean - $mainMean, 4);
    $pct = $mainMean > 0 ? round((($v2Mean - $mainMean) / $mainMean) * 100, 2) : null;

    $comparisons[] = [
        'name' => $name,
        'main_mean_ms' => $mainMean,
        'v2_mean_ms' => $v2Mean,
        'delta_ms' => $delta,
        'delta_percent' => $pct,
        'category' => categorize($name),
    ];
}

function categorize(string $name): string
{
    if (str_starts_with($name, 'crypto_')) {
        return 'crypto';
    }
    if (str_starts_with($name, 'sql_') || str_starts_with($name, 'mysql_select_')) {
        return 'sql_raw';
    }
    if (str_starts_with($name, 'e2e_') || str_starts_with($name, 'mysql_model_') || str_starts_with($name, 'app_')) {
        return 'e2e';
    }
    return 'other';
}

usort($comparisons, fn($a, $b) => ($b['delta_ms'] <=> $a['delta_ms']));

$report = [
    'generated_at' => date('c'),
    'main' => ['label' => $main['label'], 'mysql_available' => $main['mysql_available']],
    'v2' => ['label' => $v2['label'], 'mysql_available' => $v2['mysql_available']],
    'iterations' => $main['iterations'],
    'comparisons' => $comparisons,
    'summary' => summarize($comparisons),
];

function summarize(array $comparisons): array
{
    $byCat = [];
    foreach ($comparisons as $c) {
        $cat = $c['category'];
        if (!isset($byCat[$cat])) {
            $byCat[$cat] = ['count' => 0, 'total_delta_ms' => 0];
        }
        $byCat[$cat]['count']++;
        $byCat[$cat]['total_delta_ms'] += $c['delta_ms'];
    }
    return $byCat;
}

$jsonOut = $root . '/scripts/benchmark_comparison.json';
file_put_contents($jsonOut, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// HTML visualization
$html = buildHtml($report, $main, $v2);
$htmlOut = $root . '/scripts/benchmark_report.html';
file_put_contents($htmlOut, $html);

echo "Wrote {$jsonOut}\n";
echo "Wrote {$htmlOut}\n";

function buildHtml(array $report, array $main, array $v2): string
{
    $phpVersion = htmlspecialchars((string)($main['php_version'] ?? ''), ENT_QUOTES, 'UTF-8');
    $comparisons = $report['comparisons'];
    $labels = json_encode(array_column($comparisons, 'name'), JSON_UNESCAPED_UNICODE);
    $mainMeans = json_encode(array_column($comparisons, 'main_mean_ms'));
    $v2Means = json_encode(array_column($comparisons, 'v2_mean_ms'));
    $deltas = json_encode(array_column($comparisons, 'delta_ms'));
    $mysqlNote = '';
    if (!$main['mysql_available'] || !$v2['mysql_available']) {
        $mysqlNote = '<p class="note">MySQL was unavailable; SQL timings use in-memory SQLite with identical queries. Crypto and E2E deltas reflect the encryption layer.</p>';
    }

    $summaryRows = '';
    foreach ($report['summary'] as $cat => $data) {
        $avg = $data['count'] > 0 ? round($data['total_delta_ms'] / $data['count'], 4) : 0;
        $summaryRows .= "<tr><td>{$cat}</td><td>{$data['count']}</td><td>{$avg} ms avg overhead</td></tr>";
    }

    $tableRows = '';
    foreach ($comparisons as $c) {
        $pct = $c['delta_percent'] !== null ? $c['delta_percent'] . '%' : '—';
        $tableRows .= sprintf(
            '<tr><td>%s</td><td>%s</td><td>%.4f</td><td>%.4f</td><td>%.4f</td><td>%s</td><td>%s</td></tr>',
            htmlspecialchars($c['name']),
            htmlspecialchars($c['category']),
            $c['main_mean_ms'],
            $c['v2_mean_ms'],
            $c['delta_ms'],
            $pct,
            $c['delta_ms'] > 0.01 ? 'Slower on V2' : ($c['delta_ms'] < -0.01 ? 'Faster on V2' : '~Same')
        );
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PII Encryption Benchmark — main vs V2</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    body { font-family: system-ui, sans-serif; margin: 24px; background: #fafafa; color: #222; }
    h1 { font-size: 1.4rem; font-weight: 600; }
    .meta { color: #555; font-size: 0.9rem; margin-bottom: 20px; }
    .charts { display: grid; grid-template-columns: 1fr; gap: 24px; max-width: 1100px; }
    .card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; }
    table { border-collapse: collapse; width: 100%; font-size: 0.85rem; }
    th, td { border: 1px solid #e8e8e8; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
    .note { background: #fff8e6; border: 1px solid #f0e0a0; padding: 10px; border-radius: 6px; font-size: 0.9rem; }
    canvas { max-height: 420px; }
  </style>
</head>
<body>
  <h1>Query performance: main (plain) vs V2 (encrypted PII)</h1>
  <p class="meta">Generated {$report['generated_at']} · {$report['iterations']} iterations per test · PHP {$phpVersion}</p>
  {$mysqlNote}

  <div class="charts">
    <div class="card">
      <h2>Mean latency by operation (ms)</h2>
      <canvas id="barChart"></canvas>
      <p class="meta">Source: scripts/benchmark_pii_performance.php</p>
    </div>
    <div class="card">
      <h2>V2 overhead vs main (ms)</h2>
      <canvas id="deltaChart"></canvas>
    </div>
  </div>

  <div class="card" style="margin-top:24px; max-width:1100px;">
    <h2>Summary by category</h2>
    <table><thead><tr><th>Category</th><th>Tests</th><th>Avg overhead</th></tr></thead><tbody>{$summaryRows}</tbody></table>
  </div>

  <div class="card" style="margin-top:24px; max-width:1100px;">
    <h2>Full comparison</h2>
    <table>
      <thead><tr><th>Operation</th><th>Category</th><th>main (ms)</th><th>V2 (ms)</th><th>Δ (ms)</th><th>Δ %</th><th>Verdict</th></tr></thead>
      <tbody>{$tableRows}</tbody>
    </table>
  </div>

  <script>
    const labels = {$labels};
    const mainMeans = {$mainMeans};
    const v2Means = {$v2Means};
    const deltas = {$deltas};

    new Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: {
        labels,
        datasets: [
          { label: 'main (plain)', data: mainMeans, backgroundColor: 'rgba(80,80,80,0.7)' },
          { label: 'V2 (encrypted)', data: v2Means, backgroundColor: 'rgba(30,90,180,0.7)' }
        ]
      },
      options: {
        responsive: true,
        scales: { y: { title: { display: true, text: 'Mean time (ms)' } } },
        plugins: { legend: { position: 'top' } }
      }
    });

    new Chart(document.getElementById('deltaChart'), {
      type: 'bar',
      data: {
        labels,
        datasets: [{ label: 'V2 − main (ms)', data: deltas, backgroundColor: deltas.map(d => d > 0.05 ? 'rgba(200,60,60,0.75)' : 'rgba(60,160,80,0.75)') }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        scales: { x: { title: { display: true, text: 'Overhead (ms)' } } }
      }
    });
  </script>
</body>
</html>
HTML;
}

