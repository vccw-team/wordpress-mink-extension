const phantomjs = require('phantomjs-prebuilt')
const spawn = require('child_process').spawn
phantomjs.run(
  '--webdriver=4444',
  '--ignore-ssl-errors=yes',
  '--cookies-file=/tmp/webdriver_cookie.txt'
).then(program => {
  const behat = spawn('vendor/bin/behat', [], {stdio: "inherit"} )
  behat.stdout.pipe(process.stdout) // ログを標準出力にパイプ
  behat.on('exit', code => { program.kill() }) // behat終了時に、PhantomJSも終了
})
