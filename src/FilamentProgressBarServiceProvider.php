<?php

namespace MarceloAnjosDev\FilamentProgressBar;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use MarceloAnjosDev\FilamentProgressBar\Commands\FilamentProgressBarCommand;
use MarceloAnjosDev\FilamentProgressBar\Progress\CacheProgressRepository;
use MarceloAnjosDev\FilamentProgressBar\Progress\ProgressManager;
use MarceloAnjosDev\FilamentProgressBar\Testing\TestsFilamentProgressBar;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentProgressBarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-progress-bar';

    public static string $viewNamespace = 'filament-progress-bar';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('marceloanjosdev/filament-progress-bar');
            })
            ->hasRoutes('web')
            ->hasConfigFile();

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }

    }

    public function packageRegistered(): void
    {

        $this->app->singleton(CacheProgressRepository::class, function ($app) {
            /** @var CacheFactory $cache */
            $cache = $app->make(CacheFactory::class);

            $store = config('filament-progress-bar.cache_store');

            return new CacheProgressRepository(
                cache: $store ? $cache->store($store) : $cache->store(),
                prefix: (string) config('filament-progress-bar.key_prefix', 'filament-progress-bar'),
                ttlSeconds: (int) config('filament-progress-bar.ttl_seconds', 3600),
            );
        });

        $this->app->singleton(ProgressManager::class, fn ($app) => new ProgressManager(
            $app->make(CacheProgressRepository::class),
        ));
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-progress-bar/{$file->getFilename()}"),
                ], 'filament-progress-bar-stubs');
            }
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_BEFORE,
            fn (): string => view('filament-progress-bar::progress-bars')->render(),
        );

        // Testing
        Testable::mixin(new TestsFilamentProgressBar);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'marceloanjosdev/filament-progress-bar';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-progress-bar', __DIR__ . '/../resources/dist/components/filament-progress-bar.js'),
            Css::make('filament-progress-bar-styles', __DIR__ . '/../resources/dist/filament-progress-bar.css'),
            Js::make('filament-progress-bar-scripts', __DIR__ . '/../resources/dist/filament-progress-bar.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentProgressBarCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament-progress-bar_table',
        ];
    }
}
