# MaxBucknell_Prefab

Deep Gulp integration with Magento 2 for better a front-end build process.

This module is two things:

1.  A faster way of compiling static assets for a Magento 2 site.
2.  An extensible framework to integrate Magento 2 with NodeJS and Gulp, to
easily add new features to the front end build pipeline.

## Dependencies

*   [Node][node]

## Installation

```bash
composer config repositories.gulp vcs https://github.com/maxbucknell/module-gulp.git
composer config "minimum-stability" "dev"
composer config "prefer-stable" "true"
composer require maxbucknell/module-gulp
```

## Getting Started

```bash
bin/magento setup:gulp:build
bin/magento setup:gulp:install --npm
bin/magento setup:gulp:run flatten --store="default"
bin/magento setup:gulp:run --store="default" default
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

## Task Reference

### `flatten`

One of Magento's killer features is its theme hierarchy. Any particular asset
can be located in one of several places, and it's up to Magento to figure out
which one you want by a series of precedences.

This allows customisation with only minimal damage to upgradability, and a
great deal of other flexibility. Unfortunately, this can make it difficult to
use conventional build tools. In Magento 1, it was damn near impossible, and in
Magento 2, it is possible, but has either been slow or incompatible.

Magento's default implementation resolves this hierarchy and takes a similar
approach, and dumps its files into var/view_preprocessed. But by spending so
much time in PHP, they are giving up some performance benefits gained by using
such widely adopted tools as are found in the Node JS ecosystem.

This module takes a different approach: we resolve the theme hierarchy into a
directory entirely made of symlinks. What we found across our Magento 2
projects was that adding and removing files was a comparatively rare operation
as compared to changing existing files. By flattening the theme hierarchy into
a filesystem built of symlinks, we are easily able to track changes to files
that matter.

This flattened directory is then used as the source directory for Gulp and its
various plugins, to do a multitude of build related things, described below.

### `clean`

Delete `pub/static`, all build directories, and the `requirejs-config.js`
output directories.

The `flatten` task will need to re-run afterwards.

### `default`

An "empty" task that is a container for running all deployment tasks. By
default, it runs `copy`, `less`, `requirejs-config`, and `translations`, but
it is possible to add others through DI.

### `copy`

Doing no processing on static assets is a good start. This takes source files
from the flattened directory and copies them to the output directory.

### `less`

Compile stylesheets using LESS. The stylesheets to compile are worked out by
parsing the layout files for `<css` declarations.

### `requirejs-config`

Generate `requirejs-config.js`.

### `translations`

Generate `js-translation.json`. The notable difference here between Magento
and this implementation is that theme translations are accounted for, by
working around a core bug.

## Gulp Framework

This module wraps Gulp and generates all required files for building static
assets dynamically. This generation is encapsulated by the build step.

### Build Step

All tasks should be configured in a file called `gulp.xml`, which should be
placed in the `etc` directory of your module. There is a corresponding XSD
schema, as well, but a task should look like this:

```xml
<!-- The task would be called like `gulp task-name` -->
<task name="task-name">
    <dependencies>
        <!--
            The npm dependencies required for your task to work should go here.
            
            They need to include the name in npm, and a version constraint.
            These can be updated and overridden by other modules.
        -->
        <package name="lodash" version="^4.17.4" />
    </dependencies>
    <subtasks>
        <!--
            These are pre-requisite tasks to your task.
            
            Use this if you want something to run directly before your task.
        -->
        <task name="dependency-task" />
    </subtasks>
    <!--
        Use a <source> to give a path to the source file to run your task.
        
        This will be a file relative to the Gulp base directory, which will be
        built from all `gulp` directories inside modules. If you have a script
        which is in `gulp/task/task-name.js` under your module, then it will
        be located inside `Vendor_Module/task/task-name.js` after building,
        where Vendor_Module refers to your module's name.
        
        The structure of these scripts will be described further below, but
        this is what you should use if you need to actually *do* something.
    -->
    <source>Vendor_Module/task/task-name</source>
</task>

