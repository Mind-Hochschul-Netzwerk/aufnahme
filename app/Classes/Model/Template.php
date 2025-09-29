<?php
/**
 * @author Henrik Gebauer <henrik@mind-hochschul-netzwerk.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

declare(strict_types=1);

namespace App\Model;

use Parsedown;

/**
 * editierbare Text-Vorlage
 */
class Template
{
    private string $name;
    private string $label;
    private string $subject;
    private string $text;
    private string $hints;

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
    public function getFinalText(array $replacementMap = []): string
    {
        $text = $this->getText();
        foreach ($replacementMap as $key=>$replacement) {
            $text = str_replace('{$' . $key . '}', "$replacement", $text);
        }
        return $text;
    }

    public function getFinalTextMarkdown(array $replacementMap = []): string
    {
        $text = $this->getFinalText($replacementMap);
        return (new Parsedown())->text($text);
    }
}
