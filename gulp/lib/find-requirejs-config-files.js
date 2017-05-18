/**
 * Lodash. Obviously.
 */
const _ = require('lodash');

/**
 * Stream processing.
 */
const through = require('through2');

/**
 * Virtual files.
 */
const vinylFile = require('vinyl-file');

/**
 * Path manipulation.
 */
const path = require('path');

module.exports = findRequirejsConfigFiles;

/**
 * Find all files named `requirejs-config.js`
 */
function findRequirejsConfigFiles ({ themes, modules }) {
    function process (file, encoding, cb) {
        const modulePromises = _.flatMap(modules, function (moduleLocation) {
            const baseLocation = path.join(
                moduleLocation,
                'view/base/requirejs-config.js'
            );

            const frontendLocation = path.join(
                moduleLocation,
                'view/frontend/requirejs-config.js'
            );

            const base = vinylFile.read(baseLocation)
                .then(this.push.bind(this))
                .catch(_.noop);

            const frontend = vinylFile.read(frontendLocation)
                .then(this.push.bind(this))
                .catch(_.noop)

            return [ base, frontend ];
        }.bind(this));

        const themePromises = _.flatMap(themes, function (theme) {
            const themePromise = vinylFile.read(path.join(theme, 'requirejs-config.js'))
                .then(this.push.bind(this))
                .catch(_.noop);

            const modulePromises = _.map(modules, function (moduleLocation, moduleName) {
                const location = path.join(
                    theme,
                    moduleName,
                    'requirejs-config.js'
                );

                return vinylFile.read(location)
                    .then(this.push.bind(this))
                    .catch(_.noop);
            }.bind(this));

            return [ themePromise, ...modulePromises ];
        }.bind(this));

        Promise.all([
            ...modulePromises,
            ...themePromises
        ]).then(() => cb());
    }

    return through.obj(process);
}
