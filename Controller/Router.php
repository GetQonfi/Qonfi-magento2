<?php
namespace Qonfi\Qonfi\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\RequestInterface;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     */
    public function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * Match custom add-to-cart route
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfo = trim($request->getPathInfo(), '/');
        if (preg_match('#^add-to-cart/(\d+)$#', $pathInfo, $matches)) {
            $request->setModuleName('addtocart');
            $request->setControllerName('add');
            $request->setActionName('index');
            $request->setParam('id', $matches[1]);
            return $this->actionFactory->create('Qonfi\Qonfi\Controller\Add\Index');
        }
        return null;
    }
}
