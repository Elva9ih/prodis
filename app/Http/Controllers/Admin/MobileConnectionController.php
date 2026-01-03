<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileConnectionController extends Controller
{
    public function index(Request $request)
    {
        // Get the server's IP address
        $serverIp = $request->server('SERVER_ADDR') ?: $request->ip();

        // If we're on localhost, try to get the actual local IP
        if ($serverIp === '127.0.0.1' || $serverIp === '::1') {
            $serverIp = $this->getLocalIp();
        }

        $serverPort = $request->server('SERVER_PORT') ?: '8000';
        $protocol = $request->secure() ? 'https' : 'http';

        // Build the API URL
        $apiUrl = "{$protocol}://{$serverIp}:{$serverPort}/api";

        // QR code data (JSON format for the mobile app)
        $qrData = json_encode([
            'apiUrl' => $apiUrl,
            'serverName' => config('app.name'),
        ]);

        return view('admin.mobile-connection.index', compact(
            'serverIp',
            'serverPort',
            'protocol',
            'apiUrl',
            'qrData'
        ));
    }

    /**
     * Get local network IP address
     */
    private function getLocalIp(): string
    {
        // Try different methods to get local IP
        if (function_exists('shell_exec')) {
            // Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $output = shell_exec('ipconfig');
                if ($output) {
                    preg_match('/IPv4.*?:\s*([\d.]+)/', $output, $matches);
                    if (isset($matches[1])) {
                        return $matches[1];
                    }
                }
            } else {
                // Linux/Mac
                $output = shell_exec("hostname -I 2>/dev/null | awk '{print $1}'");
                if ($output) {
                    return trim($output);
                }
            }
        }

        // Fallback: try to get IP from host
        $hostname = gethostname();
        $ip = gethostbyname($hostname);

        if ($ip !== $hostname) {
            return $ip;
        }

        return '127.0.0.1';
    }
}
