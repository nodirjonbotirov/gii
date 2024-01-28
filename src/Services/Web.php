<?php


namespace Botiroff\Gii\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Web
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
        $path = app_path("Http/Controllers/Web/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the controller directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/web-controller.stub');

        $replacers = [
            "{{controllerNamespace}}" => $this->asNamespace("Http/Controllers/Web"),
            "{{controllerClassName}}" => $this->resource . 'Controller',
        ];

        self::generateCode($stubContent, $replacers, 'Controller', $path);
    }

    public function addService(): void
    {
        $path = app_path("Services/Web/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the service directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/web-service.stub');

        $replacers = [
            "{{serviceNamespace}}" => $this->asNamespace("Services/Web"),
            "{{serviceClassName}}" => $this->resource . 'Service',
        ];

        self::generateCode($stubContent, $replacers, 'Service', $path);
    }

    public function addRequest(): void
    {
        $path = app_path("Http/Requests/Web/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the controller directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/web-request.stub');

        foreach ($this->requestActions as $action) {
            $methodName = $action == 'index' ? '' : ucfirst($action);
            $replacers = [
                "{{requestNamespace}}" => $this->asNamespace("Http/Requests/Web"),
                "{{requestClassName}}" => $this->resource . $methodName . 'Request',
            ];

            self::generateCode($stubContent, $replacers, $methodName . 'Request', $path);
        }

    }

    public function addViewModel(): void
    {
        $path = app_path("ViewModels/Web/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the controller directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/web-view-model.stub');

        foreach ($this->requestActions as $action) {
            $methodName = $action == 'index' ? '' : ucfirst($action);
            $replacers = [
                "{{viewModelNamespace}}" => $this->asNamespace("ViewModels/Web"),
                "{{viewModelClassName}}" => $this->resource . $methodName . 'ViewModel',
            ];

            self::generateCode($stubContent, $replacers, $methodName . 'ViewModel', $path);
        }

    }

    public function addDTO(): void
    {
        $path = app_path("Data/Web/") . $this->capitalizedFullPath;
        File::ensureDirectoryExists($path); // Check if the controller directory exists; if not, create it.

        $stubContent = File::get($this->stubDirectory . '/web-dto.stub');

        foreach ($this->requestActions as $action) {
            $methodName = $action == 'index' ? '' : ucfirst($action);
            $replacers = [
                "{{dtoNamespace}}" => $this->asNamespace("Data/Web"),
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
