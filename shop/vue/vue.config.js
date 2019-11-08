module.exports = {
  publicPath: '',
  productionSourceMap: false,
  css: {
    // 启用 CSS modules
    modules: false,
    // 是否使用css分离插件
    extract: true,
    // 开启 CSS source maps，一般不建议开启
    sourceMap: false,
    loaderOptions: {
      stylus: {
        'resolve url': true,
        import: []
      },
      sass: {
        data: `
        @import "@/assets/style/_variable.scss";
        @import "@/assets/style/my-mint.scss";
        `
      },
      postcss: {
        plugins: [
          require('postcss-pxtorem')({
            rootValue: 25, // 换算的基数
            selectorBlackList: ['weui', 'mu'], // 忽略转换正则匹配项
            propList: ['*']
          })
        ]
      }
    }
  },
  pluginOptions: {
    'cube-ui': {
      postCompile: true,
      theme: false
    }
  },
  devServer: {
    proxy: {
      '/host': {
        // target: 'https://bd.timierhouse.com/api', // 要访问的那个url.
        target: 'https://bd.timierhouse.com/api', // 要访问的那个url.
        changeOrigin: true,
        ws: true,
        pathRewrite: {
          '^/host': '/'
        }
      }
    }
  },
  outputDir: '../public/mobile'
}
