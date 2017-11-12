const magentoData = require('prefab/MaxBucknell_Prefab/lib/magento-data');
const run = require('prefab/MaxBucknell_Prefab/lib/run');

run(`postcss -r -c prefab/MaxBucknell_Prefab/postcss.config.js ${magentoData.output_dir}/**/*.css`);
