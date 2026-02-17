<?php

namespace common\helpers;

use Yii;
use yii\web\UploadedFile;

/**
 * UploadHelper - Centralized file upload handling
 * 
 * Usage examples:
 * 
 * // Upload avatar
 * $avatarPath = UploadHelper::uploadAvatar($file, $userId);
 * 
 * // Upload room image
 * $imagePath = UploadHelper::uploadRoomImage($file, $roomId);
 * 
 * // Get URL for display
 * $avatarUrl = UploadHelper::getAvatarUrl($user->avatar);
 */
class UploadHelper
{
    // Upload types
    const TYPE_AVATAR = 'avatars';
    const TYPE_BUILDING = 'buildings';
    const TYPE_ROOM = 'rooms';
    const TYPE_EQUIPMENT = 'equipment';
    const TYPE_DOCUMENT = 'documents';
    const TYPE_TEMP = 'temp';
    
    // Allowed extensions by type
    const ALLOWED_EXTENSIONS = [
        self::TYPE_AVATAR => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        self::TYPE_BUILDING => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        self::TYPE_ROOM => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        self::TYPE_EQUIPMENT => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        self::TYPE_DOCUMENT => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
        self::TYPE_TEMP => ['*'],
    ];
    
    // Max file sizes (in bytes)
    const MAX_SIZE = [
        self::TYPE_AVATAR => 2 * 1024 * 1024,      // 2MB
        self::TYPE_BUILDING => 5 * 1024 * 1024,    // 5MB
        self::TYPE_ROOM => 5 * 1024 * 1024,        // 5MB
        self::TYPE_EQUIPMENT => 5 * 1024 * 1024,   // 5MB
        self::TYPE_DOCUMENT => 10 * 1024 * 1024,   // 10MB
        self::TYPE_TEMP => 10 * 1024 * 1024,       // 10MB
    ];
    
    /**
     * Get the base upload path
     * @return string
     */
    public static function getBasePath()
    {
        return Yii::getAlias('@uploads');
    }
    
    /**
     * Get the base upload URL
     * @return string
     */
    public static function getBaseUrl()
    {
        return Yii::getAlias('@uploadsUrl');
    }
    
    /**
     * Upload a file
     * 
     * @param UploadedFile $file The uploaded file
     * @param string $type Upload type (avatar, building, room, etc.)
     * @param string|null $customName Optional custom filename (without extension)
     * @return string|false The relative path on success, false on failure
     */
    public static function upload(UploadedFile $file, $type, $customName = null)
    {
        // Validate type
        if (!isset(self::ALLOWED_EXTENSIONS[$type])) {
            Yii::error("Invalid upload type: {$type}", __METHOD__);
            return false;
        }
        
        // Validate extension
        $extension = strtolower($file->extension);
        $allowed = self::ALLOWED_EXTENSIONS[$type];
        if ($allowed[0] !== '*' && !in_array($extension, $allowed)) {
            Yii::error("Extension not allowed: {$extension} for type {$type}", __METHOD__);
            return false;
        }
        
        // Validate size
        $maxSize = self::MAX_SIZE[$type] ?? 5 * 1024 * 1024;
        if ($file->size > $maxSize) {
            Yii::error("File too large: {$file->size} > {$maxSize}", __METHOD__);
            return false;
        }
        
        // Generate filename
        $filename = $customName 
            ? $customName . '.' . $extension
            : uniqid() . '_' . time() . '.' . $extension;
        
        // Get upload path
        $uploadPath = self::getBasePath() . '/' . $type;
        
        // Ensure directory exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Full file path
        $filePath = $uploadPath . '/' . $filename;
        
        // Save file
        if ($file->saveAs($filePath)) {
            // Return relative path
            return $type . '/' . $filename;
        }
        
        Yii::error("Failed to save file: {$filePath}", __METHOD__);
        return false;
    }
    
    /**
     * Upload avatar image
     * 
     * @param UploadedFile $file
     * @param int $userId
     * @return string|false
     */
    public static function uploadAvatar(UploadedFile $file, $userId)
    {
        return self::upload($file, self::TYPE_AVATAR, 'user_' . $userId . '_' . time());
    }
    
    /**
     * Upload building image
     * 
     * @param UploadedFile $file
     * @param int $buildingId
     * @return string|false
     */
    public static function uploadBuildingImage(UploadedFile $file, $buildingId)
    {
        return self::upload($file, self::TYPE_BUILDING, 'building_' . $buildingId . '_' . time());
    }
    
    /**
     * Upload room image
     * 
     * @param UploadedFile $file
     * @param int $roomId
     * @return string|false
     */
    public static function uploadRoomImage(UploadedFile $file, $roomId)
    {
        return self::upload($file, self::TYPE_ROOM, 'room_' . $roomId . '_' . time());
    }
    
    /**
     * Upload equipment image
     * 
     * @param UploadedFile $file
     * @param int $equipmentId
     * @return string|false
     */
    public static function uploadEquipmentImage(UploadedFile $file, $equipmentId)
    {
        return self::upload($file, self::TYPE_EQUIPMENT, 'equipment_' . $equipmentId . '_' . time());
    }
    
    /**
     * Upload document
     * 
     * @param UploadedFile $file
     * @param string|null $customName
     * @return string|false
     */
    public static function uploadDocument(UploadedFile $file, $customName = null)
    {
        return self::upload($file, self::TYPE_DOCUMENT, $customName);
    }
    
    /**
     * Get full URL for a file path
     * 
     * @param string|null $relativePath Relative path from uploads directory
     * @return string|null Full URL or null if path is empty
     */
    public static function getUrl($relativePath)
    {
        if (empty($relativePath)) {
            return null;
        }
        
        // If already a full URL, return as-is
        if (strpos($relativePath, 'http://') === 0 || strpos($relativePath, 'https://') === 0) {
            return $relativePath;
        }
        
        return self::getBaseUrl() . '/' . ltrim($relativePath, '/');
    }
    
    /**
     * Get avatar URL with fallback
     * 
     * @param string|null $avatarPath
     * @param string|null $defaultInitial Initial for default avatar
     * @return string
     */
    public static function getAvatarUrl($avatarPath, $defaultInitial = null)
    {
        if (!empty($avatarPath)) {
            return self::getUrl($avatarPath);
        }
        
        // Return a placeholder or generate initial avatar
        return null;
    }
    
    /**
     * Delete a file
     * 
     * @param string $relativePath Relative path from uploads directory
     * @return bool
     */
    public static function delete($relativePath)
    {
        if (empty($relativePath)) {
            return true;
        }
        
        $fullPath = self::getBasePath() . '/' . ltrim($relativePath, '/');
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return true;
    }
    
    /**
     * Check if file exists
     * 
     * @param string $relativePath
     * @return bool
     */
    public static function exists($relativePath)
    {
        if (empty($relativePath)) {
            return false;
        }
        
        $fullPath = self::getBasePath() . '/' . ltrim($relativePath, '/');
        return file_exists($fullPath);
    }
}
