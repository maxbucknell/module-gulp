const findLayoutDirectories = require('prefab/MaxBucknell_Prefab/lib/find-layout-directories');
const glob = require('glob');
const _ = require('lodash');
const fs = require('fs');
const path = require('path');
const magentoData = require('prefab/MaxBucknell_Prefab/lib/magento-data');

const cssPattern = /<css\s+src="([^"]+)\.css"/;
const removePattern = /<remove\s+src="([^"]+)\.css"/;

function getGlobs () {
    return _.map(
        findLayoutDirectories(),
        (directory) => `${directory}/*.xml`
    )
}

function findLessFiles()
{
    const files = _.flatMap(
        getGlobs(),
        (g) => glob.sync(g)
    );

    const contents = _.map(
        files,
        (file) => fs.readFileSync(file, { encoding: 'utf-8' })
    ).join('\n');

    const cssDeclarations = _.map(
        contents.match(new RegExp(cssPattern, 'g')),
        (match) => match.match(cssPattern)[1]
    );

    const removeDeclarations = _.map(
        contents.match(new RegExp(removePattern, 'g')),
        (match) => match.match(removePattern)[1]
    );

    const stylesheets = _.difference(
        cssDeclarations,
        removeDeclarations
    );

    const existingStylesheets = _.filter(
        stylesheets,
        (stylesheet) => fs.existsSync(path.join(magentoData.build_dir, 'flat/static', `${stylesheet}.less`))
    );

    return existingStylesheets;
}

module.exports = findLessFiles;