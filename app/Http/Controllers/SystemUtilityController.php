<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SystemUtilityController extends Controller
{
    /**
     * Display a system utilities dashboard.
     */
    public function index(Request $request)
    {
        $storageLinked = File::exists(public_path('storage'));
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        $env = app()->environment();

        return response($this->renderDashboard([
            'storage_linked'  => $storageLinked,
            'php_version'     => $phpVersion,
            'laravel_version' => $laravelVersion,
            'environment'     => $env,
        ]));
    }

    /**
     * Clear all application caches (config, route, view, general cache).
     */
    public function clearCache(Request $request)
    {
        try {
            $output = [];

            Artisan::call('optimize:clear');
            $output[] = trim(Artisan::output());

            Artisan::call('cache:clear');
            $output[] = trim(Artisan::output());

            Artisan::call('config:clear');
            $output[] = trim(Artisan::output());

            Artisan::call('route:clear');
            $output[] = trim(Artisan::output());

            Artisan::call('view:clear');
            $output[] = trim(Artisan::output());

            $combinedOutput = implode("\n", array_filter($output));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'action'  => 'clear-cache',
                    'output'  => $combinedOutput,
                ]);
            }

            return response($this->renderResult(
                title: 'Cache Cleared Successfully',
                command: 'php artisan optimize:clear && cache:clear',
                output: $combinedOutput,
                isSuccess: true
            ));
        } catch (\Throwable $e) {
            Log::error('Utility Clear Cache Failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return response($this->renderResult(
                title: 'Cache Clear Failed',
                command: 'php artisan optimize:clear',
                output: $e->getMessage() . "\n" . $e->getTraceAsString(),
                isSuccess: false
            ), 500);
        }
    }

    /**
     * Create the public storage symbolic link.
     */
    public function storageLink(Request $request)
    {
        try {
            $target = public_path('storage');
            $alreadyExists = File::exists($target) || is_link($target);

            // Recreate if requested (?force=1)
            if ($alreadyExists && $request->boolean('force')) {
                if (is_link($target) || is_dir($target)) {
                    @unlink($target);
                }
                $alreadyExists = false;
            }

            if ($alreadyExists) {
                $output = "The [public/storage] link already exists.\nDestination: " . $target . "\nTarget: " . storage_path('app/public') . "\n(Pass ?force=1 to force recreate if broken)";
                $success = true;
            } else {
                Artisan::call('storage:link');
                $output = trim(Artisan::output()) ?: "The [public/storage] link has been created successfully.";
                $success = true;
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => $success,
                    'action'  => 'storage-link',
                    'output'  => $output,
                ]);
            }

            return response($this->renderResult(
                title: 'Storage Link Result',
                command: 'php artisan storage:link',
                output: $output,
                isSuccess: $success
            ));
        } catch (\Throwable $e) {
            Log::error('Utility Storage Link Failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return response($this->renderResult(
                title: 'Storage Link Failed',
                command: 'php artisan storage:link',
                output: $e->getMessage() . "\n" . $e->getTraceAsString(),
                isSuccess: false
            ), 500);
        }
    }

    /**
     * Run pending database migrations.
     */
    public function migrate(Request $request)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = trim(Artisan::output()) ?: "Nothing to migrate or migration finished with no output.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'action'  => 'migrate',
                    'output'  => $output,
                ]);
            }

            return response($this->renderResult(
                title: 'Database Migrations Executed',
                command: 'php artisan migrate --force',
                output: $output,
                isSuccess: true
            ));
        } catch (\Throwable $e) {
            Log::error('Utility Migrate Failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return response($this->renderResult(
                title: 'Database Migration Failed',
                command: 'php artisan migrate --force',
                output: $e->getMessage() . "\n" . $e->getTraceAsString(),
                isSuccess: false
            ), 500);
        }
    }

    /**
     * Cache and optimize routes, config, and views for production performance.
     */
    public function optimize(Request $request)
    {
        try {
            $output = [];

            Artisan::call('config:cache');
            $output[] = trim(Artisan::output());

            Artisan::call('route:cache');
            $output[] = trim(Artisan::output());

            Artisan::call('view:cache');
            $output[] = trim(Artisan::output());

            $combinedOutput = implode("\n", array_filter($output));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'action'  => 'optimize',
                    'output'  => $combinedOutput,
                ]);
            }

            return response($this->renderResult(
                title: 'Application Optimized & Cached',
                command: 'php artisan config:cache && route:cache && view:cache',
                output: $combinedOutput,
                isSuccess: true
            ));
        } catch (\Throwable $e) {
            Log::error('Utility Optimize Failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return response($this->renderResult(
                title: 'Application Optimization Failed',
                command: 'php artisan optimize',
                output: $e->getMessage(),
                isSuccess: false
            ), 500);
        }
    }

    /**
     * Seed foundation data (Company, Branches, Roles, Fiscal Year, COA, etc.).
     */
    public function seed(Request $request)
    {
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\ErpFoundationSeeder',
                '--force' => true,
            ]);
            $output = trim(Artisan::output()) ?: "ErpFoundationSeeder executed successfully. Default Company, Branches, Fiscal Years and Roles are now ready.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'action'  => 'seed',
                    'output'  => $output,
                ]);
            }

            return response($this->renderResult(
                title: 'ERP Foundation Seeded',
                command: 'php artisan db:seed --class=ErpFoundationSeeder --force',
                output: $output,
                isSuccess: true
            ));
        } catch (\Throwable $e) {
            Log::error('Utility Seed Failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return response($this->renderResult(
                title: 'Database Seeding Failed',
                command: 'php artisan db:seed --class=ErpFoundationSeeder',
                output: $e->getMessage() . "\n" . $e->getTraceAsString(),
                isSuccess: false
            ), 500);
        }
    }

    /**
     * Run git pull from live server to fetch latest updates.
     */
    public function gitPull(Request $request)
    {
        try {
            $cmd = 'git pull origin main 2>&1';
            $output = shell_exec($cmd);
            if ($output === null) {
                $output = 'shell_exec not available or git command returned empty output.';
            }

            // Also clear cache after pulling
            Artisan::call('optimize:clear');
            $output .= "\n" . trim(Artisan::output());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'action'  => 'git-pull',
                    'output'  => $output,
                ]);
            }

            return response($this->renderResult(
                title: 'Git Pull & Cache Cleared',
                command: 'git pull origin main && php artisan optimize:clear',
                output: $output,
                isSuccess: true
            ));
        } catch (\Throwable $e) {
            Log::error('Utility Git Pull Failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => $e->getMessage(),
                ], 500);
            }

            return response($this->renderResult(
                title: 'Git Pull Failed',
                command: 'git pull origin main',
                output: $e->getMessage(),
                isSuccess: false
            ), 500);
        }
    }

    /**
     * Render terminal-style result page.
     */
    protected function renderResult(string $title, string $command, string $output, bool $isSuccess): string
    {
        $statusBadge = $isSuccess
            ? '<span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 9999px; font-size: 13px; font-weight: 600;">SUCCESS</span>'
            : '<span style="background: #ef4444; color: white; padding: 4px 12px; border-radius: 9999px; font-size: 13px; font-weight: 600;">ERROR</span>';

        $escapedOutput = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
        $escapedCommand = htmlspecialchars($command, ENT_QUOTES, 'UTF-8');
        $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$escapedTitle} - System Utilities</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 2rem 1rem; }
        .container { max-width: 860px; margin: 0 auto; background: #1e293b; border-radius: 12px; border: 1px solid #334155; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); overflow: hidden; }
        .header { padding: 1.5rem 2rem; border-bottom: 1px solid #334155; display: flex; align-items: center; justify-content: space-between; }
        .header h1 { font-size: 1.25rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.75rem; }
        .content { padding: 2rem; }
        .cmd-bar { background: #090d16; border: 1px solid #1e293b; border-radius: 8px; padding: 0.75rem 1rem; color: #38bdf8; font-family: monospace; font-size: 0.95rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        .terminal { background: #020617; border: 1px solid #1e293b; border-radius: 8px; padding: 1.25rem; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.9rem; color: #a7f3d0; white-space: pre-wrap; line-height: 1.6; max-height: 480px; overflow-y: auto; }
        .nav-links { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #334155; }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.15s ease; }
        .btn-primary { background: #2563eb; color: #ffffff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #334155; color: #f1f5f9; }
        .btn-secondary:hover { background: #475569; }
        .btn-outline { background: transparent; border: 1px solid #475569; color: #cbd5e1; }
        .btn-outline:hover { background: #334155; color: #ffffff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ {$escapedTitle}</h1>
            <div>{$statusBadge}</div>
        </div>
        <div class="content">
            <div class="cmd-bar">
                <span style="color: #64748b;">$</span> {$escapedCommand}
            </div>
            <div class="terminal">{$escapedOutput}</div>
            <div class="nav-links">
                <a href="/clear-cache" class="btn btn-secondary">🧹 Clear Cache</a>
                <a href="/storage-link" class="btn btn-secondary">🔗 Storage Link</a>
                <a href="/migrate" class="btn btn-secondary">🗄️ Run Migrations</a>
                <a href="/db-seed" class="btn btn-secondary">🌱 Seed Database</a>
                <a href="/git-pull" class="btn btn-secondary">📥 Git Pull</a>
                <a href="/optimize" class="btn btn-secondary">⚡ Optimize</a>
                <a href="/erp" class="btn btn-primary" style="margin-left: auto;">🏢 Go to ERP</a>
                <a href="/" class="btn btn-outline">🏠 Homepage</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Render full dashboard page.
     */
    protected function renderDashboard(array $data): string
    {
        $storageBadge = $data['storage_linked']
            ? '<span style="color: #10b981; font-weight: 600;">Linked (Active)</span>'
            : '<span style="color: #f59e0b; font-weight: 600;">Not Linked</span>';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System & Maintenance Utilities - Real Estate ERP</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 2rem 1rem; }
        .container { max-width: 920px; margin: 0 auto; background: #1e293b; border-radius: 12px; border: 1px solid #334155; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); overflow: hidden; }
        .header { padding: 1.75rem 2rem; border-bottom: 1px solid #334155; background: #182234; }
        .header h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem 0; color: #ffffff; }
        .header p { margin: 0; color: #94a3b8; font-size: 0.9rem; }
        .content { padding: 2rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
        .card { background: #0f172a; border: 1px solid #334155; border-radius: 10px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; }
        .card h3 { font-size: 1.1rem; margin: 0 0 0.5rem 0; color: #f1f5f9; display: flex; align-items: center; gap: 0.5rem; }
        .card p { font-size: 0.85rem; color: #94a3b8; margin: 0 0 1.25rem 0; line-height: 1.5; }
        .meta-box { background: #0f172a; border: 1px solid #334155; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 2rem; font-size: 0.875rem; display: flex; flex-wrap: wrap; gap: 2rem; }
        .meta-item { display: flex; flex-direction: column; gap: 0.25rem; }
        .meta-label { color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em; }
        .meta-val { color: #e2e8f0; font-weight: 600; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.15s ease; cursor: pointer; border: none; }
        .btn-emerald { background: #059669; color: #ffffff; }
        .btn-emerald:hover { background: #047857; }
        .btn-blue { background: #2563eb; color: #ffffff; }
        .btn-blue:hover { background: #1d4ed8; }
        .btn-purple { background: #7c3aed; color: #ffffff; }
        .btn-purple:hover { background: #6d28d9; }
        .btn-amber { background: #d97706; color: #ffffff; }
        .btn-amber:hover { background: #b45309; }
        .nav-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid #334155; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛠️ Maintenance & System Utilities</h1>
            <p>Direct Artisan control for cache clearing, storage symlink, and database migrations.</p>
        </div>
        <div class="content">
            <div class="meta-box">
                <div class="meta-item">
                    <span class="meta-label">PHP Version</span>
                    <span class="meta-val">{$data['php_version']}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Laravel Version</span>
                    <span class="meta-val">{$data['laravel_version']}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Environment</span>
                    <span class="meta-val">{$data['environment']}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Storage Link</span>
                    <span class="meta-val">{$storageBadge}</span>
                </div>
            </div>

            <div class="grid">
                <div class="card">
                    <div>
                        <h3>🧹 Cache Clear</h3>
                        <p>Runs <code>optimize:clear</code> to wipe compiled config, route, view, and application cache files.</p>
                    </div>
                    <a href="/clear-cache" class="btn btn-amber">Run Cache Clear</a>
                </div>

                <div class="card">
                    <div>
                        <h3>🔗 Storage Link</h3>
                        <p>Generates the <code>public/storage</code> symbolic link pointing to <code>storage/app/public</code> for uploaded media.</p>
                    </div>
                    <a href="/storage-link" class="btn btn-blue">Link Storage</a>
                </div>

                <div class="card">
                    <div>
                        <h3>🗄️ Migrate Database</h3>
                        <p>Runs all pending migrations safely with <code>--force</code> enabled for production reliability.</p>
                    </div>
                    <a href="/migrate" class="btn btn-emerald" onclick="return confirm('Execute pending database migrations?');">Run Migrations</a>
                </div>

                <div class="card">
                    <div>
                        <h3>⚡ Optimize Caches</h3>
                        <p>Caches configuration, routes, and views for lightning-fast production page loads.</p>
                    </div>
                    <a href="/optimize" class="btn btn-purple">Cache & Optimize</a>
                </div>

                <div class="card">
                    <div>
                        <h3>🌱 Seed Foundation DB</h3>
                        <p>Populates initial company, branches, fiscal year, departments, and roles into empty tables.</p>
                    </div>
                    <a href="/db-seed" class="btn btn-emerald" onclick="return confirm('Seed ERP foundation data?');">Seed Database</a>
                </div>

                <div class="card">
                    <div>
                        <h3>📥 Git Pull & Update</h3>
                        <p>Pulls latest codebase updates directly from origin main into this production environment.</p>
                    </div>
                    <a href="/git-pull" class="btn btn-blue" onclick="return confirm('Run git pull origin main?');">Pull Latest Code</a>
                </div>
            </div>

            <div class="nav-footer">
                <a href="/" style="color: #94a3b8; text-decoration: none; font-size: 0.9rem;">← Back to Website</a>
                <a href="/erp" class="btn btn-blue">Go to ERP Dashboard →</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}

