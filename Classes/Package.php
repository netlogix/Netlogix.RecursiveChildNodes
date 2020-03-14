<?php
namespace Lala\RecursiveChildNodes;

use Neos\ContentRepository\Domain\Model\Node;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Lala\RecursiveChildNodes\Domain\Service\RecursiveChildNodeService;

class Package extends BasePackage
{

	/**
	 * @param Bootstrap $bootstrap
	 */
	public function boot(Bootstrap $bootstrap)
	{
		$dispatcher = $bootstrap->getSignalSlotDispatcher();

		$dispatcher->connect(Node::class, 'afterNodeCreate', RecursiveChildNodeService::class,
			'createRecursiveChildNodes', false);
	}

}
