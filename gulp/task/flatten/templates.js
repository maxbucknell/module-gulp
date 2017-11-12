const findDirectories = require('prefab/MaxBucknell_Prefab/lib/find-directories');
const magentoData = require('prefab/MaxBucknell_Prefab/lib/magento-data');

const _ = require('lodash');
const path = require('path');
const fs = require('fs');
const promisify = require('util').promisify;

const glob = require('glob');

const symlink = promisify(fs.symlink);
const mkdirp = promisify(require('mkdirp'));

function findFilesInDirectory (directory) {
    function createFile (file) {
        return {
            input: path.join(directory.input, file),
            output: path.join(directory.output, file)
        };
    }

    return _.map(
        glob.sync(
            '**/*',
            { cwd: directory.input, nodir: true }
        ),
        createFile
    );
}

function getStaticFilesMap () {
    const directories = findDirectories.frontend('templates');

    const files = _.flatMap(
        directories,
        findFilesInDirectory
    );

    const deDuplicatedFiles = _.keyBy(
        files,
        'output'
    );

    const filesMap = _.mapValues(
        deDuplicatedFiles,
        'input'
    );

    return filesMap;
}

function flattenTemplates () {
    const files = getStaticFilesMap();

    const promises = _.map(
        files,
        (input, output) => {
            const link = path.join(magentoData.build_dir, 'flat/templates', output);
            const linkDir = path.dirname(link);
            const relativeInput = path.relative(linkDir, input);

            return mkdirp(linkDir).then(() => symlink(relativeInput, link));
        }
    );

    return Promise.all(promises).then(() => console.log('Flattened all templates'));
}

flattenTemplates();