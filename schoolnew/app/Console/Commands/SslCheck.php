<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SslCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ssl-check {--url= : The URL to check (defaults to APP_URL)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SSL certificate status and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->option('url') ?? config('app.url');

        $this->info('SSL Certificate Check');
        $this->info('=====================');
        $this->newLine();
        $this->line("URL: {$url}");
        $this->newLine();

        // Parse URL
        $parsed = parse_url($url);

        if (!$parsed || !isset($parsed['host'])) {
            $this->error('Invalid URL provided.');
            return Command::FAILURE;
        }

        $host = $parsed['host'];
        $port = 443;

        // Check if URL is HTTPS
        if (($parsed['scheme'] ?? 'http') !== 'https') {
            $this->warn('URL is not using HTTPS. SSL certificate check requires HTTPS.');
            $this->newLine();
            $this->info('Checking if HTTPS is available...');
            $url = str_replace('http://', 'https://', $url);
        }

        // Get SSL certificate info
        $this->info('1. SSL Certificate Information');
        $certInfo = $this->getCertificateInfo($host, $port);

        if ($certInfo) {
            $this->displayCertificateInfo($certInfo);
        } else {
            $this->error('   Could not retrieve SSL certificate information.');
            $this->line('   This could mean:');
            $this->line('   - The server is not accessible');
            $this->line('   - SSL certificate is not installed');
            $this->line('   - Port 443 is blocked');
            $this->newLine();
        }

        // Check Laravel configuration
        $this->newLine();
        $this->info('2. Laravel HTTPS Configuration');
        $this->checkLaravelConfig();

        // Provide setup instructions
        $this->newLine();
        $this->info('3. SSL Setup Instructions');
        $this->displaySetupInstructions();

        return Command::SUCCESS;
    }

    /**
     * Get SSL certificate information
     */
    protected function getCertificateInfo(string $host, int $port = 443): ?array
    {
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $socket = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            return null;
        }

        $params = stream_context_get_params($socket);
        fclose($socket);

        if (!isset($params['options']['ssl']['peer_certificate'])) {
            return null;
        }

        $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);

        return $cert;
    }

    /**
     * Display certificate information
     */
    protected function displayCertificateInfo(array $certInfo): void
    {
        // Subject
        $subject = $certInfo['subject']['CN'] ?? 'Unknown';
        $this->line("   Common Name: {$subject}");

        // Issuer
        $issuer = $certInfo['issuer']['O'] ?? $certInfo['issuer']['CN'] ?? 'Unknown';
        $this->line("   Issuer: {$issuer}");

        // Validity
        $validFrom = date('Y-m-d H:i:s', $certInfo['validFrom_time_t']);
        $validTo = date('Y-m-d H:i:s', $certInfo['validTo_time_t']);
        $this->line("   Valid From: {$validFrom}");
        $this->line("   Valid To: {$validTo}");

        // Check expiration
        $now = time();
        $expiresIn = $certInfo['validTo_time_t'] - $now;
        $daysUntilExpiry = floor($expiresIn / 86400);

        if ($expiresIn < 0) {
            $this->error("   Status: EXPIRED");
        } elseif ($daysUntilExpiry < 30) {
            $this->warn("   Status: Expires in {$daysUntilExpiry} days - RENEW SOON");
        } else {
            $this->line("   Status: Valid ({$daysUntilExpiry} days until expiry)");
        }

        // Alternative names
        if (isset($certInfo['extensions']['subjectAltName'])) {
            $sans = $certInfo['extensions']['subjectAltName'];
            $this->line("   Alt Names: {$sans}");
        }
    }

    /**
     * Check Laravel HTTPS configuration
     */
    protected function checkLaravelConfig(): void
    {
        // APP_URL check
        $appUrl = config('app.url');
        if (str_starts_with($appUrl, 'https://')) {
            $this->line('   [OK] APP_URL uses HTTPS');
        } else {
            $this->warn('   [WARN] APP_URL does not use HTTPS');
            $this->line('   Update .env: APP_URL=https://yourdomain.com');
        }

        // Session secure cookie
        $sessionSecure = config('session.secure');
        if ($sessionSecure === true || $sessionSecure === null) {
            $this->line('   [OK] Secure session cookies configured');
        } else {
            $this->warn('   [WARN] Secure session cookies not enabled');
            $this->line('   Update .env: SESSION_SECURE_COOKIE=true');
        }

        // Force HTTPS in middleware
        $this->line('   [INFO] Consider adding TrustProxies middleware for load balancers');
    }

    /**
     * Display SSL setup instructions
     */
    protected function displaySetupInstructions(): void
    {
        $this->line('   For Let\'s Encrypt (Certbot):');
        $this->line('   -----------------------------');
        $this->line('   1. Install Certbot: sudo apt install certbot python3-certbot-nginx');
        $this->line('   2. Obtain certificate: sudo certbot --nginx -d yourdomain.com');
        $this->line('   3. Auto-renewal: sudo certbot renew --dry-run');
        $this->newLine();

        $this->line('   For Apache:');
        $this->line('   ------------');
        $this->line('   1. Enable SSL module: sudo a2enmod ssl');
        $this->line('   2. Install certificate: sudo certbot --apache -d yourdomain.com');
        $this->newLine();

        $this->line('   After SSL is installed:');
        $this->line('   ------------------------');
        $this->line('   1. Update .env:');
        $this->line('      APP_URL=https://yourdomain.com');
        $this->line('      SESSION_SECURE_COOKIE=true');
        $this->newLine();
        $this->line('   2. Clear config cache:');
        $this->line('      php artisan config:cache');
        $this->newLine();
        $this->line('   3. Force HTTPS in web server or add middleware:');
        $this->line('      // In AppServiceProvider boot():');
        $this->line('      if (app()->environment(\'production\')) {');
        $this->line('          URL::forceScheme(\'https\');');
        $this->line('      }');
    }
}
