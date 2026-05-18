<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'app' => [
                'ok' => true,
                'env' => app()->environment(),
                'debug' => config('app.debug'),
                'url' => config('app.url'),
            ],
            'database' => [
                'ok' => false,
                'connection' => config('database.default'),
            ],
            'reverb' => [
                'ok' => filled(config('broadcasting.connections.reverb.key'))
                    && filled(config('broadcasting.connections.reverb.options.host')),
                'broadcast_connection' => config('broadcasting.default'),
                'host_set' => filled(config('broadcasting.connections.reverb.options.host')),
                'key_set' => filled(config('broadcasting.connections.reverb.key')),
            ],
        ];

        try {
            DB::connection()->getPdo();

            $checks['database']['ok'] = true;
            $checks['database']['users_table'] = Schema::hasTable('users');
            $checks['database']['conversations_table'] = Schema::hasTable('conversations');
            $checks['database']['messages_table'] = Schema::hasTable('messages');
        } catch (Throwable $exception) {
            $checks['database']['error'] = $exception->getMessage();
        }

        $ok = $checks['app']['ok'] && $checks['database']['ok'];

        return response()->json([
            'ok' => $ok,
            'service' => config('app.name'),
            'time' => now()->toIso8601String(),
            'checks' => $checks,
        ], $ok ? 200 : 503);
    }
}
