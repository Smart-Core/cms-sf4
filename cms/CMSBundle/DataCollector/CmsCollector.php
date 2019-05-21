<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\DataCollector;

use Monolith\CMSBundle\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class CmsCollector extends DataCollector
{
    use ContainerAwareTrait;

    /**
     * CmsCollector constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        /**
        $this->data += [
            'method' => $request->getMethod(),
            'acceptable_content_types' => $request->getAcceptableContentTypes(),
        ];
         */
    }

    public function setRouterData($data)
    {
        $this->data['router_data'] = $data;

        $folders = [];
        if (isset($data['folders'])) {
            foreach ($data['folders'] as $folderId => $_dummy) {
                $folder = $this->container->get('cms.folder')->get($folderId);
                $folders[$folderId] = [
                    'id' => $folder->getId(),
                    'title' => $folder->getTitle(),
                    'description' => $folder->getDescription(),
                    'uri_part' => $folder->getUriPart(),
                    'uri' => $this->container->get('cms.folder')->getUri($folder),
                    'meta' => $folder->getMeta(),
                    'template_inheritable' => $folder->getTemplateInheritable(),
                    'template_self' => $folder->getTemplateSelf(),
                    'permissions' => $folder->getPermissionsCache(),
                ];
            }
        }

        $this->data['folders'] = $folders;
    }

    public function setNodes($data)
    {
        $nodes = [];
        /** @var Node $node */
        foreach ($data as $node) {
            $nodes[$node->getId()] = [
                'id' => $node->getId(),
                'module' => $node->getModuleShortName(),
                'controller' => $node->getModule().':'.$node->getController(),
                'params' => $node->getParams(),
                'description' => $node->getDescription(),
                'template' => $node->getTemplate(),
                'region' => $node->getRegionName(),
                'folder' => $node->getFolderId().': '.$node->getFolder()->getTitle(),
                'permissions' => $node->getPermissionsCache(),
            ];
        }

        $this->data['nodes'] = $nodes;
    }

    public function getFolders(): array
    {
        if (isset($this->data['folders'])) {
            return $this->data['folders'];
        }

        return [];
    }
    
    public function getNodes(): array
    {
        if (isset($this->data['nodes'])) {
            return $this->data['nodes'];
        }

        return [];
    }

    public function getRouterData()
    {
        return $this->data['router_data'];
    }

    public function reset()
    {
        $this->data = [];
    }

    public function getName()
    {
        return 'cms';
    }
}
