module.exports = {
    store_code: process.env.STORE_CODE,
    config: JSON.parse(process.env.CONFIG),
    modules: JSON.parse(process.env.MODULES),
    themes: JSON.parse(process.env.THEMES),
    phrases: JSON.parse(process.env.PHRASES),
    base_dir: process.env.BASE_DIR,
    output_dir: process.env.OUTPUT_DIR,
    build_dir: process.env.BUILD_DIR,
    requirejs_config_dir: process.env.REQUIREJS_CONFIG_DIR,
    postcss: JSON.parse(process.env.POSTCSS)
};