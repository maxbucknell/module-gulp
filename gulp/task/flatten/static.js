const findDirectories = require('prefab/MaxBucknell_Gulp/lib/find-directories');
const magentoData = require('prefab/MaxBucknell_Gulp/lib/magento-data');

const _ = require('lodash');
const path = require('path');
const fs = require('fs');
const promisify = require('util').promisify;

const glob = require('glob');

const rm = promisify(fs.unlink);
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

    console.log(_.filter(
        glob.sync(
            '**/*',
            { cwd: directories[0].input, nodir: true }
        ),
        (file) => file.match('_lib')
    ));

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

            const createSymlink = () => symlink(relativeInput, link);

            return mkdirp(linkDir)
                .then(createSymlink)
                .catch(() => rm(link).then(createSymlink));
        }
    );

    return Promise.all(promises).then(() => console.log('Flattened static directories.'));
}

flattenStatic();