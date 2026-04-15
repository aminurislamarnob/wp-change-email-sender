const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        admin: './src/admin.js',
    },
    output: {
        path: path.resolve(__dirname, 'assets/build'),
        filename: '[name]/script.js',
    },
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules
        ],
    },
    resolve: {
        extensions: ['.js', '.jsx']
    }
};