const gulp = require('gulp');
const htmlMinifier = require('gulp-htmlmin');
const sourcemaps = require('gulp-sourcemaps');
const path = require('path');
const getGlobs = require('MaxBucknell_Gulp/lib/get-globs');

function main (config) {
    console.log(getGlobs(['html', 'htm']));
    return gulp.src(getGlobs(['html', 'htm']), { cwd: path.join(config.build_dir, 'flatten') })
        .pipe(htmlMinifier({
            collapseWhitespace: true,
            customAttrCollapse: /data-bind/,
            conservativeCollapse: true,
            ignoreCustomComments: [/ko/],
            minifyJS: true,
            minifyCSS: true,
            processScripts: ['text/x-magento-template'],
            removeScriptTypeAttributes: true,
            removeStyleLinkTypeAttributes: true,
            sortClassName: true,
            sortAttributes: true,
            removeRedundantAttributes: true,
            removeOptionalTags: true,
            removeComments: true,
        }))
        .pipe(gulp.dest(config.output_dir));
}

module.exports = main;