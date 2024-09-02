<?php

namespace App\Console\Commands;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Illuminate\Console\Command;

class TestSQSConnection extends Command
{
    protected $signature = 'test:sqs-connection';

    protected $description = 'Test the connection to AWS SQS';

    public function handle()
    {
        $this->info('Testing SQS connection...');

        try {
            $client = new SqsClient([
                'version' => 'latest',
                'region' => config('queue.connections.sqs.region'),
                'credentials' => [
                    'key' => config('queue.connections.sqs.key'),
                    'secret' => config('queue.connections.sqs.secret'),
                ],
            ]);

            // Log the credentials being used (be careful not to expose these in production)
            $this->info('Using credentials:');
            $this->info('Key: '.substr(config('queue.connections.sqs.key'), 0, 5).'...');
            $this->info('Secret: '.substr(config('queue.connections.sqs.secret'), 0, 5).'...');
            $this->info('Region: '.config('queue.connections.sqs.region'));

            $result = $client->listQueues();
            $this->info('Connection successful!');
            $this->info('Available queues:');
            foreach ($result->get('QueueUrls') as $queueUrl) {
                $this->line($queueUrl);
            }

            // Test sending a message
            $queueUrl = config('queue.connections.sqs.prefix').'/'.config('queue.connections.sqs.queue');
            $result = $client->sendMessage([
                'QueueUrl' => $queueUrl,
                'MessageBody' => 'Test message from Laravel',
            ]);
            $this->info('Test message sent successfully. MessageId: '.$result->get('MessageId'));

        } catch (AwsException $e) {
            $this->error('An error occurred:');
            $this->error($e->getMessage());
        }
    }
}
