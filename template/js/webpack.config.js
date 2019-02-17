module.exports = {
    entry: './src/App.jsx',
    mode: 'production',
    module: {
      rules: [
        {
          test: /\.(js|jsx)$/,
          exclude: /node_modules/,
          use: ['babel-loader']
        },
        {
          test: /\.css$/,
          use: ['style-loader', 'css-loader'],
        },
      ]
    },
    output: {
      path: __dirname + '../../../assets',
      filename: 'bundle.js'
    },
    // devtool: 'source-map'
};
