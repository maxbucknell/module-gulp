const magentoData = require('prefab/MaxBucknell_Prefab/lib/magento-data');
const path = require('path');
const fs = require('fs');
const promisify = require('util').promisify;
const glob = promisify(require('glob'));
const readFile = promisify(fs.readFile);
const writeFile = promisify(fs.writeFile);
const mkdirp = promisify(require('mkdirp'));

const _ = require('lodash');

function wrap (file) {
    return `(function () {
// Made by Max

${file}

require.config(config)
})();`;
}

const cwd = path.join(magentoData.build_dir, 'flat/requirejs-config');

glob('*', { cwd })
    .then(
        (filenames) => _.map(
            filenames,
            (filename) => readFile(path.join(cwd, filename), 'utf-8').catch((x) => console.log(x))
        )
    )
    .then((promises) => Promise.all(promises))
    .then((files) => _.map(files, wrap).join('\n'))
    .then((config) => {
        return Promise.all([
            readFile(path.join(magentoData.build_dir, 'flat/static/requirejs/require.js'), 'utf-8').catch((x) => console.log(x)),
            readFile(path.join(magentoData.build_dir, 'flat/static/mage/requirejs/mixins.js'), 'utf-8').catch((x) => console.log(x)),
            Promise.resolve(config)
        ])
    })
    .then((files) => files.join('\n'))
    .then((result) => console.log(result))
    .catch((x) => null);