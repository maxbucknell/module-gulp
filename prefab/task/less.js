const findLessFiles = require('prefab/MaxBucknell_Prefab/lib/find-less-files');
const magentoData = require('prefab/MaxBucknell_Prefab/lib/magento-data');
const _ = require('lodash');
const path = require('path');
const promisify = require('util').promisify;
const mkdirp = promisify(require('mkdirp'));
const run = require('prefab/MaxBucknell_Prefab/lib/run');

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
        .then(() => run(command));
}

less();