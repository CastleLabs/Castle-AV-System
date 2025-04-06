<?php
/***************************************************
 * manage_devices.php
 * - Reads and writes DBconfigs.ini
 * - Adds & removes categories/devices
 * - Has improved form visibility (borders, etc.)
 * - "x" button for removing devices w/ confirm.
 ***************************************************/

/**
 * 1) PATH & PERMISSIONS
 */
$configFilePath = __DIR__ . '/DBconfigs.ini';
$canWrite       = is_writable($configFilePath);

/**
 * 2) PARSE EXISTING INI INTO ARRAY
 */
$iniArray = parse_ini_file($configFilePath, true);
if (!$iniArray) {
    $iniArray = [];
}

/**
 * 3) HANDLE FORM SUBMISSION
 */
$message     = '';
$messageType = 'info'; // can be 'info', 'success', or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $canWrite) {
    $action = $_POST['action'] ?? '';

    // 3.1) Add Category
    if ($action === 'add_category') {
        $newCategory = trim($_POST['newCategoryName'] ?? '');
        if ($newCategory === '') {
            $message = 'Category name cannot be empty.';
            $messageType = 'error';
        } else {
            if (array_key_exists($newCategory, $iniArray)) {
                $message = "Category '$newCategory' already exists!";
                $messageType = 'error';
            } else {
                $iniArray[$newCategory] = [];
                $message = "Category '$newCategory' added successfully!";
                $messageType = 'success';
            }
        }
    }
    // 3.2) Add Device
    elseif ($action === 'add_device') {
        $deviceCategory = trim($_POST['deviceCategory'] ?? '');
        $deviceName     = trim($_POST['deviceName'] ?? '');
        $deviceIP       = trim($_POST['deviceIP'] ?? '');

        if (!$deviceCategory || !$deviceName || !$deviceIP) {
            $message = 'Please fill out all fields (Category, Device Name, Device IP).';
            $messageType = 'error';
        } else {
            if (!array_key_exists($deviceCategory, $iniArray)) {
                $message = "Category '$deviceCategory' does not exist.";
                $messageType = 'error';
            } else {
                $iniArray[$deviceCategory][$deviceName] = $deviceIP;
                $message = "Device '$deviceName' added to '$deviceCategory'.";
                $messageType = 'success';
            }
        }
    }
    // 3.3) Remove Device
    elseif ($action === 'remove_device') {
        $cat = $_POST['category'] ?? '';
        $dev = $_POST['device'] ?? '';

        if (isset($iniArray[$cat][$dev])) {
            unset($iniArray[$cat][$dev]);
            // If a category is now empty, we can either leave it or handle it
            // (Here we leave the category intact even if empty)
            $message = "Device '$dev' removed from '$cat'.";
            $messageType = 'success';
        } else {
            $message = "Error removing device '$dev'. Not found in '$cat'.";
            $messageType = 'error';
        }
    }

    // 3.4) Write updated array back to file (if not error)
    if ($messageType !== 'error') {
        if (!writeIniFile($iniArray, $configFilePath)) {
            $message = "Error writing to $configFilePath.";
            $messageType = 'error';
        }
    }
}

/**
 * FUNCTION: Write array back to .ini
 */
