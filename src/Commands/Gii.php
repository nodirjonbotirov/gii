<?php


namespace Botiroff\Gii\Commands;

use Botiroff\Gii\Services\Api;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Gii extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gii';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Service Repository Pattern code generator';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $options = [1 => 'Api', 2 => 'Web'];

        $selectedOption = $this->choice('What do you want to work towards?:', $options);
        $resourcePath = $this->ask("Type resource namespace:");

        $this->info('> You selected: ' . $selectedOption);
        if ($selectedOption == "Api")
            $this->api($resourcePath);
        return CommandAlias::SUCCESS;
    }

    /**
     * @param string $resourcePath
     * @return void
     */
    public function api(string $resourcePath): void
    {
        (new Api($resourcePath))->addController();
        $this->info('> Controller generated!');

        (new Api($resourcePath))->addService();
        $this->info('> Service generated!');

        (new Api($resourcePath))->addRequest();
        $this->info('> Requests generated!');

        (new Api($resourcePath))->addResource();
        $this->info('> Resources generated!');

        (new Api($resourcePath))->addDTO();
        $this->info('> Data transfer objects generated!');
    }


}
