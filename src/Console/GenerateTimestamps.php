<?php

namespace Tnt\Blog\Console;

use Oak\Console\Command\Command;
use Oak\Console\Command\Signature;
use Oak\Contracts\Console\InputInterface;
use Oak\Contracts\Console\OutputInterface;

use Tnt\Blog\Model\BlogPost;

class GenerateTimestamps extends Command
{
    protected function createSignature(Signature $signature): Signature
    {
        return $signature->setName('blog:timestamps')
            ->setDescription('Generate timestamps based on publication_date & publication_hour for existing Blog Posts.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $posts = BlogPost::all();

        $output->writeLine('Looping trough Blog Posts (' . $posts->count() . ')..', OutputInterface::TYPE_INFO);
        $output->newline();

        foreach ($posts as $post) {
            $post->publication_timestamp = $post->publication_date;
            $post->save();

            $output->writeLine('Updated ' . $post, OutputInterface::TYPE_PLAIN);
        }

        $output->newline();
        $output->writeLine('Done.', OutputInterface::TYPE_INFO);
    }
}