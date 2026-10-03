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