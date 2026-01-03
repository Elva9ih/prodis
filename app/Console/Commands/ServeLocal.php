<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ServeLocal extends Command
{
    protected $signature = 'serve:local {--port=8000 : The port to serve on}';
    protected $description = 'Start Laravel server on local network IP';

    public function handle()
    {
        $ip = $this->getLocalIP();
        $port = $this->option('port');
        $url = "http://{$ip}:{$port}";

        $this->info("Local IP detected: {$ip}");
        $this->info("Starting Laravel server at: {$url}");
        $this->info("Press Ctrl+C to stop\n");

        // Open browser
        $this->openBrowser($url);

        // Start server
        passthru("php artisan serve --host={$ip} --port={$port}");

        return 0;
    }

    private function getLocalIP(): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            exec('ipconfig', $output);
            foreach ($output as $line) {
                if (strpos($line, 'IPv4') !== false || strpos($line, 'Adresse IPv4') !== false) {
                    preg_match('/\d+\.\d+\.\d+\.\d+/', $line, $matches);
                    if (isset($matches[0]) && !str_starts_with($matches[0], '127.')) {
                        return $matches[0];
                    }
                }
            }
        } else {
            // Linux/Mac
            exec('hostname -I 2>/dev/null || ipconfig getifaddr en0', $output);
            if (!empty($output[0])) {
                $ips = explode(' ', trim($output[0]));
                return $ips[0];
            }
        }

        return '127.0.0.1';
    }

    private function openBrowser(string $url): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("start {$url}");
        } elseif (PHP_OS === 'Darwin') {
            exec("open {$url}");
        } else {
            exec("xdg-open {$url}");
        }
    }
}