</task>
```

Running the `bin/magento` command `setup:gulp:build` will merge all `gulp`
configuration, and build a `gulpfile.js` from it. It will map each task like
so:

```js
gulp.task('task-name', ['dependency-task'], function () {
    return require('Vendor_Module/task/task-name')(magento);
});
```

As well as a Gulpfile, it will also build `package.json` based on the
dependencies specified by each task. It's important to note that while
dependencies are specified per task, they are actually global, so the last one
to write will "win".

It will also copy the contents of any `gulp/` directory inside any modules as
siblings to the Gulpfile and npm manifest. A module's `gulp/` directory will be
copied to its module name (e.g. `/Vendor_Module`);

Once the files have been collected, it's time to install dependencies.

### Installation

Since the dependencies are all declared in `package.json`, all that is left is
to run `yarn install`. This module includes a `bin/magento` command to put you
in the correct directory:

>   If you would prefer to use npm, append the `--npm` flag.

```bash
bin/magento setup:gulp:install
```

The above commands only have to be run once, or whenever something changes.

### Running

The real magic is in running the commands. The `bin/magento` command to do this
is `setup:gulp:run`, which takes an optional `--store` flag for the store code,
and an optional `command` argument to specify the Gulp command. Both default to
`"default"`. For example:

```bash
# These two are the same
bin/magento setup:gulp:run --store="default" default
bin/magento setup:gulp:run

# Whereas this is a custom store and custom task
bin/magento setup:gulp:run --store="germany" task-name
```

This command wraps a few things inside it. First, as with the others, it moves
to the correct directory before running commands. But secondly, it passes a
Magento configuration object as a command line argument.

This configuration object is generated by a `MaxBucknell\Prefab\Api\DataProviderInterface`,
and is encoded as JSON and parsed in the Gulpfile. It contains things such as
the modules that are installed, the locale that the store is set to, and the
current theme hierarchy. This is the key piece of the integration, allowing
effectively unlimited communication from Magento to Gulp, without it having to
work anything out for itself.

By default, the configuration object contains:

*   The location of the Magento root directory.
*   The output directory for static assets (`pub/static/...`)
*   The output directory for `requirejs-config.js` (`pub/static/_requirejs`)
*   A build directory unique for this store.
*   All modules currently installed.
*   An array of theme locations, from parent to child.
*   A set of values from store configuration (by default just `general/locale/code`)
*   The store code for which we are compiling.
*   All translatable phrases.

It is possible to add more. Create a new class that implements the
`DataProviderInterface`, and include it as a constructor parameter, like so:
 
```xml
<type name="MaxBucknell\Prefab\Model\DataProvider">
    <arguments>
        <argument name="dataProviders" xsi:type="array">
            <item name="foo" xsi:type="object">Vendor\Module\Model\DataProvider\Foo</item>
            </argument>
    </arguments>
</type>
```

This would include the result of your data provider in the configuration object
under the `"foo"` key.

The final thing to note is that when it runs the `gulp` executable, it sets the
`NODE_PATH` environment variable to Gulp's directory. This means that scripts
can be imported absolutely from that directory. For example, if you have a file
under your module `Vendor_Module`, it can be imported as

```js
const script = require('Vendor_Module/lib/script');
```

from anywhere, rather than `../lib/script`, or whatever.

### Writing Tasks

As above, the calling code for a generated Gulp task looks like this:

```js
gulp.task('task-name', ['dependency-task'], function () {
    return require('Vendor_Module/task/task-name')(magento);
});
```

The exported value of `'Vendor_Module/task/task-name'` should be a function
that returns a Gulp pipeline. It takes the Magento configuration as a
parameter.

For example, here is the contents of the simplest task, the `copy` task.

```js
function getCopyBlobs () { /* ... */ }

function main (config) {
    return gulp.src(getCopyBlobs(), { cwd: path.join(config.build_dir, 'flatten') })
        .pipe(gulp.dest(config.output_dir));
}
```

### Build Directories

The configuration exports a field `build_dir`. This is unique per store, and
can be used to store intermediate results for any tasks.

The `flatten` task uses this, and creates a subdirectory called `flatten`. It
is recommended to create a subdirectory of this and store your intermediate
results inside it. This can be accessed using `path.join(config.build_dir, ...)`.

### File Structure Conventions

Currently, JavaScript files under the `gulp/` directory inside a module are
freeform, meaning that the structure inside there is up to the author. It is
recommended to split them into `lib/` and `task/`, for helper scripts and for
tasks respectively.

[node]: https://nodejs.org