module.exports = {
  assetsDir: 'assets',
  lintOnSave: undefined,
  css: {
    loaderOptions: {
      sass: {
        data: `
          @import "@/assets/scss/_variables.scss";
          @import "@/assets/scss/_mixins.scss";
          @import "@/assets/scss/_fonts.scss";
        `,
      },
    },
  },
}