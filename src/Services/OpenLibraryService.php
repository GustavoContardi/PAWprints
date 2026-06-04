<?php

namespace Services;

class OpenLibraryService
{
    private const SEARCH_URL = 'https://openlibrary.org/search.json';
    private const WORK_URL = 'https://openlibrary.org';
    private const COVER_URL = 'https://covers.openlibrary.org/b/id';

    public static function searchByTitle(string $query): ?array
    {
        $url = self::SEARCH_URL . '?q=' . urlencode($query) . '&limit=5';
        $data = self::fetch($url);
        if (!$data || empty($data['docs'])) {
            return null;
        }

        $results = [];
        foreach ($data['docs'] as $doc) {
            $coverId = $doc['cover_i'] ?? null;
            $result = [
                'title'            => $doc['title'] ?? '',
                'author'           => $doc['author_name'][0] ?? '',
                'isbn'             => $doc['isbn'][0] ?? null,
                'cover_i'          => $coverId,
                'cover_url'        => $coverId ? self::COVER_URL . "/{$coverId}-M.jpg" : null,
                'large_cover_url'  => $coverId ? self::COVER_URL . "/{$coverId}-L.jpg" : null,
                'first_publish_year' => $doc['first_publish_year'] ?? null,
                'key'              => $doc['key'] ?? null,
                'subjects'         => array_slice($doc['subject'] ?? [], 0, 5),
            ];

            if (!empty($doc['key'])) {
                $workData = self::getWorkDetails($doc['key']);
                if ($workData && !empty($workData['description'])) {
                    $desc = $workData['description'];
                    $result['description'] = is_string($desc) ? $desc : ($desc['value'] ?? '');
                }
            }

            $results[] = $result;
        }

        return $results;
    }

    public static function getWorkDetails(string $key): ?array
    {
        $url = self::WORK_URL . $key . '.json';
        return self::fetch($url);
    }

    public static function getCoverUrl(int $coverId, string $size = 'L'): string
    {
        return self::COVER_URL . "/{$coverId}-{$size}.jpg";
    }

    public static function downloadCover(int $coverId, string $destPath): ?string
    {
        $url = self::getCoverUrl($coverId, 'L');
        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            $url = self::getCoverUrl($coverId, 'M');
            $imageData = @file_get_contents($url);
        }
        if ($imageData === false) {
            return null;
        }

        $ext = 'jpg';
        $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $fullPath = rtrim($destPath, '/') . '/' . $filename;

        if (file_put_contents($fullPath, $imageData) === false) {
            return null;
        }

        return $filename;
    }

    public static function searchAndDownloadFirstCover(string $query, string $destPath): ?string
    {
        $results = self::searchByTitle($query);
        if (!$results || empty($results)) {
            return null;
        }

        foreach ($results as $result) {
            if (!empty($result['cover_i'])) {
                $filename = self::downloadCover((int)$result['cover_i'], $destPath);
                if ($filename) {
                    return $filename;
                }
            }
        }

        return null;
    }

    private static function fetch(string $url): ?array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: PAWprints/1.0\r\nAccept: application/json\r\n",
                'timeout' => 10,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }
}
