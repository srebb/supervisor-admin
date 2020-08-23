const path = require('path');

const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
    mode: 'development',
    entry: './assets/js/main.js',
    output: {
        filename: 'supervisor-admin.js',
        path: path.resolve(__dirname, './src/Resources/public/js/'),
    },
    plugins: [
        // make sure to include the plugin!
        new VueLoaderPlugin(),
    ],
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
            },
        ],
    },
};
