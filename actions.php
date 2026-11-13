<?php
// 1. Save a NEW Affiliate Program
if (isset($_POST['save_program'])) {
    $name = trim($_POST['program_name'] ?? '');
    $redirectUrl = 'index.php';

    if ($name !== '') {
        $slug = preg_replace('/[^a-z0-9]+/', '_', strtolower($name));
        $slug = trim($slug, '_') ?: 'program_' . time();
        $fileName = $slug . '.txt';

        if (file_exists($dataDir . $fileName)) {
            $fileName = $slug . '_' . time() . '.txt';
        }

        $programData = [
            'program_name' => $name,
            'login_url'    => trim($_POST['login_url'] ?? ''),
            'username'     => trim($_POST['username'] ?? ''),
            'password'     => $_POST['password'] ?? '',
            'earnings'     => (float)($_POST['earnings'] ?? 0),
            'is_favourite' => isset($_POST['is_favourite']),
            'is_active'    => isset($_POST['is_active']),
            'comments'     => $_POST['comments'] ?? '',
            'clients'      => []
        ];

        file_put_contents($dataDir . $fileName, json_encode($programData, $jsonFlags));
        $redirectUrl = 'index.php?selected=' . urlencode($fileName);
    }
    header("Location: " . $redirectUrl);
    exit;
}

// 2. Update an EXISTING Affiliate Program
if (isset($_POST['update_program'])) {
    $fileName = basename($_POST['program_file'] ?? '');
    $file = $dataDir . $fileName;
    if ($programData = loadProgram($file)) {
        $programData['program_name'] = trim($_POST['program_name'] ?? '');
        $programData['login_url']    = trim($_POST['login_url'] ?? '');
        $programData['username']     = trim($_POST['username'] ?? '');
        $programData['password']     = $_POST['password'] ?? '';
        $programData['earnings']     = (float)($_POST['earnings'] ?? 0);
        $programData['is_favourite'] = isset($_POST['is_favourite']);
        $programData['is_active']    = isset($_POST['is_active']);
        $programData['comments']     = $_POST['comments'] ?? '';

        file_put_contents($file, json_encode($programData, $jsonFlags));
    }
    header("Location: index.php?open=" . urlencode($fileName) . "#prog_" . urlencode($fileName));
    exit;
}


// 3. Add a Casino ("Client")
if (isset($_POST['save_client'])) {
    $fileName = basename($_POST['program_file'] ?? '');
    $file = $dataDir . $fileName;
    if ($programData = loadProgram($file)) {
        $programData['clients'][] = [
            'casino_name'   => trim($_POST['casino_name'] ?? ''),
            'affiliate_url' => trim($_POST['affiliate_url'] ?? ''),
            'is_active'     => true
        ];
        file_put_contents($file, json_encode($programData, $jsonFlags));
    }
    header("Location: index.php?selected=" . urlencode($fileName) . "&open=" . urlencode($fileName) . "#prog_" . urlencode($fileName));
    exit;
}

// 3b. Update Casino ("Client") URL
if (isset($_POST['update_client_url'])) {
    $fileName = basename($_POST['program_file'] ?? '');
    $file = $dataDir . $fileName;
    $index = (int)($_POST['client_index'] ?? -1);
    if (($programData = loadProgram($file)) && isset($programData['clients'][$index])) {
        $programData['clients'][$index]['affiliate_url'] = trim($_POST['affiliate_url'] ?? '');
        file_put_contents($file, json_encode($programData, $jsonFlags));
    }
    header("Location: index.php?open=" . urlencode($fileName) . "#prog_" . urlencode($fileName));
    exit;
}

// 3c. Toggle Casino ("Client") Active / Non Active
if (isset($_POST['toggle_client_active'])) {
    $fileName = basename($_POST['program_file'] ?? '');
    $file = $dataDir . $fileName;
    $index = (int)($_POST['client_index'] ?? -1);
    if (($programData = loadProgram($file)) && isset($programData['clients'][$index])) {
        $currentStatus = $programData['clients'][$index]['is_active'] ?? true;
        $programData['clients'][$index]['is_active'] = !$currentStatus;
        file_put_contents($file, json_encode($programData, $jsonFlags));
    }
    header("Location: index.php?open=" . urlencode($fileName) . "#prog_" . urlencode($fileName));
    exit;
}

// 4. Delete a Casino ("Client")
if (isset($_POST['delete_client'])) {
    $fileName = basename($_POST['program_file'] ?? '');
    $file = $dataDir . $fileName;
    $index = (int)($_POST['client_index'] ?? -1);
    if (($programData = loadProgram($file)) && isset($programData['clients'][$index])) {
        array_splice($programData['clients'], $index, 1);
        file_put_contents($file, json_encode($programData, $jsonFlags));
    }
    header("Location: index.php?open=" . urlencode($fileName) . "#prog_" . urlencode($fileName));
    exit;
}


// 5. Export all .txt files into one combined JSON download
if (isset($_GET['export'])) {
    $allData = [];
    foreach (glob($dataDir . '*.txt') as $f) {
        $allData[] = loadProgram($f);
    }
    header('Content-Type: application/json; charset=UTF-8');
    header('Content-Disposition: attachment; filename="all_affiliates_backup_' . date('Y-m-d') . '.json"');
    echo json_encode($allData, $jsonFlags);
    exit;
}

// 6. Save Ignore List
if (isset($_POST['save_ignore_list'])) {
    $ignoreText = $_POST['ignore_list_content'] ?? '';
    file_put_contents(__DIR__ . '/ignore_list.txt', $ignoreText);
    header("Location: index.php?ignore=1&saved=1");
    exit;
}


