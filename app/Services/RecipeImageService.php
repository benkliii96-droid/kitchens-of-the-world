<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\RecipeImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecipeImageService
{
    const THUMB_WIDTH = 350;
    const THUMB_HEIGHT = 200;

    public function addImages(Recipe $recipe, array $files, int $defaultIndex = 0): void
    {
        foreach ($files as $index => $file) {
            $this->addImage($recipe, $file, $index === $defaultIndex);
        }
    }

    public function addImage(Recipe $recipe, UploadedFile $file, bool $isDefault = false): RecipeImage
    {
        $folder = 'recepts/' . $recipe->id;
        $name = Str::random(20);
        $ext = strtolower($file->getClientOriginalExtension());

        $originalPath = $file->storeAs($folder . '/originals', $name . '.' . $ext, 'public');

        $image = $recipe->images()->create([
            'original_path' => $originalPath,
            'path' => $folder . '/' . $name . '.' . $ext,
            'thumbnail' => $folder . '/thumb_' . $name . '.webp',
            'is_default' => $isDefault,
        ]);

        $this->makeWatermarkedFiles($image, $recipe->title);

        return $image;
    }

    public function refreshWatermarks(Recipe $recipe): void
    {
        foreach ($recipe->images as $image) {
            $this->makeWatermarkedFiles($image, $recipe->title);
        }
    }

    public function deleteImage(RecipeImage $image): void
    {
        Storage::disk('public')->delete([
            $image->original_path,
            $image->path,
            $image->thumbnail,
        ]);

        $image->delete();
    }

    public function deleteAllImages(Recipe $recipe): void
    {
        foreach ($recipe->images as $image) {
            $this->deleteImage($image);
        }
    }

    public function deleteFolder(Recipe $recipe): void
    {
        Storage::disk('public')->deleteDirectory('recepts/' . $recipe->id);
    }

    private function makeWatermarkedFiles(RecipeImage $image, string $title): void
    {
        $disk = Storage::disk('public');

        $source = imagecreatefromstring($disk->get($image->original_path));

        $fullPath = $disk->path($image->path);
        $thumbPath = $disk->path($image->thumbnail);
        @mkdir(dirname($fullPath), 0777, true);
        @mkdir(dirname($thumbPath), 0777, true);

        $full = $this->copyImage($source);
        $this->addWatermark($full, $title);
        $this->saveImage($full, $fullPath);
        imagedestroy($full);

        $thumb = $this->makeThumbnail($source);
        $this->addWatermark($thumb, $title);
        imagewebp($thumb, $thumbPath, 85);
        imagedestroy($thumb);

        imagedestroy($source);
    }

    private function copyImage($source)
    {
        $width = imagesx($source);
        $height = imagesy($source);

        $copy = imagecreatetruecolor($width, $height);
        imagealphablending($copy, false);
        imagesavealpha($copy, true);
        imagecopy($copy, $source, 0, 0, 0, 0, $width, $height);
        imagealphablending($copy, true);

        return $copy;
    }

    private function makeThumbnail($source)
    {
        $width = imagesx($source);
        $height = imagesy($source);

        $thumb = imagecreatetruecolor(self::THUMB_WIDTH, self::THUMB_HEIGHT);
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefill($thumb, 0, 0, $white);

        $scale = min(self::THUMB_WIDTH / $width, self::THUMB_HEIGHT / $height);
        $newWidth = (int) round($width * $scale);
        $newHeight = (int) round($height * $scale);
        $x = (int) ((self::THUMB_WIDTH - $newWidth) / 2);
        $y = (int) ((self::THUMB_HEIGHT - $newHeight) / 2);

        imagecopyresampled($thumb, $source, $x, $y, 0, 0, $newWidth, $newHeight, $width, $height);

        return $thumb;
    }

    private function addWatermark($image, string $text): void
    {
        $font = resource_path('fonts/DejaVuSans.ttf');
        $width = imagesx($image);
        $height = imagesy($image);

        $size = max(10, (int) ($width / 30));
        $padding = max(5, (int) ($width / 60));

        $box = imagettfbbox($size, 0, $font, $text);
        $textWidth = abs($box[2] - $box[0]);

        $x = $width - $textWidth - $padding;
        $y = $height - $padding;

        $shadow = imagecolorallocatealpha($image, 0, 0, 0, 60);
        $color = imagecolorallocatealpha($image, 255, 255, 255, 30);

        imagettftext($image, $size, 0, $x + 1, $y + 1, $shadow, $font, $text);
        imagettftext($image, $size, 0, $x, $y, $color, $font, $text);
    }

    private function saveImage($image, string $path): void
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($ext === 'png') {
            imagesavealpha($image, true);
            imagepng($image, $path);
        } elseif ($ext === 'webp') {
            imagewebp($image, $path, 90);
        } else {
            imagejpeg($image, $path, 90);
        }
    }
}
