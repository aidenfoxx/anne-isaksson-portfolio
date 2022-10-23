# Phoxx MVC

Phoxx is an attempt at creating a modular, lightweight, HMVC framework, based on a strong core.

## Features:

- Request and response architecture
- HVMC routing
- Modular structure
- Doctrine ORM
- Extensive cache support (Memcached, Apcu, Redis, File, Array)
- Multiple renderers (Twig, Smarty, PHP)
- Easily extensible

## Notes to self:

- Stuff breaks if the `PATH_*` variables are empty, with `.` being minimum. These paths should always be absolute, except for in tests.
- Maybe Session and Cache should use "storeValue" as opposed to "setValue"?

## TODO
- Work on paths. Maybe make UNIX style paths the default? PATH_PUBLIC is also kinda weird in that is had to be relative to root.
- Investigate string concatenation performance.
- Comparing SERVER_NAME may be incorrect if SERVER_PORT differs.
