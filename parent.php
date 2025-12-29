<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - MyChild</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="./api.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, rgba(0, 0, 128, 0.9) 0%, #764ba2 100%);
        }
        .card-hover {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .sidebar-nav-item {
            transition: all 0.3s ease;
        }
        .sidebar-nav-item.active {
            background-color: rgba(147, 51, 234, 0.15);
            border-left: 4px solid #9333ea;
        }
        .sidebar-nav-item:hover {
            background-color: rgba(147, 51, 234, 0.1);
        }
        .view-section {
            display: none;
        }
        .view-section.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
                position: fixed;
            }
            #sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <nav class="bg-white shadow-md sticky top-0 z-40">
        <div class="max-w-full px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button onclick="toggleSidebar()" class="md:hidden text-gray-800 text-2xl">☰</button>
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">👪</span>
                    <h1 class="text-xl font-bold text-gray-800">MyChild</h1>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="hidden md:inline text-gray-600 text-sm">Welcome, <span class="font-bold" id="headerName"><?= $_SESSION['username'] ?></span>!</span>
                <button onclick="logout()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">Sign Out</button>
            </div>
        </div>
    </nav>

    <div class="flex h-[calc(100vh-64px)]">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-white shadow-lg overflow-y-auto transition-transform duration-300 md:transform-none z-30">
            <div class="p-6">
                <div class="mb-8">
                    <div class="text-center">
                        <div class="text-5xl mb-3">👤</div>
                        <h2 class="text-lg font-bold text-gray-800" id="sidebarName">Parent User</h2>
                        <p class="text-gray-600 text-sm mt-1" id="sidebarPhone">+250 (Not set)</p>
                    </div>
                </div>

                <nav class="space-y-2">
                    <button onclick="showSection('overview')" class="sidebar-nav-item active w-full text-left px-4 py-3 rounded-lg text-gray-700 hover:bg-purple-50 font-medium transition">
                        🏠 Home
                    </button>
                    <button onclick="showSection('children')" class="sidebar-nav-item w-full text-left px-4 py-3 rounded-lg text-gray-700 hover:bg-purple-50 font-medium transition">
                        👧 Children
                    </button>
                    <button onclick="showSection('contributors')" class="sidebar-nav-item w-full text-left px-4 py-3 rounded-lg text-gray-700 hover:bg-purple-50 font-medium transition">
                        🤝 Contributors
                    </button>
                    <button onclick="showSection('account')" class="sidebar-nav-item w-full text-left px-4 py-3 rounded-lg text-gray-700 hover:bg-purple-50 font-medium transition">
                        👤 My Account
                    </button>
                </nav>

                <hr class="my-6">

                <div class="space-y-2">
                    <button onclick="showSection('settings')" class="sidebar-nav-item w-full text-left px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 font-medium transition">
                        ⚙️ Settings
                    </button>
                    <button onclick="showSection('transactions')" class="sidebar-nav-item w-full text-left px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 font-medium transition">
                        📋 Transactions
                    </button>
                </div>

                <hr class="my-6">

                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <p class="text-gray-600 text-xs font-semibold mb-2">ACCOUNT STATUS</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Children:</span>
                            <span class="font-bold text-purple-600" id="sidebarChildCount">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Contributors:</span>
                            <span class="font-bold text-green-600" id="sidebarContribCount">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Total Saved:</span>
                            <span class="font-bold text-blue-600" id="sidebarTotal">FRW 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Backdrop for mobile -->
        <div id="sidebarBackdrop" class="hidden fixed inset-0 bg-black bg-opacity-50 md:hidden z-20" onclick="closeSidebar()"></div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <section id="overview" class="view-section active mb-8 bg-white py-8 rounded shadow-md  pl-6 w-full">
            <h2 class="text-3xl font-bold text-gray-800">
    Welcome, <span id="parentName"><?= htmlspecialchars($_SESSION['username']) ?></span>
