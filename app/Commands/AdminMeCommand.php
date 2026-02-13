<?php

declare(strict_types=1);

namespace App\Commands;

use App\Enums\TokenType;
use LaravelZero\Framework\Commands\Command;

class AdminMeCommand extends AmazeeAIBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'admin:me';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get information about the current admin user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->initializeClient(TokenType::ADMIN_TOKEN);
            $response = $this->client->getMe();
            $this->table(
                ['Field', 'Value'],
                [
                    ['Email', $response['email']],
                    ['ID', $response['id']],
                    ['Is Active', $response['is_active'] ? 'Yes' : 'No'],
                    ['Is Admin', $response['is_admin'] ? 'Yes' : 'No'],
                ]
            );
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }
}
