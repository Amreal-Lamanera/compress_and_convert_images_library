<?php

namespace FPDEV\Images;

use \Exception;
use \Intervention\Image\Drivers\Gd\Driver;
use \Intervention\Image\ImageManager;
use \ZipArchive;


/**
 * Class CompressAndConvertImages
 * @author Francesco Pieraccini
 */
class CompressAndConvertImages
{
    private int $quality;
    private string $extension;

    private const FILE_EXT_ALLOWED = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    /**
     * CompressAndConvertImages constructor.
     *
     * @param string $extension     - extension to convert
     * @param int $quality          - quality to compress
     *
     * @throws Exception
     */
    public function __construct(
        string $extension,
        int $quality
    ) {
        $this->quality = $quality;
        $this->extension = $extension;
    }

    /**
     * Getter for FILE_EXT_ALLOWED.
     *
     * @return string[]
     */
    public function getFileExtAllowed(): array
    {
        return self::FILE_EXT_ALLOWED;
    }

    /**
     * Scan the $dir and return an array with acceptedFiles
     * and discardedFiles.
     *
     * @param string $dir       - path/to/dir to scan
     *
     * @return array
     * @throws NoFilesException
     * @throws NoValidFilesException
     */
    public function getFiles(string $dir): array
    {
        $files = scandir($dir);
        $acceptedFiles = [];
        $discardedFiles = [];

        if (!$files || empty($files)) {
            throw new NoFilesException(
                "Empty directory: {$dir}"
            );
        }

        foreach ($files as $file) {
            $file_ext = explode('.', $file);
            $file_ext = end($file_ext);
            if (
                $file !== '.' &&
                $file !== '..' &&
                $file !== '.gitkeep'
            ) {
                if (
                    in_array(strtolower($file_ext), self::FILE_EXT_ALLOWED)
                ) {
                    $acceptedFiles[] = [
                        'ext' => $file_ext,
                        'filename' => $file
                    ];
                } else {
                    $discardedFiles[] = $file;
                }
            }
        }

        if (count($acceptedFiles) === 0) {
            throw new NoValidFilesException("The directory '$dir' has no valid files.");
        }

        return [$acceptedFiles, $discardedFiles];
    }

    /**
     * A function that utilizes ImageManager to convert and compress the file.
     * Once completed, the compressed file will be saved in the $whereToSaveFile directory.
     *
     * @param array $fileArray           - [filename, ext]
     * @param string $whereToGetFile     - path/to/dir with original files
     * @param string $whereToSaveFile    - path/to/dir where to put output files
     */
    public function compressConvertAndSave(
        array $fileArray,
        string $whereToGetFile,
        string $whereToSaveFile
    ) {
        // create new manager instance with desired driver
        $manager = new ImageManager(Driver::class);

        // read image from file system
        $image = $manager->read(
            $whereToGetFile . '/' . $fileArray['filename']
        );

        $file_name =
            str_replace(".{$fileArray['ext']}", '', $fileArray['filename']);
        $compressed_filepath =
            $whereToSaveFile . "/$file_name." . $this->extension;

        // encode img by path
        $encoded = $image->encodeByPath(
            $compressed_filepath,
            quality: $this->quality
        );
        $encoded->save($compressed_filepath);
    }

    /**
     * Make the zip file that contains all the images in $dirWithFiles.
     * Zip will be saved in the same directory.
     *
     * @param string $dirWithFiles  - path/to/dir with files to zip
     *
     * @return string               - zip filename
     * @throws Exception
     */
    public function zipFiles(string $dirWithFiles): string
    {
        $zip = new ZipArchive();
        $zip_filename = 'IMGS_' . date("Ymd_His") . ".zip";
        $zip_filepath = $dirWithFiles . "/$zip_filename";

        if (
            !$zip->open(
                $zip_filepath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            )
        ) {
            throw new Exception(
                "ZIP file creation failed."
            );
        }

        $files = scandir($dirWithFiles);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== '.gitkeep') {
                $zip->addFile($dirWithFiles . "/$file", $file);
            }
        }

        $zip->close();

        return $zip_filename;
    }

    /**
     * Delete all the files in $dir excepted zip files and .gitkeep file.
     * Call this if you want to clean the output from all compressed images,
     * but maintain the zip file.
     *
     * @param $dir
     */
    public function removeFilesFromDir($dir)
    {
        $files = scandir($dir);

        foreach ($files as $file) {
            if (
                $file !== '.' &&
                $file !== '..' &&
                $file !== '.gitkeep' &&
                !str_contains($file, '.zip')
            ) {
                unlink($dir . '/' . $file);
            }
        }
    }

    /**
     * Support function that returns a fileArray that can be used in the
     * compressConvertAndSave function, derived from the given $filename.
     * The fileArray has the format: `[filename, ext]`
     * 
     * @param string $filename      - string filename
     * @return array                - fileArray
     */
    public function filenameToFileArray(string $filename): array
    {
        $file_ext = explode('.', $filename);
        $file_ext = end($file_ext);
        return [
            'ext' => $file_ext,
            'filename' => $filename
        ];
    }
}
