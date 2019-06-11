<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Command;

use Monolith\CMSBundle\Entity\Site;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Kernel;

class PingCommand extends Command
{
    use ContainerAwareTrait;

    protected static $defaultName = 'cms:ping';

    protected function configure(): void
    {
        $this
            ->setDescription('Ping')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $settings = $this->container->get('settings');

        $pkey = $settings->get('cms:project_key');

        if (empty($pkey)) {
            $e = $settings->findBy('cms', 'project_key');

            if ($e) {
                $e->setValue($this->generateRandomSecret());

                $settings->updateEntity($e);
            }
        }

        $modules = [];
        foreach ($this->container->get('cms.module')->all() as $name => $moduleBundle) {
            $composerJson = $moduleBundle->getPath().'\\composer.json';

            // Получение имени пакета, а затем поиск его версии в composer.lock
            if (file_exists($composerJson)) {
                // @todo
            }

            $modules[] = [
                'class' => $moduleBundle->getNamespace().'\\'.$name,
                'is_enabled' => $moduleBundle->isEnabled(),
                'version' => null,
            ];
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $sites = [];
        foreach ($em->getRepository(Site::class)->findAll() as $site) {
            $sites[] = [
                'name' => $site->getName(),
                'is_enabled' => $site->isEnabled(),
                'domain' => $site->getDomain()->getName(),
            ];
        }

        $php_version = PHP_VERSION;
        if (preg_match('~^(\d+(?:\.\d+)*)(.+)?$~', PHP_VERSION, $matches) && isset($matches[2])) {
            $php_version = $matches[1];
        }

        $data = [
            'project_key' => $pkey,
            'modules' => $modules,
            'sites' => $sites,
            'php' => $matches[0],
            'symfony' => Kernel::VERSION,
        ];

        $url = 'http://ping.smart-core.org/';

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        //curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,3);
        curl_setopt($ch,CURLOPT_TIMEOUT,3);
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query ($data));

        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }

    /**
     * @return string The randomly generated secret
     */
    protected function generateRandomSecret()
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return hash('sha1', openssl_random_pseudo_bytes(23));
        }

        return hash('sha1', uniqid(mt_rand(), true));
    }
}
