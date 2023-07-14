<?php

namespace Sentgine\Crudwizard\Traits;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File as FacadeFile;
use Illuminate\Support\Str;

/**
 * This trait represents a set of file manipulation methods.
 */
trait File
{
    /**
     * Appends specific content to a file.
     * 
     * @param string $origin - The path to the original file.
     * @param string $destination - The path where the modified file will be saved.
     * @param string $content - The content to be appended to the file.
     * @param string $somewhere - The string after which the content should be inserted.
     * 
     * @return void
     */
    public function appendContentToAFile(string $origin, string $destination, string $content, string $somewhere): void
    {
        // Create a filesystem instance
        $filesystem = new Filesystem();

        // Get the content of the original file
        $fileContent = $filesystem->get($origin);

        // Check if the content already exists in the file
        if (!$this->isContentExistsOnTheFile($fileContent, $content)) {
            // Find the position of the "use HasFactory;" statement
            $position = strpos($fileContent, $somewhere) + strlen($somewhere);

            // Insert the new content after the "use HasFactory;" statement
            $newFileContent = substr($fileContent, 0, $position) . $content . substr($fileContent, $position);

            // Append the new content to the file
            $filesystem->put($destination, $newFileContent);
        }
    }

    /**
     * Checks if the content exists in the given file.
     * 
     * @param string $fileContent - The content of the file to be searched.
     * @param string $searchContent - The content to search for in the file.
     * 
     * @return bool - Returns true if the content exists in the file, false otherwise.
     */
    public function isContentExistsOnTheFile(string $fileContent, string $searchContent): bool
    {
        if (strpos($fileContent, $searchContent) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Generates a file by replacing placeholders with provided values.
     * 
     * @param string $origin - The path to the original file (stub).
     * @param string $destination - The path where the generated file will be saved.
     * @param array $replacements - An associative array containing the placeholders as keys and their corresponding values.
     * 
     * @return void
     */
    public function generateFileFromStub(string $origin, string $destination, array $replacements = []): void
    {
        // Create a filesystem instance
        $filesystem = new Filesystem();

        // Replace the content of the original file (stub)
        $fileContent = $this->getFileAndReplaceContent($origin, $replacements);

        // Save the rendered stub to a file
        $filesystem->put($destination, $fileContent);
    }

    /**
     * Get the file content from the original file (stub) and replace placeholders with actual values.
     *
     * @param string $origin The path to the original file (stub).
     * @param array $replacements The key-value pairs for replacing placeholders.
     * @return string The content of the file with replaced placeholders.
     */
    public function getFileAndReplaceContent(string $origin, array $replacements = []): string
    {
        // Create a filesystem instance
        $filesystem = new Filesystem();

        // Read the content of the original file (stub)
        $fileContent = $filesystem->get($origin);

        // Replace placeholders in the content with actual values
        if (!empty($replacements)) {
            foreach ($replacements as $key => $value) {
                $placeholder = '{{ ' . $key . ' }}';
                $fileContent = Str::replace($placeholder, $value, $fileContent);
            }
        }

        return $fileContent;
    }

    /**
     * Check if file exists.
     * 
     * @param string $destination - The path to the destination where the file is checked.
     * @param string $filename - The name of the file being checked.
     * 
     * @return bool
     */
    public function isFileExists(string $destination): bool
    {
        // Create a filesystem instance
        $filesystem = new Filesystem();

        // Check if the file already exists and is not forced
        if ($filesystem->exists($destination)) {
            return true;
        }

        return false;
    }

    /**
     * Get the name of the latest migration file from the `database/migrations` directory.
     *
     * @return string|null The name of the latest migration file, or null if no migration files are found.
     */
    public function getLatestMigrationFilename()
    {
        $migrationPath = database_path('migrations'); // Full path to the `database/migrations` directory
        $migrationFiles = scandir($migrationPath); // Retrieve all filenames in the directory

        $latestMigration = null; // Variable to store the latest migration filename

        foreach ($migrationFiles as $file) {
            if ($file !== '.' && $file !== '..') { // Exclude the `.` and `..` directories
                if ($latestMigration === null || strcmp($file, $latestMigration) > 0) {
                    // If `$latestMigration` is null or the current filename is greater (lexicographically) than the previous latest migration
                    $latestMigration = $file; // Assign the current filename as the new latest migration
                }
            }
        }

        return $latestMigration; // Return the name of the latest migration file
    }

    /**
     * Remove a file from the filesystem.
     *
     * @param string $pathToFile The path of the file to be removed.
     * @return bool Returns true if the file was successfully removed, false otherwise.
     */
    public function removeFile(string $pathToFile): bool
    {
        $filesystem = new Filesystem();

        // Check if the file exists
        if (!$filesystem->exists($pathToFile)) {
            return false; // File doesn't exist, return false
        }

        // Delete the file
        $filesystem->delete($pathToFile);

        return true; // File successfully removed
    }

    /**
     * Remove a directory from the filesystem.
     *
     * @param string $pathToDirectory The path of the directory to be removed.
     * @return bool Returns true if the directory was successfully removed, false otherwise.
     */
    public function removeDirectory(string $pathToDirectory): bool
    {
        $filesystem = new Filesystem();

        // Check if the directory exists
        if (!$filesystem->isDirectory($pathToDirectory)) {
            return false; // Directory doesn't exist, return false
        }

        // Delete the directory
        $filesystem->deleteDirectory($pathToDirectory);

        return true; // directory successfully removed
    }

    /**
     * Append content at the end of the file.
     * 
     * @param string $pathToFile
     * @param string $content
     * @param bool $checkIfContentExistsFlag (optional)
     * 
     * @return bool
     */
    public function appendToFile(string $pathToFile, string $content, bool $checkIfContentExistsFlag = false): bool
    {
        // Create a filesystem instance
        $filesystem = new Filesystem();

        // Read the content of the original file
        $fileContent = $filesystem->get($pathToFile);

        // If you want check the if the content exists.
        if ($checkIfContentExistsFlag && $this->isContentExistsOnTheFile($fileContent, $content)) {
            return false;
        }

        if (strpos($fileContent, $content) === false) {
            $result = file_put_contents($pathToFile, $content, FILE_APPEND);
            return $result !== false;
        }

        return false;
    }

    /**
     * Creates a directory at the specified path.
     *
     * @param string $pathToFile The path to the directory.
     * @return bool Returns true if the directory was created successfully, false otherwise.
     */
    public function createDirectory(string $pathToDirectory): bool
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($pathToDirectory)) {
            $filesystem->makeDirectory($pathToDirectory);
            return true;
        }

        return false;
    }

    /**
     * Creates a file at the specified path.
     *
     * @param string $pathToFile The path to the file.
     * @param string $content The content to write into the file.
     * @return bool Returns true if the file was created successfully, false otherwise.
     */
    public function createFile(string $pathToFile, string $content = ""): bool
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($pathToFile)) {
            $filesystem->put($pathToFile, $content);
            return true;
        }

        return false;
    }

    /**
     * Creates a directory recursively.
     *
     * @param string $path The path of the directory to create.
     * @return bool Returns true if the directory was created successfully, false otherwise.
     */
    public function createDirectoryRecursively(string $path): bool
    {
        try {
            // Replace backslashes with forward slashes
            $path = str_replace('\\', '/', $path);

            // Split the path into individual directory names
            $directories = explode('/', $path);

            // Create the first directory
            $currentPath = $directories[0];
            FacadeFile::makeDirectory($currentPath, 0777, true, true);

            // Create the remaining directories (if any)
            for ($i = 1; $i < count($directories); $i++) {
                $currentPath .= '/' . $directories[$i];
                FacadeFile::makeDirectory($currentPath, 0777, true, true);
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
