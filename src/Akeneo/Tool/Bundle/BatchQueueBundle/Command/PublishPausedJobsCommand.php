<?php

declare(strict_types=1);

namespace Akeneo\Tool\Bundle\BatchQueueBundle\Command;

use Akeneo\Tool\Component\BatchQueue\Queue\PublishPausedJobsToQueue;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright 2023 Akeneo SAS (https://www.akeneo.com)
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
final class PublishPausedJobsCommand extends Command
{
    protected static $defaultName = 'akeneo:batch:publish-paused-jobs';

    public function __construct(
        private readonly PublishPausedJobsToQueue $publishPausedJobsToQueue
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->publishPausedJobsToQueue->publishPausedJobs();

        return Command::SUCCESS;
    }
}
