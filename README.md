<a href="https://supportukrainenow.org/"><img src="https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner-direct.svg" width="100%"></a>

------

# ECB

ECB is is a deploy script runner, made for you.

## Requirements 

- php 8.0+
- composer

## Installation

Installing is just a simple global require.

```
    composer global require elijahcruz/ecb
```

## Usage

First you need to create a deploy script.

You can use ecb.json or ecb.init as your deploy script, you can create this using the following command:

 ```
    ecb init
 ```

or for JSON:

    ```
        ecb init --json
    ```

You can also Change the type and name of the project if it's different from the default:

    ```
        ecb init --type=laravel --name=MyAwesomeProject
    ```

Then you can add your commands to the ecb file. Once you have all the steps you need, just run it:

```
    ecb run
```

And that's it!
