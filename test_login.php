<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/login', 'POST', [
    'email' => 'admin@admin.com',
    'password' => 'password',
]);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() == 500) {
    if (method_exists($response, 'getOriginalContent')) {
        echo get_class($response->exception) . ": " . $response->exception->getMessage();
    }
}
