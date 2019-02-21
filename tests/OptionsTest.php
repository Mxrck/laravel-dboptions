<?php
namespace Nitro\Options\Tests;

use Nitro\Options\Facades\Options;

class OptionsTest extends BaseTestCase
{

    public function test_instance()
    {
        $this->assertInstanceOf(Options::class, option());
    }

    public function test_updateString()
    {
        $key            = 'update_test_helper_key';
        $value          = 'update_test_helper_value';
        $value_response = option()->update($key, $value);
        $this->assertEquals($value, $value_response);
        $this->assertDatabaseHas('options', ['key' => $key]);
    }

    public function test_get()
    {
        $key    = 'update_test_helper_key';
        $value  = 'update_test_helper_value_updated';
        option()->update($key, $value);
        $response = option($key);
        $this->assertEquals($value, $response);
    }

    public function test_getArray()
    {
        $key    = 'update_test_helper_key_array';
        $value  = ['one', 'two', 'three'];
        option()->update($key, $value);
        $response = option($key);
        $this->assertEquals($value, $response);
    }

    public function test_getNumbers()
    {
        $key    = 'update_test_helper_key_numbers';
        $value  = 123;
        option()->update($key, $value);
        $response = option($key);
        $this->assertEquals($value, $response);
    }

    public function test_remove()
    {
        $key    = 'update_test_helper_key_remove';
        $value  = 'somerandomvalue';
        option()->update($key, $value);
        option()->remove($key);
        $this->assertNull(option($key));
    }

    public function test_exists()
    {
        $key    = 'update_test_helper_key_exists';
        $value  = 'somerandomvalue';
        $notExists = option_exists($key);
        $this->assertFalse($notExists);

        $notExists = option()->exists($key);
        $this->assertFalse($notExists);

        option()->update($key, $value);
        $exists = option()->exists($key);
        $this->assertTrue($exists);

        $exists = option_exists($key);
        $this->assertTrue($exists);
    }

    public function test_public()
    {
        $key    = 'update_test_helper_key_public';
        $key2   = 'update_test_helper_key_public_2';
        $value  = 'i am a public option';

        option()->update($key, $value, ['public' => true]);
        $public = option()->public();
        $this->assertCount(1, $public);

        option()->update($key2, $value, ['public' => true]);
        $public = option()->public();
        $this->assertCount(2, $public);
    }

    public function test_javascript()
    {
        $key    = 'update_test_helper_key_public';
        $key2   = 'update_test_helper_key_public_2';
        $value  = 'i am a public option';

        option()->update($key, $value, ['public' => true]);
        option()->update($key2, $value, ['public' => true]);

        $javascript = option()->javascript();
        $this->assertRegExp('~\<script\s*.*\>\s*var\s*(.+?)\s*=\s*\{.*\}\</script\>~', $javascript);
    }
}