</h2>

            <p class="text-gray-600 mt-2">Manage your children's savings accounts and track contributions</p>
        </section>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow-md card-hover">
                <p class="text-purple-100 text-sm font-semibold mb-2">Total Saved</p>
                <p class="text-4xl font-bold" id="totalSaved">FRW 0</p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow-md card-hover">
                <p class="text-blue-100 text-sm font-semibold mb-2">My Children</p>
                <p class="text-4xl font-bold" id="childCount">0</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-lg shadow-md card-hover">
                <p class="text-green-100 text-sm font-semibold mb-2">Contributors</p>
                <p class="text-4xl font-bold" id="contributorCount">0</p>
            </div>
        </div>

        <!-- Quick Actions on Home -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button onclick="openDepositModal()" class="p-4 bg-purple-50 border-2 border-purple-300 rounded-lg hover:bg-purple-100 transition text-left">
                    <span class="text-2xl">💳</span>
                    <p class="font-bold text-gray-800 mt-2">Make Deposit</p>
                    <p class="text-gray-600 text-sm">Add funds to a child's account</p>
                </button>
                <button onclick="openInviteModal()" class="p-4 bg-green-50 border-2 border-green-300 rounded-lg hover:bg-green-100 transition text-left">
                    <span class="text-2xl">🤝</span>
                    <p class="font-bold text-gray-800 mt-2">Invite Contributor</p>
                    <p class="text-gray-600 text-sm">Add family or friends</p>
                </button>
                <button onclick="openAddChildModal()" class="p-4 bg-blue-50 border-2 border-blue-300 rounded-lg hover:bg-blue-100 transition text-left">
                    <span class="text-2xl">👧</span>
                    <p class="font-bold text-gray-800 mt-2">Add Child Account</p>
                    <p class="text-gray-600 text-sm">Create new savings goal</p>
                </button>
                <button onclick="openViewTransactions()" class="p-4 bg-orange-50 border-2 border-orange-300 rounded-lg hover:bg-orange-100 transition text-left">
                    <span class="text-2xl">📋</span>
                    <p class="font-bold text-gray-800 mt-2">Transaction History</p>
                    <p class="text-gray-600 text-sm">View all deposits</p>
                </button>
            </div>
        </div>
        </section>

        <!-- Children Accounts Section -->
        <section id="children" class="view-section bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">👧 Children Accounts</h2>
                <p class="text-gray-600">Manage your children's savings goals and track their progress</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Add New Child Card -->
                <div class="border-2 border-dashed border-purple-300 rounded-lg p-8 text-center hover:border-purple-500 hover:bg-purple-50 transition cursor-pointer" onclick="openAddChildModal()">
                    <div class="text-5xl mb-4">➕</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Add New Child</h3>
                    <p class="text-gray-600">Create a savings account for a child with a savings goal</p>
                    <button onclick="openAddChildModal()" class="mt-6 px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold">+ Add Child</button>
                </div>

                <!-- Children List -->
                <div id="childrenContainer" class="col-span-1 lg:col-span-2">
                    <div class="grid grid-cols-1 gap-6">
                        <p class="text-gray-500 text-center py-8">No children added yet. Click "Add New Child" to get started.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-bold text-gray-800 mb-4">Overview</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <p class="text-gray-600 text-sm">Total Children</p>
                        <p class="text-3xl font-bold text-purple-600" id="childrenPageCount">0</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <p class="text-gray-600 text-sm">Total Saved</p>
                        <p class="text-3xl font-bold text-green-600" id="childrenPageTotal">FRW 0</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <p class="text-gray-600 text-sm">Overall Progress</p>
                        <p class="text-3xl font-bold text-blue-600" id="childrenPageProgress">0%</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contributors Section -->
        <section id="contributors" class="view-section bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Contributors</h3>
                <button onclick="openInviteModal()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">🔗 Invite</button>
            </div>

            <div id="contributorsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <p class="text-gray-500 text-center col-span-full py-8">No contributors yet</p>
            </div>
        </section>

        <!-- Parent Account Management -->
        <section id="account" class="view-section mb-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">👤 My Account</h2>
                <p class="text-gray-600">Manage your account settings, payment methods, and security</p>
            </div>

            <!-- Account Info Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Account Information</h3>
                    <button onclick="openAccountSettingsModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">⚙️ Edit</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Personal Info -->
                    <div class="border rounded-lg p-6 bg-gray-50">
                        <h4 class="font-bold text-gray-800 mb-5 text-lg">Personal Information</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Full Name</p>
                                <p class="text-lg font-semibold text-gray-800" id="accountName">Parent User</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Phone Number</p>
                                <p class="text-lg font-semibold text-gray-800" id="accountPhone">+250 (Not set)</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Email Address</p>
                                <p class="text-lg font-semibold text-gray-800" id="accountEmail">Not set</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Account Created</p>
                                <p class="text-lg font-semibold text-gray-800" id="accountCreated">N/A</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Status -->
                    <div class="border rounded-lg p-6 bg-gradient-to-br from-purple-50 to-blue-50">
                        <h4 class="font-bold text-gray-800 mb-5 text-lg">Account Status</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-green-300">
                                <span class="font-semibold text-gray-800">Account Status</span>
                                <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold">Active</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-blue-300">
                                <span class="font-semibold text-gray-800">Children Accounts</span>
                                <span class="text-2xl font-bold text-purple-600" id="accountChildCount">0</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-green-300">
                                <span class="font-semibold text-gray-800">Contributors</span>
                                <span class="text-2xl font-bold text-green-600" id="accountContribCount">0</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-orange-300">
                                <span class="font-semibold text-gray-800">Total Saved</span>
                                <span class="text-2xl font-bold text-orange-600" id="accountTotalSaved">FRW 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Payment Methods</h3>
                    <button onclick="openAddPaymentMethodModal()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">+ Add Method</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50 border-2 border-blue-300 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-bold text-gray-800">📱 MTN Mobile Money</p>
                                <p class="text-gray-600 text-sm">Min: FRW 100 | Max: FRW 10M</p>
                            </div>
                            <span class="px-3 py-1 bg-green-500 text-white rounded text-xs font-bold">Active</span>
                        </div>
                        <p class="text-gray-600 text-sm mt-3">Fast and convenient mobile money transfer service</p>
                    </div>
                    <div class="p-4 bg-red-50 border-2 border-red-300 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-bold text-gray-800">📱 Airtel Money</p>
                                <p class="text-gray-600 text-sm">Min: FRW 100 | Max: FRW 5M</p>
                            </div>
                            <span class="px-3 py-1 bg-green-500 text-white rounded text-xs font-bold">Active</span>
                        </div>
                        <p class="text-gray-600 text-sm mt-3">Quick airtel mobile money transfers</p>
                    </div>
                    <div class="p-4 bg-amber-50 border-2 border-amber-300 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-bold text-gray-800">🏦 Bank Transfer</p>
                                <p class="text-gray-600 text-sm">No limits</p>
                            </div>
                            <span class="px-3 py-1 bg-yellow-500 text-white rounded text-xs font-bold">Pending</span>
                        </div>
                        <p class="text-gray-600 text-sm mt-3">Direct bank account transfer (under setup)</p>
                    </div>
                    <div class="p-4 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center min-h-32 hover:border-gray-400 hover:bg-gray-100 transition cursor-pointer" onclick="openAddPaymentMethodModal()">
                        <div class="text-center">
                            <p class="text-3xl mb-2">➕</p>
                            <p class="font-bold text-gray-800">Add Method</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Security & Settings</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <p class="font-bold text-gray-800">Change PIN</p>
                            <p class="text-gray-600 text-sm">Update your 4-digit security PIN</p>
                        </div>
                        <button onclick="openAccountSettingsModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold text-sm">Update</button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <p class="font-bold text-gray-800">Account Activity</p>
                            <p class="text-gray-600 text-sm">View login history and account access</p>
                        </div>
                        <button onclick="alert('Account activity feature coming soon')" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 font-bold text-sm">View</button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <p class="font-bold text-gray-800">Two-Factor Authentication</p>
                            <p class="text-gray-600 text-sm">Add extra security to your account</p>
                        </div>
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-bold">Coming Soon</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Settings Section -->
        <section id="settings" class="view-section hidden">
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-8">
                    <button onclick="showSection('overview')" class="text-blue-600 hover:text-blue-800 text-lg">←</button>
                    <h2 class="text-3xl font-bold text-gray-800">Account Settings</h2>
                </div>

                <!-- Account Information -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Account Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-gray-600 font-bold mb-2">Full Name</p>
                            <p id="settingsName" class="text-gray-800 text-lg">Parent User</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-bold mb-2">Email Address</p>
                            <p id="settingsEmail" class="text-gray-800 text-lg">parent@example.com</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-bold mb-2">Phone Number</p>
                            <p id="settingsPhone" class="text-gray-800 text-lg">+250 XX XXX XXXX</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-bold mb-2">Account Created</p>
                            <p id="settingsCreated" class="text-gray-800 text-lg">Today</p>
                        </div>
                    </div>

                    <button onclick="openAccountSettingsModal()" class="mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">Edit Information</button>
                </div>

                <!-- Security Settings -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Security</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <p class="font-bold text-gray-800">Change PIN</p>
                                <p class="text-gray-600 text-sm">Update your 4-digit security PIN</p>
                            </div>
                            <button onclick="openAccountSettingsModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold text-sm">Update</button>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <p class="font-bold text-gray-800">Account Activity</p>
                                <p class="text-gray-600 text-sm">View login history and account access</p>
                            </div>
                            <button onclick="alert('Account activity feature coming soon')" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 font-bold text-sm">View</button>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <p class="font-bold text-gray-800">Two-Factor Authentication</p>
                                <p class="text-gray-600 text-sm">Add extra security to your account</p>
                            </div>
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-bold">Coming Soon</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Transactions Section -->
        <section id="transactions" class="view-section hidden">
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-8">
                    <button onclick="showSection('overview')" class="text-blue-600 hover:text-blue-800 text-lg">←</button>
                    <h2 class="text-3xl font-bold text-gray-800">Transaction History</h2>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6 flex flex-col md:flex-row gap-4">
                        <input type="text" placeholder="Search transactions..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="searchTransactions">
                        <select id="filterChild" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Children</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b-2 border-gray-300">
                                <tr>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Date</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Child</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Amount</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Method</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Status</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsTable">
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3">No transactions yet</td>
                                    <td class="px-4 py-3">-</td>
                                    <td class="px-4 py-3">-</td>
                                    <td class="px-4 py-3">-</td>
                                    <td class="px-4 py-3">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Child Detail Section -->
        <section id="childDetail" class="view-section hidden">
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-8">
                    <button onclick="showSection('children')" class="text-blue-600 hover:text-blue-800 text-lg">←</button>
                    <h2 id="childDetailName" class="text-3xl font-bold text-gray-800">Child Details</h2>
                </div>

                <!-- Child Profile -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6">
                        <p class="text-gray-600 font-bold mb-2">Name</p>
                        <p id="childDetailNameText" class="text-2xl font-bold text-gray-800">Child Name</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md p-6">
                        <p class="text-gray-600 font-bold mb-2">Date of Birth</p>
                        <p id="childDetailDOB" class="text-2xl font-bold text-gray-800">00/00/0000</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md p-6">
                        <p class="text-gray-600 font-bold mb-2">Savings Goal</p>
                        <p id="childDetailGoal" class="text-2xl font-bold text-gray-800">FRW 0</p>
                    </div>
                </div>

                <!-- Balance & Progress -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <p class="text-gray-600 font-bold mb-2">Current Balance</p>
                        <p id="childDetailBalance" class="text-4xl font-bold text-green-600 mb-4">FRW 0</p>
                        <button onclick="openDepositForChild()" class="w-full px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">Make Deposit</button>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <p class="text-gray-600 font-bold mb-4">Progress to Goal</p>
                        <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                            <div id="childDetailProgress" class="bg-blue-600 h-4 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="childDetailProgressText" class="text-gray-700 text-sm">0% of goal achieved</p>
                    </div>
                </div>

                <!-- Deposit History -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Deposit History</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b-2 border-gray-300">
                                <tr>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Date</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Amount</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Method</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-800">Status</th>
                                </tr>
                            </thead>
                            <tbody id="childDepositHistoryTable">
                                <tr class="border-b border-gray-200">
                                    <td colspan="4" class="px-4 py-3 text-center text-gray-600">No deposits yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

            </div>
        </main>
    </div>

    <!-- Modal: Add Child -->
    <div id="addChildModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Add Child</h3>
                <button onclick="closeAddChildModal()" class="text-gray-500 text-2xl">✕</button>
            </div>

            <form id="addChildForm" onsubmit="addChild(event)">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Child's Name</label>
                    <input type="text" id="childName" placeholder="Enter child's name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Date of Birth</label>
                    <input type="date" id="childDOB" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Savings Goal (FRW)</label>
                    <input type="number" id="savingsGoal" placeholder="0.00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" step="0.01" min="0" required>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="closeAddChildModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Make Deposit -->
    <div id="depositModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Make Deposit</h3>
                <button onclick="closeDepositModal()" class="text-gray-500 text-2xl">✕</button>
            </div>

            <form id="depositForm" onsubmit="processDeposit(event)">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Select Child</label>
                    <select id="depositChild" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Choose a child...</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Amount (FRW)</label>
                    <input type="number" id="depositAmount" placeholder="0.00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" step="0.01" min="0" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Payment Method</label>
                    <select id="paymentMethod" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                        <option value="">Select method...</option>
                        <option value="mtn">📱 MTN Mobile Money</option>
                        <option value="airtel">📱 Airtel Money</option>
                        <option value="bank">🏦 Bank Transfer</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">PIN</label>
                    <input type="password" id="depositPIN" placeholder="Enter PIN" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="closeDepositModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold">Deposit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Invite Contributor -->
    <div id="inviteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Invite Contributor</h3>
                <button onclick="closeInviteModal()" class="text-gray-500 text-2xl">✕</button>
            </div>

            <form id="inviteForm" onsubmit="inviteContributor(event)">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Name</label>
                    <input type="text" id="contributorName" placeholder="e.g., Uncle John" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Phone Number</label>
                    <input type="tel" id="contributorPhone" placeholder="+250xxxxxxxxx" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Relationship</label>
                    <select id="relationship" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                        <option value="">Select...</option>
                        <option value="grandmother">👵 Grandmother</option>
                        <option value="grandfather">👴 Grandfather</option>
                        <option value="uncle">👨 Uncle</option>
                        <option value="aunt">👩 Aunt</option>
                        <option value="cousin">👤 Cousin</option>
                        <option value="friend">👫 Friend</option>
                    </select>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="closeInviteModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold">Send Invite</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Transactions -->
    <div id="transactionsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-96 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Transactions</h3>
                <button onclick="closeTransactionsModal()" class="text-gray-500 text-2xl">✕</button>
            </div>

            <div id="transactionsList" class="space-y-3">
                <p class="text-gray-500 text-center py-8">No transactions yet</p>
            </div>
        </div>
    </div>

    <!-- Modal: Account Settings -->
    <div id="accountSettingsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Account Settings</h3>
                <button onclick="closeAccountSettingsModal()" class="text-gray-500 text-2xl">✕</button>
            </div>

            <form id="accountSettingsForm" onsubmit="updateAccountSettings(event)">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Full Name</label>
                    <input type="text" id="settingsName" placeholder="Your full name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Phone Number</label>
                    <input type="tel" id="settingsPhone" placeholder="+250xxxxxxxxx" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Email Address</label>
                    <input type="email" id="settingsEmail" placeholder="your@email.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Change PIN</label>
                    <input type="password" id="newPin" placeholder="New PIN (4 digits)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" minlength="4" maxlength="4">
                    <p class="text-gray-600 text-sm mt-2">Leave empty to keep current PIN</p>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="closeAccountSettingsModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Add Payment Method -->
    <div id="addPaymentMethodModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Add Payment Method</h3>
                <button onclick="closeAddPaymentMethodModal()" class="text-gray-500 text-2xl">✕</button>
            </div>

            <form id="paymentMethodForm" onsubmit="addPaymentMethod(event)">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Payment Method Type</label>
                    <select id="methodType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select method...</option>
                        <option value="mtn">📱 MTN Mobile Money</option>
                        <option value="airtel">📱 Airtel Money</option>
                        <option value="bank">🏦 Bank Transfer</option>
                        <option value="visa">💳 Visa Card</option>
                        <option value="mastercard">💳 Mastercard</option>
                    </select>
                </div>

                <div class="mb-4" id="accountNumberField">
                    <label class="block text-gray-700 font-bold mb-2">Account/Phone Number</label>
                    <input type="text" id="methodAccount" placeholder="Enter account number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4" id="cardDetailsField" style="display: none;">
                    <label class="block text-gray-700 font-bold mb-2">Card Number</label>
                    <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="grid grid-cols-2 gap-4 mt-3">
                        <input type="text" placeholder="MM/YY" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" placeholder="CVV" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Account Name</label>
                    <input type="text" id="methodName" placeholder="How you want to call this method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="closeAddPaymentMethodModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">Add Method</button>
                </div>
            </form>
        </div>
    </div>

