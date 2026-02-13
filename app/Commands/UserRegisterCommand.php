<?php

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;

class UserRegisterCommand extends AmazeeAIBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:register {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a new user';

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
            $this->initializeClient(TokenType::NO_TOKEN);

            $response = $this->client->register($email, $password);
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
