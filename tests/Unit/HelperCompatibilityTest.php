<?php

namespace Mrabbani\Tests\Unit;

require_once __DIR__ . '/../../helpers/file.php';
require_once __DIR__ . '/../../helpers/helpers.php';

class HelperCompatibilityTest extends \PHPUnit\Framework\TestCase
{
    public function testGetBaseFolderReturnsParentDirectoryWithTrailingSlash(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'module-manager-');

        try {
            $expected = dirname($file) . '/';

            $this->assertSame($expected, get_base_folder($file));
        } finally {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function testJsonEncodePretifyUsesPrettyPrintedJson(): void
    {
        $this->assertSame(
            "{\n    \"name\": \"module\"\n}",
            json_encode_pretify(['name' => 'module'])
        );
    }
}
