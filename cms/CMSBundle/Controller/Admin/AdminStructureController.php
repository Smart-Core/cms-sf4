<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller\Admin;

use Monolith\CMSBundle\Entity\Folder;
use Monolith\CMSBundle\Entity\Node;
use Monolith\CMSBundle\Entity\Region;
use Monolith\CMSBundle\Entity\UserGroup;
use Monolith\CMSBundle\Form\Type\NodeDefaultPropertiesFormType;
use Monolith\CMSBundle\Form\Type\NodeSetupControllerFormType;
use Monolith\CMSBundle\Module\AbstractNodePropertiesFormType;
use Monolith\CMSBundle\Module\ModuleBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Smart\CoreBundle\Controller\Controller;
use Smart\CoreBundle\Form\TypeResolverTtait;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN_SYSTEM') or is_granted('ROLE_SUPER_ADMIN')")
 *
 * @Route("/structure")
 */
class AdminStructureController extends Controller
{
    use TypeResolverTtait;

    /**
     * New style structure.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/megastructure/", name="cms_admin.megastructure")
     */
    public function megastructureAction(Request $request): Response
    {
        $cmsFolder = $this->get('cms.folder');

        if (null === $cmsFolder->get(1)) {
            return $this->redirectToRoute('cms_admin.structure_folder_create');
        }

        $structureHash = $cmsFolder->getStructureHash();
        $structure     = $cmsFolder->getStructureArray();

        if ($request->isXmlHttpRequest() and $request->isMethod('POST')) {
            if ($request->request->has('action')) {
                if ($request->request->has('folder_id')) {
                    $folder = $cmsFolder->get((int) $request->request->get('folder_id'));
                    $destinationFolder = $cmsFolder->get((int) $request->request->get('destination_folder_id'));
                    $position = (int) $request->request->get('position');

                    $folder->setParentFolder($destinationFolder);
                    $folder->setPosition($position);

                    $cmsFolder->update($folder);

                    $structureHash = $cmsFolder->getStructureHash();

                    return new JsonResponse([
                        'structure_hash' => $structureHash,
                        'message' => 'Folder move succesful',
                    ]);
                } elseif ($request->request->has('node_id')) {
                    $node = $this->get('cms.node')->get((int) $request->request->get('node_id'));
                    $destinationFolder = $cmsFolder->get((int) $request->request->get('destination_folder_id'));
                    $position = (int) $request->request->get('position');

                    $node->setFolder($destinationFolder);
                    $node->setPosition($position);

                    $this->get('cms.node')->update($node);

                    $structureHash = $cmsFolder->getStructureHash();

                    return new JsonResponse([
                        'structure_hash' => $structureHash,
                        'message' => 'Node move succesful',
                    ]);
                } else {
                    return new JsonResponse([
                        'structure_hash' => $structureHash,
                        'error' => 'Не указан объект для действия',
                    ], 405);
                }
            }

            return new JsonResponse([
                'structure_hash' => $structureHash,
                'message' => 'test success',
            ]);
        }

        if ($request->isXmlHttpRequest() or $request->query->has('api')) {
            return new JsonResponse([
                'structure_hash' => $structureHash,
                'structure' => $structure,
            ]);
        }

        return $this->render('@CMS/Admin/Structure/mega_structure.html.twig', [
            'structure'      => $structure, // @todo remove
            'structure_hash' => $structureHash,
            'structure_json' => json_encode($structure, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/", name="cms_admin.structure")
     */
    public function structureAction(): Response
    {
        $site = $this->get('cms.context')->getSite();

        if (null === $site->getRootFolder()) {
            return $this->redirectToRoute('cms_admin.structure_folder_create');
        }

        return $this->render('@CMS/Admin/Structure/structure.html.twig');
    }

    /**
     * Создание папки.
     *
     * @param Request      $request
     * @param Folder|null  $parent
     *
     * @return Response|RedirectResponse
     *
     * @Route("/folder/create/", name="cms_admin.structure_folder_create")
     * @Route("/folder/create/{parent<\d+>}/", name="cms_admin.structure_folder_create_in_folder")
     */
    public function folderCreateAction(Request $request, Folder $parent = null): Response
    {
        $cmsFolder = $this->get('cms.folder');
        $site      = $this->get('cms.context')->getSite();

        if (empty($parent)) {
            $parent = $site->getRootFolder();

            if ($parent instanceof Folder) {
                $parent = $cmsFolder->get($parent->getId());
            }
        }

        $folder = $cmsFolder->create();
        $folder->setUser($this->getUser());

        if (empty($parent)) {
            $folder->setTitle($this->get('translator')->trans('Homepage'));
        } else {
            $folder->setParentFolder($parent);
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        foreach ($em->getRepository(UserGroup::class)->findBy(['is_default_folders_granted_read' => 1]) as $userGroup) {
            $folder->addGroupGrantedRead($userGroup);
        }

        foreach ($em->getRepository(UserGroup::class)->findBy(['is_default_folders_granted_write' => 1]) as $userGroup) {
            $folder->addGroupGrantedWrite($userGroup);
        }

        $form = $cmsFolder->createForm($folder, [
            'action' => $this->generateUrl('cms_admin.structure_folder_create'),
        ]);

        // Для корневой папки удаляются некоторые поля формы
        if (empty($parent)) {
            $form
                ->remove('uri_part')
                ->remove('parent_folder')
                ->remove('router_node_id')
                ->remove('is_active')
                ->remove('is_file')
                ->remove('pos');
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has('create')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    /** @var Folder $f */
                    $f = $form->getData();

                    $syslog = $this->get('cms.syslog')->create($f);

                    $cmsFolder->update($f);

                    if ($syslog) {
                        $syslog->setEntityId($f->getId());
                        $this->get('cms.syslog')->updateSyslogEntity($syslog);
                    }

                    if (empty($f->getParentFolder())) {
                        $site->setRootFolder($f);

                        $this->persist($site, true);
                    }

                    $this->get('cms.cache')->invalidateTag('folder');

                    if ($request->isXmlHttpRequest()) {
                        return new JsonResponse([
                            'structure_hash' => $cmsFolder->getStructureHash(),
                            'message' => 'Folder created successful',
                            'data' => [
                                'new_folder' => [ // @todo сделать у папок метод getDataAsArray()
                                    'id' => $f->getId(),
                                    'uri_part' => $f->getUriPart(),
                                    'full_path' => $cmsFolder->getUri($f),
                                    'title' => $f->getTitle(),
                                    'description' => $f->getDescription(),
                                    'is_file' => $f->isFile(),
                                    'meta' => $f->getMeta(),
                                    'redirect_to' => $f->getRedirectTo(),
                                    'template_inheritable' => $f->getTemplateInheritable(),
                                    'template_self' => $f->getTemplateSelf(),
                                    'is_active' => $f->isActive(),
                                    'permissions' => $f->getPermissionsCache(),
                                    'created_at' => $f->getCreatedAt()->format('Y-m-d H:i:s'),
                                    'position' => $f->getPosition(),
                                    'user' => [
                                        'id' => $f->getUser()->getId(),
                                        'username' => $f->getUser()->getUsername(),
                                    ],
                                    'router_node_id' => $f->getRouterNodeId(), // @todo relations
                                    'nodes' => [],
                                    'folders' => [],
                                ]
                            ],
                        ]);
                    }

                    if (!$request->query->has('_overlay')) {
                        $this->addFlash('success', 'Папка создана.');
                    }

                    if ($request->query->has('_overlay')) {
                        return $this->forward('CMSBundle:Layer:index', [
                            'payload' => [
                                'type'    => 'success',
                                'message' => 'Папка создана'
                            ]
                        ]);
                    }

                    if ($request->query->has('redirect_to')) {
                        return $this->get('cms.router')->redirect($folder);
                    }

                    return $this->redirectToRoute('cms_admin.structure');
                }
            } elseif ($request->request->has('delete')) {
                die('@todo');
            }
        }

        return $this->render('@CMS/Admin/Structure/folder_create.html.twig', [
            'form'       => $form->createView(),
            'folderPath' => $cmsFolder->getUri($parent),
        ]);
    }

    /**
     * Редактирование папки.
     *
     * @param Request     $request
     * @param Folder|null $folder
     *
     * @return Response|RedirectResponse
     *
     * @Route("/folder/{id<\d+>}/", name="cms_admin.structure_folder")
     */
    public function folderEditAction(Request $request, Folder $folder = null): Response
    {
        if (empty($folder)) {
            return $this->redirectToRoute('cms_admin.megastructure');
        }

        $form = $this->get('cms.folder')->createForm($folder);

        // Для корневой папки удаляются некоторые поля формы
        if (1 == $folder->getId()) {
            $form
                ->remove('uri_part')
                ->remove('parent_folder')
                ->remove('is_active')
                ->remove('is_file')
                ->remove('pos');
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has('update')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $this->get('cms.folder')->update($form->getData());

                    $this->get('cms.cache')->invalidateTags(['node', 'folder']);

                    if (!$request->query->has('_overlay')) {
                        $this->addFlash('success', 'Папка обновлена.');
                    }

                    if ($request->query->has('_overlay')) {
                        return $this->forward('CMSBundle:Layer:index', [
                            'payload' => [
                                'type'    => 'success',
                                'message' => 'Папка обновлена'
                            ]
                        ]);
                    }

                    if ($request->query->has('redirect_to')) {
                        return $this->get('cms.router')->redirect($folder);
                    }

                    return $this->redirectToRoute('cms_admin.structure');
                }
            } elseif ($request->request->has('delete')) {
                $form->handleRequest($request);

                /** @var $folder \Monolith\CMSBundle\Entity\Folder */
                $folder = $form->getData();
                $folder->setIsDeleted(true);

                $this->persist($folder, true);

                $this->get('cms.cache')->invalidateTags(['node', 'folder']);

                if (!$request->query->has('_overlay')) {
                    $this->addFlash('success', 'Папка <b>' . $folder->getTitle() . '</b> (' . $folder->getId() . ') удалена.');
                }

                if ($request->query->has('redirect_to')) {
                    return $this->get('cms.router')->redirect($folder->getParentFolder());
                }

                if ($request->query->has('_overlay')) {
                    return $this->forward('CMSBundle:Layer:index', [
                        'payload' => [
                            'type'    => 'success',
                            'message' => 'Папка <b>' . $folder->getTitle() . '</b> (' . $folder->getId() . ') удалена.'
                        ]
                    ]);
                }

                return $this->redirectToRoute('cms_admin.structure');

            }
        }

        $allow_delete = $folder->getId() != 1 ? true : false;

        if ($allow_delete and ($folder->getChildren()->count() > 0 or $folder->getNodes()->count() > 0 or $folder->getRegions()->count() > 0)) {
            $allow_delete = false;
        }

        return $this->render('@CMS/Admin/Structure/folder_edit.html.twig', [
            'allow_delete'  => $allow_delete,
            'folderPath'    => $this->get('cms.folder')->getUri($folder),
            'form'          => $form->createView(),
            'folder'        => $folder,
        ]);
    }

    /**
     * Редактирование области.
     *
     * @param Request $request
     * @param Region  $region
     *
     * @return Response|RedirectResponse
     *
     * @Route("/region/{id<\d+>}/", name="cms_admin.structure_region_edit")
     */
    public function regionEditAction(Request $request, Region $region)
    {
        $form = $this->get('cms.region')->createForm($region);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($request->request->has('update')) {
                if ($form->isValid()) {
                    $this->get('cms.region')->update($form->getData());
                    $this->addFlash('success', 'Область обновлена.');

                    $this->get('cms.cache')->invalidateTags(['node', 'folder']);

                    return $this->redirectToRoute('cms_admin.structure_region');
                }
            } elseif ($request->request->has('delete')) {
                $region = $form->getData();

                if ('content' == $region->getName()) {
                    $this->addFlash('error', 'Нельзя удалить область content');
                } elseif (0 < $this->get('cms.node')->countInRegion($region)) {
                    $this->addFlash('error', 'Нельзя удалить область пока в неё включены модули');
                } else {
                    $this->get('cms.region')->remove($region);
                    $this->addFlash('success', 'Область удалена.');

                    return $this->redirectToRoute('cms_admin.structure_region');
                }
            }
        }

        return $this->render('@CMS/Admin/Structure/region_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Отображение списка всех регионов, а также форма добавления нового.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/region/", name="cms_admin.structure_region")
     */
    public function regionIndexAction(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $engineRegion = $this->get('cms.region');

        $region = $engineRegion->create();
        $region->setUser($this->getUser());

        $form = $engineRegion->createForm($region);

        $form
            ->remove('groups_granted_read')
            ->remove('groups_granted_write')
        ;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                foreach ($em->getRepository(UserGroup::class)->findBy(['is_default_regions_granted_read' => 1]) as $userGroup) {
                    $region->addGroupGrantedRead($userGroup);
                }

                foreach ($em->getRepository(UserGroup::class)->findBy(['is_default_regions_granted_write' => 1]) as $userGroup) {
                    $region->addGroupGrantedWrite($userGroup);
                }

                $engineRegion->update($region);
                $this->addFlash('success', 'Область создана.');

                return $this->redirectToRoute('cms_admin.structure_region');
            }
        }

        return $this->render('@CMS/Admin/Structure/region_index.html.twig', [
            'all_regions' => $engineRegion->all(),
            'form'        => $form->createView(),
        ]);
    }

