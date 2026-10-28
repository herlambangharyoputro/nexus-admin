<?php

namespace Nexus\Admin\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Nexus\Admin\Tests\TestCase;

class FormComponentsTest extends TestCase
{
    private function lokasi(array $attributes = []): Model
    {
        $model = new class extends Model {
            protected $guarded = [];
        };
        $model->forceFill(array_merge([
            'id' => 7, 'kode' => 'GD-A-01', 'nama' => 'Ruang Lobby', 'kategori' => 'umum',
            'bisa_dipinjam' => true, 'deskripsi' => 'Lantai dasar',
        ], $attributes));
        $model->exists = true;

        return $model;
    }

    /* ── form.group: error otomatis ─────────────────────────── */

    public function test_group_shows_error_automatically_from_name(): void
    {
        $this->withErrors(['nama' => ['Nama wajib diisi.']]);

        $html = $this->render('<x-nexus::form.group label="Nama" name="nama" required hint="Sesuai KTP"><x-nexus::form.input name="nama" /></x-nexus::form.group>');

        $this->assertHtmlContains('<p class="form-error" id="nama-error">Nama wajib diisi.</p>', $html);
        $this->assertStringNotContainsString('Sesuai KTP', $html, 'Error menggantikan hint.');
        $this->assertStringContainsString('for="nama"', $html);
        $this->assertStringContainsString('class="form-group has-error"', $html);
        $this->assertSame(1, substr_count($html, 'class="form-required"'), 'Penanda wajib tunggal.');
    }

    public function test_group_shows_hint_when_no_error(): void
    {
        $html = $this->render('<x-nexus::form.group label="Nama" name="nama" hint="Sesuai KTP"><x-nexus::form.input name="nama" /></x-nexus::form.group>');

        $this->assertHtmlContains('<p class="form-hint" id="nama-hint">Sesuai KTP</p>', $html);
        $this->assertStringContainsString('aria-describedby="nama-hint"', $html);
        $this->assertStringNotContainsString('aria-invalid', $html);
    }

    public function test_input_is_invalid_with_aria_when_error(): void
    {
        $this->withErrors(['nama' => ['Nama wajib diisi.']]);

        $html = $this->render('<x-nexus::form.group label="Nama" name="nama" required><x-nexus::form.input name="nama" /></x-nexus::form.group>');

        $this->assertMatchesRegularExpression('/<input[^>]+class="form-input is-invalid"/', $html);
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertStringContainsString('aria-describedby="nama-error"', $html);
        $this->assertStringContainsString('aria-required="true"', $html);
        $this->assertStringContainsString('id="nama"', $html);
    }

    public function test_nested_array_names_map_to_dot_keys_and_ids(): void
    {
        $this->withErrors(['items.0.qty' => ['Jumlah minimal 1.']]);

        $html = $this->render('<x-nexus::form.group label="Qty" name="items[0][qty]"><x-nexus::form.input name="items[0][qty]" type="number" /></x-nexus::form.group>');

        $this->assertStringContainsString('id="items-0-qty"', $html);
        $this->assertStringContainsString('id="items-0-qty-error"', $html);
        $this->assertStringContainsString('Jumlah minimal 1.', $html);
        $this->assertStringContainsString('form-input is-invalid', $html);
    }

    /* ── Backward compat: prop lama ─────────────────────────── */

    public function test_legacy_error_prop_still_works_and_wins(): void
    {
        $this->withErrors(['email' => ['Dari bag.']]);

        $html = $this->render('<x-nexus::form.group label="Email" for="email" error="Email tidak valid"><x-nexus::form.input id="email" name="email" /></x-nexus::form.group>');

        $this->assertStringContainsString('Email tidak valid', $html);
        $this->assertStringNotContainsString('Dari bag.', $html);
        $this->assertStringContainsString('for="email"', $html);
        $this->assertStringContainsString('form-input is-invalid', $html, 'Input membaca error group via @aware.');
    }

    public function test_legacy_errors_first_pattern_and_old_value_pattern(): void
    {
        $this->withErrors(['name' => ['Nama wajib.']])->withOld(['name' => 'Budi']);

        $html = $this->render(
            '<x-nexus::form.group label="Nama" for="name" required :error="$errors->first(\'name\')">'
            . '<x-nexus::form.input id="name" name="name" :value="old(\'name\', $user->name ?? null)" />'
            . '</x-nexus::form.group>',
            ['user' => (object) ['name' => 'Lama']]
        );

        $this->assertStringContainsString('Nama wajib.', $html);
        $this->assertStringContainsString('value="Budi"', $html);
    }

    public function test_group_without_name_renders_like_before(): void
    {
        $html = $this->render('<x-nexus::form.group label="Catatan" full><x-nexus::form.textarea rows="3" placeholder="Tulis" /></x-nexus::form.group>');

        $this->assertStringContainsString('class="form-group full"', $html);
        $this->assertStringContainsString('<label class="form-label" >', preg_replace('/\s+/', ' ', $html));
        $this->assertStringNotContainsString('form-error', $html);
    }

