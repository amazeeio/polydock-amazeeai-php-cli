<?php

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;

class AdminCreatePrivateAIKeysCommand extends AmazeeAIBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'admin:create-private-ai-keys
        {region : The ID of the region}
        {name : Name for the private AI keys record}
        {user-id : The ID of the user to associate the private AI keys with}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create new private AI keys for a specific region for a specific user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $regionId = $this->argument('region');
            $name = $this->argument('name');
            $userId = $this->argument('user-id');

            if (! $regionId) {
                $this->error('Region ID is required');

                return 1;
            }

            if (! $name) {
                $this->error('Name is required');

                return 1;
            }

            if (! $userId) {
                $this->error('User ID is required');

                return 1;
            }

            $this->initializeClient(TokenType::ADMIN_TOKEN);

            // First verify the region exists and is accessible
            try {
                $region = $this->client->getRegion($regionId);
                $this->info(sprintf('Creating private AI keys in region: %s', $region['name']));
            } catch (\Exception $e) {
                $this->error('Invalid or inaccessible region');

                return 1;
            }

            // Create the private AI keys
            $response = $this->client->createPrivateAIKeys($regionId, $name, $userId);

            $this->info('Private AI keys created successfully!');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $response['name']],
                    ['Region', $response['region']],
                    ['Database Name', $response['database_name']],
                    ['Database Host', $response['database_host']],
                    ['Database Username', $response['database_username']],
                    ['Database Password', $response['database_password']],
                    ['LiteLLM Token', $response['litellm_token']],
                    ['LiteLLM API URL', $response['litellm_api_url']],
                    ['Created At', $response['created_at']],
                    ['Owner ID', $response['owner_id']],
                ]
            );

            $this->warn('Important: Store these credentials securely - they cannot be retrieved later!');

            return 0;
        } catch (HttpException $e) {
            $this->error(sprintf(
                'HTTP Error %d: %s',
                $e->getStatusCode(),
                json_encode($e->getResponse(), JSON_PRETTY_PRINT)
            ));

            return 1;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }
    }
}
