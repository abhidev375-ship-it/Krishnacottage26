<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /**
     * Directory where page settings JSON files are persisted.
     */
    public static function getJsonStorageDirectory(): string
    {
        $dir = storage_path('app/settings');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
        return $dir;
    }

    /**
     * Resolve absolute file path for a relative settings file path.
     */
    public static function resolveJsonFilePath(string $relativePath): string
    {
        $isAbsolute = str_starts_with($relativePath, '/') ||
                      str_starts_with($relativePath, '\\') ||
                      (strlen($relativePath) > 2 && ctype_alpha($relativePath[0]) && $relativePath[1] === ':');

        if ($isAbsolute) {
            return $relativePath;
        }

        // Check in storage/app/
        $candidate1 = storage_path('app/' . ltrim($relativePath, '/\\'));
        if (File::exists($candidate1)) {
            return $candidate1;
        }

        // Check directly in storage/
        $candidate2 = storage_path(ltrim($relativePath, '/\\'));
        if (File::exists($candidate2)) {
            return $candidate2;
        }

        return $candidate1;
    }

    /**
     * Get setting value by key. Supports string, boolean, integer, json, and json_file paths.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        // 1. JSON file handler: stored as file path in DB
        if ($setting->type === 'json_file' || ($setting->type === 'json' && str_ends_with($setting->value, '.json'))) {
            $filePath = static::resolveJsonFilePath($setting->value);
            if (File::exists($filePath)) {
                $content = File::get($filePath);
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
            // Fallback to decoding value if file not found
            $inlineDecoded = json_decode($setting->value, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $inlineDecoded : $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => intval($setting->value),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set setting value by key.
     * Page settings (arrays, objects, or keys ending in _content/_settings or in page groups)
     * are automatically saved as pretty JSON files in storage/app/settings/{key}.json
     * and their relative file paths are stored in the database.
     */
    public static function set(string $key, mixed $value, string $group = 'general', ?string $description = null): self
    {
        $type = gettype($value);

        // Check if this should be stored as a JSON file
        $isPageSetting = ($type === 'array' || $type === 'object') ||
                         in_array($group, ['homepage', 'page_settings', 'cms', 'contact']) ||
                         str_ends_with($key, '_content') ||
                         str_ends_with($key, '_settings');

        if ($isPageSetting && ($type === 'array' || $type === 'object' || is_string($value))) {
            return static::setJsonFile($key, is_string($value) ? (json_decode($value, true) ?? ['content' => $value]) : (array) $value, $group, $description);
        }

        $encoded = match ($type) {
            'boolean' => $value ? '1' : '0',
            default => strval($value),
        };

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $encoded,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Explicitly save array/data as a JSON file and store the file path in DB.
     */
    public static function setJsonFile(string $key, array $data, string $group = 'page_settings', ?string $description = null): self
    {
        $dir = static::getJsonStorageDirectory();
        $fileName = $key . '.json';
        $fullPath = $dir . DIRECTORY_SEPARATOR . $fileName;
        $relativePath = 'settings/' . $fileName;

        // Save pretty JSON file
        File::put(
            $fullPath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $relativePath,
                'group' => $group,
                'type' => 'json_file',
                'description' => $description ?? "Page settings JSON file ({$relativePath})",
            ]
        );
    }
}
