<?php
/**
 * Copyright 2015 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

# [START all]
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

// create the Silex application
$app = new Application();
$app->register(new TwigServiceProvider());
$app['twig.path'] = [ __DIR__ ];

$app->get('/', function () use ($app) {
    $my_bucket = $app['bucket_name'];
    if ($my_bucket == '<your-bucket-name>') {
        return 'Set <code>&lt;your-bucket-name&gt;</code> to the name of your '
            . 'cloud storage bucket in <code>index.php</code>';
    }
    if (!in_array('gs', stream_get_wrappers())) {
        return 'This application can only run in AppEngine or the Dev AppServer environment.';
    }

    $write = '';
    $stream = '';
    if (file_exists("gs://${my_bucket}/hello.txt")) {
        $write = file_get_contents("gs://${my_bucket}/hello.txt");
    }
    if (file_exists("gs://${my_bucket}/hello_stream.txt")) {
        $stream = file_get_contents("gs://${my_bucket}/hello_stream.txt");
    }

    return $app['twig']->render('storage.html.twig', [
        'write_content' => $write,
        'stream_content' => $stream,
    ]);
});

$app->get('/file.txt', function () use ($app) {
    $filePath = __DIR__ . '/file.txt';
    # [START read_simple]
    $fileContents = file_get_contents($filePath);
    # [END read_simple]
    return $fileContents;
});

$app->post('/write', function (Request $request) use ($app) {
    $newFileContent = $request->get('content');
    $my_bucket = $app['bucket_name'];
    # [START write_simple_options]
    $options = ['gs' => ['Content-Type' => 'text/plain']];
    $ctx = stream_context_create($options);
    # [START write_simple]
    file_put_contents("gs://${my_bucket}/hello.txt", $newFileContent, 0, $ctx);
    # [END write_simple]
    # [END write_simple_options]

    return $app->redirect('/');
});

$app->post('/write/stream', function (Request $request) use ($app) {
    $newFileContent = $request->get('content');
    $my_bucket = $app['bucket_name'];
    # [START write_stream]
    $fp = fopen("gs://${my_bucket}/hello_stream.txt", 'w');
    fwrite($fp, $newFileContent);
    fclose($fp);
    # [END write_stream]

    return $app->redirect('/');
});

return $app;

