<?php
// This file is generated. Do not modify it manually.
return array(
	'ofw-native' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'ofw/testimonial-native',
		'version' => '1.0.0',
		'title' => 'Testimonial (native)',
		'category' => 'text',
		'icon' => 'format-quote',
		'description' => 'Testimonial card built as a native block. Edited directly in the canvas.',
		'keywords' => array(
			'testimonial',
			'quote'
		),
		'attributes' => array(
			'quote' => array(
				'type' => 'string',
				'default' => ''
			),
			'author' => array(
				'type' => 'string',
				'default' => ''
			),
			'role' => array(
				'type' => 'string',
				'default' => ''
			),
			'photoId' => array(
				'type' => 'number',
				'default' => 0
			),
			'photoUrl' => array(
				'type' => 'string',
				'default' => ''
			),
			'variant' => array(
				'type' => 'string',
				'enum' => array(
					'light',
					'dark'
				),
				'default' => 'light'
			)
		),
		'supports' => array(
			'html' => false,
			'anchor' => true
		),
		'textdomain' => 'ofw',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScript' => 'file:./view.js'
	)
);
