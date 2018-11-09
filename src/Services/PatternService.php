<?php


namespace Laratomics\Services;


use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Laratomics\Models\Pattern;
use Spatie\YamlFrontMatter\YamlFrontMatter;


class PatternService
{
    const INITIAL_STATE = 'TODO';
    const BLADE_EXTENSION = 'blade.php';
    const SASS_EXTENSION = 'scss';
    const MARKDOWN_EXTENSION = 'md';

    /**
     * Crate all required files for the given new Pattern.
     *
     * @param string $name
     * @param string $description
     * @return Pattern
     */
    public function createPattern(string $name, string $description): Pattern
    {
        $pattern = new Pattern();
        $pattern->name = $name;
        $pattern->template = $this->createBladeFile($name);
        $pattern->markdown = $this->createMarkdownFile($name, $description);
        $pattern->sass = $this->createSassFile($name);
        return $pattern;
    }

    /**
     * Create a new Blade file for the Pattern.
     *
     * @param string $pattern
     * @return string
     */
    public function createBladeFile(string $pattern): string
    {
        $file = $this->getFileLocation($pattern, self::BLADE_EXTENSION);
        $content = "<!-- {$pattern} -->";
        File::put($file, $content);
        return $content;
    }

    /**
     * Create a new Markdown file for the Pattern.
     *
     * @param string $pattern
     * @param string $description
     * @return string
     */
    public function createMarkdownFile(string $pattern, string $description): string
    {
        $file = $this->getFileLocation($pattern, self::MARKDOWN_EXTENSION);

        $content = sprintf("---
        status: %s
        values:
        ---
        {$description}", self::INITIAL_STATE);
        $content = str_replace('        ', '', $content);
        File::put($file, $content);
        return $content;
    }

    /**
     * Create a sass file for the newly created component and import it in parent and main sass files.
     *
     * @param string $pattern
     * @return string
     */
    public function createSassFile(string $pattern): string
    {
        $file = $this->getFileLocation($pattern, self::SASS_EXTENSION);
        $content = "/* {$pattern} */";
        File::put($file, $content);

        $this->importInParentSassFile($pattern);
        return $content;
    }

    /**
     * Import the generated sass file in the parent sass file.
     *
     * @param string $pattern
     */
    private function importInParentSassFile(string $pattern): void
    {
        $parts = explode('.', $pattern);
        $parent = array_shift($parts);
        $parentSassPath = config('workshop.patternPath') . "/{$parent}/{$parent}.scss";
        $includeFile = implode('/', $parts);

        /*
         * Import in sass file in parent sass file
         */
        if (!File::exists($parentSassPath)) {
            $content = "@import \"{$includeFile}\";";

            $this->importInMainSassFile($parent);
        }

        File::append($parentSassPath, $content);
    }

    /**
     * Import in main sass file
     * @param string $parent
     */
    private function importInMainSassFile(string $parent): void
    {
        File::append(config('workshop.patternPath') . '/patterns.scss',
            "@import \"{$parent}/{$parent}\";");
    }

    /**
     * @param $name
     * @param array $values
     * @return Pattern
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function loadPattern($name, $values = []): Pattern
    {
        $pattern = new Pattern();
        $pattern->template = $this->loadBladeFile($name);
        $markdown = $this->loadMarkdownFile($name);
        $pattern->markdown = $markdown;
        $pattern->metadata = YamlFrontMatter::parse($markdown);
        $pattern->sass = $this->loadSassFile($name);
        $pattern->state = 'DONE';

        /*
         * Create the preview
         */
        $values = !is_null($pattern->metadata->values) ? $pattern->metadata->values : array_merge($values, []);
        $pattern->preview = compileBladeString($pattern->template, $values);

        return $pattern;
    }

    /**
     * Load the pattern template.
     *
     * @param string $pattern
     * @return string
     * @throws FileNotFoundException
     */
    public function loadBladeFile(string $pattern): string
    {
        $file = $this->getFileLocation($pattern, self::BLADE_EXTENSION);
        return File::get($file);
    }

    /**
     * Load the pattern markdown file.
     *
     * @param string $pattern
     * @return string
     * @throws FileNotFoundException
     */
    public function loadMarkdownFile(string $pattern): string
    {
        $file = $this->getFileLocation($pattern, self::MARKDOWN_EXTENSION);
        return File::get($file);
    }

    /**
     * Load the pattern sass file.
     *
     * @param string $pattern
     * @return string
     * @throws FileNotFoundException
     */
    public function loadSassFile(string $pattern): string
    {
        $file = $this->getFileLocation($pattern, self::SASS_EXTENSION);
        return File::get($file);
    }

    /**
     * Get the full file location (path, filename, extension).
     *
     * @param string $pattern
     * @param string $extension
     * @return string
     */
    private function getFileLocation(string $pattern, string $extension): string
    {
        $parts = explode('.', $pattern);
        $filename = array_pop($parts);
        $subpath = implode('/', $parts);
        $directory = config('workshop.patternPath') . "/{$subpath}";

        $this->createIfMissing($directory);

        $fullFilename = "{$filename}.{$extension}";

        $location = "{$directory}/{$fullFilename}";
        return $location;
    }

    /**
     * Check if the given directory exists. If it does not exists, the whole directory path is created.
     *
     * @param $directory
     */
    private function createIfMissing($directory): void
    {
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 493, true);
        }
    }

}