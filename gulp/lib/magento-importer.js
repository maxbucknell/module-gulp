const fs = require('fs');
const path = require('path');
const through = require('through2');
const _ = require('lodash');

module.exports = magentoImporter;

/**
 * Process @magento_import directives.
 *
 * Magento LESS files have a wildcard importer to import all module stylesheet
 * files. This doesn't work by default in LESS, so we have to compile it
 * separately.
 *
 * We grep for the pattern, then we find all possible stylesheets matching those
 * parameters, and replace the directive with stock @imports.
 */
function magentoImporter(config) {
    const modules = Object.keys(config.modules);
    const build_dir = path.join(config.build_dir, 'flatten');

    function process(file, encoding, cb) {
        if (file.isNull()) {
            return cb(null, file);
        }

        if (file.isStream()) {
            return cb(new Error('Yaaaaa'));
        }

        const str = file.contents.toString();

        const result = str.replace(
            /\/\/@magento_import '(.*)';/g,
            function flattenImport(match, include) {
                const includes =  _.chain(modules)
                    .map((module) => `../${module}/css/${include}`)
                    .filter((fn) => fs.existsSync(path.join(build_dir, 'css', fn)))
                    .map((fn) => `@import '${fn}';`)
                    .value();

                const files = [];

                if (fs.existsSync(path.join(build_dir, include))) {
                    files.push(`@import '${include}';`);
                }

                return [
                    ...includes,
                    ...files,
                ].join('\n');
            }
        );

        file.contents = Buffer.from(result);

        return cb(null, file);
    }

    return through.obj(process);
}
