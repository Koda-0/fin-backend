<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // redirect if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="1.png" type="image/x-icon">
    <!-- Font Awesome Free -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body class="bg-gray-100">
    <aside class="w-64 bg-gray-900 text-white h-screen flex flex-col fixed">
                <div class="p-6 text-2xl font-bold border-b border-gray-700">
                  Admin Panel
              </div>
            <div class="links flex-1 p-4 space-y-4">
                <a href="" class="block px-4 py-2 rounded bg-gray-700">Dashboard</a>
                <a href="" class="block px-4 py-2 rounded bg-gray-700">Users</a>
                <a href="" class="block px-4 py-2 rounded bg-gray-700">Transaction</a>
                <a href="" class="block px-4 py-2 rounded bg-gray-700">Settings</a>
            </div>
    </aside>

    <main class="flex-1  p-6 space-y-8 ml-64">
        <div class="flex justify-between ">
            <h1 class="text-3xl font-bold ">Dashboard</h1> <br>
            <button onclick="window.location.href='logout.php'" class="bg-red-500 px-4 py-2 rounded text-center text-white hover:bg-red-600">
                Logout <i class="fa-solid fa-power-off"></i>
            </button>
        </div>
        <div class="bg-white shadow border py-6 px-4  rounded-lg">
        <p class="text-xl">Welcome Back In Simulation , Mr. <?= $_SESSION['admin'] ?></p>
        </div>
      <div class="mt-8">
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white flex flex-col p-6 rounded shadow">
                <h2>Total Users</h2>
                <?php
                $i=0;
                $res = $conn->query("SELECT * FROM users");

                while($row = $res->fetch_assoc()){
                    $i++;
                }
                ?>
                <p class="font-bold text-3xl"><?= $i ?></p>
            </div>
            <div class="bg-white flex flex-col p-6 rounded shadow">
                <h2>Transactions</h2>
                <?php
                $total=0;
                $sql= $conn->query("SELECT * FROM transactions WHERE status='completed'");
                while($row = $sql->fetch_assoc()){
                    $total += $row['amount'];
                }
                ?>
                <p class="font-bold text-3xl">FRW <?= $total ?></p>
            </div>
            <div class="bg-white flex flex-col p-6 rounded shadow">
                <h2>Pensing Issues</h2>
                <p class="font-bold text-3xl">0</p>
            </div>
        </div>
      </div>

<div class="bg-white py-6 px-6 border rounded-lg shadow mt-8">
  <h2 class="text-xl font-semibold mb-4">Registered Users</h2>

  <div class="overflow-x-auto">
    <table class="w-full border-collapse">
      <thead>
         <tr class="bg-gray-100 text-left">
          <th class="p-3 border">SN</th>
          <th class="p-3 border">Username</th>
          <th class="p-3 border">Role</th>
          <th class="p-3 border text-center" colspan="2">Actions</th>
        </tr>
      </thead>

      <tbody>
<?php
$i = 0;
$res = $conn->query("SELECT * FROM users");

while ($row = $res->fetch_assoc()) {
  $i++;
?>
  <tr class="hover:bg-gray-50">
    <td class="p-3 border"><?= $i ?></td>
    <td class="p-3 border"><?= htmlspecialchars($row['full_name']) ?></td>
          <td class="p-3 border"><?= $row ['full_name'] ?></td>
          <td class="p-3 border">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
             <?= $row['role']; ?>
            </span>
          </td>
          <td class="p-3 border text-center">
            <button class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600">
              Edit
            </button>
          </td>
          <td class="p-3 border text-center">
            <button class="bg-red-500 px-4 py-2 rounded text-white hover:bg-red-600">
              Block
            </button>
          </td>
        </tr>
      </tbody>
      <?php } ?>
    </table>
  </div>
</div>

<div class="bg-white py-6 px-6 border rounded-lg shadow mt-8">
  <h2 class="text-xl font-semibold mb-4">Transactions</h2>

  <div class="overflow-x-auto">
    <table class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-3 border">SN</th>
          <th class="p-3 border">Username</th>
          <th class="p-3 border">Amount</th>
          <th class="p-3 border">Status</th>
          <th class="p-3 border text-center" colspan="2">Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $i = 0;
        $res = $conn->query("SELECT * FROM transactions JOIN users ON transactions.parent_id = users.user_id");
        while ($row = $res->fetch_assoc()){
            $i++;
        ?>
        <tr class="hover:bg-gray-50">
          <td class="p-3 border"><?= $i ?></td>
          <td class="p-3 border"><?= htmlspecialchars($row['full_name']) ?></td>
          <td class="p-3 border">frw <?= htmlspecialchars($row['amount']) ?></td>
          <td class="p-3 border">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
              <?= $row['status'] ?>
            </span>
          </td>
          <td class="p-3 border text-center">
            <button class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-600">
              APPROVE
            </button>
          </td>
          <td class="p-3 border text-center">
            <button class="bg-red-500 px-4 py-2 rounded text-white hover:bg-red-600">
                REJECT
            </button>
          </td>
        </tr>
      </tbody>
      <?php
        }
        ?>
    </table>
  </div>
</div>


    </main>

</body>
</html>