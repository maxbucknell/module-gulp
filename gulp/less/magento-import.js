const magentoData = require('MaxBucknell_Gulp/lib/magento-data');
const _ = require('lodash');
const path = require('path');
const fs = require('fs');

const plugin = {
    expr: /\/\/\s*@magento_import\s+(['"])(.+\.less)\1\s*;/g,

    process: function (fileContents) {
        return fileContents.replace(
            this.expr,
            this.getImport.bind(this)
        );
    },

    getImport: function (line, quote, file) {
        const possibleFiles = _.map(
            magentoData.modules,
            (location, moduleName) => path.join(moduleName, 'css', file)
        );

        const files = _.filter(
            possibleFiles,
            (possibleFile) => fs.existsSync(path.join(magentoData.build_dir, 'flat/static', possibleFile))
        );

        const importStatements = _.map(
            files,
            (file) => `@import '../${file}';`
        );

        return importStatements.join('\n');
    }
};

module.exports = {
    install: function (less, pluginManager) {
        pluginManager.addPreProcessor(plugin, 1);
    }
}
