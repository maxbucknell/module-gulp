const fs = require('fs-extra');
const path = require('path');
const magentoData = require('prefab/MaxBucknell_Prefab/lib/magento-data');
const _ = require('lodash');

const validExtensions = _.map(
    process.argv.slice(2),
    (ext) => `.${ext}`
);

function copy () {
    console.log(magentoData.build_dir, magentoData.output_dir);
    fs.copy(
        path.join(magentoData.build_dir, 'flat/static'),
        magentoData.output_dir,
        {
            dereference: true,
            filter: (src) => {
                const ext = path.extname(src);

                return !(ext.length) || _.includes(validExtensions, ext);
            }
        }
    )
}

copy();