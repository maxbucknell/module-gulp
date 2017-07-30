const findLessFiles = require('MaxBucknell_Gulp/lib/find-less-files');
const magentoData = require('MaxBucknell_Gulp/lib/magento-data');
const _ = require('lodash');
const path = require('path');
const spawn = require('spawn-command');
const IS_WINDOWS = /^win/.test(process.platform);
const promisify = require('util').promisify;
const mkdirp = promisify(require('mkdirp'));

function getSpawnOpts () {
    const result = {};

    if (IS_WINDOWS) {
        result.detached = false;
    }

    return result;
}

function less () {
    const commandTemplate = _.template(process.argv[2], { interpolate: /{{([\s\S]+?)}}/g });
    const files = findLessFiles();

    const args = _.map(
        files,
        (file) => ({
            input: path.join(magentoData.build_dir, 'flat/static', `${file}.less`),
            output: path.join(magentoData.output_dir, `${file}.css`)
        })
    );

    const directories = _.map(
        files,
        (file) => path.dirname(path.join(magentoData.output_dir, file))
    );

    const commands = _.map(
        args,
        commandTemplate
    );

    const command = `concurrently "${commands.join('" "')}"`;

    Promise.all(_.map(directories, (d) => mkdirp(d)))
        .then(
            () => {
                const runner = spawn(
                    command,
                    getSpawnOpts()
                );

                runner.stdout.on('data', function (data) {
                    console.log(data.toString('utf-8'));
                });

                runner.stderr.on('data', function (data) {
                    console.log(data.toString('utf-8'));
                });
            }
        );
}

less();