function writeIniFile($assocArr, $path) {
    $content = '';
    foreach ($assocArr as $section => $values) {
        $content .= "[$section]\n";
        foreach ($values as $key => $val) {
            $content .= "{$key} = {$val}\n";
        }
        $content .= "\n";
    }
    return file_put_contents($path, $content) !== false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Manage IOT Devices</title>
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --primary: #3B82F6;
            --primary-dark: #2563EB;
            --success: #10B981;
            --error: #EF4444;
            --warning: #F59E0B;
        }

        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .header {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
            box-shadow: var(--shadow-sm);
        }

        .dark {
            background-color: #0F172A !important;
            color: #E5E7EB !important;
        }
        .dark .header {
            background: rgba(15, 23, 42, 0.8) !important;
            border-bottom: 1px solid rgba(55, 65, 81, 0.5) !important;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        .status-banner {
            transition: opacity 0.3s ease;
        }

        .dark .text-gray-600 {
            color: #9CA3AF !important;
        }
        .dark .bg-white {
            background-color: #1E293B !important;
            color: #E5E7EB !important;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
        }
        .dark .card {
            background-color: #1E293B !important;
        }

        /* Make fields have a visible border and black text in light mode */
        input, select, option, textarea {
            color: #000 !important;  /* black text in light mode */
            background-color: #fff;   /* white bg in light mode */
            border: 1px solid #cbd5e1; /* Tailwind's gray-300 */
            padding: 0.5rem;
        }
        .dark input,
        .dark select,
        .dark option,
        .dark textarea {
            background-color: #374151 !important; /* Tailwind's gray-700 */
            border: 1px solid #4b5563;            /* Tailwind's gray-600 */
            color: #E5E7EB !important;            /* gray-200 text in dark mode */
        }

        /* A small "x" remove button style */
        .remove-btn {
            color: #EF4444;       /* red-500 in Tailwind */
            font-weight: bold;
            margin-left: 0.5rem;
            cursor: pointer;
        }
        .remove-btn:hover {
            color: #DC2626;       /* red-600 */
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 text-gray-900 transition-colors duration-300">
    <!-- Header -->
    <header class="header sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <!-- Example icon -->
                        <svg class="h-8 w-8 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <h1 class="ml-3 text-xl font-semibold">Manage IOT Devices</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Example link back to dashboard -->
                    <a href="index.html" class="btn-primary inline-flex items-center">
                        <span>Back to Dashboard</span>
                    </a>
                    <button id="themeToggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                        <!-- Icon toggles via JS -->
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M20.354 15.354A9 9 0 018.646 3.646 
                                     9.003 9.003 0 0012 21
                                     a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Status Banner -->
        <?php if ($message): ?>
            <?php
                // Map message type to Tailwind classes
                $typeToClasses = [
                    'info'    => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-200',
                    'error'   => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-200',
                    'success' => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-200'
                ];
                $classes = $typeToClasses[$messageType] ?? $typeToClasses['info'];
            ?>
            <div class="status-banner mb-6 rounded-lg p-4 <?php echo $classes; ?>">
                <p class="text-sm">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if (!$canWrite): ?>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 mb-8">
                <p class="text-red-700 dark:text-red-200">
                    <strong>Warning:</strong> The web server cannot write to 
                    <code><?php echo $configFilePath; ?></code>.
                    Please fix permissions (e.g., <code>chmod 664 DBconfigs.ini</code>).
                </p>
            </div>
        <?php endif; ?>

        <!-- FORMS AT THE TOP -->
        <!-- Add Category Form -->
        <div class="card mb-8 p-6">
            <h2 class="text-xl font-semibold mb-4">Add New Category</h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add_category" />
                
                <div>
                    <label for="newCategoryName" class="block font-medium mb-1">
                        Category Name
                    </label>
                    <input 
                        type="text" 
                        id="newCategoryName" 
                        name="newCategoryName"
                        required 
                        class="w-full rounded 
                               focus:outline-none focus:border-indigo-500 
                               dark:focus:border-indigo-400"
                    />
                </div>
                <button type="submit" class="btn-primary" 
                        <?php echo $canWrite ? '' : 'disabled'; ?>>
                    Add Category
                </button>
            </form>
        </div>

        <!-- Add Device Form -->
        <div class="card mb-8 p-6">
            <h2 class="text-xl font-semibold mb-4">Add New Device</h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add_device" />

                <div>
                    <label for="deviceCategory" class="block font-medium mb-1">
                        Select Category
                    </label>
                    <select 
                        id="deviceCategory" 
                        name="deviceCategory" 
                        class="w-full rounded 
                               focus:outline-none focus:border-indigo-500 
                               dark:focus:border-indigo-400"
                        required
                    >
                        <option value="" disabled selected>-- Choose a category --</option>
                        <?php foreach ($iniArray as $cat => $arr): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="deviceName" class="block font-medium mb-1">
                        Device Name
                    </label>
                    <input 
                        type="text" 
                        id="deviceName" 
                        name="deviceName" 
                        required
                        class="w-full rounded 
                               focus:outline-none focus:border-indigo-500 
                               dark:focus:border-indigo-400"
                    />
                </div>
                <div>
                    <label for="deviceIP" class="block font-medium mb-1">
                        Device IP/Port or URL
                    </label>
                    <input 
                        type="text" 
                        id="deviceIP" 
                        name="deviceIP" 
                        placeholder="e.g., 192.168.0.10:5000"
                        required
                        class="w-full rounded 
                               focus:outline-none focus:border-indigo-500 
                               dark:focus:border-indigo-400"
                    />
                </div>
                <button type="submit" class="btn-primary" 
                        <?php echo $canWrite ? '' : 'disabled'; ?>>
                    Add Device
                </button>
            </form>
        </div>

        <!-- EXISTING CATEGORIES/DEVICES (below forms) -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4">Current Categories &amp; Devices</h2>
            <ul class="space-y-4">
            <?php if (count($iniArray) === 0): ?>
                <li class="card p-4">
                    No categories found in <code>DBconfigs.ini</code>.
                </li>
            <?php else: ?>
                <?php foreach ($iniArray as $category => $devices): ?>
                    <li class="card p-4">
                        <h3 class="text-lg font-semibold mb-2">
                            <?php echo htmlspecialchars($category); ?>
                        </h3>
                        <?php if (empty($devices)): ?>
                            <p class="text-gray-600 dark:text-gray-400">
                                No devices in this category yet.
                            </p>
                        <?php else: ?>
                            <ul class="list-disc ml-6 space-y-1">
                                <?php foreach ($devices as $deviceName => $deviceIP): ?>
                                    <li>
                                        <?php echo htmlspecialchars($deviceName) . " = " . htmlspecialchars($deviceIP); ?>
                                        <!-- Remove device form -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="remove_device">
                                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                                            <input type="hidden" name="device" value="<?php echo htmlspecialchars($deviceName); ?>">
                                            <button type="submit" 
                                                    class="remove-btn"
                                                    onclick="return confirm('Are you sure you want to remove device <?php echo htmlspecialchars($deviceName); ?>?');">
                                                x
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            </ul>
        </div>
    </main>

    <!-- Theming Script -->
    <script>
        const themeToggle = document.getElementById('themeToggle');
        
        // Load theme from localStorage
        (function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                document.body.classList.add('dark');
            }
            updateThemeIcon();
        })();

        themeToggle.addEventListener('click', toggleTheme);

        function toggleTheme() {
            document.body.classList.toggle('dark');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const isDark = document.body.classList.contains('dark');
            themeToggle.innerHTML = isDark 
                ? '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>'
                : '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>';
        }
    </script>
</body>
</html>
