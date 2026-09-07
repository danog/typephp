# TypePHP macOS AppKit example

This is a native macOS GUI application built from TypePHP and Objective-C++.
TypePHP defines the window layout, application state, click behavior and event
loop. Objective-C++ remains a thin platform bridge that exposes primitive AppKit
operations. Clicking the native button returns a control ID to TypePHP, which
updates the status label.

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

The project uses an `.mm` source because PHPX exposes a C++ API. Its project
configuration enables ARC and links both AppKit and Foundation. This example is
macOS-only; an iOS application would additionally require Xcode project and
code-signing integration, so it is intentionally not mixed into this desktop
example.
