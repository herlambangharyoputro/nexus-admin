<?php

namespace Nexus\Admin\Support;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Support\Arr;
use Illuminate\Support\ViewErrorBag;
use UnitEnum;

/**
 * Helper presentasi untuk komponen form.* — murni fungsi, tanpa state.
 *
 * Satu tempat untuk aturan yang dipakai lintas komponen agar id, kunci error,
 * dan nilai awal field selalu konsisten (lihat docs/rfc-0.5.md §3).
 */
final class Field
{
    /**
     * Nama input → kunci dot-notation untuk $errors / old().
     *   'items[0][nama]' → 'items.0.nama', 'tags[]' → 'tags'
     */
    public static function key(?string $name): ?string
    {
        if ($name === null || $name === '') {
            return null;
        }

        $key = preg_replace('/\[\]$/', '', $name);
        $key = str_replace(['[]', '[', ']'], ['', '.', ''], $key);

        return trim($key, '.');
    }

    /**
     * Nama input / kunci error → id elemen yang valid & stabil.
     *   'items[0][nama]' → 'items-0-nama', 'items.0.nama' → 'items-0-nama'
     */
    public static function id(?string $name): ?string
    {
        $key = self::key($name);

        if ($key === null) {
            return null;
        }

        return trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', $key), '-');
    }

    /**
     * Pesan error pertama untuk field (null bila tidak ada).
     *
     * @param  mixed  $errors  ViewErrorBag / MessageBag yang di-share Laravel
     */
    public static function error($errors, ?string $name): ?string
    {
        $key = self::key($name);

        // ViewErrorBag meneruskan first() ke bag "default" lewat __call.
        if ($errors instanceof ViewErrorBag) {
            $errors = $errors->getBag('default');
        }

        if ($key === null || ! $errors instanceof MessageBag) {
            return null;
        }

        $message = $errors->first($key);

        if ($message === '') {
            // Field array (mis. tags[]) → tampilkan juga error elemen (tags.0, ...).
            $message = $errors->first($key . '.*');
        }

        return $message === '' ? null : $message;
    }

    /** Apakah request ini membawa old input (setelah redirect()->withInput()). */
    public static function hasOldInput(): bool
    {
        $request = request();

        return $request->hasSession() && $request->session()->hasOldInput();
    }

    /** Apakah old input memuat kunci ini (nilai null/'' tetap dihitung ada). */
    public static function hasOld(?string $name): bool
    {
        $key = self::key($name);

        return $key !== null
            && self::hasOldInput()
            && Arr::has(request()->session()->getOldInput(), $key);
    }

    /**
     * Nilai awal field. Prioritas:
     *   1. old input untuk kunci ini (field yang dikosongkan user tidak diisi ulang)
     *   2. nilai eksplisit (prop value/selected — perilaku lama)
     *   3. nilai model dari form-page :model
     */
    public static function value(?string $name, $explicit = null, $model = null, bool $useOld = true)
    {
        $key = self::key($name);

        if ($useOld && self::hasOld($name)) {
            return request()->old($key);
        }

        if ($explicit !== null) {
            return $explicit;
        }

        if ($key !== null && $model !== null) {
            return data_get($model, $key);
        }

        return null;
    }

    /**
     * Normalisasi nilai untuk atribut value: enum → value, tanggal → format
     * sesuai type input, bool → 1/0.
     */
    public static function scalar($value, string $type = 'text')
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        if ($value instanceof DateTimeInterface) {
            return match ($type) {
                'date'           => $value->format('Y-m-d'),
                'datetime-local' => $value->format('Y-m-d\TH:i'),
                'time'           => $value->format('H:i'),
                'month'          => $value->format('Y-m'),
                default          => $value->format('Y-m-d H:i:s'),
            };
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return $value;
    }

    /** Apakah $value termasuk pilihan terpilih (string-compare, dukung array). */
    public static function selected($option, $selected): bool
    {
        $option = (string) self::scalar($option);

        if (is_iterable($selected)) {
            foreach ($selected as $item) {
                if ((string) self::scalar($item) === $option) {
                    return true;
                }
            }

            return false;
        }

        return $selected !== null && (string) self::scalar($selected) === $option;
    }
}
