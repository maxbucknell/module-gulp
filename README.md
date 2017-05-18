# M2 Gulp

A no-nonsense, compatibility oriented Gulp pipeline for front end development
with Magento 2.

By this, we mean that you don't have to change your code to use this. If you've
got a theme that works with `setup:static-content:deploy` or Magento's `grunt`
implementation, it will work with this, and probably be faster.

## Installation

### Dependencies

*	[Node][install-node]
*	[Composer][install-composer]

Install via Composer! (Packagist entry is coming)

```bash
composer config repositories.gulp vcs https://github.com/maxbucknell/module-gulp.git
composer config "minimum-stability" "dev"
composer config "prefer-stable" "true"
composer require maxbucknell/module-gulp
```

## Getting Started

This will walk you through setting up M2 Gulp, and having it work on your
theme.

Once installed, its NPM dependencies will need to be installed. From your root
directory:

```bash
bin/magento gulp:install
```

That's all the set up you'll need to do. The next thing to do is build the
flattened theme hierarchy, which is then used as the source location for the
other gulp tasks.

```bash
bin/magento gulp:build default
```

> `default` here is a store code, and you can set it to whatever you need.

Once that is done, you can set up the watch, which runs all tasks needed for
static asset compilation whenever things change.

```bash
bin/magento gulp:watch default
```

Once it's ready, you can load the site in your browser, and watch it load.

If you change a file, it will let you know in the console, and rebuild the
invalidated assets for you automatically.

### Flattened Theme Directory

One of Magento's killer features is its theme hierarchy. Any particular asset
can be located in one of several places, and it's up to Magento to figure out
which one you want by a series of precedences.

This allows customisation with only minimal damage to upgradability, and a great
deal of other flexibility. Unfortunately, this can make it difficult to use
conventional build tools. In Magento 1, it was damn near impossible, and in
Magento 2, it is possible, but has either been slow or incompatible.

Magento's default implementation resolves this hierarchy and takes a similar
approach, and dumps its files into `var/view_preprocessed`. But by spending so
much time in PHP, they are giving up some performance benefits gained by using
such widely adopted tools as are found in the Node JS ecosystem.

M2 Gulp takes a different approach: we resolve the theme hierarchy into
a directory entirely made of symlinks. What we found across our Magento
2 projects was that adding and removing files was a comparatively rare operation
as compared to changing existing files. By flattening the theme hierarchy into
a filesystem built of symlinks, we are easily able to track changes to files
that matter.

This flattened directory is then used as the source directory for Gulp and its
various plugins, to do a multitude of build related things.

### Choosing CSS Compilation Targets

This is done the same way as Magento: The XML layout tree is parsed, and the CSS
directives are extracted.

[install-node]: https://nodejs.org/
[install-composer]: https://getcomposer.org/download/
