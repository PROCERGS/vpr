var points = [
<?php
$history = $newVotesPerMinute;

$entries = array();
$current = 0;
$delta = 0;
$total = count($history);
$deltas = array();
foreach ($history as $entry) {
/*    if (count($entries) == $total - 5) {
        break;
    }*/
    $delta = $entry['new_votes'];
    $deltas[] = $delta;
    
    $current += $delta;
    $date = DateTime::createFromFormat('Y-m-d H:i', $entry['time']);
    $month = $date->format('m') - 1;
    $year = $date->format('Y');
    $dateFormated = "$year, $month, " . $date->format('d, H, i, s');
    $entries[] = "[new Date($dateFormated), $delta]";
    $lastUpdate = $date->format('H:i');
}

echo implode(",\n", $entries);
?>
];
var current = '<?= number_format($current, 0, ',', '.') ?>';
var lastUpdate = '<?= $lastUpdate ?>';
var lastDelta = '<?= number_format($delta, 0, ',', '.') ?>';
var cacheHit = <?= $cacheHit?'true':'false' ?>;
var average = <?= round(array_sum($deltas)/count($deltas), 0) ?>;