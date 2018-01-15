# MaxBucknell_prefab

Deep npm integration with Magento 2 for better a front-end build process.

This module is two things:

1.  A faster way of compiling static assets for a Magento 2 site.
2.  An extensible framework to integrate Magento 2 with NodeJS, to
easily add new features to the front end build pipeline.

## Dependencies

*   [Node][node]

## Installation

```bash
composer config repositories.prefab vcs git@github.com:maxbucknell/module-prefab
composer config "minimum-stability" "dev"
composer config "prefer-stable" "true"
composer require maxbucknell/module-prefab
```

## Getting Started

```bash
bin/magento setup:prefab:build
bin/magento setup:prefab:install
bin/magento setup:prefab:run flatten --store="default"
bin/magento setup:prefab:run --store="default" clean flatten build
```

The above will compile all static assets required for the default store, and
will probably do it a little bit faster than Magento's default implementations.

For the record, here's what is compiled:

*   Basic assets are copied (CSS, JS, images, etc.)
*   LESS stylesheets
*   `requirejs-config.js`
*   `js-translation.json` (also includes theme translations)

The aim with each of these tasks was to maintain as much compatibility with
Magento as possible, so the above commands should compile any theme that is
valid and works out of the box.

### npm Scripts

The underlying technology here is a collection of build tools strung together by
a Unix shell and npm. If you want to learn more about npm scripts as a build
tool, I recommend these resources:

