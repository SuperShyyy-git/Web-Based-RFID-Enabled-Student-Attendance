<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentRecord;

echo "=== Generating Face Encodings for Students ===\n\n";

// Get all students who have a face_image but no face_encoding
$students = StudentRecord::whereNotNull('face_image')
    ->where(function ($query) {
        $query->whereNull('face_encoding')
            ->orWhere('face_encoding', '');
    })
    ->get();

echo "Found " . $students->count() . " students without face encoding.\n\n";

foreach ($students as $student) {
    echo "Processing: {$student->first_name} {$student->last_name} (RFID: {$student->rfid})\n";
    echo "  Face Image: {$student->face_image}\n";

    if (!$student->face_image || !file_exists(public_path($student->face_image))) {
        echo "  ❌ Face image file not found!\n\n";
        continue;
    }

    // Try to generate face encoding
    $pythonPath = 'python'; // Use 'python' from PATH
    $scriptPath = base_path('scripts/generate_encoding.py');
    $imagePath = $student->face_image;

    $command = "$pythonPath \"$scriptPath\" \"$imagePath\"";

    echo "  Running: $command\n";

    $output = shell_exec($command);
    $result = json_decode($output, true);

    if (isset($result['encoding'])) {
        $student->face_encoding = json_encode($result['encoding']);
        $student->save();
        echo "  ✅ Face encoding generated and saved!\n\n";
    } else {
        echo "  ❌ Failed to generate encoding\n";
        echo "  Error: " . ($result['error'] ?? 'Unknown error') . "\n";
        echo "  Raw output: $output\n\n";
    }
}

echo "=== Done ===\n";
