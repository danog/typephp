# TypePHP Apple native GUI examples

This directory contains matching macOS AppKit and iPhoneOS UIKit applications.
Both targets reuse `php-src/application.php`: TypePHP defines the window layout,
application state and click behavior, while Objective-C++ only exposes small
native UI operations. Clicking the native button returns a control ID to
TypePHP, which updates the status label.

## macOS

Requirements:

- macOS with Xcode Command Line Tools
- PHP 8.4 or 8.5 built with the embed SAPI (`libphp.dylib`)
- a matching PHPX build
- `PHPX_HOME` pointing to the PHPX source tree

Build from the TypePHP repository root:

```sh
export PHPX_HOME=/path/to/phpx
php bin/tpc.php examples/objective-c-macos/project.yml --no-progress
```

Run the native executable:

```sh
./examples/objective-c-macos/typephp_macos_hello
```

To create a standard `.app` bundle that can be launched from Finder:

```sh
sh examples/objective-c-macos/package-app.sh
open "examples/objective-c-macos/dist/TypePHP macOS Hello.app"
```

## iPhoneOS (physical iPhone)

The iPhone build is cross-compiled on macOS. It requires full Xcode (Command
Line Tools alone do not contain the iPhoneOS SDK), an Apple Development signing
identity, a provisioning profile for `org.swoole.typephp.ios-hello`, and an
`iphoneos-arm64` TypePHP SDK with this layout:

```text
<sdk>/
├── include/php/...
├── lib/
│   ├── libphp.a
│   ├── libphpx.a
│   ├── libgmp.a
│   ├── libgmpxx.a
│   └── libmpfr.a
└── .typephp-ios-sdk-abi
```

The PHP and every third-party archive in this prefix must be built for
`arm64-apple-ios`; a macOS/Homebrew archive cannot be linked into an iPhoneOS
binary. Apple system libraries and UIKit/Foundation remain SDK frameworks and
are linked by Xcode rather than copied into `libphp.a`.

After selecting full Xcode, verify the SDK:

```sh
sudo xcode-select --switch /Applications/Xcode.app/Contents/Developer
xcrun --sdk iphoneos --show-sdk-path
```

Install this SDK at `ios/iphoneos-arm64` inside the PHPX checkout, matching the
integrated layouts already used by `full-static/sdk` and
`wasm/wasm32-wasip2`. Build the executable from the TypePHP repository root;
`PHPX_HOME` selects both PHPX sources and the target SDK:

```sh
export PHPX_HOME=/path/to/phpx
php bin/tpc.php examples/objective-c-macos/ios.yml --no-progress
```

The compiler produces `examples/objective-c-macos/typephp_ios_hello`. Package
and sign it with a development provisioning profile and identity installed in
the login keychain:

```sh
export TYPEPHP_IOS_PROVISIONING_PROFILE=/path/to/profile.mobileprovision
export TYPEPHP_IOS_CODE_SIGN_IDENTITY='Apple Development: Your Name (TEAMID)'
sh examples/objective-c-macos/package-ios-app.sh
```

Find the connected iPhone and install the bundle:

```sh
xcrun devicectl list devices
xcrun devicectl device install app \
    --device <device-id> \
    'examples/objective-c-macos/dist/TypePHP iOS Hello.app'
```

The `.mm` bridges are deliberately thin. AppKit/UIKit own native controls and
deliver events, but the shared TypePHP class owns the application behavior.
