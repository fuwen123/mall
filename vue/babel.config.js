module.exports = {
  presets: ['@vue/app'],
  plugins: [
    // [
    //   'import',
    //   {
    //     libraryName: 'vant',
    //     libraryDirectory: 'es',
    //     style: true
    //   }
    // ]
    // [
    //   'import',
    //   {
    //     libraryName: 'vant',
    //     libraryDirectory: 'es',
    //     style: true
    //   },
    //   'vant'
    // ],
    [
      'component',
      {
        libraryName: 'mint-ui',
        // libraryDirectory: 'es',
        style: true
      }
    ]
  ]
}
