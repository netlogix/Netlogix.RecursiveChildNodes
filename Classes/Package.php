<?php
declare(strict_types=1);

namespace Netlogix\RecursiveChildNodes;

use Neos\ContentRepository\Domain\Model\Node;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Netlogix\RecursiveChildNodes\Domain\Service\RecursiveChildNodeService;

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
