<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestVoter implements VoterInterface
{
    /** @var string */
    protected $adminPath;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * RequestVoter constructor.
     *
     * @param RequestStack $requestStack
     * @param string       $adminPath
     */
    public function __construct(RequestStack $requestStack, string $adminPath)
    {
        $this->adminPath    = $adminPath;
        $this->requestStack = $requestStack;
    }

    /**
     * @param ItemInterface $item
     *
     * @return bool
     */
    public function matchItem(ItemInterface $item): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        $parent = $item->getParent();

        while (null !== $parent->getParent()) {
            $parent = $parent->getParent();
        }

        $length = empty($item->getUri()) ? 0 : strlen($item->getUri());

        if ($item->getUri() === $request->getRequestUri() or
            $item->getUri() === $request->attributes->get('__cms_current_folder_path', false)
        ) {
            // URL's completely match
            return true;
        } elseif (
            $item->getUri() !== $request->getBaseUrl().'/' and
            $item->getUri() !== $request->getBaseUrl().'/'.$this->adminPath.'/' and
            $item->getUri() === substr($request->getRequestUri(), 0, $length) and
            $request->attributes->get('__selected_inheritance', true) and
            $parent->getExtra('select_intehitance', true)
        ) {
            // URL isn't just "/" and the first part of the URL match
            return true;
        }

        return false;
    }
}
