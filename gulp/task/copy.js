const gulp = require('gulp');
const path = require('path');

const _ = require('lodash');

function getCopyBlobs () {
    const fileTypes = [
        'css',
        'csv',
        'eot',
        'gif',
        'htc',
        'htm',
        'html',
        'ico',
        'jbf',
        'jpg',
        'js',
        'json',
        'less',
        'map',
        'md',
        'png',
        'sass',
        'scss',
        'svg',
        'swf',
        'ttf',
        'txt',
        'woff',
        'woff2',
    ];

    // All possible globs.
    const src = _.map(fileTypes, (ext) => `**/*.${ext}`);

    return src;
}

function main (config) {
    return gulp.src(getCopyBlobs(), { cwd: path.join(config.build_dir, 'flatten') })
        .pipe(gulp.dest(config.output_dir));
}

module.exports = main;