<?php

namespace App\Console\Commands;

use App\Support\MenuImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshMenuImagesCommand extends Command
{
    protected $signature = 'menus:refresh-images';

    protected $description = 'Update menu images to match menu names and inventory codes';

    public function handle(): int
    {
        $updated = 0;

        foreach (DB::table('menus')->get() as $menu) {
            $imgUrl = MenuImage::resolve($menu);

            if ($menu->img_url !== $imgUrl) {
                DB::table('menus')->where('id', $menu->id)->update([
                    'img_url' => $imgUrl,
                    'updated_at' => now(),
                ]);
                $updated++;
            }
        }

        $this->info("Updated {$updated} menu image(s).");

        return self::SUCCESS;
    }
}
