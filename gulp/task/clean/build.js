const magentoData = require('prefab/MaxBucknell_Gulp/lib/magento-data');
const rimraf = require('rimraf');

rimraf(
    magentoData.build_dir,
    () => console.log(`Build directory cleared for ${magentoData.store_code}`)
);