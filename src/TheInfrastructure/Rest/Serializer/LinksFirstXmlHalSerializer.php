<?php

declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Rest\Serializer;

use DOMElement;
use DOMNode;
use DOMNodeList;
use Hateoas\Model\Embedded;
use Hateoas\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;
use LogicException;
use RuntimeException;

class LinksFirstXmlHalSerializer implements SerializerInterface
{
    /** @var SerializerInterface */
    private $embeddedSerializer;

    public function __construct(SerializerInterface $embeddedSerializer)
    {
        $this->embeddedSerializer = $embeddedSerializer;
    }

    public function serializeLinks(
        array $links,
        SerializationVisitorInterface $visitor,
        SerializationContext $context
    ): void {
        if (!$visitor instanceof XmlSerializationVisitor) {
            throw new LogicException(
                'XML Serializers can be visited only by Xml Visitors'
            );
        }
        $node = $visitor->getCurrentNode();
        if (!$node instanceof DOMElement) {
            throw new RuntimeException('Nothing to serialize!');
        }

        foreach ($links as $link) {
            if ('self' === $link->getRel()) {
                foreach ($link->getAttributes() as $key => $value) {
                    $node->setAttribute($key, $value);
                }

                $node->setAttribute('href', $link->getHref());

                continue;
            }

            $linkNode = $visitor->getDocument()->createElement('link');
            $node->insertBefore($linkNode, $this->firstNonLinkChild($node->childNodes));

            $linkNode->setAttribute('rel', $link->getRel());
            $linkNode->setAttribute('href', $link->getHref());

            foreach ($link->getAttributes() as $attributeName => $attributeValue) {
                $linkNode->setAttribute($attributeName, $attributeValue);
            }
        }
    }

    /**
     * @param Embedded[] $embeddeds
     */
    public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context): void
    {
        $this->embeddedSerializer->serializeEmbeddeds($embeddeds, $visitor, $context);
    }

    private function firstNonLinkChild(DOMNodeList $nodes): ?DOMNode
    {
        /** @var DOMNode $node */
        foreach ($nodes as $node) {
            if ($node->nodeName !== 'link') {
                return $node;
            }
        }
        return null;
    }
}
