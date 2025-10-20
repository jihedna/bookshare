<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SimpleOcrService
{
    /**
     * Extract text from an image using Tesseract OCR
     *
     * @param string $imagePath Path to the image file
     * @return string Extracted text
     */
    public function extractText(string $imagePath): string
    {
        // Preprocess the image
        $preprocessedPath = $this->preprocessImage($imagePath);
        
        // Use Tesseract OCR to extract text
        try {
            $output = [];
            $command = 'tesseract ' . escapeshellarg($preprocessedPath) . ' stdout';
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                Log::error('Tesseract OCR failed with return code: ' . $returnCode);
                return '';
            }
            
            return implode("\n", $output);
        } catch (\Exception $e) {
            Log::error('Error running Tesseract OCR: ' . $e->getMessage());
            return '';
        } finally {
            // Clean up the preprocessed image if it's different from the original
            if ($preprocessedPath !== $imagePath && file_exists($preprocessedPath)) {
                @unlink($preprocessedPath);
            }
        }
    }
    
    /**
     * Preprocess the image for better OCR results
     *
     * @param string $imagePath Path to the image file
     * @return string Path to the preprocessed image
     */
    private function preprocessImage(string $imagePath): string
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            Log::warning('GD extension not available for image preprocessing');
            return $imagePath;
        }
        
        try {
            // Get image info
            $imageInfo = getimagesize($imagePath);
            if ($imageInfo === false) {
                return $imagePath;
            }
            
            // Create image resource based on type
            $mime = $imageInfo['mime'];
            switch ($mime) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($imagePath);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($imagePath);
                    break;
                default:
                    return $imagePath;
            }
            
            if ($image === false) {
                return $imagePath;
            }
            
            // Auto-rotate based on EXIF data (if it's a JPEG)
            if ($mime === 'image/jpeg') {
                $image = $this->autoRotateImage($image, $imagePath);
            }
            
            // Convert to grayscale
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            
            // Increase contrast
            imagefilter($image, IMG_FILTER_CONTRAST, -10);
            
            // Save the preprocessed image
            $preprocessedPath = $imagePath . '.processed.jpg';
            imagejpeg($image, $preprocessedPath, 90);
            imagedestroy($image);
            
            return $preprocessedPath;
        } catch (\Exception $e) {
            Log::error('Error preprocessing image: ' . $e->getMessage());
            return $imagePath;
        }
    }
    
    /**
     * Auto-rotate image based on EXIF data
     *
     * @param resource $image Image resource
     * @param string $imagePath Path to the image file
     * @return resource Rotated image resource
     */
    private function autoRotateImage($image, string $imagePath)
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }
        
        try {
            $exif = @exif_read_data($imagePath);
            if ($exif === false || !isset($exif['Orientation'])) {
                return $image;
            }
            
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;
                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }
            
            return $image;
        } catch (\Exception $e) {
            return $image;
        }
    }
}
