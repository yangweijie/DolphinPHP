<?php

declare(strict_types=1);

namespace app\common\tools;

use think\Exception;

class DotEnv
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var array<string, string>|null
     */
    private $values;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->getData();
    }

    /**
     * @return array<string, string>
     */
    private function getData(): array
    {
        $file = fopen($this->path, 'r');
        while (!feof($file)) {
            $line = fgets($file);
            if (is_string($line) && !str_starts_with($line, '#') && strpos($line, '=') > 0) {
                $a = explode('=', trim($line));
                $this->values[trim($a[0])] = trim($a[1]);
            }
        }

        fclose($file);

        return $this->values?? [];
    }



    public function asArray(): array
    {
        return $this->values;
    }

    public function add(string $key, string $value): void
    {
        if (!$this->values) {
            $this->values = $this->asArray();
        }

        $this->values[$key] = $value;
    }

    public function write(): void
    {
        if (!$this->values) {
            return;
        }
        ksort($this->values);

        $content = '';
        foreach ($this->values as $key => $value) {
            $content .= $key . ' = ' . $value . \PHP_EOL;
        }

        if (!$handle = fopen($this->path, 'w')) {
            throw new Exception();
        }

        $success = fwrite($handle, $content);
        fclose($handle);

        if (!$success) {
            throw new Exception();
        }
    }

    /**
     *
     */
    public function overwriteFromEnv(): void
    {
        if (!$this->values) {
            $this->values = $this->asArray();
        }

        foreach (($_ENV + $_SERVER) as $key => $value) {
            if (isset($this->values[$key])) {
                $this->values[$key] = $value;
            }
        }

        $this->write();
    }
}
