const magentoData = require('MaxBucknell_Gulp/lib/magento-data');
const run = require('MaxBucknell_Gulp/lib/run');

run(`postcss -r -c MaxBucknell_Gulp/postcss.config.js ${magentoData.output_dir}/**/*.css`);
