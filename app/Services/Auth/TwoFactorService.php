<?php

namespace App\Services\Auth;

use App\Models\User;
use PragmaRX\Google2FALaravel\Facade as Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;

class TwoFactorService
{
    /**
     * Generate a new 2FA secret for the user.
     */
    public function generateSecret(): string
    {
        return Google2FA::generateSecretKey();
    }

    /**
     * Get the QR Code SVG for the user's secret.
     */
    public function getQrCodeSvg(User $user, string $secret): string
    {
        $companyName = config('app.name', 'CatalogApp');
        $qrCodeUrl = Google2FA::getQRCodeUrl(
            $companyName,
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        return $writer->writeString($qrCodeUrl);
    }

    /**
     * Validate the TOTP code.
     */
    public function verifyCode(string $secret, string $code): bool
    {
        return Google2FA::verifyKey($secret, $code);
    }

    /**
     * Generate recovery codes.
     */
    public function generateRecoveryCodes(): array
    {
        return collect(range(1, 8))->map(function () {
            return Str::random(10) . '-' . Str::random(10);
        })->all();
    }
}
