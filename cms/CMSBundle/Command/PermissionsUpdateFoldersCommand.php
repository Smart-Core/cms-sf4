<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PermissionsUpdateFoldersCommand extends Command
{
    protected static $defaultName = 'cms:permissions:update-folders';

    protected function configure(): void
    {
        $this
            ->setDescription('Update all folders permissions with default value from user groups.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // @todo предупрежения, подтверждение, инвалидация кеша

        $this->getContainer()->get('cms.security')->updateAllFoldersByDefaults();
    }
}
