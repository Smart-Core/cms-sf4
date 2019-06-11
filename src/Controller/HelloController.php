<?php

namespace App\Controller;

use Monolith\CMSBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello", name="hello")
     */
    public function index(Request $request, ContainerInterface $container)
    {
        $settings = $container->get('settings');

        $pkey = $settings->get('cms:project_key');

        if (empty($pkey)) {
            $e = $settings->findBy('cms', 'project_key');

            if ($e) {
                $e->setValue($this->generateRandomSecret());

                $settings->updateEntity($e);
            }
        }

        $modules = [];
        foreach ($container->get('cms.module')->all() as $name => $moduleBundle) {
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

        $em = $container->get('doctrine.orm.entity_manager');

        $sites = [];
        foreach ($em->getRepository(Site::class)->findAll() as $site) {
            $sites[] = [
                'name' => $site->getName(),
                'is_enabled' => $site->isEnabled(),
                'domain' => $site->getDomain()->getName(),
            ];
        }

        preg_match('~^(\d+(?:\.\d+)*)(.+)?$~', (string) PHP_VERSION, $matches);

        $data = [
            'project_key' => $pkey,
            'modules' => $modules,
            'sites' => $sites,
            'domain' => $request->server->get('SERVER_NAME'),
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
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));

        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->render('hello/index.html.twig', [
            'controller_name' => 'HelloController',
        ]);
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
