const magentoData = require('prefab/MaxBucknell_Gulp/lib/magento-data');
const run = require('prefab/MaxBucknell_Gulp/lib/run');

run(`postcss -r -c prefab/MaxBucknell_Gulp/postcss.config.js ${magentoData.output_dir}/**/*.css`);
