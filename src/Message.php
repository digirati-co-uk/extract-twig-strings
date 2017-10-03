<?php


namespace Digirati\ExtractTwigStrings;

/**
 * A representation of an {@code i18n} message keyed by the file/location it was found.
 */
class Message
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $sourceLocation;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $source, string $sourceLocation, string $value)
    {
        $this->source = $source;
        $this->sourceLocation = $sourceLocation;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getSourceLocation(): string
    {
        return $this->sourceLocation;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}