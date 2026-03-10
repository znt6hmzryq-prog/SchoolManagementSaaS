<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log($user, string $action, string $resourceType, $resourceId, array $metadata = [])
    {
        $request = request();

        AuditLog::create([
            'school_id' => $user->school_id,
            'user_id' => $user->id,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'metadata' => $metadata,
        ]);
    }
}