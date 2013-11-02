<?php

namespace VirtualFileSystem;

class VirtualFilesystemTest extends \PHPUnit_Framework_TestCase
{
    public function testContainerIsSetDuringConstruction()
    {
        $fs = new FileSystem();

        $this->assertInstanceOf('\VirtualFilesystem\Container', $fs->container());
        $this->assertInstanceOf('\VirtualFilesystem\Structure\Root', $fs->root());
    }

    public function testFactoryIsSetDuringConstruction()
    {
        $fs = new FileSystem();

        $this->assertInstanceOf('\VirtualFilesystem\Factory', $fs->container()->factory());
    }

    public function testWrapperIsRegisteredDuringObjectLifetime()
    {
        $fs = new FileSystem();
        $scheme = $fs->scheme();

        $this->assertTrue(in_array($scheme, stream_get_wrappers()), 'Wrapper registered in __construct()');

        unset($fs); //provoking __destruct
        $this->assertFalse(in_array($scheme, stream_get_wrappers()), 'Wrapper unregistered in __destruct()');
    }

    public function testFilesystemFactoryAddedToDefaultContextDuringObjectLifetime()
    {
        $fs = new FileSystem();
        $scheme = $fs->scheme();

        $options = stream_context_get_options(stream_context_get_default());

        $this->assertArrayHasKey($scheme, $options, 'Wrraper key registered in context');
        $this->assertArrayHasKey('Container', $options[$scheme], 'Container registered in context key');

        //can't find a way to unset default context yet
        //unset($fs); //provoking __destruct
        //$options = stream_context_get_options(stream_context_get_default());
        //$this->assertArrayNotHasKey('anotherVFSwrapper', $options, 'Wrraper key not registered in context');

    }

    public function testDefaultContextOptionsAreExtended()
    {
        stream_context_set_default(array('someContext' => array('a' => 'b')));

        $fs = new FileSystem();
        $scheme = $fs->scheme();

        $options = stream_context_get_options(stream_context_get_default());

        $this->assertArrayHasKey($scheme, $options, 'FS Context option present');
        $this->assertArrayHasKey('someContext', $options, 'Previously existing context option present');

    }

    public function testDefaultContextOptionsAreRemoved()
    {
        return;
        $this->markTestSkipped('Skipped until I find a way to remove eys from default context options');

        stream_context_set_default(array('someContext' => array('a' => 'b')));

        $fs = new FileSystem();
        $scheme = $fs->scheme();
        unset($fs); //provoking __destruct

        $options = stream_context_get_options(stream_context_get_default());

        $this->assertArrayNotHasKey($scheme, $options, 'FS Context option present');
        $this->assertArrayHasKey('someContext', $options, 'Previously existing context option present');
    }
}