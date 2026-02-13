<?php

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;
use Illuminate\Contracts\Container\BindingResolutionException;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputOption;

abstract class AmazeeAIBaseCommand extends Command
{
    protected ?string $token = null;

    protected Client $client;

    protected string $tokenFile = '.amazeeai-user.token';

    protected TokenType $tokenType = TokenType::USER_TOKEN; // Default to user token

    public function __construct()
    {
        parent::__construct();
        $this->addTokenOption();
    }

    protected function addTokenOption(): void
    {
        $this->addOption('token', 't', InputOption::VALUE_OPTIONAL, 'The API token to use');
    }

    protected function storeUserToken(string $token): void
    {
        file_put_contents($this->tokenFile, $token);
        chmod($this->tokenFile, 0600); // Secure the file
    }

    protected function clearUserToken(): void
    {
        if (file_exists($this->tokenFile)) {
            unlink($this->tokenFile);

            $this->info('User token cleared');
        }
    }

    protected function getUserToken(): ?string
    {
        if (! file_exists($this->tokenFile)) {
            return null;
        }

        return trim(file_get_contents($this->tokenFile));
    }

    protected function getToken(): string
    {
        // Command line token always takes precedence
        if ($this->option('token')) {
            $this->info('Using token from command line');

            return $this->option('token');
        }

        // For NO_TOKEN, return empty string
        if ($this->tokenType === TokenType::NO_TOKEN) {
            return '';
        }

        $runtimeToken = null;

        // If using user token, try to get it from file
        if ($this->tokenType === TokenType::USER_TOKEN) {
            $runtimeToken = $this->getUserToken();
            if ($runtimeToken) {
                $this->info('Using stored user token');
            }
        } elseif ($this->tokenType === TokenType::ADMIN_TOKEN) {
            // Use environment variable for admin token
            $this->info('Using token from environment variable');
            $runtimeToken = env('POLYDOCK_AMAZEEAI_ADMIN_TOKEN');
        }

        if (! $runtimeToken) {
            throw new \RuntimeException('No token available. Please login first or provide a token via --token option or POLYDOCK_AMAZEEAI_ADMIN_TOKEN environment variable.');
        }

        return $runtimeToken;
    }

    /**
     * @throws BindingResolutionException
     */
    protected function initializeClient(TokenType $tokenType = TokenType::USER_TOKEN): void
    {
        $this->tokenType = $tokenType;
        $this->token = $this->getToken();
        $this->client = app()->make(Client::class, ['token' => $this->token]);
    }
}
