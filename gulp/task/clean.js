const rimraf = require('rimraf');

function main (config) {
    rimraf.sync(config.build_dir);
    rimraf.sync(config.output_dir);
}

module.exports = main;