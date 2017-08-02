const magentoData = require('MaxBucknell_Gulp/lib/magento-data');

const _ = require('lodash');
const path = require('path');
const fs = require('fs');

module.exports = {
    frontend: findFrontendDirectories,
    static: findStaticDirectories,
    requirejsConfig: findRequirejsConfigFiles
};

function findFrontendModuleDirectories (name) {
    return _.flatMap(
        magentoData.modules,
        (location, moduleName) => ([
            {
                input: path.join(location, 'view/base', name),
                output: moduleName
            },
            {
                input: path.join(location, 'view/frontend', name),
                output: moduleName
            }
        ])
    );
}

function getThemeModuleOverrides (theme, name) {
    return _.flatMap(
        magentoData.modules,
        (location, moduleName) => ([
            {
                input: path.join(theme, moduleName, name),
                output: moduleName
            }
        ])
    );
}

function findFrontendThemeDirectories (name) {
    return _.flatMap(
        magentoData.themes,
        (theme) => getThemeModuleOverrides(theme, name)
    );
}

function findFrontendDirectories (name) {
    const moduleLocations = findFrontendModuleDirectories(name);
    const themeLocations = findFrontendThemeDirectories(name);

    return _.concat(
        moduleLocations,
        themeLocations
    );
}

function findStaticThemeDirectories () {
    return _.flatMapDeep(
        magentoData.themes,
        (theme) => ([
            getThemeModuleOverrides(theme, 'web'),
            {
                input: path.join(theme, 'web'),
                output: ''
            },
            {
                input: path.join(theme, 'web/i18n', magentoData.config['general/locale/code']),
                output: ''
            }
        ])
    );
}

function findStaticDirectories () {
    const moduleLocations = findFrontendModuleDirectories('web');
    const themeLocations = findStaticThemeDirectories('web');

    return _.concat(
        [
            {
                input: path.join(magentoData.base_dir, 'lib/web'),
                output: ''
            }
        ],
        moduleLocations,
        themeLocations
    )
}

function findRequirejsConfigFiles () {
    const moduleLocations = findFrontendModuleDirectories('requirejs-config.js');
    const themeLocations = _.flatMapDeep(
        magentoData.themes,
        (theme) => ([
            getThemeModuleOverrides(theme, 'requirejs-config.js'),
            {
                input: path.join(theme, 'requirejs-config.js'),
                output: ''
            }
        ])
    );

    return _.concat(
        moduleLocations,
        themeLocations
    );
}