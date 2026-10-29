<?php

namespace Nexus\Admin\Tests\Feature;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Nexus\Admin\Tests\TestCase;

class LayoutExtensionTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/nexus-views-' . uniqid();
        File::ensureDirectoryExists($this->dir . '/nexus');
        $this->app['view']->addLocation($this->dir);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dir);
        parent::tearDown();
    }

    private function page(): string
    {
        File::put($this->dir . '/halaman.blade.php', "@extends('nexus::layouts.app')\n@section('page-title', 'Aset')\n@section('content')\nISI\n@endsection");

        return view('halaman')->render();
    }

    public function test_defaults_render_without_consumer_files(): void
    {
        $html = $this->page();

        $this->assertStringContainsString('Nexus Admin', $html);
        $this->assertStringContainsString('class="sidebar-nav"', $html);
        $this->assertStringContainsString('data-action="notifications"', $html);
        $this->assertStringContainsString('Andi Pratama', $html, 'Tamu → data contoh untuk demo.');
        $this->assertStringContainsString('id="nexusConfirm"', $html);
    }

    public function test_consumer_extension_files_replace_defaults(): void
    {
        File::put($this->dir . '/nexus/brand.blade.php', '<div class="logo-text">SIMANTARA</div>');
        File::put($this->dir . '/nexus/sidebar-nav.blade.php', '<a class="nav-item" href="/aset">Aset Saya</a>');
        File::put($this->dir . '/nexus/topbar-actions.blade.php', '<span id="lonceng">5</span>');
        File::put($this->dir . '/nexus/user-menu.blade.php', '<a class="dropdown-item" href="/profil">Profil &amp; Password</a>');
        File::put($this->dir . '/nexus/head.blade.php', '<link rel="icon" href="/fav.ico">');

        $html = $this->page();

        $this->assertStringContainsString('SIMANTARA', $html);
        $this->assertStringContainsString('Aset Saya', $html);
        $this->assertStringNotContainsString('/nexus-product', $html, 'Nav contoh tidak ikut.');
        $this->assertStringContainsString('id="lonceng"', $html);
        $this->assertStringNotContainsString('data-action="notifications"', $html);
        $this->assertStringContainsString('Profil &amp; Password', $html);
        $this->assertStringContainsString('<link rel="icon" href="/fav.ico">', $html);
    }

    public function test_topbar_uses_authenticated_user_and_post_logout(): void
    {
        Route::post('/logout', fn () => 'ok')->name('logout');
        $user = new User();
        $user->forceFill(['name' => 'Siti Aminah', 'role_label' => 'Operator']);
        $this->actingAs($user);

        $html = $this->page();

        $this->assertStringContainsString('Siti Aminah', $html);
        $this->assertStringContainsString('Operator', $html);
        $this->assertStringContainsString('>SA</div>', $html);
        $this->assertStringNotContainsString('Andi Pratama', $html);
        $this->assertMatchesRegularExpression('/<form method="POST" action="http:\/\/localhost\/logout">/', $html);
    }
}
