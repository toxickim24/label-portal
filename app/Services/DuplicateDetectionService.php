<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Collection;

class DuplicateDetectionService
{
    /**
     * Find duplicates by email.
     *
     * @param string $email
     * @return Contact|null
     */
    public function findByEmail(string $email): ?Contact
    {
        if (empty($email)) {
            return null;
        }

        return Contact::where('email', $email)->first();
    }

    /**
     * Find duplicates by phone.
     *
     * @param string $phone
     * @return Contact|null
     */
    public function findByPhone(string $phone): ?Contact
    {
        if (empty($phone)) {
            return null;
        }

        // Normalize phone number (remove non-numeric characters)
        $normalizedPhone = preg_replace('/[^0-9]/', '', $phone);

        return Contact::whereRaw('REPLACE(REPLACE(REPLACE(phone, "-", ""), "(", ""), ")", "") LIKE ?', ["%{$normalizedPhone}%"])
            ->first();
    }

    /**
     * Find duplicates by name and address combination.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $address
     * @return Contact|null
     */
    public function findByNameAndAddress(string $firstName, string $lastName, string $address): ?Contact
    {
        if (empty($firstName) || empty($lastName) || empty($address)) {
            return null;
        }

        return Contact::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->where('address', 'LIKE', "%{$address}%")
            ->first();
    }

    /**
     * Detect duplicate contact using multiple strategies.
     *
     * @param array $data
     * @return array
     */
    public function detect(array $data): array
    {
        $duplicates = [];

        // Check by email (primary)
        if (!empty($data['email'])) {
            $contact = $this->findByEmail($data['email']);
            if ($contact) {
                $duplicates[] = [
                    'type' => 'email',
                    'contact' => $contact,
                    'message' => "Duplicate found by email: {$data['email']}",
                ];
            }
        }

        // Check by phone (secondary)
        if (!empty($data['phone']) && empty($duplicates)) {
            $contact = $this->findByPhone($data['phone']);
            if ($contact) {
                $duplicates[] = [
                    'type' => 'phone',
                    'contact' => $contact,
                    'message' => "Duplicate found by phone: {$data['phone']}",
                ];
            }
        }

        // Check by name + address (tertiary)
        if (
            !empty($data['first_name']) &&
            !empty($data['last_name']) &&
            !empty($data['address']) &&
            empty($duplicates)
        ) {
            $contact = $this->findByNameAndAddress($data['first_name'], $data['last_name'], $data['address']);
            if ($contact) {
                $duplicates[] = [
                    'type' => 'name_address',
                    'contact' => $contact,
                    'message' => "Duplicate found by name and address",
                ];
            }
        }

        return [
            'is_duplicate' => !empty($duplicates),
            'matches' => $duplicates,
        ];
    }

    /**
     * Handle duplicate based on resolution strategy.
     *
     * @param Contact $existing
     * @param array $newData
     * @param string $strategy (skip, update, merge)
     * @return array
     */
    public function handleDuplicate(Contact $existing, array $newData, string $strategy = 'skip'): array
    {
        switch ($strategy) {
            case 'update':
                // Overwrite existing with new data
                $existing->update($newData);
                return [
                    'action' => 'updated',
                    'contact' => $existing,
                ];

            case 'merge':
                // Merge data (keep non-empty fields from both)
                $mergedData = [];
                foreach ($newData as $key => $value) {
                    // Use new value if it's not empty, otherwise keep existing
                    $mergedData[$key] = !empty($value) ? $value : $existing->{$key};
                }
                $existing->update($mergedData);
                return [
                    'action' => 'merged',
                    'contact' => $existing,
                ];

            case 'skip':
            default:
                // Keep existing, ignore new data
                return [
                    'action' => 'skipped',
                    'contact' => $existing,
                ];
        }
    }

    /**
     * Batch detect duplicates from array of data.
     *
     * @param array $dataArray
     * @return Collection
     */
    public function batchDetect(array $dataArray): Collection
    {
        $results = collect();

        foreach ($dataArray as $index => $data) {
            $detection = $this->detect($data);
            $results->push([
                'row_index' => $index,
                'data' => $data,
                'is_duplicate' => $detection['is_duplicate'],
                'matches' => $detection['matches'],
            ]);
        }

        return $results;
    }
}
