# xo-lib [![Build Status](https://travis-ci.org/vatesfr/xo-lib.png?branch=master)](https://travis-ci.org/vatesfr/xo-lib)

> Library to connect to XO-Server.

## Install

Installation of the [npm package](https://npmjs.org/package/xo-lib):

```
npm install --save xo-lib
```

Then require the package:

```javascript
import Xo from 'xo-lib'
```

## Usage

> If the URL is not provided and the current environment is a web
> browser, the location of the current page will be used.

```javascript
// Connect to XO.
const xo = new Xo({ url: 'https://xo.company.tld' })

// Let's start by opening the connection.
await xo.connect()

// Must sign in before being able to call any methods (all calls will
// be buffered until signed in).
await xo.signIn({
  email: 'admin@admin.net',
  password: 'admin'
})

console('signed as', xo.user)
```

The credentials can also be passed directly to the constructor:

```javascript
const xo = Xo({
  url: 'https://xo.company.tld',
  credentials: {
    email: 'admin@admin.net',
    password: 'admin',
  }
})

xo.open()

xo.on('authenticated', () => {
  console.log(xo.user)
})
```

> If the URL is not provided and the current environment is a web
> browser, the location of the current page will be used.

### Connection

```javascript
await xo.open()

console.log('connected')
```

### Disconnection

```javascript
xo.close()

console.log('disconnected')
```

### Method call

```javascript
const token = await xo.call('token.create')

console.log('Token created', token)
```

### Status

The connection status is available through the status property which
is *open*, *connecting* or *closed*.

```javascript
console.log('%s to xo-server', xo.status)
```

### Current user

Information about the user account used to sign in is available
through the `user` property.

```javascript
console.log('Current user is', xo.user)
```

> This property is null when the status is not connected.

### Events

```javascript
xo.on('open', () => {
  console.log('connected')
})
```

```javascript
xo.on('closed', () => {
  console.log('disconnected')
})
```

```javascript
xo.on('notification', function (notif) {
  console.log('notification:', notif.method, notif.params)
})
```

```javascript
xo.on('authenticated', () => {
  console.log('authenticated as', xo.user)
})

xo.on('authenticationFailure', () => {
  console.log('failed to authenticate')
})
```

## Development

```
# Install dependencies
> npm install

# Run the tests
> npm test

# Continuously compile
> npm run dev

# Continuously run the tests
> npm run dev-test

# Build for production (automatically called by npm install)
> npm run build
```

## Contributions

Contributions are *very* welcomed, either on the documentation or on
the code.

You may:

- report any [issue](https://github.com/vatesfr/xo-lib/issues)
  you've encountered;
- fork and create a pull request.

## License

ISC © [Vates SAS](http://vates.fr)
