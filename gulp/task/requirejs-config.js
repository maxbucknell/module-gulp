const findRequirejsConfigFiles = require('MaxBucknell_Gulp/lib/find-requirejs-config-files');
const wrapRequirejsConfig = require('MaxBucknell_Gulp/lib/wrap-requirejs-config');
const concat = require('gulp-concat');
const gulp = require('gulp');

function main (config) {
    return gulp.src(config.base_dir)
        .pipe(findRequirejsConfigFiles(config))
        .pipe(wrapRequirejsConfig())
        .pipe(concat('requirejs-config.js'))
        .pipe(gulp.dest(config.requirejs_config_dir));
}

module.exports = main;