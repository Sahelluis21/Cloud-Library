require_once __DIR__ . '/../controller/AuthController.php';
require_once __DIR__ . '/../controller/HomeController.php';

Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/home', [HomeController::class, 'index']);
Route::post('/logout', [AuthController::class, 'logout']);
