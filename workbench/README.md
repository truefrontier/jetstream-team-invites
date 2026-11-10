# Test app stubs

The package expects a Jetstream app with `App\Models\User` and `App\Models\Team`.
These are the smallest versions of those classes the tests need. They are loaded
only through `autoload-dev`, so they never ship to apps that install the package.
