<?php
require 'vendor/autoload.php';
use Carbon\Carbon;

$log = (object)['status' => 'absent', 'check_in' => '05:29:00', 'date' => '2026-05-04 00:00:00'];
$isScheduled = true;
$canReevaluate = true;
$effectiveStart = '06:00:00';
$emp = (object)['squad_id' => 1];

if ($log->check_in && $isScheduled && $canReevaluate) {
    $checkInTime = date('H:i', strtotime($log->check_in));
    $targetInTime = date('H:i', strtotime($effectiveStart));
    
    $dateStr = Carbon::parse($log->date)->format('Y-m-d');
    $actualIn = Carbon::parse($dateStr.' '.$checkInTime);
    $targetIn = Carbon::parse($dateStr.' '.$targetInTime);
    
    $diffMinutes = $actualIn->diffInMinutes($targetIn, false);

    if ($diffMinutes >= -180) {
        if ($actualIn > $targetIn) {
            $log->status = 'late';
            $log->late_minutes = (int)$diffMinutes;
        } else {
            $log->status = $emp->squad_id ? 'picket' : 'present';
            $log->late_minutes = 0;
        }
    } else {
        $log->status = 'absent';
    }
}
echo "Status: " . $log->status . "\n";
