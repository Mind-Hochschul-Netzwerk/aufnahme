{if empty($isEmbedded)}<h1>{$introTemplate->getSubject()|escape}</h1>{/if}

{$introTemplate->getFinalTextMarkdown()}
