# ReflectionBrowser
A webapp to browse the PHP runtime, like classes, methods, extensions.

# Key features
* No external dependencies
* A very sweet way to explore the PHP runtime
* Documentation by looking what is **actually** there

# Known issues
* No active maintainer. (Please open an issue if you like to take over!)
* No support for code and extension loading for a code base inspection. This can be done by loading the source via <code>include()</code> in <code>index.php</code> or make it sweet by exposing it in the UI. You can keep track of the loaded stuff in a session which makes it multi-user friendly.
* No symbol search.
* Not profiled and optimized for performance because for me it is fast enough.
