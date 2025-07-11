<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Stopwatch\Stopwatch;

$stopwatch = new Stopwatch();
$stopwatch->start('audit_total');

// Mesure du chargement de la page d'accueil
$start = microtime(true);
$homepage = file_get_contents('http://localhost:8000/');
$end = microtime(true);

$duration = round(($end - $start) * 1000, 2); // en ms
$stopwatchEvent = $stopwatch->stop('audit_total');

$report = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Audit de Performance Symfony</title>
</head>
<body>
    <h1>📊 Audit de Performance - Symfony TodoList</h1>

    <h2>⏱ Temps de chargement HTTP</h2>
    <p><strong>Durée:</strong> {$duration} ms (GET /)</p>

    <h2>⚙️ Stopwatch Symfony</h2>
    <p><strong>Durée mesurée :</strong> {$stopwatchEvent->getDuration()} ms</p>

    <h2>📁 Fichier Xdebug généré ?</h2>
    <p>
HTML;

$latestCachegrind = shell_exec('ls -t /var/tmp/cachegrind.out.* 2>/dev/null | head -n1');
if ($latestCachegrind) {
    $report .= "<code>$latestCachegrind</code>";
} else {
    $report .= "❌ Aucun fichier Xdebug trouvé dans /tmp";
}

$report .= <<<HTML
    </p>

    <h2>📄 Rapport Apache Bench</h2>
    <pre>
HTML;

$abPath = '/tmp/audit_ab.txt';
if (file_exists($abPath)) {
    $report .= htmlspecialchars(file_get_contents($abPath));
} else {
    $report .= "❌ Aucun test AB trouvé à $abPath\n(Utilisez : ab -n 100 -c 10 http://localhost:8000/ > /tmp/audit_ab.txt)";
}

$report .= <<<HTML
    </pre>
</body>
</html>
HTML;

// Écriture du rapport
file_put_contents(__DIR__ . '/audit_performance.html', $report);
echo "✅ Rapport généré : audit_performance.html\n";
