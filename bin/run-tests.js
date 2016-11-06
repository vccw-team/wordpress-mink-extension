const phantomjs = require( 'phantomjs-prebuilt' )
const spawn = require( 'child_process' ).spawn
phantomjs.run(
  '--webdriver=4444',
  '--ignore-ssl-errors=yes',
  '--cookies-file=/tmp/webdriver_cookie.txt'
).then( program => {
  const behat = spawn( 'vendor/bin/behat', [], { stdio: "inherit" } )
  behat.on( 'exit', ( code ) => {
    program.kill()
    process.exit( code );
  } )
} )
