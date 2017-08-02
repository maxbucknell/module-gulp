const findDirectories = require('MaxBucknell_Gulp/lib/find-directories');
const magentoData = require('MaxBucknell_Gulp/lib/magento-data');
const _ = require('lodash');
const fs = require('fs');
const path = require('path');
const promisify = require('util').promisify;

const rm = promisify(fs.unlink);
const symlink = promisify(fs.symlink);
const mkdirp = promisify(require('mkdirp'));

const existing = _.filter(
    findDirectories.requirejsConfig(),
    (file) => fs.existsSync(file.input)
);

function getIdentifier (x) {
    return ('0000' + x).slice(-4);
}

const files = _.map(
    existing,
    (file, index) => ({
        input: file.input,
        output: path.join(`${getIdentifier(index++)}.js`)
    })
);

const promises = _.map(
    files,
    (file) => {
        const link = path.join(magentoData.build_dir, 'flat/requirejs-config', file.output);
        const linkDir = path.dirname(link);
        const relativeInput = path.relative(linkDir, file.input);

        const createSymlink = () => symlink(relativeInput, link);

        return mkdirp(linkDir)
            .then(createSymlink)
            .catch(() => rm(link).then(createSymlink));
    }
);

return Promise.all(promises).then(() => console.log('Flattened requirejs-config.js files.'));
