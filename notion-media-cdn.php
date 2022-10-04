<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use Ahc\Cli\Application;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

// Build container and use DI.
$builder = new DI\ContainerBuilder();
$container = $builder->build();

$app = new Application('Notion media CDN', '0.0.1');

$app
    ->command('media:upload', 'Upload a media item')
    ->argument('<directory>', 'Directory to upload the file to')
    ->argument('<mediaUrl>', 'he URL of the media item to add to the CDN')
    ->action(function (string $directory, string $mediaUrl) use ($container) {
        $client = $container->get(Client::class);

        $filename = Uuid::uuid4() . '.' . pathinfo(parse_url($mediaUrl, PHP_URL_PATH), PATHINFO_EXTENSION);;
        file_put_contents(__DIR__ . '/' . $directory . '/' . $filename, $client->get($mediaUrl)->getBody()->getContents());
    });

$app->handle($_SERVER['argv']);