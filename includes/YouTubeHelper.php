<?php

class YouTubeHelper {
    
    public static function fetchVideoData($url) {
        $videoId = self::extractVideoId($url);
        if (!$videoId) {
            return null;
        }
        
        $data = [
            'video_id' => $videoId,
            'title' => '',
            'description' => '',
            'thumbnail' => '',
            'duration' => ''
        ];
        
        // Get data from YouTube oEmbed API (no API key required)
        $oembedUrl = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$videoId}&format=json";
        $response = @file_get_contents($oembedUrl);
        
        if ($response) {
            $oembedData = json_decode($response, true);
            if ($oembedData) {
                $data['title'] = $oembedData['title'] ?? '';
                $data['description'] = $oembedData['author_name'] ?? '';
            }
        }
        
        // Get thumbnail
        $data['thumbnail'] = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
        
        // Try to get additional data from YouTube page
        $pageUrl = "https://www.youtube.com/watch?v={$videoId}";
        $html = @file_get_contents($pageUrl);
        
        if ($html) {
            // Extract title from meta tags if not already set
            if (empty($data['title']) && preg_match('/<meta property="og:title" content="([^"]+)"/', $html, $matches)) {
                $data['title'] = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
            }
            
            // Extract description
            if (preg_match('/<meta property="og:description" content="([^"]+)"/', $html, $matches)) {
                $data['description'] = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
            }
            
            // Extract duration
            if (preg_match('/"lengthSeconds":"(\d+)"/', $html, $matches)) {
                $seconds = intval($matches[1]);
                $minutes = floor($seconds / 60);
                $secs = $seconds % 60;
                $data['duration'] = sprintf("%d:%02d", $minutes, $secs);
            }
        }
        
        return $data;
    }
    
    public static function extractVideoId($url) {
        // Support various YouTube URL formats
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $match)) {
            return $match[1];
        }
        
        // Check if it's already a video ID
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }
        
        return null;
    }
    
    public static function getThumbnailUrl($videoId, $quality = 'maxresdefault') {
        // Available qualities: default, mqdefault, hqdefault, sddefault, maxresdefault
        return "https://img.youtube.com/vi/{$videoId}/{$quality}.jpg";
    }
}
