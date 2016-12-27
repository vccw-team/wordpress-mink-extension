const phantomjs = require( 'phantomjs-prebuilt' )
const spawn = require( 'child_process' ).spawn

const argv = process.argv
argv.shift()
argv.shift()

args = [
  'php',
  '-d',
  'memory_limit=-1',
  'vendor/bin/behat'
]

phantomjs.run(
  '--webdriver=4444',
  '--ignore-ssl-errors=yes',
  '--cookies-file=/tmp/webdriver_cookie.txt'
).then( program => {
  const behat = spawn( '/usr/bin/env', args.concat( argv ), { stdio: "inherit" } )
  behat.on( 'exit', ( code ) => {
    program.kill()
    process.exit( code );
  } )
} )