    /**
     * Создание новой ноды.
     *
     * @param Request $request
     * @param int     $folder_pid
     *
     * @return RedirectResponse|Response
     *
     * @Route("/node/create/", name="cms_admin.structure_node_create")
     * @Route("/node/create/{folder_pid<\d+>}/", name="cms_admin.structure_node_create_in_folder")
     */
    public function nodeCreateAction(Request $request, $folder_pid = 1)
    {
        $moduleManager = $this->get('cms.module');
        $modules_controllers = $moduleManager->allNodeModulesControllers();

        $folderManager = $this->get('cms.folder');

        if (null === $folder = $folderManager->get($folder_pid)) {
            return $this->redirectToRoute('cms_admin.structure_folder_create');
        }

        $nodeManager = $this->get('cms.node');
        $node = $nodeManager->factory();
        $node->setUser($this->getUser())
            ->setFolder($folder);

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        foreach ($em->getRepository(UserGroup::class)->findBy(['is_default_nodes_granted_read' => 1]) as $userGroup) {
            $node->addGroupGrantedRead($userGroup);
        }

        foreach ($em->getRepository(UserGroup::class)->findBy(['is_default_nodes_granted_write' => 1]) as $userGroup) {
            $node->addGroupGrantedWrite($userGroup);
        }

        $form = $nodeManager->createForm($node);
//        $form->remove('controller');

        if ($request->isMethod('POST')) {
            if ($request->request->has('create')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    /** @var $node \Monolith\CMSBundle\Entity\Node */
                    $node = $form->getData();

                    $nodeManager->create($node);

                    // Если у модуля есть роутинги, тогда нода подключается к папке как роутер.
                    $folder = $node->getFolder();
                    // @todo убрать проверку на роутеры контроллера в менеджер.
                    if ($this->container->has('cms.router_module.'.$node->getController()) and !$folder->getRouterNodeId()) {
                        $folder->setRouterNodeId($node->getId());
                        $folderManager->update($folder);
                    }

                    $this->get('cms.cache')->invalidateTag('node');

                    /*
                    if (!$request->query->has('_overlay')) {
                        $this->addFlash('success', 'Нода создана.');
                    }

                    if ($request->query->has('_overlay')) {
                        return $this->forward('CMSBundle:AdminStructure:nodeEdit', [
                            'request' => $request,
                            'node'    => $node
                        ]);
                    }

                    if ('front' === $request->query->get('redirect_to')) {
                        return $this->redirectToRoute('cms_admin.structure_node_setup_controller', [
                            'id' => $node->getId(),
                            'redirect_to' => 'front',
                        ]);
                    }
                    */

                    return $this->redirectToRoute('cms_admin.structure_node_properties', ['id' => $node->getId()]);
                }
            }
        }