* [Why npm Scripts? | CSS Tricks](https://css-tricks.com/why-npm-scripts)
* [scripts | npm Documentation](https://docs.npmjs.com/misc/scripts)

The integration between npm and Magento is based on various data providers that
run inside Magento and expose an environment variable that can be used on the
npm side.

To help with debugging and testing data providers, there is a command `env`
included. By running `bin/magento setup:prefab:run env`, you can see all set
environment variables, including those added by this module.

## Task Overview

### `clean`

Remove a store's compiled assets in `pub/static`, and the flattened theme
hierarchy (`pub/static/prefab_build`).

### `flatten`

Resolve the theme hierarchy into a flattened directory of symlinks that point to
source files. For example, if a theme has overriden the file
`Magento_Customer::web/template/authentication-popup.html`, then the symlink
would point to the theme's file, rather than the template from the module.

This process allows npm to operate on a single directory tree, while retaining
the full flexibility of Magento's theme hierarchy.

### `build`

Compile all static assets from the flattened theme hierarchy into `pub/static`.

Note that `flatten` needs to be run before `build`.

## CLI Reference

### Build

`setup:prefab:build`

Builds the build system. In particular, it does two things:

1. Parses every module's `prefab.xml` and generates `package.json`.
2. Copy all `prefab` directories inside a module into a shared `prefab` build
directory, where they are accessible by npm scripts.

### Install

`setup:prefab:install`

Installs all dependencies specified by Prefab's `package.json`. This should be
run after `setup:prefab:build`.

### Run

`setup:prefab:run [options] [--] [<commands>]...`

`<commands>` is a sequence of one or more npm commands, that are run in
sequence. So, for example, `css copy` will run the stylesheet compiler, and then
copy static assets.

#### Options

`--store=[STORE_CODE]` (`-s [STORE_CODE]`)

The store for which to compile assets. Defaults to `default`.

## Prefab Framework

This module wraps npm and generates all required files for building static
assets dynamically. This generation is encapsulated by the build step.

### Build Step

All tasks should be configured in a file called `prefab.xml`, which should be
placed in the `etc` directory of your module. There is a corresponding XSD
schema, as well, but a task should look like this:

```xml
<!-- The task would be called like `npm run task-name` -->
<task name="task-name">
    <dependencies>
        <!--
            The npm dependencies required for your task to work should go here.

            They need to include the name in npm, and a version constraint.
            These can be updated and overridden by other modules.
        -->
        <package name="lodash" version="^4.17.4" />
    </dependencies>
    <command>echo "This is a running task"</command>
</task>
```

Running the `bin/magento` command `setup:prefab:build` will merge all `prefab`
configuration into npm's `package.json` file, so it'll look like this:

```js
{
    // ...
    "scripts": {
        "task-name": "echo \"This is a running task\"",
        // ...
    }
}
```

It will also copy the contents of any `prefab/` directory inside any modules as
siblings to the npm manifest. A module's `prefab/` directory will be copied to
its module name (e.g. `/Vendor_Module`). These can be loaded in tasks, if the
`command` in `prefab.xml` is specified to be something like `node
Vendor_Module/task.js`.

This command only needs to be run once, or whenever a `prefab.xml` changes.

Once the files have been collected, it's time to install dependencies.

### Installation

Since the dependencies are all declared in `package.json`, all that is left is
to run `npm install`. This module includes a `bin/magento` command to put you in
the correct directory:

```bash
bin/magento setup:prefab:install
```

As with `build`, the above commands only have to be run once, or whenever
something changes.

### Running

The real magic is in running the commands. The `bin/magento` command to do this
is `setup:prefab:run`, which takes an optional `--store` flag for the store
code, and a list of commands to run. The `--store` defaults to `default`.

```bash
# These two are the same
bin/magento setup:prefab:run --store="default" build
bin/magento setup:prefab:run build

# Whereas this is a custom store and custom task
bin/magento setup:prefab:run --store="germany" task-name
```

This command wraps a few things inside it. First, as with the others, it moves
to the correct directory before running commands. But secondly, it sets
a collection of environment variables before invoking npm. This is the essence
of the integration.

By default, the configuration object contains:

* The store code that is being compiled (`STORE_CODE`)
* Some store configuration as a JSON object (`CONFIG`)
* A list of all installed modules with absolute locations on disk as a JSON
array (`MODULES`)
* A list of the store's theme hierarchy, from child theme to parent theme, as
a JSON array (`THEMES`)
* The translation dictionary as a JSON object (`PHRASES`)
* The root directory of the Magento installation (`BASE_DIR`)
* The target directory for compiled assets (`OUTPUT_DIR`)
* A safe build directory in which to place intermediate assets (`BUILD_DIR`)
* The location of `requirejs-config.js` (`REQUIREJS_CONFIG_DIR`)

It is possible to add more. Create a new class that implements the
`DataProviderInterface`, and include it as a constructor parameter, like so:
 
```xml
<type name="MaxBucknell\prefab\Model\DataProvider">
    <arguments>
        <argument name="dataProviders" xsi:type="array">
            <item name="FOO" xsi:type="object">Vendor\Module\Model\DataProvider\Foo</item>
        </argument>
    </arguments>
</type>
```

This will make Prefab provision the environment variable `FOO` to be whatever
the output of `Vendor\Module\Model\DataProvider\Foo::getData()` is.

The final thing to note is that when it runs the `prefab` executable, it sets the
`NODE_PATH` environment variable to prefab's directory. This means that scripts
can be imported absolutely from that directory. For example, if you have a file
under your module `Vendor_Module`, it can be imported as

```js
const script = require('Vendor_Module/lib/script');
```

from anywhere, rather than `../lib/script`, or whatever.

### Writing Tasks

In general, most tasks should be able to be written as simple commands using npm
packages. But sometimes, one needs something more custom. To do this, create
a JS file in your module's `prefab directory`, and set the task's command to be
`node Vendor_Module/task-file.js`.

`task-file.js` should be a Node script, and it can use any of the Node standard
library. In addition, the module packages some useful library files.

* `MaxBucknell_Prefab/lib/magento-data.js` packages all the standard environment
variables into a JavaScript object, parsing JSON as necessary.
* `MaxBucknell_Prefab/lib/run.js` allows the passing of a command to run as
a separate process.

### Build Directories

The configuration exports an environment variable `BUILD_DIR`. This is unique
per store, and can be used to store intermediate results for any tasks.

The `flatten` task uses this, and creates a subdirectory called `flatten`. It
is recommended to create a subdirectory of this and store your intermediate
results inside it. This can be accessed using `path.join(config.build_dir, ...)`.

### File Structure Conventions

Currently, JavaScript files under the `prefab/` directory inside a module are
freeform, meaning that the structure inside there is up to the author. It is
recommended to split them into `lib/` and `task/`, for helper scripts and for
tasks respectively.

[node]: https://nodejs.org
