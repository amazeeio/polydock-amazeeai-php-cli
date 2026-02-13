<?php

declare(strict_types=1);

namespace App\Commands;

use App\Enums\TokenType;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;

class UserListRegionsCommand extends AmazeeAIBaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'user:list-regions';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List available regions for the current user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->initializeClient(TokenType::USER_TOKEN);

            $regions = $this->client->listRegions();

            if (empty($regions)) {
                $this->info('No regions available');

                return 0;
            }

            $this->table(
                ['ID', 'Name', 'Description', 'Status'],
                array_map(fn ($region) => [
                    $region['id'],
                    $region['name'],
                    $region['description'] ?? 'N/A',
                    $region['status'] ?? 'active',
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
