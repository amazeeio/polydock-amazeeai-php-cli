<?php

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;

class AdminGetRegionCommand extends AmazeeAIBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'admin:get-region {id : The ID of the region}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get detailed information about a specific region (admin access)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $regionId = $this->argument('id');
            if (! $regionId) {
                $this->error('Region ID is required');

                return 1;
            }

            $this->initializeClient(TokenType::ADMIN_TOKEN);

            $region = $this->client->getRegion($regionId);

            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $region['id']],
                    ['Name', $region['name']],
                    ['Description', $region['description'] ?? 'N/A'],
                    ['Status', $region['status'] ?? 'active'],
                    ['Created At', $region['created_at'] ?? 'N/A'],
                    ['Updated At', $region['updated_at'] ?? 'N/A'],
                    ['Total Users', $region['total_users'] ?? 'N/A'],
                    ['Active Projects', $region['active_projects'] ?? 'N/A'],
                ]
            );

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
