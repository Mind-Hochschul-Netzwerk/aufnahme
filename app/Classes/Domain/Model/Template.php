<?php
namespace MHN\Aufnahme\Domain\Model;

/**
 * @author Henrik Gebauer <mensa@henrik-gebauer.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

/**
 * editierbare Text-Vorlage
 */
class Template
{
    private $name;
    private $label;
    private $subject;
    private $text;
    private $hints;

    public function __construct(string $name, string $label, string $subject, string $text, string $hints)
    {
        $this->name = $name;
        $this->label = $label;
        $this->subject = $subject;
        $this->text = $text;
        $this->hints = $hints;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getHints(): string
    {
        return $this->hints;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @param $replacementMap = [$key => $replacement, ...]
     */
    public function getFinalText(array $replacementMap): string
    {
        $text = $this->getText();
        foreach ($values as $key=>$replacement) {
            $text = str_replace('{$' . $k . '}', $v, $text);
        }
        return $text;
    }
}
