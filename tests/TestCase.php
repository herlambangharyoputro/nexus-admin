<?php

namespace Nexus\Admin\Tests;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Nexus\Admin\NexusServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [NexusServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Sesi aktif di request agar old() & session() bekerja seperti di aplikasi.
        $this->app['request']->setLaravelSession($this->app['session.store']);
        $this->withErrors([]);
    }

    /** Share $errors seperti middleware ShareErrorsFromSession. */
    protected function withErrors(array $messages, string $bag = 'default'): static
    {
        $errors = (new ViewErrorBag)->put($bag, new MessageBag($messages));
        $this->app['view']->share('errors', $errors);

        return $this;
    }

    /** Simulasikan redirect()->withInput($input). */
    protected function withOld(array $input): static
    {
        $this->app['session.store']->flashInput($input);
        // flashInput menaruh ke _flash.new; old() membaca _old_input langsung.
        $this->app['session.store']->put('_old_input', $input);

        return $this;
    }

    /** Bandingkan HTML tanpa peduli spasi ganda antar-atribut. */
    protected function assertHtmlContains(string $needle, string $html, string $message = ''): void
    {
        $this->assertStringContainsString(self::squish($needle), self::squish($html), $message);
    }

    protected static function squish(string $html): string
    {
        return preg_replace(['/\s+/', '/\s+>/', '/>\s+</'], [' ', '>', '><'], $html);
    }

    protected function render(string $template, array $data = []): string
    {
        return Blade::render($template, $data, deleteCachedView: true);
    }
}
