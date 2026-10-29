<?php

namespace Nexus\Admin\Tests\Feature;

use Illuminate\Support\Carbon;
use Nexus\Admin\Tests\TestCase;

class DetailComponentsTest extends TestCase
{
    public function test_detail_page_with_actions_aside_and_tabs(): void
    {
        $html = $this->render(<<<'BLADE'
            <x-nexus::detail-page title="Laptop Dell" description="AST-001" back="/aset">
                <x-slot:actions><a class="btn" href="/aset/1/edit">Edit</a></x-slot:actions>
                <x-nexus::description-list :items="['Kode' => 'AST-001', 'Lokasi' => null]" />
                <x-slot:aside><img src="/a.jpg" alt=""></x-slot:aside>
                <x-slot:tabs>Riwayat</x-slot:tabs>
            </x-nexus::detail-page>
            BLADE);

        $this->assertStringContainsString('class="page-pattern detail-page has-aside"', $html);
        $this->assertStringContainsString('<h1 class="card-title">Laptop Dell</h1>', $html);
        $this->assertStringContainsString('<a href="/aset" class="btn btn-outline">Kembali</a>', $html);
        $this->assertStringContainsString('href="/aset/1/edit"', $html);
        $this->assertStringContainsString('<aside class="page-aside">', $html);
        $this->assertStringContainsString('page-tabs', $html);
        $this->assertHtmlContains('<dt class="dl-label">Kode</dt>', $html);
        $this->assertHtmlContains('<dd class="dl-value is-empty">—</dd>', $html);
    }

    public function test_description_list_layout_cols_and_values(): void
    {
        $html = $this->render(<<<'BLADE'
            <x-nexus::description-list cols="3" layout="horizontal">
                <x-nexus::description-item label="Tanggal" :value="$d" />
                <x-nexus::description-item label="Aktif" :value="true" />
                <x-nexus::description-item label="Catatan" full><b>penting</b></x-nexus::description-item>
            </x-nexus::description-list>
            BLADE, ['d' => Carbon::parse('2026-01-05')]);

        $this->assertStringContainsString('class="dl dl-cols-3 dl-horizontal"', $html);
        $this->assertStringContainsString('05/01/2026', $html);
        $this->assertStringContainsString('Ya', $html);
        $this->assertStringContainsString('class="dl-item full"', $html);
        $this->assertStringContainsString('<b>penting</b>', $html);
    }
}
