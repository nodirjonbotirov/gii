<?php


namespace Botiroff\Gii\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Api
{
    public string $resource;
    public string $stubDirectory;
    public string $capitalizedPath;
    public string $capitalizedFullPath;
    public array $requestActions = ['index', 'store', 'update'];

    public function __construct(string $fullPath)
    {
        $capitalizedFullPath = $this->capitalizePath($fullPath);

        $this->resource = end($capitalizedFullPath);
        $this->capitalizedPath = implode('/', array_slice($capitalizedFullPath, 0, -1));
        $this->capitalizedFullPath = implode('/', $capitalizedFullPath);
        $this->stubDirectory = __DIR__ . "/../Stubs";
    }

    /**
     *  The controller has only two responsibilities: to receive requests and return responses.
     * @return void
     */
    public function addController(): void
    {
        $path = app_path("Http/Controllers/Api/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the controller directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/api-controller.stub');

        $replacers = [
            "{{controllerNamespace}}" => $this->asNamespace("Http/Controllers/Api"),
            "{{controllerClassName}}" => $this->resource . 'Controller',
        ];

        self::generateCode($stubContent, $replacers, 'Controller', $path);
    }

    public function addService(): void
    {
        $path = app_path("Services/Api/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the service directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/api-service.stub');

        $replacers = [
            "{{serviceNamespace}}" => $this->asNamespace("Services/Api"),
            "{{serviceClassName}}" => $this->resource . 'Service',
        ];

        self::generateCode($stubContent, $replacers, 'Service', $path);
    }

    public function addRequest(): void
    {
        $path = app_path("Http/Requests/Api/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the controller directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/api-request.stub');

        foreach ($this->requestActions as $action) {
            $methodName = $action == 'index' ? '' : ucfirst($action);
            $replacers = [
                "{{requestNamespace}}" => $this->asNamespace("Http/Requests/Api"),
                "{{requestClassName}}" => $this->resource . $methodName . 'Request',
            ];

            self::generateCode($stubContent, $replacers, $methodName . 'Request', $path);
        }

    }

    public function addResource(): void
    {
        $path = app_path("Http/Resources/Api/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the controller directory exists; if not, create it.

        $resourceStubContent = File::get($this->stubDirectory . '/api-resource.stub');

        $resourceReplacers = [
            "{{resourceNamespace}}" => $this->asNamespace("Http/Resources/Api"),
            "{{resourceClassName}}" => $this->resource . 'Resource',
            "{{collectionClassName}}" => $this->resource . 'Collection',
        ];

        self::generateCode($resourceStubContent, $resourceReplacers, 'Resource', $path);

        $collectionStubContent = File::get($this->stubDirectory . '/api-collection.stub');

        $collectionReplacers = [
            "{{collectionNamespace}}" => $this->asNamespace("Http/Resources/Api"),
            "{{collectionClassName}}" => $this->resource . 'Collection',
        ];

        self::generateCode($collectionStubContent, $collectionReplacers, 'Collection', $path);

    }

    public function addDTO(): void
    {
        $path = app_path("Data/Api/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the controller directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/api-dto.stub');

        foreach ($this->requestActions as $action) {
            $methodName = $action == 'index' ? '' : ucfirst($action);
            $replacers = [
                "{{dtoNamespace}}" => $this->asNamespace("Data/Api"),
                "{{dtoClassName}}" => $this->resource . $methodName . 'Data',
            ];

            self::generateCode($stubContent, $replacers, $methodName . 'Data', $path);
        }
    }

    private function capitalizePath(string $fullPath): array
    {
        $explodedArray = explode('/', $fullPath);
        return array_map(function ($element) {
            return Str::singular(ucfirst($element));
        }, $explodedArray);
    }

    private function asNamespace(string $path): string
    {
        return str_replace('/', '\\', "App/{$path}/{$this->capitalizedFullPath}");
    }

    private function generateCode(string $stub, array $replacers, string $suffix, string $path): void
    {
        $content = strtr($stub, $replacers);

        $resource = $path . '/' . $this->resource . $suffix . '.php';
        if (!File::exists($resource))
            File::put($resource, $content);
    }

}
