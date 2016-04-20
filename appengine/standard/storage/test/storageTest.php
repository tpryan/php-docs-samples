<?php
/**
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
use Silex\WebTestCase;

class storageTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../app.php';

        // set some parameters for testing
        $app['session.test'] = true;
        $app['debug'] = true;

        // prevent HTML error exceptions
        unset($app['exception_handler']);

        // the bucket name doesn't matter because we mock the stream wrapper
        $app['bucket_name'] = 'test-bucket';

        // register the mock stream handler
        if (!in_array('gs', stream_get_wrappers())) {
            stream_wrapper_register('gs',
                '\google\appengine\ext\cloud_storage_streams\CloudStorageStreamWrapper');
        }

        return $app;
    }

    public function testHome()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testRead()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/file.txt');
        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $fileTxt = file_get_contents(__DIR__ . '/../file.txt');

        $this->assertEquals($response->getContent(), $fileTxt);
    }

    public function testWrite()
    {
        $client = $this->createClient();

        $time = date('Y-m-d H:i:s');
        $crawler = $client->request('POST', '/write', [
            'content' => sprintf('doot doot (%s)', $time),
        ]);

        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());

        $crawler = $client->followRedirect();
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($time, $response->getContent());
    }

    public function testWriteStream()
    {
        $client = $this->createClient();

        $time = date('Y-m-d H:i:s');
        $crawler = $client->request('POST', '/write/stream', [
            'content' => sprintf('doot doot (%s)', $time),
        ]);

        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());

        $crawler = $client->followRedirect();
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($time, $response->getContent());
    }
}
