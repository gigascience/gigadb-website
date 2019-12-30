module.exports = function (api) {

	console.log('********** Loading Babel config ***************************');
  api.cache(true);

  const presets = [ '@babel/preset-env' ];
  // const plugins = [ 'istanbul' ];

  return {
    presets,
    "env": {
            "test": {
                "plugins": ["istanbul"]
            }
        }
  };
}