<script>
            let parentData = {
            name: 'Parent User',
            email: '',
            phone: '',
            children: [],
            contributors: [],
            transactions: [],
            paymentMethods: [
                { type: 'mtn', name: 'My MTN', status: 'Active' },
                { type: 'airtel', name: 'My Airtel', status: 'Active' }
            ],
            pin: '1234',
            accountCreated: new Date().toLocaleDateString()
        };

        // Initialize
        window.addEventListener('DOMContentLoaded', function() {
            loadData();
            updateDashboard();
            populateChildSelects();
            updateAccountDisplay();
        });

        // Add Child
        function addChild(event) {
            event.preventDefault();
            const child = {
                id: 'child-' + Date.now(),
                name: document.getElementById('childName').value,
                dob: document.getElementById('childDOB').value,
                goal: parseFloat(document.getElementById('savingsGoal').value),
                balance: 0
            };
            parentData.children.push(child);
            saveData();
            closeAddChildModal();
            document.getElementById('addChildForm').reset();
            updateDashboard();
        }

        // Process Deposit
        function processDeposit(event) {
            event.preventDefault();
            
            const childId = document.getElementById('depositChild').value;
            const amount = parseFloat(document.getElementById('depositAmount').value);
            const method = document.getElementById('paymentMethod').value;
            const pin = document.getElementById('depositPIN').value;

            if (pin !== parentData.pin) {
                alert('Invalid PIN');
                return;
            }

            const child = parentData.children.find(c => c.id === childId);
            if (child) {
                child.balance += amount;
                parentData.transactions.unshift({
                    id: 'txn-' + Date.now(),
                    childName: child.name,
                    amount: amount,
                    method: method,
                    date: new Date().toLocaleDateString(),
                    status: 'Completed'
                });
                saveData();
                closeDepositModal();
                document.getElementById('depositForm').reset();
                updateDashboard();
                alert('Deposit successful!');
            }
        }

        // Invite Contributor
        function inviteContributor(event) {
            event.preventDefault();
            const contributor = {
                id: 'contrib-' + Date.now(),
                name: document.getElementById('contributorName').value,
                phone: document.getElementById('contributorPhone').value,
                relationship: document.getElementById('relationship').value,
                status: 'Invited',
                contributed: 0
            };
            parentData.contributors.push(contributor);
            saveData();
            closeInviteModal();
            document.getElementById('inviteForm').reset();
            updateDashboard();
            alert('Invitation sent!');
        }

        // Update Dashboard
        function updateDashboard() {
            // Update metrics
            const totalSaved = parentData.children.reduce((sum, child) => sum + child.balance, 0);
            document.getElementById('totalSaved').textContent = 'FRW ' + totalSaved.toLocaleString();
            document.getElementById('childCount').textContent = parentData.children.length;
            document.getElementById('contributorCount').textContent = parentData.contributors.length;

            // Update sidebar stats
            updateSidebarStats();

            // Render children
            const childContainer = document.getElementById('childrenContainer');
            if (parentData.children.length === 0) {
                childContainer.innerHTML = '<div class="grid grid-cols-1 gap-6"><p class="text-gray-500 text-center py-8">No children added yet. Click "Add New Child" to get started.</p></div>';
            } else {
                childContainer.innerHTML = '<div class="grid grid-cols-1 gap-6">' + parentData.children.map(child => {
                    const age = Math.floor((new Date() - new Date(child.dob)) / (365.25 * 24 * 60 * 60 * 1000));
                    const progress = Math.min((child.balance / child.goal) * 100, 100);
                    return `
                        <div class="border rounded-lg p-6 card-hover bg-white shadow-md">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-800">${child.name}</h4>
                                    <p class="text-gray-600 text-sm mt-1">${age} years old</p>
                                </div>
                                <span class="text-4xl">👧</span>
                            </div>
                            <div class="space-y-4">
                                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg">
                                    <p class="text-gray-600 text-sm font-semibold mb-1">Current Balance</p>
                                    <p class="text-3xl font-bold text-green-600">FRW ${child.balance.toLocaleString()}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm font-semibold mb-2">Savings Goal: FRW ${child.goal.toLocaleString()}</p>
                                    <div class="w-full bg-gray-300 rounded-full h-3 overflow-hidden">
                                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all" style="width: ${progress}%"></div>
                                    </div>
                                    <p class="text-gray-700 text-sm font-semibold mt-2">${progress.toFixed(1)}% Complete</p>
                                </div>
                                <button onclick="depositForChild('${child.id}')" class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700 font-bold transition">💳 Add Deposit</button>
                            </div>
                        </div>
                    `;
                }).join('') + '</div>';
            }

            // Render contributors
            const contribContainer = document.getElementById('contributorsContainer');
            if (parentData.contributors.length === 0) {
                contribContainer.innerHTML = '<p class="text-gray-500 text-center col-span-full py-8">No contributors yet</p>';
            } else {
                contribContainer.innerHTML = parentData.contributors.map(contrib => `
                    <div class="border border-green-300 rounded-lg p-4 bg-green-50">
                        <h4 class="font-bold text-gray-800 mb-2">${contrib.name}</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            <span class="font-semibold">${contrib.relationship}</span> • ${contrib.phone}
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="px-3 py-1 rounded text-sm ${contrib.status === 'Active' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white'}">${contrib.status}</span>
                            <p class="text-lg font-bold text-green-600">FRW ${contrib.contributed}</p>
                        </div>
                    </div>
                `).join('');
            }

            // Update Children Page Stats
            document.getElementById('childrenPageCount').textContent = parentData.children.length;
            document.getElementById('childrenPageTotal').textContent = 'FRW ' + totalSaved.toLocaleString();
            const totalGoal = parentData.children.reduce((sum, child) => sum + child.goal, 0);
            const overallProgress = totalGoal > 0 ? Math.round((totalSaved / totalGoal) * 100) : 0;
            document.getElementById('childrenPageProgress').textContent = overallProgress + '%';

            // Update Account Page Stats
            document.getElementById('accountChildCount').textContent = parentData.children.length;
            document.getElementById('accountContribCount').textContent = parentData.contributors.length;
            document.getElementById('accountTotalSaved').textContent = 'FRW ' + totalSaved.toLocaleString();
        }

        // Populate Child Selects
        function populateChildSelects() {
            const select = document.getElementById('depositChild');
            select.innerHTML = '<option value="">Choose a child...</option>' + 
                parentData.children.map(c => `<option value="${c.id}">${c.name} - FRW ${c.balance.toLocaleString()}</option>`).join('');
        }

        // Deposit for Child
        function depositForChild(childId) {
            document.getElementById('depositChild').value = childId;
            openDepositModal();
        }

        // View Transactions
        function openViewTransactions() {
            const container = document.getElementById('transactionsList');
            if (parentData.transactions.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8">No transactions yet</p>';
            } else {
                container.innerHTML = parentData.transactions.map(txn => `
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded border-l-4 border-green-500">
                        <div>
                            <p class="font-bold text-gray-800">${txn.childName}</p>
                            <p class="text-gray-600 text-sm">${txn.date}</p>
                        </div>
                        <p class="font-bold text-lg text-green-600">+FRW ${txn.amount.toLocaleString()}</p>
                    </div>
                `).join('');
            }
            document.getElementById('transactionsModal').classList.remove('hidden');
        }

        // Modal Functions
        function openAddChildModal() { document.getElementById('addChildModal').classList.remove('hidden'); }
        function closeAddChildModal() { document.getElementById('addChildModal').classList.add('hidden'); }
        function openDepositModal() { document.getElementById('depositModal').classList.remove('hidden'); }
        function closeDepositModal() { document.getElementById('depositModal').classList.add('hidden'); }
        function openInviteModal() { document.getElementById('inviteModal').classList.remove('hidden'); }
        function closeInviteModal() { document.getElementById('inviteModal').classList.add('hidden'); }
        function closeTransactionsModal() { document.getElementById('transactionsModal').classList.add('hidden'); }
        function openAccountSettingsModal() { 
            document.getElementById('settingsName').value = parentData.name;
            document.getElementById('settingsPhone').value = parentData.phone || '';
            document.getElementById('settingsEmail').value = parentData.email || '';
            document.getElementById('accountSettingsModal').classList.remove('hidden'); 
        }
        function closeAccountSettingsModal() { document.getElementById('accountSettingsModal').classList.add('hidden'); }
        function openAddPaymentMethodModal() { document.getElementById('addPaymentMethodModal').classList.remove('hidden'); }
        function closeAddPaymentMethodModal() { document.getElementById('addPaymentMethodModal').classList.add('hidden'); }

        // Update Account Display
        function updateAccountDisplay() {
            document.getElementById('accountName').textContent = parentData.name;
            document.getElementById('accountPhone').textContent = parentData.phone || '+250 (Not set)';
            document.getElementById('accountEmail').textContent = parentData.email || 'Not set';
            document.getElementById('accountCreated').textContent = parentData.accountCreated;
        }

        // Update Account Settings
        function updateAccountSettings(event) {
            event.preventDefault();
            parentData.name = document.getElementById('settingsName').value;
            parentData.phone = document.getElementById('settingsPhone').value;
            parentData.email = document.getElementById('settingsEmail').value;
            
            const newPin = document.getElementById('newPin').value;
            if (newPin) {
                if (newPin.length !== 4) {
                    alert('PIN must be 4 digits');
                    return;
                }
                parentData.pin = newPin;
            }
            
            saveData();
            updateAccountDisplay();
            closeAccountSettingsModal();
            alert('Account settings updated successfully!');
        }

        // Add Payment Method
        function addPaymentMethod(event) {
            event.preventDefault();
            const methodType = document.getElementById('methodType').value;
            const methodName = document.getElementById('methodName').value;
            const methodAccount = document.getElementById('methodAccount').value;

            if (!methodType || !methodName) {
                alert('Please fill all required fields');
                return;
            }

            parentData.paymentMethods.push({
                type: methodType,
                name: methodName,
                account: methodAccount,
                status: 'Pending Verification'
            });

            saveData();
            closeAddPaymentMethodModal();
            document.getElementById('paymentMethodForm').reset();
            updateDashboard();
            alert('Payment method added! Awaiting verification.');
        }

        // Show Child Detail
        function showChildDetail(childId) {
            const child = parentData.children.find(c => c.id === childId);
            if (!child) return;

            // Update child detail section
            document.getElementById('childDetailName').textContent = child.name;
            document.getElementById('childDetailNameText').textContent = child.name;
            document.getElementById('childDetailDOB').textContent = child.dob;
            document.getElementById('childDetailGoal').textContent = 'FRW ' + child.goal.toLocaleString();
            document.getElementById('childDetailBalance').textContent = 'FRW ' + child.balance.toLocaleString();
            
            // Calculate and update progress
            const progress = child.goal > 0 ? Math.round((child.balance / child.goal) * 100) : 0;
            document.getElementById('childDetailProgress').style.width = progress + '%';
            document.getElementById('childDetailProgressText').textContent = progress + '% of goal achieved';

            // Populate deposit history for this child
            const childTransactions = parentData.transactions.filter(t => t.childName === child.name);
            const historyTable = document.getElementById('childDepositHistoryTable');
            
            if (childTransactions.length === 0) {
                historyTable.innerHTML = '<tr class="border-b border-gray-200"><td colspan="4" class="px-4 py-3 text-center text-gray-600">No deposits yet</td></tr>';
            } else {
                historyTable.innerHTML = childTransactions.map(txn => `
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">${txn.date}</td>
                        <td class="px-4 py-3 text-gray-800 font-bold">FRW ${txn.amount.toLocaleString()}</td>
                        <td class="px-4 py-3 text-gray-700">${txn.method}</td>
                        <td class="px-4 py-3"><span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm font-semibold">${txn.status}</span></td>
                    </tr>
                `).join('');
            }

            // Store current child for deposit
            window.currentChildId = childId;

            // Show child detail section
            showSection('childDetail');
        }

        // Open Deposit Modal for Child
        function openDepositForChild() {
            if (!window.currentChildId) return;
            
            const child = parentData.children.find(c => c.id === window.currentChildId);
            if (child) {
                document.getElementById('depositChild').value = window.currentChildId;
                document.getElementById('depositModal').classList.remove('hidden');
            }
        }

        // Update Transactions Table
        function updateTransactionsTable() {
            const filterChild = document.getElementById('filterChild').value;
            const searchTerm = document.getElementById('searchTransactions').value.toLowerCase();
            
            let transactions = parentData.transactions;
            
            // Filter by child if selected
            if (filterChild) {
                transactions = transactions.filter(t => t.childName === filterChild);
            }
            
            // Filter by search term
            if (searchTerm) {
                transactions = transactions.filter(t => 
                    t.childName.toLowerCase().includes(searchTerm) ||
                    t.method.toLowerCase().includes(searchTerm) ||
                    t.date.includes(searchTerm)
                );
            }

            const table = document.getElementById('transactionsTable');
            if (transactions.length === 0) {
                table.innerHTML = '<tr class="border-b border-gray-200"><td colspan="5" class="px-4 py-3 text-center text-gray-600">No transactions found</td></tr>';
            } else {
                table.innerHTML = transactions.map(txn => `
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">${txn.date}</td>
                        <td class="px-4 py-3 text-gray-800 font-semibold">${txn.childName}</td>
                        <td class="px-4 py-3 text-gray-800 font-bold">FRW ${txn.amount.toLocaleString()}</td>
                        <td class="px-4 py-3 text-gray-700">${txn.method}</td>
                        <td class="px-4 py-3"><span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm font-semibold">${txn.status}</span></td>
                    </tr>
                `).join('');
            }
        }

        // Update Settings Display
        function updateSettingsDisplay() {
            document.getElementById('settingsName').textContent = parentData.name;
            document.getElementById('settingsEmail').textContent = parentData.email || 'Not set';
            document.getElementById('settingsPhone').textContent = parentData.phone || 'Not set';
            document.getElementById('settingsCreated').textContent = parentData.accountCreated;
        }

        // Update Account Display
        function updateAccountDisplay() {
            document.getElementById('accountName').textContent = parentData.name;
            document.getElementById('accountEmail').textContent = parentData.email || '(Not provided)';
            document.getElementById('accountPhone').textContent = parentData.phone || '(Not provided)';
            updateSettingsDisplay();
            
            // Populate filter children in transactions
            const filterChild = document.getElementById('filterChild');
            filterChild.innerHTML = '<option value="">All Children</option>' + 
                parentData.children.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
            
            updateTransactionsTable();
        }

        // Click handlers for child cards
        function setupChildCardHandlers() {
            const childCards = document.querySelectorAll('[data-child-id]');
            childCards.forEach(card => {
                card.style.cursor = 'pointer';
                card.onclick = function() {
                    const childId = this.getAttribute('data-child-id');
                    showChildDetail(childId);
                };
            });
        }

        // Show Section - Toggle view sections
        function showSection(sectionId) {
            // Hide all view sections
            const sections = document.querySelectorAll('.view-section');
            sections.forEach(section => {
                section.classList.add('hidden');
                section.classList.remove('active');
            });

            // Show the selected section
            const selectedSection = document.getElementById(sectionId);
            if (selectedSection) {
                selectedSection.classList.remove('hidden');
                selectedSection.classList.add('active');
            }

            // Update active nav item
            const navItems = document.querySelectorAll('.sidebar-nav-item');
            navItems.forEach(item => {
                item.classList.remove('active');
            });

            // Find and activate the button that triggered this
            const activeButton = document.querySelector(`[onclick="showSection('${sectionId}')"]`);
            if (activeButton) {
                activeButton.classList.add('active');
            }

            // Close sidebar on mobile
            closeSidebar();

            // Setup handlers if it's the children section
            if (sectionId === 'children') {
                setTimeout(setupChildCardHandlers, 100);
            }

            // Update display for specific sections
            if (sectionId === 'transactions') {
                updateTransactionsTable();
            }
            if (sectionId === 'settings') {
                updateSettingsDisplay();
            }
        }

        // Modal Functions
        function openAddChildModal() {
            document.getElementById('addChildModal').classList.remove('hidden');
        }

        function closeAddChildModal() {
            document.getElementById('addChildModal').classList.add('hidden');
        }

        function openDepositModal() {
            document.getElementById('depositModal').classList.remove('hidden');
            populateChildSelects();
        }

        function closeDepositModal() {
            document.getElementById('depositModal').classList.add('hidden');
        }

        function openInviteModal() {
            document.getElementById('inviteModal').classList.remove('hidden');
            populateChildSelects();
        }

        function closeInviteModal() {
            document.getElementById('inviteModal').classList.add('hidden');
        }

        function openAccountSettingsModal() {
            document.getElementById('settingsName').value = parentData.name;
            document.getElementById('settingsPhone').value = parentData.phone;
            document.getElementById('settingsEmail').value = parentData.email;
            document.getElementById('newPin').value = '';
            document.getElementById('accountSettingsModal').classList.remove('hidden');
        }

        function closeAccountSettingsModal() {
            document.getElementById('accountSettingsModal').classList.add('hidden');
        }

        function openAddPaymentMethodModal() {
            document.getElementById('addPaymentMethodModal').classList.remove('hidden');
        }

        function closeAddPaymentMethodModal() {
            document.getElementById('addPaymentMethodModal').classList.add('hidden');
        }

        function openViewTransactions() {
            showSection('transactions');
        }

        function openViewContributor(contributorId) {
            const contrib = parentData.contributors.find(c => c.id === contributorId);
            if (contrib) {
                document.getElementById('viewContributorModal').classList.remove('hidden');
                document.getElementById('contributorDetail').innerHTML = `
                    <h3 class="text-xl font-bold text-gray-800 mb-2">${contrib.name}</h3>
                    <p class="text-gray-600 mb-1"><strong>Phone:</strong> ${contrib.phone}</p>
                    <p class="text-gray-600 mb-1"><strong>Relationship:</strong> ${contrib.relationship}</p>
                    <p class="text-gray-600 mb-4"><strong>Status:</strong> ${contrib.status}</p>
                    <p class="text-green-600 font-bold mb-4">Total Contributed: FRW ${contrib.contributed.toLocaleString()}</p>
                `;
            }
        }

        function closeViewContributorModal() {
            document.getElementById('viewContributorModal').classList.add('hidden');
        }

        // Storage
        function saveData() {
            localStorage.setItem('parentData', JSON.stringify(parentData));
        }

        function loadData() {
            const saved = localStorage.getItem('parentData');
            if (saved) parentData = JSON.parse(saved);
            // Call api.js function to fetch and display username from database
            displayUsername();
        }

        // Sidebar Functions
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('hidden');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.remove('open');
            backdrop.classList.add('hidden');
        }

        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
                closeSidebar();
            }
            // Update active nav item
            document.querySelectorAll('.sidebar-nav-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function updateSidebarStats() {
            const totalSaved = parentData.children.reduce((sum, child) => sum + child.balance, 0);
            document.getElementById('sidebarChildCount').textContent = parentData.children.length;
            document.getElementById('sidebarContribCount').textContent = parentData.contributors.length;
            document.getElementById('sidebarTotal').textContent = 'FRW ' + totalSaved.toLocaleString();
        }

        // Storage

        // Logout
        function logout() {
            if (confirm('Sign out?')) {
                localStorage.removeItem('parentData');
                window.location.href = './login.html';
            }
        }
</script>
</body>
</html>
