<?php

namespace App\Commands;

use App\Enums\TokenType;
use Illuminate\Contracts\Container\BindingResolutionException;

class HealthCommand extends AmazeeAIBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call the API Health Endpoint';

    /**
     * Execute the console command.
     *
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        $this->initializeClient(TokenType::NO_TOKEN);
        $response = $this->client->health();

        if (is_array($response) && isset($response['status'])) {
            if ($response['status'] === 'healthy') {
                $this->info('healthy');
            } else {
                $this->error($response['status']);
            }
        } else {
            $this->error(print_r($response, true));
        }
    }
}
