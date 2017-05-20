const gulp = require('gulp');
const _ = require('lodash');
const findLayoutDirectories = require('MaxBucknell_Gulp/lib/find-layout-directories');
const findLessFiles = require('MaxBucknell_Gulp/lib/find-less-files');
const magentoImporter = require('MaxBucknell_Gulp/lib/magento-importer');
const less = require('gulp-less');
const sourcemaps = require('gulp-sourcemaps');
const postcss = require('gulp-postcss');
const rtlcss = require('rtlcss');
const rename = require('gulp-rename');

function main (config) {
    const plugins = [];

    if (config.config['general/locale/code'].slice(0, 2) === 'ar') {
        plugins.push(rtlcss());
    }

    _.each(config.postcss, function (pluginConfig, name) {
        plugins.push(require(name).apply(null, pluginConfig.args));
    });

    return gulp.src(findLayoutDirectories(config))
        .pipe(findLessFiles(config))
        .pipe(magentoImporter(config))
        .pipe(sourcemaps.init())
        .pipe(less())
        .pipe(postcss(plugins))
        .pipe(rename(function (path) {
            path.dirname = path.dirname.replace(`../build/${config.store_code}/flatten/`, '');
        }))
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest(config.output_dir));
}

module.exports = main;