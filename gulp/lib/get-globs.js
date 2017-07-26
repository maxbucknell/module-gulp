const _ = require('lodash');

function getCopyBlobs (fileTypes) {
    // All possible globs.
    return _.map(fileTypes, (ext) => `**/*.${ext}`);
}

module.exports = getCopyBlobs;