    public function test_error_false_disables_automatic_error(): void
    {
        $this->withErrors(['nama' => ['Tidak boleh tampil.']]);

        $html = $this->render('<x-nexus::form.group label="Nama" name="nama" :error="false"><x-nexus::form.input name="nama" /></x-nexus::form.group>');

        $this->assertStringNotContainsString('Tidak boleh tampil.', $html);
    }

    /* ── old() + fallback model ─────────────────────────────── */

    public function test_input_prefers_old_then_value_then_model(): void
    {
        $model = $this->lokasi();

        $fromModel = $this->render('<x-nexus::form-page action="/x" :model="$m"><x-nexus::form.input name="nama" /></x-nexus::form-page>', ['m' => $model]);
        $this->assertStringContainsString('value="Ruang Lobby"', $fromModel);

        $fromValue = $this->render('<x-nexus::form-page action="/x" :model="$m"><x-nexus::form.input name="nama" value="Eksplisit" /></x-nexus::form-page>', ['m' => $model]);
        $this->assertStringContainsString('value="Eksplisit"', $fromValue);

        $this->withOld(['nama' => 'Dari Old']);
        $fromOld = $this->render('<x-nexus::form-page action="/x" :model="$m"><x-nexus::form.input name="nama" value="Eksplisit" /></x-nexus::form-page>', ['m' => $model]);
        $this->assertStringContainsString('value="Dari Old"', $fromOld);
    }

    public function test_cleared_field_is_not_refilled_from_model(): void
    {
        $this->withOld(['nama' => null, 'kode' => 'X']);

        $html = $this->render('<x-nexus::form-page action="/x" :model="$m"><x-nexus::form.input name="nama" /></x-nexus::form-page>', ['m' => $this->lokasi()]);

        $this->assertStringNotContainsString('Ruang Lobby', $html);
    }

    public function test_password_is_never_refilled(): void
    {
        $this->withOld(['password' => 'rahasia']);

        $html = $this->render('<x-nexus::form.input name="password" type="password" />');

        $this->assertStringNotContainsString('rahasia', $html);
    }

    public function test_date_input_formats_carbon_value(): void
    {
        $html = $this->render('<x-nexus::form-page action="/x" :model="$m"><x-nexus::form.input name="tanggal" type="date" /></x-nexus::form-page>', [
            'm' => ['tanggal' => \Illuminate\Support\Carbon::parse('2026-03-04 10:00:00')],
        ]);

        $this->assertStringContainsString('value="2026-03-04"', $html);
    }

    public function test_select_and_textarea_use_old_and_model(): void
    {
        $model = $this->lokasi();
        $tpl = '<x-nexus::form-page action="/x" :model="$m">'
            . '<x-nexus::form.select name="kategori" :options="[\'umum\' => \'Umum\', \'lab\' => \'Lab\']" placeholder="Pilih" />'
            . '<x-nexus::form.textarea name="deskripsi" />'
            . '</x-nexus::form-page>';

        $html = $this->render($tpl, ['m' => $model]);
        $this->assertMatchesRegularExpression('/<option value="umum"\s+selected/', $html);
        $this->assertStringContainsString('>Lantai dasar</textarea>', $html);

        $this->withOld(['kategori' => 'lab', 'deskripsi' => 'Baru']);
        $html = $this->render($tpl, ['m' => $model]);
        $this->assertMatchesRegularExpression('/<option value="lab"\s+selected/', $html);
        $this->assertStringContainsString('>Baru</textarea>', $html);
    }

    public function test_select_legacy_selected_prop_and_multiple(): void
    {
        $html = $this->render('<x-nexus::form.select name="role" :options="[\'admin\'=>\'Admin\',\'staff\'=>\'Staff\']" selected="staff" />');
        $this->assertMatchesRegularExpression('/<option value="staff"\s+selected/', $html);

        $html = $this->render('<x-nexus::form.select name="tags[]" multiple :options="[1=>\'A\',2=>\'B\',3=>\'C\']" :selected="[1,3]" />');
        $this->assertMatchesRegularExpression('/<option value="1"\s+selected/', $html);
        $this->assertMatchesRegularExpression('/<option value="3"\s+selected/', $html);
        $this->assertDoesNotMatchRegularExpression('/<option value="2"\s+selected/', $html);
        $this->assertStringContainsString('id="tags"', $html);
    }

    public function test_textarea_legacy_slot_is_rendered_raw(): void
    {
        $html = $this->render('<x-nexus::form.textarea name="bio">Halo &amp; dunia</x-nexus::form.textarea>');

        $this->assertStringContainsString('>Halo &amp; dunia</textarea>', $html);
    }

    /* ── Komponen baru ──────────────────────────────────────── */

    public function test_checkbox_reads_model_old_and_hidden_unchecked(): void
    {
        $tpl = '<x-nexus::form-page action="/x" :model="$m"><x-nexus::form.checkbox name="bisa_dipinjam" label="Boleh" unchecked="0" /></x-nexus::form-page>';

        $html = $this->render($tpl, ['m' => $this->lokasi()]);
        $this->assertStringContainsString('<input type="hidden" name="bisa_dipinjam" value="0">', $html);
        $this->assertMatchesRegularExpression('/type="checkbox"[^>]*checked/s', $html);

        // Setelah submit tanpa centang: old input berisi "0" → tidak tercentang.
        $this->withOld(['bisa_dipinjam' => '0']);
        $html = $this->render($tpl, ['m' => $this->lokasi()]);
        $this->assertDoesNotMatchRegularExpression('/type="checkbox"[^>]*checked/s', $html);
    }