        return $this->render('@CMS/Admin/Structure/node_create.html.twig', [
            'form'       => $form->createView(),
            'folderPath' => $folderManager->getUri($folder_pid),
            'modules_node_controllers' => $modules_controllers,
        ]);
    }

    /**
     * Редактирование ноды.
     *
     * @param Request $request
     * @param Node    $node
     *
     * @return RedirectResponse|Response
     *
     * @Route("/node/{id<\d+>}/", name="cms_admin.structure_node_properties")
     */
    public function nodeEditAction(Request $request, Node $node)
    {
        $nodeManager = $this->get('cms.node');

        if (empty($node)) {
            return $this->redirectToRoute('cms_admin.structure');
        }

        if (empty($node->getController())) {
            if ($request->query->has('_overlay')) {
                return $this->forward('CMSBundle:AdminStructure:nodeSetupController', [
                    'request' => $request,
                    'node'    => $node
                ]);
            }

            return $this->redirectToRoute('cms_admin.structure_node_setup_controller', ['id' => $node->getId()]);
        }

        $nodeParams = $node->getParams();
        $form = $nodeManager->createForm($node);

        /** @var AbstractNodePropertiesFormType $propertiesFormType */
        $propertiesFormType = $nodeManager->getPropertiesFormType($node);

        if ($propertiesFormType == NodeDefaultPropertiesFormType::class) {
            //$method = $nodeManager->getReflectionMethod($node, $node->getController());

            $cmsModule = $this->get('cms.module');

            $params = [];
            foreach ($cmsModule->getNodeControllersByName($node->getModule()) as $name => $data) {
                if ($node->getController() == $data['class']) {
                    $params = $data['params'];

                    break;
                }
            }
            dump($params);
            $parameters = [];
            /**
            foreach ($params as $parameter) {
                $class = $parameter->getClass();
                if ($class instanceof \ReflectionClass and
                    ($class->name == 'Monolith\CMSBundle\Entity\Node' or
                     $class->name == 'Symfony\Component\HttpFoundation\Request'
                    )
                ) {
                    continue;
                }

                $parameters[$parameter->name] = [
                    'type'     => $parameter->getType() ? $parameter->getType()->getName() : 'text',
                    'default'  => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                    'nullable' => $parameter->getType() and $parameter->getType()->allowsNull() ? true : false,
                ];
            }
            */

            foreach ($params as $key => $val) {
                $parameters[$key] = [
                    'type'     => is_bool($val) ? 'bool' : 'text',
                    'default'  => $val,
                    'nullable' => false,
                ];
            }

            $fb = $this->container->get('form.factory')->createNamedBuilder('node_properties', $type = FormType::class, null, [
                'csrf_protection' => false,
            ]);
            foreach ($parameters as $parameter => $data) {
                $fb->add($parameter, $this->resolveTypeName($data['type']), [
                    'data' => $node->getParam($parameter, $data['default']),
                    'required' => $data['nullable'] ? false : true,
                ]);
            }

            $form_properties = $fb->getForm();
        } else {
            $form_properties = $this->createForm($propertiesFormType, $nodeParams);
        }

        $form->remove('module');

        if ($request->isMethod('POST')) {
            if ($request->request->has('update')) {
                $form->handleRequest($request);
                $form_properties->handleRequest($request);

                if ($form->isValid()
                    and (// @todo отрефакторить!!!
                        (empty($nodeParams) and !$form_properties->isValid())
                        or (!empty($nodeParams) and $form_properties->isValid())
                        or (empty($nodeParams) and $form_properties->isValid())
                    )
                ) {
                    /** @var $updatedNode \Monolith\CMSBundle\Entity\Node */
                    $updatedNode = $form->getData();
                    if (empty($form_properties->getData())) {
                        $form_properties_data = [];
                    } else {
                        $form_properties_data = $form_properties->getData();
                    }

                    $updatedNode->setParams($form_properties_data);
                    $nodeManager->update($updatedNode);

                    $this->get('cms.cache')->invalidateTag('node');

                    if (!$request->query->has('_overlay')) {
                        $this->addFlash('success', 'Параметры модуля <b>' . $node->getModule() . '</b> (' . $node->getId() . ') обновлены.');
                    }

                    if ($request->query->has('_overlay') && $request->query->get('_overlay') !== '1') {
                        return $this->forward('CMSBundle:Layer:index', [
                            'payload' => [
                                'type'    => 'success',
                                'message' => 'Параметры модуля <b>'.$node->getModule().'</b> ('.$node->getId().') обновлены.'
                            ]
                        ]);
                    } elseif ($request->query->get('_overlay') === '1') {
                        return $this->forward('CMSBundle:Layer:index', [
                            'payload' => [
                                'type'    => 'success',
                                'message' => 'Нода <b>'.$node->getModule().'</b> ('.$node->getId().') успешно обновлена.'
                            ]
                        ]);
                    }

                    if ($request->query->has('redirect_to')) {
                        return $this->get('cms.router')->redirect($updatedNode);
                    }

                    return $this->redirectToRoute('cms_admin.structure');
                } else {
                    ld('Ошибка валидации формы');
                    ld($nodeParams);
                    ld($form_properties->isValid());
                }
            } elseif ($request->request->has('delete')) {
                $form->handleRequest($request);

                /** @var $node \Monolith\CMSBundle\Entity\Node */
                $node = $form->getData();
                $node
                    ->setIsDeleted(true)
                    ->setDeletedAt(new \DateTime())
                ;

                $node->getFolder()->setRouterNodeId(null);

                $this->persist($node, true);

                $this->get('cms.cache')->invalidateTag('node');

                if (!$request->query->has('_overlay')) {
                    $this->addFlash('success', 'Нода <b>' . $node->getModule() . '</b> (' . $node->getId() . ') удалена.');
                }

                if ($request->query->has('_overlay')) {
                    return $this->forward('CMSBundle:Layer:index', [
                        'payload' => [
                            'type'    => 'success',
                            'message' => 'Нода <b>' . $node->getModule() . '</b> (' . $node->getId() . ') удалена.'
                        ]
                    ]);
                }

                if ($request->query->has('redirect_to')) {
                    return $this->get('cms.router')->redirect($node); // @todo
                }

                return $this->redirectToRoute('cms_admin.structure');
            }
        }

        $bundle = $this->container->get('kernel')->getBundle($node->getModule());
        if ($bundle instanceof ModuleBundle and $bundle->isEnabled()) {
            $is_enabled = true;
        } else {
            $is_enabled = false;
        }

        return $this->render('@CMS/Admin/Structure/node_edit.html.twig', [
            'allow_delete'    => true,
            'form'            => $form->createView(),
            'form_properties' => $form_properties->createView(),
            'form_properties_template' => $propertiesFormType::getTemplate(),
            'node'            => $node,
            'is_enabled'      => $is_enabled,
        ]);
    }

    /**
     * @param Request $request
     * @param Node    $node
     *
     * @return RedirectResponse|Response
     *
     * @Route("/node/{id<\d+>}/setup_controller/", name="cms_admin.structure_node_setup_controller")
     */
    public function nodeSetupControllerAction(Request $request, Node $node): Response
    {
        $methods = $this->get('cms.node')->getReflectionMethods($node);
        if (count($methods) === 1) {
            foreach ($methods as $name => $_method) {
                $node->setController($name);

                break;
            }

            $this->persist($node, true);

            if ($request->query->has('_overlay')) {
                return $this->redirectToRoute('cms_admin.structure_node_properties', [
                    'id' => $node->getId(),
                    '_overlay' => '1'
                ]);
            }

            if ('front' === $request->query->get('redirect_to')) {
                return $this->get('cms.router')->redirect($node);
            }

            return $this->redirectToRoute('cms_admin.structure_node_properties', [
                'id' => $node->getId(),
            ]);
        }

        $form = $this->createForm(NodeSetupControllerFormType::class, $node);
        $form->add('update', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('update')->isClicked() and $form->isValid()) {
                $this->persist($form->getData(), true);

                if (!$request->query->has('_overlay')) {
                    $this->addFlash('success', 'Нода создана');
                }

                if ($request->query->has('_overlay')) {
                    return $this->forward('CMSBundle:Layer:index', [
                        'payload' => [
                            'type'    => 'success',
                            'message' => 'Нода создана'
                        ]
                    ]);
                }

                if ('front' === $request->query->get('redirect_to')) {
                    return $this->redirectToRoute('cms_admin.structure_node_properties', [
                        'id' => $node->getId(),
                        'redirect_to' => 'front',
                    ]);
                }

                return $this->redirectToRoute('cms_admin.structure_node_properties', ['id' => $node->getId()]);
            }
        }

        return $this->render('@CMS/Admin/Structure/node_setup_controller.html.twig', [
            'form'            => $form->createView(),
            'node'            => $node,
            // @todo разобраться с $request->query->has('_overlay')
            // почему-то в шаблоне метод app.request.query.has('_overlay') не дает true, поэтому посылаю переменную
            // _overlay отдельно... странно в других шаблонах всё ок, да и в контроллере query->has('_overlay') is true
            '_overlay'        => $request->query->has('_overlay') ? true : false
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/trash/", name="cms_admin.structure_trash")
     *
     * @todo пагинация и табы.
     */
    public function trashAction(): Response
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        return $this->render('@CMS/Admin/Structure/trash.html.twig', [
            'deleted_folders' => $em->getRepository(Folder::class)->findDeleted(),
            'deleted_nodes'   => $em->getRepository(Node::class)->findDeleted(),
        ]);
    }

    /**
     * @param Folder $folder
     *
     * @return RedirectResponse
     *
     * @Route("/trash/restore_folder/{id<\d+>}/", name="cms_admin.structure_trash_restore_folder")
     */
    public function trashRestoreFolderAction(Folder $folder): RedirectResponse
    {
        $folder->setIsDeleted(false);

        $this->persist($folder, true);

        $this->addFlash('success', 'Папка восстановлена.');

        return $this->redirectToRoute('cms_admin.structure_trash');
    }

    /**
     * @param Folder $folder
     *
     * @return RedirectResponse
     *
     * @Route("/trash/purge_folder/{id<\d+>}/", name="cms_admin.structure_trash_restore_folder")
     */
    public function trashPurgeFolderAction(Folder $folder): RedirectResponse
    {
        $this->get('cms.folder')->remove($folder);

        $this->addFlash('success', 'Папка удалена.');

        return $this->redirectToRoute('cms_admin.structure_trash');
    }

    /**
     * @param Node $node
     *
     * @return RedirectResponse
     *
     * @Route("/trash/restore_node/{id<\d+>}/", name="cms_admin.structure_trash_restore_node")
     */
    public function trashRestoreNodeAction(Node $node): RedirectResponse
    {
        $node->setIsDeleted(false);

        // Если у модуля есть роутинги, тогда нода подключается к папке как роутер.
        $folder = $node->getFolder();
        if ($this->container->has('cms.router_module.'.$node->getController()) and !$folder->getRouterNodeId()) {
            $folder->setRouterNodeId($node->getId());
        }

        $this->persist($node, true);

        $this->addFlash('success', 'Нода восстановлена.');

        return $this->redirectToRoute('cms_admin.structure_trash');
    }

    /**
     * @param Node $node
     *
     * @return RedirectResponse
     *
     * @Route("/trash/purge_node/{id<\d+>}/", name="cms_admin.structure_trash_purge_node")
     */
    public function trashPurgeNodeAction(Node $node): RedirectResponse
    {
        $this->get('cms.node')->remove($node);

        $this->addFlash('success', 'Нода удалена.');

        return $this->redirectToRoute('cms_admin.structure_trash');
    }
}
