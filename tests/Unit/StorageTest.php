<?php

use League\Flysystem\Filesystem;
use Sammyjo20\Saloon\Helpers\Storage;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Sammyjo20\Saloon\Exceptions\DirectoryNotFoundException;

test('it will throw an exception if the base directory does not exist', function () {
    new Storage('example');
})->throws(DirectoryNotFoundException::class, 'The directory "example" does not exist or is not a valid directory.');

test('you can check if a file exists', function () {
    $storage = new Storage('tests');

    expect($storage->exists('Pest.php'))->toBeTrue();
    expect($storage->missing('Pest.php'))->toBeFalse();
});

test('you can check if a file is missing', function () {
    $storage = new Storage('tests');

    expect($storage->exists('HelloWorld.php'))->toBeFalse();
    expect($storage->missing('HelloWorld.php'))->toBeTrue();
});

test('you can retrieve a file from storage', function () {
    $storage = new Storage('tests');

    $file = $storage->get('Pest.php');

    expect($file)->toEqual(file_get_contents('tests/Pest.php'));
});

test('you can put a file in storage', function () {
    $filesystem = new Filesystem(new LocalFilesystemAdapter('tests/Fixtures/Saloon'));
    $filesystem->deleteDirectory('/');
    $filesystem->createDirectory('/');

    $storage = new Storage('tests/Fixtures/Saloon');

    expect($storage->exists('example.txt'))->toBeFalse();

    $storage->put('example.txt', 'Hello World');

    expect($storage->exists('example.txt'))->toBeTrue();

    expect($storage->get('example.txt'))->toEqual('Hello World');
});

test('it will create a file with nested folders', function () {
    $filesystem = new Filesystem(new LocalFilesystemAdapter('tests/Fixtures/Saloon'));
    $filesystem->deleteDirectory('/');
    $filesystem->createDirectory('/');

    $path = 'some' . DIRECTORY_SEPARATOR . 'other' . DIRECTORY_SEPARATOR . 'directories' . DIRECTORY_SEPARATOR . 'example.txt';

    $storage = new Storage('tests' . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'Saloon');

    expect($storage->exists($path))->toBeFalse();

    $storage->put($path, 'Hello World');

    expect($storage->exists($path))->toBeTrue();

    expect($storage->get($path))->toEqual('Hello World');
});
