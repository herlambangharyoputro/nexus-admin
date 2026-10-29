<?php

namespace Nexus\Admin\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Nexus\Admin\Tests\TestCase;

class IndexComponentsTest extends TestCase
{
    private function visit(string $uri): void
    {
        $request = Request::create($uri);
        $request->setLaravelSession($this->app['session.store']);
        $this->app->instance('request', $request);
    }

    private function paginator(int $total, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $count = max(0, min($perPage, $total - ($page - 1) * $perPage));

        return new LengthAwarePaginator($count > 0 ? range(1, $count) : [], $total, $perPage, $page, [
            'path' => request()->url(),
        ]);
    }

    private const TEMPLATE = <<<'BLADE'
        <x-nexus::index-page title="Daftar Aset" :count="true" :paginator="$p"
            :columns="[['label' => 'Aset', 'sort' => 'nama'], 'Lokasi', ['label' => 'Aksi']]"
            create-url="/aset/create" create-label="Tambah Aset" empty="Belum ada data aset.">
            @foreach($p as $i)
                <tr><td>Aset {{ $i }}</td><td>Lobby</td><td><x-nexus::row-actions view="/aset/{{ $i }}" edit="/aset/{{ $i }}/edit" delete="/aset/{{ $i }}" /></td></tr>
            @endforeach
        </x-nexus::index-page>
        BLADE;

