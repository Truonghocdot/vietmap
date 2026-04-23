<?php

namespace App\Support;

use App\Models\Package;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class MirrorPageRenderer
{
    public function render(string $relativePath): string
    {
        $absolutePath = resource_path('mirror-site/' . ltrim($relativePath, '/'));

        abort_unless(is_file($absolutePath), 404);

        $html = file_get_contents($absolutePath);

        return $this->applySharedReplacements($html);
    }

    public function renderHome(Collection $packages): string
    {
        $html = $this->render('index.html');

        if ($packages->isEmpty()) {
            return $html;
        }

        $servicePayload = [
            'vietmap' => [
                'name' => 'VIETMAP LIVE PRO',
                'packages' => $packages
                    ->map(function (Package $package): array {
                        $discount = null;

                        if ($package->compare_at_price && $package->compare_at_price > $package->price) {
                            $discount = (int) round(
                                100 - (($package->price / $package->compare_at_price) * 100)
                            );
                        }

                        $tags = collect([$package->badge])
                            ->filter()
                            ->map(fn (?string $tag): string => Str::slug($tag))
                            ->values()
                            ->all();

                        return [
                            'id' => $package->id,
                            'hours' => $package->duration_hours,
                            'name' => $package->name,
                            'price' => $package->price,
                            'oldPrice' => $package->compare_at_price ?: $package->price,
                            'tags' => $tags,
                            'discount' => $discount,
                        ];
                    })
                    ->values()
                    ->all(),
            ],
        ];

        $replacement = 'const servicePricesV2 = ' . json_encode(
            $servicePayload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        ) . ';';

        return preg_replace('/const servicePricesV2 = \{.*?\n\};/s', $replacement, $html) ?: $html;
    }

    protected function applySharedReplacements(string $html): string
    {
        $appUrl = rtrim(config('app.url'), '/');

        $replacements = [
            'https://thuevietmap.vn' => $appUrl,
            'https://thuevietmap.vn/order-detail' => route('orders.search'),
            'href="thue-vietmap-live-pro.html"' => 'href="' . route('storefront.home') . '"',
            'href="/thue-vietmap-live-pro"' => 'href="' . route('storefront.home') . '"',
            'href="order-history-ip.html"' => 'href="' . route('orders.history') . '"',
            'href="../order-history-ip.html"' => 'href="' . route('orders.history') . '"',
            'href="ma-giam-gia.html"' => 'href="/ma-giam-gia"',
            'href="../ma-giam-gia.html"' => 'href="/ma-giam-gia"',
            'href="terms.html"' => 'href="/terms"',
            'href="../terms.html"' => 'href="/terms"',
            'href="lien-he.html"' => 'href="/lien-he"',
            'href="../lien-he.html"' => 'href="/lien-he"',
            'href="blog.html"' => 'href="/blog"',
            'href="../blog.html"' => 'href="/blog"',
            'action="https://thuevietmap.vn/order-detail"' => 'action="' . route('orders.search') . '"',
            "action='https://thuevietmap.vn/order-detail'" => "action='" . route('orders.search') . "'",
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }
}
