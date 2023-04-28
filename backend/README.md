# Backend

## Development
### Starting the server
```
    ./vendor/bin/sail up
```

Look into ``composer.json``, the scripts section provides for an easy way of executing 
out code quality assurance scripts. You can run them by 
```
composer <script_json_key>
```

or simply run all of them with ``composer check``. You should run this everytime before you push.
