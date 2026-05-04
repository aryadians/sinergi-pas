<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$e = App\Models\Employee::where('full_name', 'like', '%PUJI%')->first();
if ($e) {
    echo "Employee ID: " . $e->id . "\n";
    echo "Employee Squad ID: " . ($e->squad_id ?? 'NULL') . "\n";
    $svc = app(App\Services\ScheduleService::class);
    print_r($svc->getEffectiveSchedule($e, '2026-05-04'));
    
    echo "\n\nAttendance Record:\n";
    $att = App\Models\Attendance::where('employee_id', $e->id)->whereDate('date', '2026-05-04')->first();
    print_r($att ? $att->toArray() : 'No attendance record');
} else {
    echo "Employee not found";
}
