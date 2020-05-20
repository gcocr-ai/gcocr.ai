# gcocr.ai

## Description

This repository contains source code for gcocr.ai that uses advanced artificial intelligence to compile C code from a picture of handwritten notes.

## Usage

### Requirements

Make sure you have php (version 7 or higher) installed and that the curl extension is also installed and enabled in php.ini.

### Installing and running

To run a mirror of the service locally, first:
```bash
git clone https://github.com/k0tix/gcocr.ai.git
cd gcorc.ai
```

Then set your environment variables with the help of an example file:
```bash
cp config.php.example config.php
```

And use php's built-in web server to run the service:
```bash
php -S localhost:8000
```


You can compile base64 image by sending it to *localhost:8000/public/indexkax.php* with the following format:
```json
{
    "image": "your base64 encoded image"
}
```

Homepage is served at *localhost:8000/public*
