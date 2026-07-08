<?php

require_once __DIR__ . '/../vendor/autoload.php';

$GLOBALS['module_manager_test_base_path'] = $GLOBALS['module_manager_test_base_path'] ?? sys_get_temp_dir() . '/module-manager-tests';
$GLOBALS['module_manager_test_config'] = $GLOBALS['module_manager_test_config'] ?? [];

if (!function_exists('base_path')) {
    function base_path($path = '')
    {
        $root = rtrim($GLOBALS['module_manager_test_base_path'], DIRECTORY_SEPARATOR);

        return $path === '' ? $root : $root . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        if ($key === null) {
            return $GLOBALS['module_manager_test_config'];
        }

        return $GLOBALS['module_manager_test_config'][$key] ?? $default;
    }
}

if (!class_exists('File')) {
    class File
    {
        public static function exists($path): bool
        {
            return file_exists($path);
        }

        public static function isFile($path): bool
        {
            return is_file($path);
        }

        public static function get($path, $lock = false): string
        {
            return file_get_contents($path);
        }

        public static function put($path, $contents): int|false
        {
            $directory = dirname($path);
            self::ensureDirectory($directory);

            return file_put_contents($path, $contents);
        }

        public static function directories($path): array
        {
            if (!is_dir($path)) {
                return [];
            }

            $directories = glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];

            sort($directories);

            return $directories;
        }

        public static function glob($pattern): array
        {
            return glob($pattern) ?: [];
        }

        public static function makeDirectory($path, $mode = 0777, $recursive = false, $force = false): bool
        {
            if (is_dir($path)) {
                return true;
            }

            self::ensureDirectory($path, (int) $mode, (bool) $recursive);

            return true;
        }

        public static function delete($paths): bool
        {
            foreach ((array) $paths as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }

            return true;
        }

        private static function ensureDirectory(string $directory, int $mode = 0777, bool $recursive = true): void
        {
            if ($directory === '' || is_dir($directory)) {
                return;
            }

            if (!mkdir($directory, $mode, $recursive) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }
    }
}
