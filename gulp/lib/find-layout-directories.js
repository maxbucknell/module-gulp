/**
 * Lodash. Obviously.
 */
const _ = require('lodash');

/**
 * Path manipulation.
 */
const path = require('path');

const magentoData = require('MaxBucknell_Gulp/lib/magento-data');

module.exports = findLayoutDirectories;

/**
 * Based on the themes and modules, find all layout directories.
 *
 * Emits a stream of directories containing static assets, and
 */
function findLayoutDirectories() {
    const globs = [];

    _.each(magentoData.modules, function (moduleLocation) {
        globs.push(path.join(moduleLocation, 'view/base/layout'));
        globs.push(path.join(moduleLocation, 'view/frontend/layout'));
    });

    // Theme files, including module overrides
    _.each(magentoData.themes, function (theme) {
        _.each(magentoData.modules, function (moduleLocation, moduleName) {
            globs.push(path.join(theme, moduleName, 'layout'));
        });
    });

    return globs;
}