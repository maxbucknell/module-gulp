const findDirectories = require('MaxBucknell_Gulp/lib/find-directories');
const magentoData = require('MaxBucknell_Gulp/lib/magento-data');

const _ = require('lodash');
const path = require('path');
const fs = require('fs');
const promisify = require('util').promisify;

const glob = require('glob');

const symlink = promisify(fs.symlink);
const mkdirp = promisify(require('mkdirp'));

function getStaticFilesMap () {
    const directories = findDirectories.static();

    const files = _.flatMap(
        directories,
        (directory) => _.map(
            glob.sync(
                '**/*',
                { cwd: directory.input, nodir: true }
            ),
            (file) => ({
                input: path.join(directory.input, file),
                output: path.join(directory.output, file)
            })
        )
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

function flattenStatic () {
    const files = getStaticFilesMap();

    const promises = _.map(
        files,
        (input, output) => {
            const link = path.join(magentoData.build_dir, 'flat/static', output);
            const linkDir = path.dirname(link);
            const relativeInput = path.relative(linkDir, input);

            return mkdirp(linkDir).then(() => symlink(relativeInput, link));
        }
    );

    return Promise.all(promises).then('Flattened');
}

flattenStatic();