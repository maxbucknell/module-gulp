const gulp = require('gulp');
const uglify = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
const path = require('path');
const getGlobs = require('MaxBucknell_Gulp/lib/get-globs');

function main (config) {
    return gulp.src(getGlobs(['js']), { cwd: path.join(config.build_dir, 'flatten') })
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest(config.output_dir));
}

module.exports = main;