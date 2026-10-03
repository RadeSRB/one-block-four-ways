# One block, four ways

I built the same testimonial card four times, once for each way of making Gutenberg blocks that I see people use in 2026:

1. Secure Custom Fields (SCF), the free plugin from WordPress.org
2. a PHP-only block, new in WordPress 7.0
3. a native block, React in the editor and PHP on the front end
4. Block Bindings, with no custom block at all

Visitors see the same card every time. What changes is how much code you write and how the block feels to the person editing the page. That difference is what my talk *Jedan blok, četiri načina* (One block, four ways) is about, and this repo is the code from it.

## Try it in your browser

[Open the demo in WordPress Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/RadeSRB/one-block-four-ways/main/blueprint.json)

Playground installs SCF and the four plugins, adds the demo content and opens a page with all four cards. You're logged in as admin, so open that page in the editor and click on each card. That's where the four versions are really different.

## Side by side

| | 01 SCF | 02 PHP-only | 03 Native | 04 Block Bindings |
|---|---|---|---|---|
| Custom block | `ofw/testimonial-scf` | `ofw/testimonial-php` | `ofw/testimonial-native` | none, a pattern of core blocks |
| Build step | no | no | yes (`npm run build`) | no |
| Needs | SCF (or ACF PRO 6.6+) | WordPress 7.0+ | nothing extra | nothing extra |
| Editing | text inline, the rest in the expanded editor | sidebar controls generated from the attributes | everything in place, in the canvas | in place, values are saved to post meta |
| Photo | media library | URL typed into a text field | media library | featured image |
| Where the data lives | block comment in `post_content` | block comment in `post_content` | block comment in `post_content` | `wp_postmeta` of a testimonial post |

01 to 03 are rendered on the server, so I can change their markup later without the editor complaining about invalid blocks.

## The four versions

### 01-scf

A `block.json` with an `acf` key, a PHP template and the field group registered in PHP. It uses ACF Blocks v3 (`"blockVersion": 3`) with `autoInlineEditing`, so the quote, author and role are typed right into the card. The photo and the light/dark variant are in the expanded editor.

SCF is a fork of ACF and kept all of ACF's names (`get_field()`, `acf_add_local_field_group()`, the `acf/` hooks), so this should also run on ACF PRO 6.6+. I only tested it on SCF. The plugin header has `Requires Plugins: secure-custom-fields`, so on ACF PRO remove that line.

### 02-php-only

The shortest one. A single `register_block_type()` call with `'supports' => array( 'autoRegister' => true )`, no JavaScript and no build step. WordPress builds the sidebar controls from the attributes, but only for `string`, `integer`, `boolean` and `enum`. There's no media picker, so the photo is a URL you paste into a text field. That's where this approach runs out.

### 03-native

The classic way, set up the same as `@wordpress/create-block --variant dynamic` does it. `edit.js` uses `RichText`, `MediaUpload` and `InspectorControls`, and `render.php` outputs the front end. Everything is edited in the canvas. The `build/` folder is committed, so the plugin works without npm.

To start a block like this from scratch:

```bash
npx @wordpress/create-block@latest my-block --variant dynamic
```

### 04-block-bindings

No custom block here. Every testimonial is a post of the `ofw_testimonial` type. The quote, author and role are post meta (`ofw_quote`, `ofw_author`, `ofw_role`) and the photo is the featured image.

The card in `patterns/testimonial-card.html` is only core blocks: a Group with a Featured Image, a Quote and two Paragraphs. The paragraphs are bound to meta through `core/post-meta`, so they don't store any text, just which field to read. The same markup is also the post type template, so when you edit a testimonial you type into the card and the values go to post meta.

On the page, the `ofw/testimonials` pattern is a Query Loop over those posts. The pattern isn't synced: the page keeps its own copy of the layout, but the data stays live. Add a new testimonial and it shows up at the top without touching the page.

The one thing I couldn't bind is light/dark. Block Bindings connect content (text, images, links), not CSS classes, so the dark version is a block style registered with `register_block_style()`. In WordPress 7.1 an inserted pattern opens in content-only mode, so click **Edit pattern**, select a card and pick **Testimonial dark** under Styles. It changes every card in that Query Loop, not a single testimonial. The style also shows up on every Group block on the site, but the CSS only targets the card.

## Install on your own site

1. Install Secure Custom Fields. On ACF PRO 6.6+, remove the `Requires Plugins` line from `01-scf/ofw-scf.php` instead.
2. Copy the numbered folders into `wp-content/plugins/` and activate the ones you want. All four can be active at the same time.
3. For the demo page, also copy `demo-content` and activate it last. It creates the "Testimonials demo" page, three testimonial posts and three placeholder photos.

`demo-content` is the same plugin the Playground link uses. I kept the demo data in a plugin and not in the blueprint because the SCF and native blocks store the photo's attachment ID, and that ID only exists once the image is uploaded to the site. The plugin uploads the photos first and then writes the real IDs into the blocks, the same way in Playground and on a normal site.

## Changing the native block

```bash
cd 03-native
npm install
npm start        # rebuilds on every save
npm run build    # production build into build/
```

## Tested with

WordPress 7.1.2, PHP 8.3 and Secure Custom Fields 6.9.5. All blocks are valid in the editor and render without PHP warnings. I haven't tried 01 on ACF PRO.

## Learn more

### Native blocks

- [Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [Tutorial: Build your first block](https://developer.wordpress.org/block-editor/getting-started/tutorial/), from `create-block` to a finished dynamic block
- [Fundamentals of Block Development](https://developer.wordpress.org/block-editor/getting-started/fundamentals/) and [Static or Dynamic rendering of a block](https://developer.wordpress.org/block-editor/getting-started/fundamentals/static-dynamic-rendering/)
- [Metadata in block.json](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/) and [Attributes](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-attributes/)
- [@wordpress/create-block](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-create-block/) and [@wordpress/scripts](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/)
- [Introduction to Block Development](https://learn.wordpress.org/course/introduction-to-block-development-build-your-first-custom-block/), a free course on Learn WordPress
- [block-development-examples](https://github.com/WordPress/block-development-examples), official examples that all run in Playground

### PHP-only blocks

- [PHP-only block registration](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/), the WordPress 7.0 dev note

### SCF / ACF blocks

- [Secure Custom Fields Handbook](https://developer.wordpress.org/secure-custom-fields/)
- [ACF Blocks](https://www.advancedcustomfields.com/resources/blocks/), [ACF Blocks configuration via block.json](https://www.advancedcustomfields.com/resources/acf-block-configuration-via-block-json/) and [ACF Blocks V3](https://www.advancedcustomfields.com/resources/acf-blocks-v3/). SCF uses the same API, so they work for SCF too.

### Block Bindings

- [Bindings](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/) in the Block Editor Handbook
- [Introducing Block Bindings, part 1: connecting custom fields](https://developer.wordpress.org/news/2024/02/introducing-block-bindings-part-1-connecting-custom-fields/)
- [Introducing Block Bindings, part 2: Working with custom binding sources](https://developer.wordpress.org/news/2024/03/introducing-block-bindings-part-2-working-with-custom-binding-sources/)
- [Getting and setting Block Binding values in the Editor](https://developer.wordpress.org/news/2024/10/getting-and-setting-block-binding-values-in-the-editor/)

### What's next

- [Interactivity API Reference](https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/)
- [WordPress Playground Docs](https://developer.wordpress.org/playground/)
- [WordPress Developer Blog](https://developer.wordpress.org/news/), with a monthly "What's new for developers" post

## License

GPL-2.0-or-later