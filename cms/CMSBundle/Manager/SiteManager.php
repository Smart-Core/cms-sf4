<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Monolith\CMSBundle\Entity\Domain;
use Monolith\CMSBundle\Entity\Folder;
use Monolith\CMSBundle\Entity\Language;
use Monolith\CMSBundle\Entity\Site;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Filesystem\Filesystem;

class SiteManager
{
    use ContainerAwareTrait;

    protected $em;

    /**
     * SyslogManager constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @todo !!!
     * 1) Проверка на наличие таблиц в БД
     *    - Language
     *    - Domain
     *    - Folder
     *    - Site
     *    - User
     * 2) Проверка на существование дефолтной темы.
     */
    public function init()
    {
        //$output->write('Create Default Site Data:');

        $site = $this->em->getRepository('CMSBundle:Site')->findOneBy([]);

        if ($site instanceof Site) {
            return;
        }

        $user = $this->em->getRepository('App:User')->findOneBy([], ['id' => 'ASC']);

        //$output->write(' Language');

        $language = new Language();
        $language
            ->setCode('ru')
            ->setName('Русский')
            ->setUser($user)
        ;
        $em->persist($language);
        $em->flush($language);

        //$output->write(', Domain');

        $domain = new Domain();
        $domain
            ->setName('localhost')
            ->setUser($user)
            ->setLanguage($language)
        ;
        $em->persist($domain);
        $em->flush($domain);

        //$output->write(', Folder');
        $folder = new Folder();
        $folder
            ->setUser($user)
            ->setTitle('Главная')
        ;
        $em->persist($folder);
        $em->flush($folder);

        //$output->write(', Site');
        $site = new Site();
        $site
            ->setUser($user)
            ->setDomain($domain)
            ->setLanguage($language)
            ->setName('My new Site')
            ->setRootFolder($folder)
            ->setTheme('default')
        ;
        $em->persist($site);
        $em->flush($site);

        //$output->write(', Theme');
        $fileSystem = new Filesystem();
        //$fileSystem->mirror('src/Monolith/Bundle/CMSGeneratorBundle/Resources/skeleton/theme/', 'themes/default/');

        //$output->write(' <info>OK</info>'.PHP_EOL);
    }
}
