/**
 * Lodash. Obviously.
 */
const _ = require('lodash');

/**
 * Stream manipulation.
 */
const through = require('through2');

/**
 * Path manipulation.
 */
const path = require('path');

/**
 * File objects.
 */
const Vinyl = require('vinyl');

module.exports = findLayoutDirectories;

/**
 * Based on the themes and modules, find all layout directories.
 *
 * Emits a stream of directories containing static assets, and
 */
function findLayoutDirectories({themes, modules}) {
    const globs = [];

    _.each(modules, function (moduleLocation, moduleName) {
        globs.push(path.join(moduleLocation, 'view/base/layout/*.xml'));
        globs.push(path.join(moduleLocation, 'view/frontend/layout/*.xml'));
    });

    // Theme files, including module overrides
    _.each(themes, function (theme) {
        _.each(modules, function (moduleLocation, moduleName) {
            globs.push(path.join(theme, moduleName, 'layout/*.xml'));
        });
    });

    return globs;
}
