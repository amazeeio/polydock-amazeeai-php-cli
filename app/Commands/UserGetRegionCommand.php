<?php

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;

class UserGetRegionCommand extends AmazeeAIBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'user:get-region {id : The ID of the region}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get details of a specific region';

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

            $this->initializeClient(TokenType::USER_TOKEN);

            $region = $this->client->getRegion($regionId);

            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $region['id']],
                    ['Name', $region['name']],
                    ['Description', $region['description'] ?? 'N/A'],
                    ['Status', $region['status'] ?? 'active'],
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
