const spawn = require('spawn-command');
const IS_WINDOWS = /^win/.test(process.platform);

function getSpawnOpts () {
    const result = {};

    if (IS_WINDOWS) {
        result.detached = false;
    }

    return result;
}

function main (command) {
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

module.exports = main;