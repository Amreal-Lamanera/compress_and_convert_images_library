# Compress And Convert Images Library <a href="https://www.francescopieraccini.it/"><img src="./light_blue_nome.svg"  alt="FrancescoPieraccini Logo" width="75" style="vertical-align:middle; padding-left:1rem;"/></a>
Hello there! This is my compress and convert library. It is based on the intervention/image package,
and its goal is to help users manage images by converting and compressing them.

If you encounter any issues or have questions, feel free to reach out! 
And don't forget to check out my website at:
[Francesco Pieraccini Website](https://www.francescopieraccini.it/)


## Installation
    composer require fpdev/compress_and_convert


## Usage

- Create a `CompressAndConvert` object using the constructor, which requires two
  mandatory parameters:
  
    1. `string $extension` - extension to convert
    2. `int $quality` - quality to compress

    Here's an example of how to create it:
    ```php
    $obj = new FPDEV\Images\CompressAndConvert(extension: 'webp', quality: 50);
    ```
    This will create an object of `CompressAndConvertImages` that can be used to convert images to 
  webp format and compress them to 50% quality.


- `compressConvertAndSave` function:

    This function requires three mandatory parameters:
    
    1. `array $fileArray` - [filename, ext]
    2. `string $whereToGetFile` - path/to/dir with original files
    3. `string $whereToSaveFile` - path/to/dir where to put output files
    
    This function utilizes `ImageManager` to convert and compress the file as you set in 
    the constructor. Once completed, the converted and compressed file will be saved in the `$whereToSaveFile` directory
    and the function returns the compressed file name.


- `getFileExtAllowed` function:

    This is a getter function that returns the allowed file extensions.


- `getFileArraysFromDir` function:

    This function requires one mandatory parameter:
    
    1. `string $dir` - path/to/dir to scan
    
    This function scans the `$dir` and returns an array with accepted and discarded 
    files in this format:

    ```php
    $acceptedFiles = ['ext', 'filename'];
    $discardedFiles = ['filename'];
    ```


- `zipFiles` function:

    This function requires one mandatory parameter:
    
    1. `string $dirWithFiles` - path/to/dir with files to zip
    
    This function creates a zip file containing all the images in `$dirWithFiles`. 
    The zip file will be saved in the same directory.


- `removeFilesFromDir` function:

    This function requires one mandatory parameter:
    
    1. `string $dir` - path/to/dir
    
    This function deletes all the files in `$dir` except zip files and .gitkeep file.
    Call this function if you want to clean the output from all compressed images 
    but maintain the zip file.

- `filenameToFileArray` function:

    This function requires one mandatory parameter:
    
    1. `string $filename` - a filename
    
    This function returns a fileArray that can be used in the `compressConvertAndSave` function, derived from the given filename. The fileArray has the format: `[filename, ext]`

### License

This library is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
