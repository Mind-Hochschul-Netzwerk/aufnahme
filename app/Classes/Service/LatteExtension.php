<?php
/**
 * @author Henrik Gebauer <henrik@mind-hochschul-netzwerk.de>
 * @license https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0
 */

declare(strict_types=1);

namespace App\Service;

use App\Model\FormData;
use Parsedown;

/**
 * Latte extension
 */
class LatteExtension extends \Latte\Extension {
    public function __construct(
        private bool $isEmbedded,
    ) {}

	/**
	 * Returns a list of |filters.
	 * @return array<string, callable>
	 */
	public function getFilters(): array
	{
		return [
            'markdown' => fn($text) => new \Latte\Runtime\Html(new Parsedown()->text($text)),
        ];
	}

	/**
	 * Returns a list of functions used in templates.
	 * @return array<string, callable>
	 */
	public function getFunctions(): array
	{
        return [
            'isEmbedded' => fn() => $this->isEmbedded,
            'getCountryNames' => fn() => FormData::COUNTRY_NAMES,
        ];
	}
}
