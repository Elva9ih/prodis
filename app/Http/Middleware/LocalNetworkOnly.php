<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalNetworkOnly
{
    /**
     * Local network IP ranges
     */
    private array $allowedRanges = [
        '10.0.0.0/8',       // Class A private
        '172.16.0.0/12',    // Class B private
        '192.168.0.0/16',   // Class C private
        '127.0.0.0/8',      // Localhost
        '::1',              // IPv6 localhost
    ];

    /**
     * Handle an incoming request.
     * Only allow requests from local network IPs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Allow if local network check is disabled in config
        if (config('app.disable_local_network_check', false)) {
            return $next($request);
        }

        if (!$this->isLocalNetwork($ip)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Synchronization is only allowed from the local network.',
            ], 403);
        }

        return $next($request);
    }

    /**
     * Check if IP is in local network range
     */
    private function isLocalNetwork(string $ip): bool
    {
        // Handle IPv6 localhost
        if ($ip === '::1') {
            return true;
        }

        foreach ($this->allowedRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        list($subnet, $bits) = explode('/', $range);

        $ip = ip2long($ip);
        $subnet = ip2long($subnet);

        if ($ip === false || $subnet === false) {
            return false;
        }

        $mask = -1 << (32 - (int)$bits);
        $subnet &= $mask;

        return ($ip & $mask) === $subnet;
    }
}
