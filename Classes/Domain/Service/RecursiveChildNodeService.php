<?php
declare(strict_types=1);

namespace Netlogix\RecursiveChildNodes\Domain\Service;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\ContentRepository\Domain\Utility\NodePaths;
use Neos\ContentRepository\Utility;
use Neos\Flow\Annotations as Flow;
use Netlogix\RecursiveChildNodes\Error\NodeNotFoundException;

/**
 * @Flow\Scope("singleton")
 */
class RecursiveChildNodeService
{

	const PROPERTY_DEFINITION = 'recursiveChildNodes';

	/**
	 * @Flow\Inject
	 * @var NodeTypeManager
	 */
	protected $nodeTypeManager;

	/**
	 * @param NodeInterface $node
	 * @throws NodeNotFoundException
	 * @throws \Neos\ContentRepository\Exception\NodeExistsException
	 * @throws \Neos\ContentRepository\Exception\NodeTypeNotFoundException
	 */
	public function createRecursiveChildNodes(NodeInterface $node)
	{
        $nodeTypeConfiguration = $node->getNodeType()->getFullConfiguration();

		if (!$this->hasRecursiveChildNodeDefiniton($nodeTypeConfiguration)) {
			return;
		}

		foreach ($nodeTypeConfiguration['options'][self::PROPERTY_DEFINITION] as $childNodeName => $configuration) {
			$targetNode = $node->getNode($childNodeName);

			if (!$targetNode) {
				throw new NodeNotFoundException('Cannot create recursive childNodes: targetNode "%s" does not exist', 1539332890);
			}

			foreach ($configuration as $childNodeDefinition) {
				$this->createChildNodesForNode($targetNode, $childNodeDefinition);
			}
		}
	}

	/**
	 * @param NodeInterface $node
	 * @param array $childNodeDefinition
	 * @throws \Neos\ContentRepository\Exception\NodeExistsException
	 * @throws \Neos\ContentRepository\Exception\NodeTypeNotFoundException
	 * @throws NodeNotFoundException
	 */
	protected function createChildNodesForNode(NodeInterface $node, array $childNodeDefinition)
	{
		$type = $childNodeDefinition['type'];
		// TODO: Implement properties with eel expressions
		// $properties = $childNodeDefinition['properties'];

		$nodeType = $this->nodeTypeManager->getNodeType($type);
		$nodeName = NodePaths::generateRandomNodeName();
		$childNodeIdentifier = Utility::buildAutoCreatedChildNodeIdentifier($nodeName, $node->getIdentifier());
		$createdChildNode = $node->createNode($nodeName, $nodeType, $childNodeIdentifier);

		if ($this->hasRecursiveChildNodeDefiniton($childNodeDefinition)) {
			foreach ($childNodeDefinition['options'][self::PROPERTY_DEFINITION] as $subChildNodeName => $subConfiguration) {
				$targetNode = $createdChildNode->getNode($subChildNodeName);

				if (!$targetNode) {
					throw new NodeNotFoundException('Cannot create recursive childNodes: targetNode "%s" does not exist', 1539332890);
				}

				foreach ($subConfiguration as $subChildNodeDefinition) {
					$this->createChildNodesForNode($targetNode, $subChildNodeDefinition);
				}
			}
		}
	}

	/**
	 * @param array $configuration
	 * @return bool
	 */
	protected function hasRecursiveChildNodeDefiniton(array $configuration)
	{
		return array_key_exists('options', $configuration)
			&& array_key_exists(self::PROPERTY_DEFINITION, $configuration['options']);
	}

}
