<?php
session_start();
include 'conn.php';
header('Content-Type: application/json');

// Check if user is logged in
function checkAuth() {
    if (!isset($_SESSION['id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    return (int)$_SESSION['id'];
}

$action = $_GET['action'] ?? '';

// Get User Data
if ($action === 'getUser') {
    $user_id = checkAuth();
    $stmt = $conn->prepare("SELECT user_id, full_name, phone, province, district, created_at FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
    $stmt->close();
    exit;
}

// Get Children
if ($action === 'getChildren') {
    $user_id = checkAuth();
    $stmt = $conn->prepare("SELECT child_id, parent_id, name, dob, savings_goal, Reg_Number, balance, created_at FROM children WHERE parent_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $children = [];
    
    while ($row = $result->fetch_assoc()) {
        $children[] = [
            'id' => $row['child_id'],
            'name' => $row['name'],
            'dob' => $row['dob'],
            'goal' => floatval($row['savings_goal']),
            'reg_number' => $row['Reg_Number'] ?? '',
            'balance' => floatval($row['balance']),
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $children]);
    $stmt->close();
    exit;
}

// Add Child
if ($action === 'addChild') {
    $user_id = checkAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($data['name'] ?? '');
    $dob = $data['dob'] ?? '';
    $goal = floatval($data['goal'] ?? 0);
    
    if (empty($name) || empty($dob) || $goal <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }
    
    // Generate Registration Number: YYYY-MYCHILD-{5 random digits}
    $year = date('y');
    $randomDigits = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
    $regNumber = $year . '-MYCHILD-' . $randomDigits;
    
    $stmt = $conn->prepare("INSERT INTO children (parent_id, name, dob, savings_goal, Reg_Number, balance) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("issds", $user_id, $name, $dob, $goal, $regNumber);
    
    if ($stmt->execute()) {
        $child_id = $conn->insert_id;
        echo json_encode(['success' => true, 'data' => ['id' => $child_id, 'name' => $name, 'dob' => $dob, 'goal' => $goal, 'reg_number' => $regNumber, 'balance' => 0]]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add child: ' . $conn->error]);
    }
    $stmt->close();
    exit;
}

// Get Contributors
if ($action === 'getContributors') {
    $user_id = checkAuth();
    $stmt = $conn->prepare("SELECT contributor_id, parent_id, name, phone, relationship, status, total_contributed, created_at FROM contributors WHERE parent_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contributors = [];
    
    while ($row = $result->fetch_assoc()) {
        $contributors[] = [
            'id' => $row['contributor_id'],
            'name' => $row['name'],
            'phone' => $row['phone'],
            'relationship' => $row['relationship'],
            'status' => $row['status'],
            'contributed' => floatval($row['total_contributed']),
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $contributors]);
    $stmt->close();
    exit;
}

// Add Contributor
if ($action === 'addContributor') {
    $user_id = checkAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($data['name'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $relationship = trim($data['relationship'] ?? '');
    
    if (empty($name) || empty($phone) || empty($relationship)) {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO contributors (parent_id, name, phone, relationship, status, total_contributed) VALUES (?, ?, ?, ?, 'Invited', 0)");
    $stmt->bind_param("isss", $user_id, $name, $phone, $relationship);
    
    if ($stmt->execute()) {
        $contrib_id = $conn->insert_id;
        echo json_encode(['success' => true, 'data' => ['id' => $contrib_id, 'name' => $name, 'phone' => $phone, 'relationship' => $relationship, 'status' => 'Invited', 'contributed' => 0]]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add contributor']);
    }
    $stmt->close();
    exit;
}

// Make Deposit
if ($action === 'makeDeposit') {
    $user_id = checkAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    
    $child_id = intval($data['child_id'] ?? 0);
    $amount = floatval($data['amount'] ?? 0);
    $method = trim($data['method'] ?? '');
    $pin = trim($data['pin'] ?? '');
    
    if ($child_id <= 0 || $amount <= 0 || empty($method) || empty($pin)) {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }
    
    // Verify PIN
    $stmt = $conn->prepare("SELECT pin FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    
    $storedPin = $user['pin'];
    $pinValid = false;
    
    // Check if PIN is hashed (starts with $2y$, $2a$, $2b$, or similar)
    if (password_verify($pin, $storedPin)) {
        // PIN is hashed and matches
        $pinValid = true;
    } elseif ($pin === $storedPin) {
        // PIN is plain text and matches (for backward compatibility with old records)
        $pinValid = true;
    }
    
    if (!$pinValid) {
        echo json_encode(['success' => false, 'error' => 'Invalid PIN. Please enter the same password you use to login.']);
        exit;
    }
    
    // Verify child belongs to parent
    $stmt = $conn->prepare("SELECT child_id FROM children WHERE child_id = ? AND parent_id = ?");
    $stmt->bind_param("ii", $child_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Child not found']);
        $stmt->close();
        exit;
    }
    $stmt->close();
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update child balance
        $stmt = $conn->prepare("UPDATE children SET balance = balance + ? WHERE child_id = ?");
        $stmt->bind_param("di", $amount, $child_id);
        $stmt->execute();
        $stmt->close();
        
        // Get child name for transaction record
        $stmt = $conn->prepare("SELECT name FROM children WHERE child_id = ?");
        $stmt->bind_param("i", $child_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $child = $result->fetch_assoc();
        $child_name = $child['name'];
        $stmt->close();
        
        // Record transaction
        $stmt = $conn->prepare("INSERT INTO transactions (parent_id, child_id, amount, payment_method, status, transaction_date) VALUES (?, ?, ?, ?, 'Completed', NOW())");
        $stmt->bind_param("iids", $user_id, $child_id, $amount, $method);
        $stmt->execute();
        $stmt->close();
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Deposit successful']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Transaction failed']);
    }
    exit;
}

// Get Transactions
if ($action === 'getTransactions') {
    $user_id = checkAuth();
    $child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : 0;
    
    if ($child_id > 0) {
        $stmt = $conn->prepare("SELECT t.transaction_id, t.child_id, c.name as child_name, t.amount, t.payment_method, t.status, t.transaction_date FROM transactions t JOIN children c ON t.child_id = c.child_id WHERE t.parent_id = ? AND t.child_id = ? ORDER BY t.transaction_date DESC");
        $stmt->bind_param("ii", $user_id, $child_id);
    } else {
        $stmt = $conn->prepare("SELECT t.transaction_id, t.child_id, c.name as child_name, t.amount, t.payment_method, t.status, t.transaction_date FROM transactions t JOIN children c ON t.child_id = c.child_id WHERE t.parent_id = ? ORDER BY t.transaction_date DESC");
        $stmt->bind_param("i", $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = [];
    
    while ($row = $result->fetch_assoc()) {
            $method = $row['payment_method'];
            // Format payment method for display
            $methodDisplay = $method;
            if ($method === 'mtn') $methodDisplay = '📱 MTN Mobile Money';
            elseif ($method === 'airtel') $methodDisplay = '📱 Airtel Money';
            elseif ($method === 'bank') $methodDisplay = '🏦 Bank Transfer';
            
            $transactions[] = [
            'id' => $row['transaction_id'],
            'child_id' => $row['child_id'],
            'childName' => $row['child_name'],
            'amount' => floatval($row['amount']),
            'method' => $methodDisplay,
            'status' => $row['status'],
            'date' => date('Y-m-d', strtotime($row['transaction_date']))
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $transactions]);
    $stmt->close();
    exit;
}

// Update Account Settings
if ($action === 'updateAccount') {
    $user_id = checkAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = trim($data['name'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $email = trim($data['email'] ?? '');
    $newPin = trim($data['pin'] ?? '');
    
    $updates = [];
    $params = [];
    $types = '';
    
    if (!empty($name)) {
        $updates[] = "full_name = ?";
        $params[] = $name;
        $types .= "s";
    }
    
    if (!empty($phone)) {
        $updates[] = "phone = ?";
        $params[] = $phone;
        $types .= "s";
    }
    
    if (!empty($email)) {
        $updates[] = "email = ?";
        $params[] = $email;
        $types .= "s";
    }
    
    if (!empty($newPin) && strlen($newPin) === 4) {
        $hashedPin = password_hash($newPin, PASSWORD_DEFAULT);
        $updates[] = "pin = ?";
        $params[] = $hashedPin;
        $types .= "s";
    }
    
    if (empty($updates)) {
        echo json_encode(['success' => false, 'error' => 'No updates provided']);
        exit;
    }
    
    $params[] = $user_id;
    $types .= "i";
    
    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        if (!empty($name)) {
            $_SESSION['username'] = $name;
        }
        echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update account']);
    }
    $stmt->close();
    exit;
}

// Get Dashboard Stats
if ($action === 'getStats') {
    $user_id = checkAuth();
    
    // Get total saved
    $stmt = $conn->prepare("SELECT COALESCE(SUM(balance), 0) as total FROM children WHERE parent_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Get children count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM children WHERE parent_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $children_count = $result->fetch_assoc()['count'];
    $stmt->close();
    
    // Get contributors count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM contributors WHERE parent_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contributors_count = $result->fetch_assoc()['count'];
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'totalSaved' => floatval($total),
            'childrenCount' => intval($children_count),
            'contributorsCount' => intval($contributors_count)
        ]
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);

