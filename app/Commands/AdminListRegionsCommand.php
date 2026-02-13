<?php

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;

class AdminListRegionsCommand extends AmazeeAIBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'admin:list-regions';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all regions (admin access)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->initializeClient(TokenType::ADMIN_TOKEN);

            $regions = $this->client->listRegions();

            if (empty($regions)) {
                $this->info('No regions available');

                return 0;
            }

            $this->table(
                ['ID', 'Name', 'Description', 'Status', 'Created At', 'Updated At'],
                array_map(fn ($region) => [
                    $region['id'],
                    $region['name'],
                    $region['description'] ?? 'N/A',
                    $region['status'] ?? 'active',
                    $region['created_at'] ?? 'N/A',
                    $region['updated_at'] ?? 'N/A',
                ], $regions)
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
