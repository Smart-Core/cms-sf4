<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class CreateThemesSymlinkCommand extends Command
{
    use ContainerAwareTrait;

    protected static $defaultName = 'cms:themes:create-symlinks';

    protected function configure(): void
    {
        $this
            ->setDescription('Create symlinks from Themes publics dir to project public.')
            ->addOption('relative', null, InputOption::VALUE_NONE, 'Make relative symlinks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $themes = $this->container->get('cms.theme')->createSymlinks(true);

        if (empty($themes)) {
            return;
        }

        $style = new TableStyle();
        $style
            ->setVerticalBorderChars(' ', ' ')
            ->setDefaultCrossingChar(' ')
        ;

        $table = new Table($output);
        $table
            ->setHeaders(['Theme', 'Target', 'Method'])
            ->setStyle($style)
        ;

        foreach ($themes as $data) {
            $table->addRow([
                $data['theme'],
                $data['target'],
                $data['method'],
            ]);
        }

        $table->render();
    }
}
