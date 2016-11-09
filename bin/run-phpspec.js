const phantomjs = require( 'phantomjs-prebuilt' )
const spawn = require( 'child_process' ).spawn

const argv = process.argv
argv.shift()
argv.shift()

phantomjs.run(
  '--webdriver=4444',
  '--ignore-ssl-errors=yes',
  '--cookies-file=/tmp/webdriver_cookie.txt'
).then( program => {
  const phpspec = spawn( 'vendor/bin/phpspec', [ 'run' ], { stdio: "inherit" } )
  phpspec.on( 'exit', ( code ) => {
    program.kill()
    process.exit( code );
  } )
} )
