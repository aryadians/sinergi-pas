<?php
require 'vendor/autoload.php';

use Carbon\Carbon;

$log = (object)['status' => 'absent', 'check_in' => '05:29:00', 'date' => '2026-05-04'];
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
    
    $diffMinutes = $actualIn->diffInMinutes($targetIn, false); // Negative if early

    echo "actualIn: $actualIn\n";
    echo "targetIn: $targetIn\n";
    echo "diffMinutes: $diffMinutes\n";

    if ($diffMinutes >= -180) {
        if ($actualIn > $targetIn) {
            $log->status = 'late';
            $log->late_minutes = (int)$diffMinutes;
            echo "Result: LATE\n";
        } else {
            $log->status = $emp->squad_id ? 'picket' : 'present';
            $log->late_minutes = 0;
            echo "Result: PRESENT/PICKET\n";
        }
    } else {
        $log->status = 'absent';
        echo "Result: ABSENT\n";
    }
}
echo "Final status: {$log->status}\n";
