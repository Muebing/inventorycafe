<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public static function log(string $aktivitas, ?string $deskripsi = null, ?string $modelType = null, ?int $modelId = null): void
    {
        ActivityLog::create([
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'user_id' => auth()->id(),
            'model_type' => $modelType,
            'model_id' => $modelId,
        ]);
    }
}
