<?php

namespace Monolith\ModuleTexter\Controller;

use Monolith\CMSBundle\Module\CacheTrait;
use Monolith\ModuleTexter\Entity\TextItem;
use Monolith\ModuleTexter\Entity\TextItemHistory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    use CacheTrait;

    /**
     * @param  Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!empty($this->node)) {
            if (empty($item = $this->getDoctrine()->getRepository(TextItem::class)->find($this->text_item_id))) {
                throw $this->createNotFoundException();
            }

            return $this->itemAction($request, $item);
        }

        // @todo !!! pagination
        $items = $this->getDoctrine()->getRepository(TextItem::class)->findAll();

        /** @var $item TextItem */
        foreach ($items as $item) {
            $folderPath = null;
            $folder = null;
            foreach ($this->get('cms.node')->findByModule('TexterModuleBundle') as $node) {
                if ($node->getParam('text_item_id') === (int) $item->getId()) {
                    $folderPath = $this->get('cms.folder')->getUri($node);
                    $folder = $node->getFolder();

                    break;
                }
            }

            $item->_folderPath = $folderPath;
            $item->_folder = $folder;
        }

        return $this->render('@TexterModule/Admin/index.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * @param  Request  $request
     * @param  TextItem $item
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|JsonResponse
     */
    public function itemAction(Request $request, TextItem $item)
    {
        $folderPath = null;
        foreach ($this->get('cms.node')->findByModule('TexterModuleBundle') as $node) {
            if ($node->getParam('text_item_id') === (int) $item->getId()) {
                $folderPath = $this->get('cms.folder')->getUri($node);

                break;
            }
        }

        if ($request->isMethod('POST')) {
            $oldItem = clone $item;

            $data = $request->request->get('texter');
            $item
                ->setText($data['text'])
                ->setMeta($data['meta'])
            ;

            $this->getCacheService()->invalidateTag('monolith_module.texter');

            // @todo сделать глобальную настройку, включающую выравниватель кода.
            if ($item->getEditor()) {
                $item->setText($this->get('html.tidy')->prettifyFragment($item->getText()));
            }

            try {
                $this->persist($item, true);

                $history = new TextItemHistory($oldItem);
                $this->persist($history, true);

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse([
                        'message' => 'Text updated successful',
                        'data' => [
                            'item_id'  => $item->getId(),
                            '_node_id' => empty($this->node) ?: $this->node->getId(),
                        ],
                    ]);
                }

                if (!$request->query->has('_overlay')) {
                    $this->addFlash('success', 'Текст обновлён (id: <b>' . $item->getId() . '</b>)');
                }

                if ($request->query->has('_overlay')) {
                    return $this->forward('CMSBundle:Layer:index', [
                        'payload' => [
                            'type'    => 'success',
                            'message' => 'Текст обновлён (id: <b>' . $item->getId() . '</b>)'
                        ]
                    ]);
                }

                if ($request->request->has('update_and_redirect_to_site') and $folderPath) {
                    return $this->redirect($folderPath);
                } else {
                    return $this->redirectToRoute('monolith_module.texter.admin');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', ['sql_debug' => $e->getMessage()]);

                return $this->redirectToRoute('monolith_module.texter.admin.edit', ['id' => $item->getId()]);
            }
        }

        $item->_folderPath = $folderPath;

        return $this->render('@TexterModule/Admin/edit.html.twig', [
            '_node_id' => empty($this->node) ?: $this->node->getId(),
            'item'     => $item,
        ]);
    }

    /**
     * @param  TextItem $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @todo пагинацию.
     */
    public function historyAction(TextItem $item)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $itemsHistory = $em->getRepository(TextItemHistory::class)->findBy(
            ['item' => $item],
            ['created_at' => 'DESC']
        );

        return $this->render('@TexterModule/Admin/history.html.twig', [
            'item' => $item,
            'items_history' => $itemsHistory,
        ]);
    }

    /**
     * @param TextItemHistory $itemHistory
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyViewAction(TextItemHistory $itemHistory)
    {
        return $this->render('@TexterModule/Admin/history_view.html.twig', [
            'item_history' => $itemHistory,
        ]);
    }

    /**
     * @param  int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function rollbackAction($id)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $historyItem = $em->find(TextItemHistory::class, $id);

        if ($historyItem) {
            $item = $em->find(TextItem::class, $historyItem->getItemId());
            $item
                ->setEditor($historyItem->getEditor())
                ->setLocale($historyItem->getLocale())
                ->setMeta($historyItem->getMeta())
                ->setText($historyItem->getText())
                ->setText($historyItem->getText())
                ->setUser($historyItem->getUser())
            ;

            $this->persist($item, true);

            $this->addFlash('success', 'Откат успешно выполнен.');
        } else {
            $this->addFlash('error', 'Непредвиденная ошибка при выполнении отката');
        }

        return $this->redirectToRoute('monolith_module.texter.admin');
    }
}
