<?php

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;
use LaravelZero\Framework\Commands\Command;

class AdminCreateUserCommand extends AmazeeAIBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

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

        $password = $this->argument('password');
        if (! $password) {
            $this->error('No password provided');

            return;
        }

        try {
            $this->initializeClient(TokenType::ADMIN_TOKEN);

            $existingUsers = $this->client->searchUsers($email);
            if (count($existingUsers) > 0) {
                $this->error('A user with this email already exists');

                return;
            }

            $response = $this->client->createUser($email, $password);
            $this->info('User created successfully!');
            $this->table(['Field', 'Value'], [
                ['Email', $response['email']],
                ['ID', $response['id']],
            ]);
        } catch (HttpException $e) {
            $this->error(sprintf(
                'HTTP Error %d: %s',
                $e->getStatusCode(),
                json_encode($e->getResponse(), JSON_PRETTY_PRINT)
            ));

            return;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return;
        }
    }
}
