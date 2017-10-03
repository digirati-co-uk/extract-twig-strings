<?php


namespace Digirati\ExtractTwigStrings;


use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Node;

/**
 * Extracts {@link Message}s from {@code Twig} templates that use {@code translate} calls to translate messages.
 */
class MessageExtractor
{
    public function apply(Node $node): ?Message
    {
        if ($node instanceof FunctionExpression) {
            $functionName = $node->getAttribute('name');
            $functionArguments = $node->getNode('arguments');

            if ($functionName !== 'translate' || $functionArguments->count() < 1) {
                return null;
            }

            $functionArgumentValues = array_map(
                function (Node $node) {
                    if ($node->hasAttribute('value')) {
                        return $node->getAttribute('value');
                    } else {
                        throw new \RuntimeException("Unable to identify value for argument: ${node}");
                    }
                },
                iterator_to_array($functionArguments)
            );

            return new Message(
                $node->getTemplateName(),
                $node->getTemplateLine(),
                implode("", $functionArgumentValues)
            );
        }

        return null;
    }
}