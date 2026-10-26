<?php

namespace Database\Factories\Concerns;

trait UsesPublicImageAssets
{
    protected function randomPublicImagePath(): string
    {
        $files = glob(public_path('assets/media/images/*')) ?: [];
        $files = array_values(array_filter(
            $files,
            static fn (string $file): bool => is_file($file)
                && preg_match('/\.(jpe?g|png|webp|gif)$/i', $file) === 1,
        ));

        if ($files === []) {
            return 'assets/media/images/1.jpg';
        }

        return 'assets/media/images/' . basename(fake()->randomElement($files));
    }
}
