const gulp = require('gulp');

const findStaticDirectories = require('MaxBucknell_Gulp/lib/find-static-directories');
const findFiles = require('MaxBucknell_Gulp/lib/find-files');
const generateSymlinks = require('MaxBucknell_Gulp/lib/generate-symlinks');

const path = require('path');

function main (config) {
    return gulp.src(config.base_dir)
        .pipe(findStaticDirectories(config))
        .pipe(findFiles())
        .pipe(generateSymlinks(path.join(config.build_dir, 'flatten')));
}

module.exports = main;