    public function test_index_page_renders_header_toolbar_table_and_pagination(): void
    {
        $this->visit('/aset');
        $html = $this->render(self::TEMPLATE, ['p' => $this->paginator(25)]);

        $this->assertStringContainsString('<h1 class="card-title">Daftar Aset <span class="page-card-count">25</span></h1>', $html);
        $this->assertStringContainsString('href="/aset/create"', $html);
        $this->assertMatchesRegularExpression('/<form method="GET" action="http:\/\/localhost\/aset" class="index-filter" role="search">/', $html);
        $this->assertStringContainsString('name="search"', $html);
        $this->assertStringNotContainsString('>Reset</a>', $html, 'Reset hanya saat ada filter.');
        $this->assertStringContainsString('data-stack-table', $html);
        $this->assertStringContainsString('data-col-role="primary"', $html);
        $this->assertStringContainsString('data-col-role="actions"', $html);
        $this->assertStringContainsString('Menampilkan 1–10 dari 25', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
    }

    public function test_sort_links_toggle_direction_and_reset_page(): void
    {
        $this->visit('/aset?sort=nama&dir=asc&page=2&search=meja');
        $html = $this->render(self::TEMPLATE, ['p' => $this->paginator(25, 2)]);

        $this->assertStringContainsString('aria-sort="ascending"', $html);
        $this->assertStringContainsString('th-sortable is-sorted', $html);
        // Klik lagi → desc, page dibuang, search dipertahankan.
        $this->assertMatchesRegularExpression('/href="http:\/\/localhost\/aset\?sort=nama&amp;dir=desc&amp;search=meja"/', $html);
        // Sort/dir dipertahankan saat mencari.
        $this->assertStringContainsString('<input type="hidden" name="sort" value="nama">', $html);
        // Pagination membawa query string.
        $this->assertMatchesRegularExpression('/href="http:\/\/localhost\/aset\?sort=nama&amp;dir=asc&amp;search=meja&amp;page=3"/', $html);
    }

    public function test_empty_state_distinguishes_no_data_from_no_match(): void
    {
        $this->visit('/aset');
        $empty = $this->render(self::TEMPLATE, ['p' => $this->paginator(0)]);
        $this->assertStringContainsString('Belum ada data aset.', $empty);
        $this->assertStringContainsString('Tambah Aset', $empty);
        $this->assertStringNotContainsString('<table', $empty);

        $this->visit('/aset?search=zzz');
        $noMatch = $this->render(self::TEMPLATE, ['p' => $this->paginator(0)]);
        $this->assertStringContainsString('Tidak ada data yang cocok dengan pencarian/filter.', $noMatch);
        $this->assertStringContainsString('Reset filter', $noMatch);
        $this->assertStringNotContainsString('Belum ada data aset.', $noMatch);
    }

    public function test_page_and_sort_params_do_not_count_as_filter(): void
    {
        $this->visit('/aset?page=1&sort=nama&dir=desc&search=');
        $html = $this->render(self::TEMPLATE, ['p' => $this->paginator(0)]);

        $this->assertStringContainsString('Belum ada data aset.', $html);
    }

    public function test_flash_messages_are_rendered(): void
    {
        $this->visit('/aset');
        $this->app['session.store']->put('success', 'Aset berhasil disimpan.');
        $html = $this->render(self::TEMPLATE, ['p' => $this->paginator(3)]);

        $this->assertStringContainsString('alert alert-success', $html);
        $this->assertStringContainsString('Aset berhasil disimpan.', $html);
    }

    public function test_legacy_simantara_column_format_is_accepted(): void
    {
        $this->visit('/aset');
        $html = $this->render(
            '<x-nexus::index-page :rows="[1]" :columns="[[\'Aset\',\'\',\'nama\'], [\'Aksi\',\'text-center\']]"><tr><td>a</td><td>b</td></tr></x-nexus::index-page>'
        );

        $this->assertStringContainsString('th-sortable', $html);
        $this->assertMatchesRegularExpression('/class="text-center cell-end"\s+data-col-role="actions"/', $html);
    }

    public function test_row_actions_menu(): void
    {
        $html = $this->render('<x-nexus::row-actions view="/a/1" edit="/a/1/edit" delete="/a/1" delete-confirm="Hapus A?"><a class="dropdown-item" href="/qr">QR</a></x-nexus::row-actions>');

        $this->assertStringContainsString('data-dropdown="float"', $html);
        $this->assertStringContainsString('aria-label="Aksi"', $html);
        $this->assertStringContainsString('href="/a/1"', $html);
        $this->assertStringContainsString('href="/a/1/edit"', $html);
        $this->assertStringContainsString('data-confirm="Hapus A?"', $html);
        $this->assertStringContainsString('name="_method" value="DELETE"', $html);
        $this->assertLessThan(strpos($html, 'action="/a/1"'), strpos($html, 'href="/qr"'), 'Item slot sebelum Hapus.');
    }

    public function test_row_actions_accepts_simantara_aliases(): void
    {
        $html = $this->render('<x-nexus::row-actions primary-href="/a/1" primary-label="Lihat" />');

        $this->assertStringContainsString('href="/a/1"', $html);
        $this->assertStringContainsString('Lihat', $html);
    }

    public function test_pagination_links_view_for_paginator_links(): void
    {
        $this->visit('/aset?q=x');
        $p = $this->paginator(30, 2)->withQueryString();
        $html = (string) $p->links('nexus::pagination.links');

        $this->assertStringContainsString('class="pagination"', $html);
        $this->assertStringContainsString('rel="prev"', $html);
        $this->assertStringContainsString('q=x', $html);
    }

    public function test_pagination_single_page_hides_buttons(): void
    {
        $html = $this->render('<x-nexus::pagination :current="1" :last="1" :from="1" :to="4" :total="4" />');

        $this->assertStringContainsString('Menampilkan 1–4 dari 4', $html);
        $this->assertStringNotContainsString('pagination-btns', $html);
    }

    /* ── Backward compat (deprecated tetap jalan) ───────────── */

    public function test_deprecated_datatable_and_table_actions_still_render(): void
    {
        $html = $this->render('<x-nexus::datatable title="Pengguna"><x-slot:head><th data-sort="n">Nama</th></x-slot:head><tr><td>A</td><td><x-nexus::table-actions edit="/u/1/edit" delete="/u/1" /></td></tr></x-nexus::datatable>');

        $this->assertStringContainsString('data-table-search', $html);
        $this->assertStringContainsString('data-sort-table', $html);
        $this->assertStringContainsString('aria-label="Edit"', $html);
        $this->assertStringContainsString('value="DELETE"', $html);
    }
}
