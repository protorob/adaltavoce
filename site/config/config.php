<?php

return [
	// Disabled while actively adding/removing icons in assets/icons/ during
	// development: the plugin caches its folder scan keyed by field config
	// (not by folder contents), so a new SVG never invalidates the cache on
	// its own — see README's "Site panel defaults".
	'tobimori.icon-field' => [
		'cache' => false
	],

	// Global block list for every `type: blocks` field. Kirby's default is
	// just the core blocks below; custom blocks (site/blueprints/blocks/,
	// site/snippets/blocks/) only show up in the Panel once listed here.
	// Setting this once means new page blueprints get them without repeating
	// a `fieldsets:` list per field.
	'blocks.fieldsets' => [
		'code'     => 'blocks/code',
		'gallery'  => 'blocks/gallery',
		'heading'  => 'blocks/heading',
		'image'    => 'blocks/image',
		'line'     => 'blocks/line',
		'list'     => 'blocks/list',
		'markdown' => 'blocks/markdown',
		'quote'    => 'blocks/quote',
		'text'     => 'blocks/text',
		'video'    => 'blocks/video',

		'child-pages' => 'blocks/child-pages',
	]
];
