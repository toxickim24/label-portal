<?php

namespace App\Services;

use Exception;

class CSVParserService
{
    /**
     * Parse a CSV file and return headers and data.
     *
     * @param string $filePath
     * @return array
     */
    public function parse(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new Exception('File not found');
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new Exception('Unable to open file');
        }

        $headers = [];
        $data = [];
        $rowNumber = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($rowNumber === 1) {
                // First row is headers
                $headers = array_map('trim', $row);
                continue;
            }

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Combine headers with row data
            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? '';
            }

            $data[] = $rowData;
        }

        fclose($handle);

        return [
            'headers' => $headers,
            'data' => $data,
            'total_rows' => count($data),
        ];
    }

    /**
     * Detect the delimiter used in the CSV file.
     *
     * @param string $filePath
     * @return string
     */
    public function detectDelimiter(string $filePath): string
    {
        $delimiters = [',', ';', "\t", '|'];
        $handle = fopen($filePath, 'r');
        $firstLine = fgets($handle);
        fclose($handle);

        $maxCount = 0;
        $detectedDelimiter = ',';

        foreach ($delimiters as $delimiter) {
            $count = substr_count($firstLine, $delimiter);
            if ($count > $maxCount) {
                $maxCount = $count;
                $detectedDelimiter = $delimiter;
            }
        }

        return $detectedDelimiter;
    }

    /**
     * Get preview of CSV file (first N rows).
     *
     * @param string $filePath
     * @param int $limit
     * @return array
     */
    public function preview(string $filePath, int $limit = 10): array
    {
        $result = $this->parse($filePath);

        return [
            'headers' => $result['headers'],
            'data' => array_slice($result['data'], 0, $limit),
            'total_rows' => $result['total_rows'],
        ];
    }

    /**
     * Suggest column mapping based on common aliases.
     *
     * @param array $headers
     * @return array
     */
    public function suggestMapping(array $headers): array
    {
        $aliases = [
            'first_name' => ['first name', 'firstname', 'fname', 'given name', 'forename'],
            'last_name' => ['last name', 'lastname', 'lname', 'surname', 'family name'],
            'email' => ['email', 'e-mail', 'email address', 'mail'],
            'phone' => ['phone', 'telephone', 'tel', 'mobile', 'cell', 'phone number'],
            'address' => ['address', 'street', 'street address', 'address 1'],
            'city' => ['city', 'town'],
            'state' => ['state', 'province', 'region'],
            'zip' => ['zip', 'postal code', 'postcode', 'zip code', 'postal'],
            'contact_type' => ['type', 'contact type', 'category', 'lead type'],
            'status' => ['status', 'stage', 'pipeline'],
            'source' => ['source', 'lead source', 'origin'],
            'priority' => ['priority', 'importance'],
        ];

        $mapping = [];

        foreach ($headers as $header) {
            $normalizedHeader = strtolower(trim($header));

            foreach ($aliases as $field => $aliasList) {
                if (in_array($normalizedHeader, $aliasList)) {
                    $mapping[$header] = $field;
                    break;
                }
            }

            // If no match found, leave unmapped
            if (!isset($mapping[$header])) {
                $mapping[$header] = null;
            }
        }

        return $mapping;
    }

    /**
     * Validate CSV file.
     *
     * @param string $filePath
     * @return array
     */
    public function validate(string $filePath): array
    {
        $errors = [];

        // Check file exists
        if (!file_exists($filePath)) {
            $errors[] = 'File does not exist';
            return ['valid' => false, 'errors' => $errors];
        }

        // Check file size (max 10MB)
        $fileSize = filesize($filePath);
        if ($fileSize > 10 * 1024 * 1024) {
            $errors[] = 'File size exceeds 10MB limit';
        }

        // Check if file is readable
        if (!is_readable($filePath)) {
            $errors[] = 'File is not readable';
        }

        // Try to parse first few rows
        try {
            $this->preview($filePath, 5);
        } catch (Exception $e) {
            $errors[] = 'Invalid CSV format: ' . $e->getMessage();
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
