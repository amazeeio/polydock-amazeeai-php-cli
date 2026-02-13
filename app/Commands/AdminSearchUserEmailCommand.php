<?php

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;
use LaravelZero\Framework\Commands\Command;

class AdminSearchUserEmailCommand extends AmazeeAIBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:search-user-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search for a user by email';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $email = $this->argument('email');
        if (! $email) {
            $this->error('No email provided');

            return;
        }

        try {
            $this->initializeClient(TokenType::ADMIN_TOKEN);
            $response = $this->client->searchUsers($email);
            $this->table(
                ['email', 'id', 'is_active', 'is_admin'],
                array_map(fn ($user) => [
                    $user['email'],
                    $user['id'],
                    $user['is_active'] ? 'Yes' : 'No',
                    $user['is_admin'] ? 'Yes' : 'No',
                ], $response)
            );
        } catch (HttpException $e) {
            $this->error(sprintf(
                'HTTP Error %d: %s',
                $e->getStatusCode(),
                json_encode($e->getResponse(), JSON_PRETTY_PRINT)
            ));
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return;
        }
    }
}
