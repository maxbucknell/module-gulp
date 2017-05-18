const rimraf = require('rimraf');

function main (config) {
    rimraf.sync(config.build_dir);
    rimraf.sync(config.output_dir);
    rimraf.sync(config.requirejs_config_dir);
}

module.exports = main;