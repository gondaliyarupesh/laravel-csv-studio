<?php

namespace AestheticStudio\CsvImporter\Services;

class CsvParserService
{
    /**
     * Parse a CSV file and return the headers and data.
     *
     * @param string $filePath
     * @param int $limit Max rows to parse (0 for unlimited)
     * @return array
     */
    public function parse(string $filePath, int $limit = 0): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \InvalidArgumentException("CSV file is not readable: " . $filePath);
        }

        // Auto-detect separator
        $separator = $this->detectSeparator($filePath);

        $headers = [];
        $rows = [];
        $rowCount = 0;

        if (($handle = fopen($filePath, 'r')) !== false) {
            // Read UTF-8 BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            while (($data = fgetcsv($handle, 0, $separator)) !== false) {
                // Skip empty rows
                if (count($data) === 1 && $data[0] === null) {
                    continue;
                }

                // Convert encoding to UTF-8 if needed
                $data = array_map(function ($value) {
                    if ($value === null) return '';
                    $encoding = mb_detect_encoding($value, mb_detect_order(), true);
                    if ($encoding && $encoding !== 'UTF-8') {
                        return mb_convert_encoding($value, 'UTF-8', $encoding);
                    }
                    return trim($value);
                }, $data);

                if (empty($headers)) {
                    // Filter headers to remove empty values or clean spaces
                    $headers = array_map(function ($header) {
                        return trim($header) ?: 'Column_' . uniqid();
                    }, $data);
                } else {
                    // Match data length with headers count
                    $row = [];
                    foreach ($headers as $index => $header) {
                        $row[$header] = $data[$index] ?? '';
                    }
                    $rows[] = $row;
                    $rowCount++;

                    if ($limit > 0 && $rowCount >= $limit) {
                        break;
                    }
                }
            }
            fclose($handle);
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * Auto-detect CSV separator (, or ; or \t or |)
     */
    protected function detectSeparator(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ',';
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if (!$firstLine) {
            return ',';
        }

        $delimiters = [
            ',' => substr_count($firstLine, ','),
            ';' => substr_count($firstLine, ';'),
            "\t" => substr_count($firstLine, "\t"),
            '|' => substr_count($firstLine, '|'),
        ];

        arsort($delimiters);
        return key($delimiters) ?: ',';
    }
}
