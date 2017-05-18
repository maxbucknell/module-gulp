const gulp = require('gulp');
const path = require('path');

const findPhrases = require('MaxBucknell_Gulp/lib/find-phrases');
const buildTranslations = require('MaxBucknell_Gulp/lib/build-translations');

function main (config) {
    return gulp.src(['**/*.js', '**/*.html'], { cwd: path.join(config.build_dir, 'flatten') })
        .pipe(findPhrases())
        .pipe(buildTranslations(config))
        .pipe(gulp.dest(config.output_dir));
}

module.exports = main;