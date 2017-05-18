const gulp = require('gulp');
const findLayoutDirectories = require('MaxBucknell_Gulp/lib/find-layout-directories');
const findLessFiles = require('MaxBucknell_Gulp/lib/find-less-files');
const magentoImporter = require('MaxBucknell_Gulp/lib/magento-importer');
const less = require('gulp-less');
const sourcemaps = require('gulp-sourcemaps');

function main (config) {
    return gulp.src(findLayoutDirectories(config))
        .pipe(findLessFiles(config))
        .pipe(magentoImporter(config))
        .pipe(sourcemaps.init())
        .pipe(less())
        .pipe(sourcemaps.write('../maps/css'))
        .pipe(gulp.dest(config.output_dir));
}

module.exports = main;