    public function test_radio_group_selected_from_old_and_error(): void
    {
        $this->withErrors(['kondisi' => ['Pilih kondisi.']])->withOld(['kondisi' => 'rusak']);

        $html = $this->render('<x-nexus::form.group label="Kondisi" name="kondisi" required><x-nexus::form.radio-group name="kondisi" :options="[\'baik\'=>\'Baik\',\'rusak\'=>\'Rusak\']" inline /></x-nexus::form.group>');

        $this->assertStringContainsString('role="radiogroup"', $html);
        $this->assertStringContainsString('aria-labelledby="kondisi-label"', $html);
        $this->assertStringContainsString('id="kondisi-label"', $html);
        $this->assertMatchesRegularExpression('/value="rusak"\s+id="kondisi-1"\s+checked/', $html);
        $this->assertStringContainsString('form-radio-group inline is-invalid', $html);
        $this->assertStringContainsString('Pilih kondisi.', $html);
    }

    public function test_file_hint_and_preview(): void
    {
        $html = $this->render('<x-nexus::form.group label="Foto" name="foto"><x-nexus::form.file name="foto" accept="image/png" formats="JPG/PNG" max-size="3 MB" preview current="/img/a.jpg" /></x-nexus::form.group>');

        $this->assertStringContainsString('Format JPG/PNG, maks 3 MB.', $html);
        $this->assertStringContainsString('aria-describedby="foto-format"', $html);
        $this->assertStringContainsString('data-file-preview', $html);
        $this->assertStringContainsString('src="/img/a.jpg"', $html);
    }

    public function test_form_grid_cols(): void
    {
        $this->assertStringContainsString('class="form-grid form-grid-3"', $this->render('<x-nexus::form-grid cols="3">x</x-nexus::form-grid>'));
        $this->assertStringContainsString('class="form-grid form-grid-2"', $this->render('<x-nexus::form-grid cols="9">x</x-nexus::form-grid>'));
    }

    /* ── form-page ──────────────────────────────────────────── */

    public function test_form_page_create_uses_post_and_simpan(): void
    {
        $html = $this->render('<x-nexus::form-page action="/lokasi" title="Tambah Lokasi" cancel="/lokasi" size="sm">f</x-nexus::form-page>');

        $this->assertStringContainsString('method="POST"', $html);
        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringNotContainsString('name="_method"', $html);
        $this->assertStringContainsString('>Simpan</button>', $html);
        $this->assertStringContainsString('<a href="/lokasi" class="btn btn-outline">Batal</a>', $html);
        $this->assertStringContainsString('page-size-sm', $html);
        $this->assertStringContainsString('<h1 class="card-title">Tambah Lokasi</h1>', $html);
    }

    public function test_form_page_edit_spoofs_put_from_model(): void
    {
        $html = $this->render('<x-nexus::form-page action="/lokasi/7" :model="$m" files>f</x-nexus::form-page>', ['m' => $this->lokasi()]);

        $this->assertStringContainsString('<input type="hidden" name="_method" value="PUT">', $html);
        $this->assertStringContainsString('enctype="multipart/form-data"', $html);
        $this->assertStringContainsString('>Simpan Perubahan</button>', $html);
    }

    public function test_form_page_explicit_method_and_get(): void
    {
        $patch = $this->render('<x-nexus::form-page action="/x" method="patch">f</x-nexus::form-page>');
        $this->assertStringContainsString('value="PATCH"', $patch);

        $get = $this->render('<x-nexus::form-page action="/x" method="GET">f</x-nexus::form-page>');
        $this->assertStringContainsString('method="GET"', $get);
        $this->assertStringNotContainsString('_token', $get);
    }

    public function test_form_page_error_summary_links_to_fields(): void
    {
        $this->withErrors(['nama' => ['Nama wajib diisi.'], 'items.0.qty' => ['Qty salah.']]);

        $html = $this->render('<x-nexus::form-page action="/x">f</x-nexus::form-page>');

        $this->assertStringContainsString('data-error-summary', $html);
        $this->assertStringContainsString('<a href="#nama">Nama wajib diisi.</a>', $html);
        $this->assertStringContainsString('<a href="#items-0-qty">Qty salah.</a>', $html);
    }

    public function test_form_page_attributes_split_between_root_and_form(): void
    {
        $html = $this->render('<x-nexus::form-page action="/x" class="mb-5" id="frm" novalidate>f</x-nexus::form-page>');

        $this->assertMatchesRegularExpression('/<div class="page-pattern form-page page-size-md mb-5"/', $html);
        $this->assertMatchesRegularExpression('/<form[^>]+id="frm"[^>]*novalidate/s', $html);
    }
}
