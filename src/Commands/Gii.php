<?php


namespace Botiroff\Gii\Commands;

use Botiroff\Gii\Services\Api;
use Botiroff\Gii\Services\Web;
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
        $options = [1 => 'Api', 2 => 'Web', 3 => 'Console'];

        $selectedOption = $this->choice('What do you want to work towards?:', $options);
        $resourcePath = $this->ask("Type resource namespace:");

        $this->info('> You selected: ' . $selectedOption);
        if ($selectedOption == "Api")
            $this->api($resourcePath);

        if ($selectedOption == "Web")
            $this->web($resourcePath);

        if ($selectedOption == "Console")
            $this->info('Soon!');
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

    /**
     * @param string $resourcePath
     * @return void
     */
    public function web(string $resourcePath): void
    {
        (new Web($resourcePath))->addController();
        $this->info('> Controller generated!');

        (new Web($resourcePath))->addService();
        $this->info('> Service generated!');

        (new Web($resourcePath))->addRequest();
        $this->info('> Requests generated!');

        (new Web($resourcePath))->addViewModel();
        $this->info('> ViewModels generated!');

        (new Web($resourcePath))->addDTO();
        $this->info('> Data transfer objects generated!');
    }
}
