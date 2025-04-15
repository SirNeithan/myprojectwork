<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#5D5CDE',
                        secondary: '#6366F1',
                        success: '#10B981',
                        danger: '#EF4444',
                        warning: '#F59E0B',
                        info: '#3B82F6',
                    }
                }
            }
        }
    </script>
    <!-- Simulate PHP-like database with JavaScript -->
    <script>
        // Mock database in localStorage-like structure
        const mockDatabase = {
            elections: [
                {
                    id: 1,
                    title: "Student Council 2025",
                    description: "Election for the Student Council officers for the academic year 2025-2026",
                    startDate: "2025-06-01",
                    endDate: "2025-06-02",
                    status: "active",
                    candidateCount: 12,
                    voterCount: 450,
                    votesCount: 320
                },
                {
                    id: 2,
                    title: "Board Member Election",
                    description: "Annual election for organization board members",
                    startDate: "2025-06-05",
                    endDate: "2025-06-09",
                    status: "active",
                    candidateCount: 8,
                    voterCount: 324,
                    votesCount: 198
                },
                {
                    id: 3,
                    title: "Faculty Senate Vote",
                    description: "Election for Faculty Senate representative positions",
                    startDate: "2025-06-10",
                    endDate: "2025-06-13",
                    status: "active",
                    candidateCount: 15,
                    voterCount: 480,
                    votesCount: 349
                },
                {
                    id: 4,
                    title: "Homecoming Court",
                    description: "Vote for Homecoming King and Queen candidates",
                    startDate: "2025-05-15",
                    endDate: "2025-05-17",
                    status: "completed",
                    candidateCount: 10,
                    voterCount: 1200,
                    votesCount: 856
                }
            ],
            candidates: [
                { id: 1, name: "John Steven", position: "President", election_id: 1, votes: 125, photo: null },
                { id: 2, name: "Nalule Catherine", position: "President", election_id: 1, votes: 142, photo: null },
                { id: 3, name: "George Mugerwa", position: "Vice President", election_id: 1, votes: 98, photo: null },
                { id: 4, name: "Birungi Bridget", position: "Vice President", election_id: 1, votes: 102, photo: null },
                { id: 5, name: "Alvin Jude", position: "Chair", election_id: 2, votes: 67, photo: null },
                { id: 6, name: "Ssenkooto John Davis", position: "Chair", election_id: 2, votes: 131, photo: null }
            ],
            voters: [
                { id: 1, name: "Paul Muhanguzi", email: "muhanguzi.paul@stud.umu.ac.ug", registered_date: "2025-05-20", type: "Student", has_voted: true },
                { id: 2, name: "Assiimwe Jackline", email: "jackline.assiimwe@stud.umu.ac.ug", registered_date: "2025-05-20", type: "Student", has_voted: true },
                { id: 3, name: "Neithan Abel", email: "neithan.abel@stud.umu.ac.ug", registered_date: "2025-05-20", type: "Faculty", has_voted: true },
                { id: 4, name: "Nakanwagi Angella", email: "angella.nakanwagi@stud.umu.ac.ug", registered_date: "2025-05-20", type: "Staff", has_voted: false },
                { id: 5, name: "Pamella Maureen", email: "pamella.maureen@stud.umu.ac.ug", registered_date: "2025-05-20", type: "Student", has_voted: false },
                { id: 6, name: "Nsubuga Nicholas Paul", email: "nsubugpaul.nicholas@stud.umu.ac.ug", registered_date: "2025-05-20", type: "Alumni", has_voted: true }
            ],
            activities: [
                { id: 1, type: "register", description: "John Steven registered for Student Council election", time: "10 minutes ago" },
                { id: 2, type: "vote", description: "85 new votes in Faculty Senate Election", time: "25 minutes ago" },
                { id: 3, type: "alert", description: "High traffic detected on Board Member Election", time: "45 minutes ago" },
                { id: 4, type: "delete", description: "Club President Election was removed", time: "1 hour ago" },
                { id: 5, type: "edit", description: "Student Council election details updated", time: "2 hours ago" },
                { id: 6, type: "add", description: "New candidate added to Board Member Election", time: "3 hours ago" }
            ],
            settings: {
                siteTitle: "E-Voting Administration",
                allowGuestAccess: false,
                requireEmailVerification: true,
                votingHours: "6:00 AM - 6:00 PM",
                sessionTimeout: 30
            }
        };

        // Simulated API functions to interact with the mock database
        const mockAPI = {
            // Elections
            getElections: function(status = null) {
                if (status) {
                    return mockDatabase.elections.filter(e => e.status === status);
                }
                return mockDatabase.elections;
            },
            getElectionById: function(id) {
                return mockDatabase.elections.find(e => e.id === parseInt(id));
            },
            createElection: function(election) {
                const newId = Math.max(...mockDatabase.elections.map(e => e.id), 0) + 1;
                const newElection = { ...election, id: newId };
                mockDatabase.elections.push(newElection);
                this.addActivity('add', `New election "${election.title}" created`);
                return newElection;
            },
            updateElection: function(id, updates) {
                const index = mockDatabase.elections.findIndex(e => e.id === parseInt(id));
                if (index !== -1) {
                    mockDatabase.elections[index] = { ...mockDatabase.elections[index], ...updates };
                    this.addActivity('edit', `Election "${mockDatabase.elections[index].title}" details updated`);
                    return mockDatabase.elections[index];
                }
                return null;
            },
            deleteElection: function(id) {
                const index = mockDatabase.elections.findIndex(e => e.id === parseInt(id));
                if (index !== -1) {
                    const deleted = mockDatabase.elections[index];
                    mockDatabase.elections.splice(index, 1);
                    this.addActivity('delete', `Election "${deleted.title}" was removed`);
                    return true;
                }
                return false;
            },
            
            // Candidates
            getCandidates: function(electionId = null) {
                if (electionId) {
                    return mockDatabase.candidates.filter(c => c.election_id === parseInt(electionId));
                }
                return mockDatabase.candidates;
            },
            getCandidateById: function(id) {
                return mockDatabase.candidates.find(c => c.id === parseInt(id));
            },
            createCandidate: function(candidate) {
                const newId = Math.max(...mockDatabase.candidates.map(c => c.id), 0) + 1;
                const newCandidate = { ...candidate, id: newId, votes: 0 };
                mockDatabase.candidates.push(newCandidate);
                
                const election = this.getElectionById(candidate.election_id);
                this.addActivity('add', `New candidate "${candidate.name}" added to ${election.title}`);
                
                return newCandidate;
            },
            updateCandidate: function(id, updates) {
                const index = mockDatabase.candidates.findIndex(c => c.id === parseInt(id));
                if (index !== -1) {
                    mockDatabase.candidates[index] = { ...mockDatabase.candidates[index], ...updates };
                    this.addActivity('edit', `Candidate "${mockDatabase.candidates[index].name}" information updated`);
                    return mockDatabase.candidates[index];
                }
                return null;
            },
            deleteCandidate: function(id) {
                const index = mockDatabase.candidates.findIndex(c => c.id === parseInt(id));
                if (index !== -1) {
                    const deleted = mockDatabase.candidates[index];
                    mockDatabase.candidates.splice(index, 1);
                    this.addActivity('delete', `Candidate "${deleted.name}" was removed`);
                    return true;
                }
                return false;
            },
            
            // Voters
            getVoters: function(type = null) {
                if (type) {
                    return mockDatabase.voters.filter(v => v.type === type);
                }
                return mockDatabase.voters;
            },
            getVoterById: function(id) {
                return mockDatabase.voters.find(v => v.id === parseInt(id));
            },
            createVoter: function(voter) {
                const newId = Math.max(...mockDatabase.voters.map(v => v.id), 0) + 1;
                const newVoter = { 
                    ...voter, 
                    id: newId, 
                    registered_date: new Date().toISOString().slice(0, 10),
                    has_voted: false
                };
                mockDatabase.voters.push(newVoter);
                this.addActivity('register', `New voter "${voter.name}" registered`);
                return newVoter;
            },
            updateVoter: function(id, updates) {
                const index = mockDatabase.voters.findIndex(v => v.id === parseInt(id));
                if (index !== -1) {
                    mockDatabase.voters[index] = { ...mockDatabase.voters[index], ...updates };
                    if (updates.has_voted) {
                        this.addActivity('vote', `${mockDatabase.voters[index].name} has cast their vote`);
                    } else {
                        this.addActivity('edit', `Voter "${mockDatabase.voters[index].name}" information updated`);
                    }
                    return mockDatabase.voters[index];
                }
                return null;
            },
            deleteVoter: function(id) {
                const index = mockDatabase.voters.findIndex(v => v.id === parseInt(id));
                if (index !== -1) {
                    const deleted = mockDatabase.voters[index];
                    mockDatabase.voters.splice(index, 1);
                    this.addActivity('delete', `Voter "${deleted.name}" was removed`);
                    return true;
                }
                return false;
            },
            
            // Activities
            getActivities: function(limit = null) {
                let activities = [...mockDatabase.activities];
                activities.sort((a, b) => {
                    // Sort by most recent first
                    return a.id < b.id ? 1 : -1;
                });
                if (limit) {
                    return activities.slice(0, limit);
                }
                return activities;
            },
            addActivity: function(type, description) {
                // Get time description
                const getTimeDescription = () => {
                    return 'just now';
                };
                
                const newId = Math.max(...mockDatabase.activities.map(a => a.id), 0) + 1;
                const activity = {
                    id: newId,
                    type: type,
                    description: description,
                    time: getTimeDescription()
                };
                
                mockDatabase.activities.unshift(activity);
                return activity;
            },
            
            // Settings
            getSettings: function() {
                return mockDatabase.settings;
            },
            updateSettings: function(updates) {
                mockDatabase.settings = { ...mockDatabase.settings, ...updates };
                this.addActivity('edit', 'System settings were updated');
                return mockDatabase.settings;
            },
            
            // Stats and analytics
            getStats: function() {
                const activeElections = mockDatabase.elections.filter(e => e.status === "active").length;
                const totalVoters = mockDatabase.voters.length;
                const votedVoters = mockDatabase.voters.filter(v => v.has_voted).length;
                const voterTurnout = totalVoters > 0 ? ((votedVoters / totalVoters) * 100).toFixed(1) : 0;
                
                return {
                    activeElections,
                    totalVoters,
                    votedVoters,
                    voterTurnout
                };
            },
            getVoterDistribution: function() {
                const types = [...new Set(mockDatabase.voters.map(v => v.type))];
                return types.map(type => {
                    const count = mockDatabase.voters.filter(v => v.type === type).length;
                    const percentage = ((count / mockDatabase.voters.length) * 100).toFixed(1);
                    return { type, count, percentage };
                });
            },
            getVotingActivityData: function() {
                // Mock hourly voting data
                return {
                    labels: ['6 AM', '8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM', '8 PM'],
                    data: [12, 19, 35, 62, 39, 80, 70, 53]
                };
            }
        };
    </script>
    <style>
        /* Custom styles */
        .sidebar-menu-item.active {
            background-color: rgba(99, 102, 241, 0.1);
            border-left: 4px solid #5D5CDE;
        }

        .sidebar-menu-item:hover:not(.active) {
            background-color: rgba(99, 102, 241, 0.05);
        }

        .toggle-checkbox:checked {
            right: 0;
            border-color: #5D5CDE;
        }

        .toggle-checkbox:checked + .toggle-label {
            background-color: #5D5CDE;
        }

        /* Dark mode styles */
        .dark .sidebar {
            background-color: #1E1E1E;
        }
        
        .dark .main-content {
            background-color: #181818;
        }

        /* Animation for loader */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loader {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Check for dark mode -->
    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (event.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar fixed lg:static inset-y-0 left-0 w-64 md:w-72 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-r border-gray-200 dark:border-gray-700 shadow-sm z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="px-4 py-6 flex items-center border-b border-gray-200 dark:border-gray-700">
                    <i class="fas fa-vote-yea text-primary text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold text-primary">E-Voting Admin</h1>
                </div>

                <!-- Menu -->
                <nav class="flex-1 overflow-y-auto py-4">
                    <ul class="px-2">
                        <li class="mb-1">
                            <a href="#dashboard" class="sidebar-menu-item active flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded">
                                <i class="fas fa-tachometer-alt w-5 text-center mr-3"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#elections" class="sidebar-menu-item flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded">
                                <i class="fas fa-poll w-5 text-center mr-3"></i>
                                <span>Elections</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#candidates" class="sidebar-menu-item flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded">
                                <i class="fas fa-user-tie w-5 text-center mr-3"></i>
                                <span>Candidates</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#voters" class="sidebar-menu-item flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded">
                                <i class="fas fa-users w-5 text-center mr-3"></i>
                                <span>Voters</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#results" class="sidebar-menu-item flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded">
                                <i class="fas fa-chart-bar w-5 text-center mr-3"></i>
                                <span>Results</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="#settings" class="sidebar-menu-item flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 rounded">
                                <i class="fas fa-cog w-5 text-center mr-3"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- User info -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Admin User</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">admin@example.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 bg-gray-50 dark:bg-gray-900 overflow-y-auto">
            <!-- Top Navbar -->
            <header class="bg-white dark:bg-gray-800 shadow-sm py-4 px-6 flex items-center justify-between">
                <!-- Mobile menu toggle -->
                <button id="sidebar-toggle" class="lg:hidden text-gray-600 dark:text-gray-200 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Title -->
                <h1 id="page-title" class="text-xl font-bold text-gray-800 dark:text-gray-100 hidden sm:block">Administrator Dashboard</h1>

                <!-- Right side actions -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button id="notification-button" class="text-gray-600 dark:text-gray-300 focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notification-count" class="absolute top-0 right-0 h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">3</span>
                        </button>
                    </div>

                    <!-- Dark mode toggle -->
                    <div class="flex items-center">
                        <span class="mr-2 text-sm text-gray-600 dark:text-gray-300">
                            <i class="fas fa-sun"></i>
                        </span>
                        <div class="relative inline-block w-10 align-middle select-none">
                            <input type="checkbox" id="dark-mode-toggle" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer focus:outline-none"
                                   onclick="toggleDarkMode()"/>
                            <label for="dark-mode-toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                            <i class="fas fa-moon"></i>
                        </span>
                    </div>

                    <!-- Logout -->
                    <button class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <main class="p-6">
                <!-- Page content will be loaded here -->
                <div id="content-area">
                    <!-- Dashboard content (default view) -->
                    <div id="dashboard-content" class="page-content active">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4">Dashboard Overview</h2>
                            
                            <!-- Stats Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                                <!-- Active Elections Card -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-full bg-primary bg-opacity-10 text-primary mr-4">
                                            <i class="fas fa-vote-yea text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Active Elections</p>
                                            <p id="active-elections-count" class="text-2xl font-bold">3</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                        <span class="text-green-500 mr-1"><i class="fas fa-arrow-up"></i> 25%</span>
                                        <span>from last month</span>
                                    </div>
                                </div>
                                
                                <!-- Total Voters Card -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-full bg-green-500 bg-opacity-10 text-green-500 mr-4">
                                            <i class="fas fa-users text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Voters</p>
                                            <p id="total-voters-count" class="text-2xl font-bold">1,254</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                        <span class="text-green-500 mr-1"><i class="fas fa-arrow-up"></i> 12%</span>
                                        <span>from last month</span>
                                    </div>
                                </div>
                                
                                <!-- Votes Cast Card -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-full bg-blue-500 bg-opacity-10 text-blue-500 mr-4">
                                            <i class="fas fa-check-square text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Votes Cast</p>
                                            <p id="votes-cast-count" class="text-2xl font-bold">867</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                        <span class="text-green-500 mr-1"><i class="fas fa-arrow-up"></i> 42%</span>
                                        <span>from yesterday</span>
                                    </div>
                                </div>
                                
                                <!-- Voter Turnout Card -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="p-3 rounded-full bg-purple-500 bg-opacity-10 text-purple-500 mr-4">
                                            <i class="fas fa-chart-pie text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Voter Turnout</p>
                                            <p id="voter-turnout" class="text-2xl font-bold">69.1%</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                        <span class="text-red-500 mr-1"><i class="fas fa-arrow-down"></i> 3%</span>
                                        <span>from last election</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Active Elections List & Chart Section -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                                <!-- Active Elections Table -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-4">Active Elections</h3>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Election</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">End Date</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="active-elections-table" class="divide-y divide-gray-200 dark:divide-gray-700">
                                                <!-- Table content will be loaded dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4 flex justify-center">
                                        <button id="create-election-btn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">
                                            <i class="fas fa-plus mr-1"></i> Create New Election
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Voting Activity Chart -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-4">Voting Activity</h3>
                                    <div class="h-72">
                                        <canvas id="votingActivityChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Activity & Voter Distribution Section -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Recent Activity -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
                                    <div id="recent-activity-list" class="space-y-4">
                                        <!-- Activity content will be loaded dynamically -->
                                    </div>
                                </div>
                                
                                <!-- Voter Distribution -->
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-4">Voter Distribution</h3>
                                    <div class="h-72">
                                        <canvas id="voterDistributionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Elections content -->
                    <div id="elections-content" class="page-content hidden">
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold">Manage Elections</h2>
                                <button id="elections-create-btn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">
                                    <i class="fas fa-plus mr-1"></i> Create Election
                                </button>
                            </div>

                            <!-- Filters -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <div class="flex-1">
                                        <label for="election-status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                        <select id="election-status-filter" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="all">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="upcoming">Upcoming</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label for="election-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                                        <input type="text" id="election-search" placeholder="Search elections..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                    <div class="flex items-end">
                                        <button id="election-filter-btn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Elections Table -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Start Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">End Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Candidates</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Votes</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="elections-table" class="divide-y divide-gray-200 dark:divide-gray-700">
                                            <!-- Elections will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-4 flex justify-between items-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Showing <span id="elections-showing">1-4</span> of <span id="elections-total">4</span> elections
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-400 disabled:opacity-50">
                                            Previous
                                        </button>
                                        <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-400 disabled:opacity-50">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Candidates content -->
                    <div id="candidates-content" class="page-content hidden">
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold">Manage Candidates</h2>
                                <button id="candidates-create-btn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">
                                    <i class="fas fa-plus mr-1"></i> Add Candidate
                                </button>
                            </div>

                            <!-- Filters -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <div class="flex-1">
                                        <label for="candidate-election-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Election</label>
                                        <select id="candidate-election-filter" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">All Elections</option>
                                            <!-- Elections will be loaded here -->
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label for="candidate-position-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Position</label>
                                        <select id="candidate-position-filter" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">All Positions</option>
                                            <option value="President">President</option>
                                            <option value="Vice President">Vice President</option>
                                            <option value="Secretary">Secretary</option>
                                            <option value="Treasurer">Treasurer</option>
                                            <option value="Chair">Chair</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label for="candidate-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                                        <input type="text" id="candidate-search" placeholder="Search candidates..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                    <div class="flex items-end">
                                        <button id="candidate-filter-btn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Candidates List -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="candidates-grid">
                                    <!-- Candidates will be loaded here -->
                                </div>

                                <!-- Empty State -->
                                <div id="candidates-empty" class="hidden text-center py-12">
                                    <div class="text-gray-400 dark:text-gray-500 text-5xl mb-4">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400 mb-2">No candidates found</h3>
                                    <p class="text-gray-500 dark:text-gray-500 mb-4">Try changing your filters or add a new candidate</p>
                                    <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none">
                                        <i class="fas fa-plus mr-2"></i> Add Candidate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Voters content -->
                    <div id="voters-content" class="page-content hidden">
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold">Manage Voters</h2>
                                <button id="voters-create-btn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">
                                    <i class="fas fa-plus mr-1"></i> Add Voter
                                </button>
                            </div>

                            <!-- Filters -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <div class="flex-1">
                                        <label for="voter-type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                        <select id="voter-type-filter" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">All Types</option>
                                            <option value="Student">Student</option>
                                            <option value="Faculty">Faculty</option>
                                            <option value="Staff">Staff</option>
                                            <option value="Alumni">Alumni</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label for="voter-status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Voting Status</label>
                                        <select id="voter-status-filter" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">All Status</option>
                                            <option value="voted">Has Voted</option>
                                            <option value="not_voted">Has Not Voted</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label for="voter-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                                        <input type="text" id="voter-search" placeholder="Search voters..." class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                    </div>
                                    <div class="flex items-end">
                                        <button id="voter-filter-btn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Voters Table -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Registration Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="voters-table" class="divide-y divide-gray-200 dark:divide-gray-700">
                                            <!-- Voters will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-4 flex justify-between items-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Showing <span id="voters-showing">1-6</span> of <span id="voters-total">6</span> voters
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-400 disabled:opacity-50">
                                            Previous
                                        </button>
                                        <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-400 disabled:opacity-50">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results content -->
                    <div id="results-content" class="page-content hidden">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-6">Election Results</h2>

                            <!-- Election Selector -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6 border border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <div class="flex-1">
                                        <label for="results-election-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Election</label>
                                        <select id="results-election-select" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            <option value="">Choose an election...</option>
                                            <!-- Elections will be loaded here -->
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <button id="export-results-btn" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none" disabled>
                                            <i class="fas fa-download mr-1"></i> Export Results
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Results Dashboard -->
                            <div id="results-dashboard" class="hidden">
                                <!-- Summary -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-medium mb-2">Voter Turnout</h3>
                                        <div class="flex items-center">
                                            <div class="w-16 h-16 flex items-center justify-center rounded-full border-4 border-primary">
                                                <span id="result-turnout-percent" class="text-2xl font-bold">0%</span>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    <span id="result-votes-cast">0</span> of <span id="result-total-voters">0</span> voters
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-medium mb-2">Time Remaining</h3>
                                        <div id="election-status-indicator">
                                            <!-- Status will be loaded dynamically -->
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-medium mb-2">Total Candidates</h3>
                                        <p id="result-candidate-count" class="text-3xl font-bold">0</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Across <span id="result-position-count">0</span> positions</p>
                                    </div>
                                </div>

                                <!-- Results by Position -->
                                <div id="results-positions" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Position results will be loaded here -->
                                </div>
                            </div>

                            <!-- Select Election Prompt -->
                            <div id="select-election-prompt" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-10 border border-gray-200 dark:border-gray-700 text-center">
                                <div class="text-gray-400 dark:text-gray-500 text-5xl mb-4">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400 mb-2">No election selected</h3>
                                <p class="text-gray-500 dark:text-gray-500 mb-4">Please select an election from the dropdown menu to view results</p>
                            </div>
                        </div>
                    </div>

                    <!-- Settings content -->
                    <div id="settings-content" class="page-content hidden">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-6">System Settings</h2>

                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                                <form id="settings-form">
                                    <!-- General Settings -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">General Settings</h3>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="site-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Site Title</label>
                                                <input type="text" id="site-title" name="siteTitle" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            </div>
                                            <div>
                                                <label for="voting-hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Voting Hours</label>
                                                <input type="text" id="voting-hours" name="votingHours" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Security Settings -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Security Settings</h3>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                            <div>
                                                <label for="session-timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Session Timeout (minutes)</label>
                                                <input type="number" id="session-timeout" name="sessionTimeout" min="5" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="require-email-verification" name="requireEmailVerification" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                                                <label for="require-email-verification" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                    Require email verification for voters
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="allow-guest-access" name="allowGuestAccess" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                                                <label for="allow-guest-access" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                    Allow guest access to view results
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Backup & Maintenance -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Backup & Maintenance</h3>
                                        
                                        <div class="flex space-x-4 mb-4">
                                            <button type="button" id="backup-data-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                                                <i class="fas fa-database mr-2"></i> Backup Data
                                            </button>
                                            <button type="button" id="clear-cache-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                                                <i class="fas fa-broom mr-2"></i> Clear Cache
                                            </button>
                                        </div>
                                        
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <p>Last backup: <span id="last-backup-date">Never</span></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-4">
                                        <button type="button" id="reset-settings-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                                            Reset to Default
                                        </button>
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90 focus:outline-none">
                                            Save Settings
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Form Modals -->
    <!-- Election Form Modal -->
    <div id="election-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="election-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">Create Election</h3>
                <button class="modal-close text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="election-form">
                <input type="hidden" id="election-id">
                <div class="space-y-4">
                    <div>
                        <label for="election-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input type="text" id="election-title" name="title" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="election-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea id="election-description" name="description" rows="3" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="election-start-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input type="date" id="election-start-date" name="startDate" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label for="election-end-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input type="date" id="election-end-date" name="endDate" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                    <div>
                        <label for="election-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select id="election-status" name="status" class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="upcoming">Upcoming</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="modal-cancel px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90">
                        Save Election
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Candidate Form Modal -->
    <div id="candidate-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="candidate-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">Add Candidate</h3>
                <button class="modal-close text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="candidate-form">
                <input type="hidden" id="candidate-id">
                <div class="space-y-4">
                    <div>
                        <label for="candidate-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" id="candidate-name" name="name" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="candidate-position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Position</label>
                        <input type="text" id="candidate-position" name="position" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="candidate-election" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Election</label>
                        <select id="candidate-election" name="election_id" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="">Select Election</option>
                            <!-- Elections will be loaded here -->
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="modal-cancel px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90">
                        Save Candidate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Voter Form Modal -->
    <div id="voter-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="voter-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">Add Voter</h3>
                <button class="modal-close text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="voter-form">
                <input type="hidden" id="voter-id">
                <div class="space-y-4">
                    <div>
                        <label for="voter-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" id="voter-name" name="name" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="voter-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" id="voter-email" name="email" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="voter-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                        <select id="voter-type" name="type" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="Student">Student</option>
                            <option value="Faculty">Faculty</option>
                            <option value="Staff">Staff</option>
                            <option value="Alumni">Alumni</option>
                        </select>
                    </div>
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" id="voter-has-voted" name="has_voted" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                            <label for="voter-has-voted" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Has already voted
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="modal-cancel px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90">
                        Save Voter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirmation</h3>
                <button id="close-confirmation" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="confirmation-content" class="py-2">
                <!-- Confirmation content will be inserted here -->
            </div>
            <div class="mt-6 flex justify-end">
                <button id="confirmation-cancel" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md mr-2 hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
                <button id="confirmation-confirm" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90 focus:outline-none">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notification-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notification</h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modal-content" class="py-2">
                <!-- Modal content will be inserted here -->
            </div>
            <div class="mt-6 flex justify-end">
                <button id="modal-confirm" class="bg-primary text-white px-4 py-2 rounded-md mr-2 hover:bg-opacity-90">Confirm</button>
                <button id="modal-cancel" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Add JavaScript for functionality -->
    <script>
        // Initialize variables and DOM elements
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const sidebarMenuItems = document.querySelectorAll('.sidebar-menu-item');
        const notificationModal = document.getElementById('notification-modal');
        const closeModal = document.getElementById('close-modal');
        const modalConfirm = document.getElementById('modal-confirm');
        const modalCancel = document.getElementById('modal-cancel');
        const modalContent = document.getElementById('modal-content');
        const pageTitle = document.getElementById('page-title');
        const pageContents = document.querySelectorAll('.page-content');
        
        // Track current active page
        let currentPage = 'dashboard';
        
        // Setup dark mode toggle
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
            
            // Update charts to reflect theme
            updateChartsForTheme();
        }
        
        // Set initial state of dark mode toggle
        if (document.documentElement.classList.contains('dark')) {
            darkModeToggle.checked = true;
        }
        
        // Toggle sidebar on mobile
        sidebarToggle.addEventListener('click', () => {
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
        
        // Handle sidebar menu item clicks
        sidebarMenuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Prevent default action
                e.preventDefault();
                
                // Remove active class from all items
                sidebarMenuItems.forEach(i => i.classList.remove('active'));
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Get the href attribute to determine which section to show
                const target = this.getAttribute('href').substring(1);
                
                // On mobile, close the sidebar after selection
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                }
                
                // Change page content
                showPage(target);
            });
        });
        
        // Function to show specific page content
        function showPage(page) {
            // Update current page
            currentPage = page;
            
            // Hide all page contents
            pageContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show the selected page content
            const selectedContent = document.getElementById(`${page}-content`);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
            
            // Update page title
            updatePageTitle(page);
            
            // Load page-specific data
            loadPageData(page);
        }
        
        // Update page title based on current page
        function updatePageTitle(page) {
            let title = 'Administrator Dashboard';
            
            switch (page) {
                case 'elections':
                    title = 'Manage Elections';
                    break;
                case 'candidates':
                    title = 'Manage Candidates';
                    break;
                case 'voters':
                    title = 'Manage Voters';
                    break;
                case 'results':
                    title = 'Election Results';
                    break;
                case 'settings':
                    title = 'System Settings';
                    break;
            }
            
            pageTitle.textContent = title;
        }
        
        // Load page-specific data
        function loadPageData(page) {
            switch (page) {
                case 'dashboard':
                    loadDashboardData();
                    break;
                case 'elections':
                    loadElectionsData();
                    break;
                case 'candidates':
                    loadCandidatesData();
                    break;
                case 'voters':
                    loadVotersData();
                    break;
                case 'results':
                    loadResultsData();
                    break;
                case 'settings':
                    loadSettingsData();
                    break;
            }
        }
        
        // Close modal when clicking close button or cancel
        closeModal.addEventListener('click', () => notificationModal.classList.add('hidden'));
        modalCancel.addEventListener('click', () => notificationModal.classList.add('hidden'));
        modalConfirm.addEventListener('click', () => notificationModal.classList.add('hidden'));
        
        // Show notification modal with custom message
        function showNotification(message, onConfirm = null) {
            modalContent.innerHTML = `<p class="text-gray-800 dark:text-gray-200">${message}</p>`;
            notificationModal.classList.remove('hidden');
            
            if (onConfirm) {
                modalConfirm.onclick = () => {
                    onConfirm();
                    notificationModal.classList.add('hidden');
                };
            } else {
                modalConfirm.onclick = () => notificationModal.classList.add('hidden');
            }
        }

        // Setup confirmation modal
        const confirmationModal = document.getElementById('confirmation-modal');
        const closeConfirmation = document.getElementById('close-confirmation');
        const confirmationContent = document.getElementById('confirmation-content');
        const confirmationCancel = document.getElementById('confirmation-cancel');
        const confirmationConfirm = document.getElementById('confirmation-confirm');
        
        closeConfirmation.addEventListener('click', () => confirmationModal.classList.add('hidden'));
        confirmationCancel.addEventListener('click', () => confirmationModal.classList.add('hidden'));
        
        function showConfirmation(message, onConfirm) {
            confirmationContent.innerHTML = `<p class="text-gray-800 dark:text-gray-200">${message}</p>`;
            confirmationModal.classList.remove('hidden');
            
            confirmationConfirm.onclick = () => {
                onConfirm();
                confirmationModal.classList.add('hidden');
            };
        }
        
        // Create charts using Chart.js
        function createCharts() {
            // Voting Activity Chart
            const votingActivityCtx = document.getElementById('votingActivityChart').getContext('2d');
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#d1d5db' : '#4b5563';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            
            const votingActivityData = mockAPI.getVotingActivityData();
            
            const votingActivityChart = new Chart(votingActivityCtx, {
                type: 'line',
                data: {
                    labels: votingActivityData.labels,
                    datasets: [{
                        label: 'Votes per Hour',
                        data: votingActivityData.data,
                        borderColor: '#5D5CDE',
                        backgroundColor: 'rgba(93, 92, 222, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: textColor
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor
                            }
                        },
                        x: {
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor
                            }
                        }
                    }
                }
            });
            
            // Voter Distribution Chart
            const voterDistributionCtx = document.getElementById('voterDistributionChart').getContext('2d');
            
            const voterDistribution = mockAPI.getVoterDistribution();
            const distributionLabels = voterDistribution.map(item => item.type);
            const distributionData = voterDistribution.map(item => item.count);
            
            const voterDistributionChart = new Chart(voterDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: distributionLabels,
                    datasets: [{
                        data: distributionData,
                        backgroundColor: [
                            '#5D5CDE',
                            '#10B981',
                            '#F59E0B',
                            '#3B82F6'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
            
            // Store charts in window object for later access
            window.appCharts = {
                votingActivityChart,
                voterDistributionChart
            };
        }
        
        // Update charts when theme changes
        function updateChartsForTheme() {
            if (!window.appCharts) return;
            
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#d1d5db' : '#4b5563';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            
            // Update Voting Activity Chart
            window.appCharts.votingActivityChart.options.scales.y.grid.color = gridColor;
            window.appCharts.votingActivityChart.options.scales.x.grid.color = gridColor;
            window.appCharts.votingActivityChart.options.scales.y.ticks.color = textColor;
            window.appCharts.votingActivityChart.options.scales.x.ticks.color = textColor;
            window.appCharts.votingActivityChart.options.plugins.legend.labels.color = textColor;
            window.appCharts.votingActivityChart.update();
            
            // Update Voter Distribution Chart
            window.appCharts.voterDistributionChart.options.plugins.legend.labels.color = textColor;
            window.appCharts.voterDistributionChart.update();
        }
        
        // Load dashboard data
        function loadDashboardData() {
            // Get statistics
            const stats = mockAPI.getStats();
            
            // Update stat cards
            document.getElementById('active-elections-count').textContent = stats.activeElections;
            document.getElementById('total-voters-count').textContent = stats.totalVoters.toLocaleString();
            document.getElementById('votes-cast-count').textContent = stats.votedVoters.toLocaleString();
            document.getElementById('voter-turnout').textContent = `${stats.voterTurnout}%`;
            
            // Load active elections table
            const activeElections = mockAPI.getElections('active');
            const activeElectionsTable = document.getElementById('active-elections-table');
            
            let activeElectionsHTML = '';
            activeElections.forEach(election => {
                activeElectionsHTML += `
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-medium">${election.title}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Active
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${election.endDate}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <button class="text-primary hover:text-primary-dark mr-2" onclick="viewElection(${election.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="text-info hover:text-blue-700 mr-2" onclick="editElection(${election.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-danger hover:text-red-700" onclick="deleteElection(${election.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
            });
            
            activeElectionsTable.innerHTML = activeElectionsHTML;
            
            // Load recent activities
            const activities = mockAPI.getActivities(4);
            const recentActivityList = document.getElementById('recent-activity-list');
            
            let activitiesHTML = '';
            activities.forEach(activity => {
                let iconClass = '';
                let bgClass = '';
                
                switch (activity.type) {
                    case 'register':
                        iconClass = 'fas fa-user-plus';
                        bgClass = 'bg-blue-100 dark:bg-blue-900 text-blue-500';
                        break;
                    case 'vote':
                        iconClass = 'fas fa-vote-yea';
                        bgClass = 'bg-green-100 dark:bg-green-900 text-green-500';
                        break;
                    case 'alert':
                        iconClass = 'fas fa-exclamation-triangle';
                        bgClass = 'bg-yellow-100 dark:bg-yellow-900 text-yellow-500';
                        break;
                    case 'delete':
                        iconClass = 'fas fa-trash';
                        bgClass = 'bg-red-100 dark:bg-red-900 text-red-500';
                        break;
                    case 'edit':
                        iconClass = 'fas fa-edit';
                        bgClass = 'bg-purple-100 dark:bg-purple-900 text-purple-500';
                        break;
                    case 'add':
                        iconClass = 'fas fa-plus';
                        bgClass = 'bg-indigo-100 dark:bg-indigo-900 text-indigo-500';
                        break;
                    default:
                        iconClass = 'fas fa-info-circle';
                        bgClass = 'bg-gray-100 dark:bg-gray-700 text-gray-500';
                }
                
                activitiesHTML += `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full ${bgClass} flex items-center justify-center">
                            <i class="${iconClass}"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium">${activity.type.charAt(0).toUpperCase() + activity.type.slice(1)}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${activity.description}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">${activity.time}</p>
                    </div>
                </div>
                `;
            });
            
            recentActivityList.innerHTML = activitiesHTML;
        }
        
        // Load elections data
        function loadElectionsData() {
            const elections = mockAPI.getElections();
            const electionsTable = document.getElementById('elections-table');
            
            let electionsHTML = '';
            elections.forEach(election => {
                // Determine status badge style
                let statusBadgeClass = '';
                switch (election.status) {
                    case 'active':
                        statusBadgeClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                        break;
                    case 'upcoming':
                        statusBadgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                        break;
                    case 'completed':
                        statusBadgeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        break;
                }
                
                electionsHTML += `
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${election.id}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-medium">${election.title}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${election.description.substring(0, 40)}${election.description.length > 40 ? '...' : ''}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${election.startDate}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${election.endDate}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full ${statusBadgeClass}">
                            ${election.status.charAt(0).toUpperCase() + election.status.slice(1)}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${election.candidateCount}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${election.votesCount} / ${election.voterCount}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <button class="text-primary hover:text-primary-dark mr-2" onclick="viewElection(${election.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="text-info hover:text-blue-700 mr-2" onclick="editElection(${election.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-danger hover:text-red-700" onclick="deleteElection(${election.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
            });
            
            electionsTable.innerHTML = electionsHTML;
            
            // Update pagination info
            document.getElementById('elections-showing').textContent = `1-${elections.length}`;
            document.getElementById('elections-total').textContent = elections.length;
            
            // Update election dropdowns on other pages
            updateElectionDropdowns();
        }
        
        // Load candidates data
        function loadCandidatesData() {
            const candidates = mockAPI.getCandidates();
            const candidatesGrid = document.getElementById('candidates-grid');
            const candidatesEmpty = document.getElementById('candidates-empty');
            
            // First update election filter dropdown
            const electionFilter = document.getElementById('candidate-election-filter');
            
            const elections = mockAPI.getElections();
            let electionOptions = '<option value="">All Elections</option>';
            elections.forEach(election => {
                electionOptions += `<option value="${election.id}">${election.title}</option>`;
            });
            electionFilter.innerHTML = electionOptions;
            
            if (candidates.length === 0) {
                candidatesGrid.innerHTML = '';
                candidatesEmpty.classList.remove('hidden');
            } else {
                candidatesEmpty.classList.add('hidden');
                
                let candidatesHTML = '';
                candidates.forEach(candidate => {
                    const election = mockAPI.getElectionById(candidate.election_id);
                    
                    candidatesHTML += `
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden">
                        <div class="p-4 text-center">
                            <div class="w-20 h-20 rounded-full bg-gray-200 dark:bg-gray-600 mx-auto mb-3 flex items-center justify-center">
                                ${candidate.photo ? 
                                    `<img src="${candidate.photo}" alt="${candidate.name}" class="w-full h-full rounded-full object-cover">` : 
                                    `<i class="fas fa-user-tie text-3xl text-gray-400 dark:text-gray-500"></i>`
                                }
                            </div>
                            <h3 class="text-lg font-medium">${candidate.name}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">${candidate.position}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">${election ? election.title : 'Unknown Election'}</p>
                            <div class="mt-2 px-3 py-1 bg-primary bg-opacity-10 text-primary text-sm rounded-full inline-block">
                                Votes: ${candidate.votes}
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-600 flex justify-between">
                            <button class="text-gray-600 dark:text-gray-300 hover:text-primary" onclick="editCandidate(${candidate.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-gray-600 dark:text-gray-300 hover:text-primary" onclick="viewCandidate(${candidate.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-gray-600 dark:text-gray-300 hover:text-danger" onclick="deleteCandidate(${candidate.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    `;
                });
                
                candidatesGrid.innerHTML = candidatesHTML;
            }
        }
        
        // Load voters data
        function loadVotersData() {
            const voters = mockAPI.getVoters();
            const votersTable = document.getElementById('voters-table');
            
            let votersHTML = '';
            voters.forEach(voter => {
                const statusClass = voter.has_voted ? 
                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                
                const statusText = voter.has_voted ? 'Voted' : 'Not Voted';
                
                votersHTML += `
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${voter.id}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">${voter.name}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${voter.email}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${voter.type}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">${voter.registered_date}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full ${statusClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <button class="text-primary hover:text-primary-dark mr-2" onclick="viewVoter(${voter.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="text-info hover:text-blue-700 mr-2" onclick="editVoter(${voter.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-danger hover:text-red-700" onclick="deleteVoter(${voter.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
            });
            
            votersTable.innerHTML = votersHTML;
            
            // Update pagination info
            document.getElementById('voters-showing').textContent = `1-${voters.length}`;
            document.getElementById('voters-total').textContent = voters.length;
        }
        
        // Load results data
        function loadResultsData() {
            // Populate election dropdown
            const resultElectionSelect = document.getElementById('results-election-select');
            const elections = mockAPI.getElections();
            
            let electionOptions = '<option value="">Choose an election...</option>';
            elections.forEach(election => {
                electionOptions += `<option value="${election.id}">${election.title}</option>`;
            });
            
            resultElectionSelect.innerHTML = electionOptions;
            
            // Add change event listener
            resultElectionSelect.addEventListener('change', function() {
                const electionId = this.value;
                
                if (electionId) {
                    // Show results dashboard and hide prompt
                    document.getElementById('results-dashboard').classList.remove('hidden');
                    document.getElementById('select-election-prompt').classList.add('hidden');
                    document.getElementById('export-results-btn').removeAttribute('disabled');
                    
                    // Load election results
                    loadElectionResults(electionId);
                } else {
                    // Show prompt and hide dashboard
                    document.getElementById('results-dashboard').classList.add('hidden');
                    document.getElementById('select-election-prompt').classList.remove('hidden');
                    document.getElementById('export-results-btn').setAttribute('disabled', '');
                }
            });
        }
        
        // Load election results
        function loadElectionResults(electionId) {
            const election = mockAPI.getElectionById(electionId);
            const candidates = mockAPI.getCandidates(electionId);
            
            // Update summary
            document.getElementById('result-turnout-percent').textContent = `${Math.round((election.votesCount / election.voterCount) * 100)}%`;
            document.getElementById('result-votes-cast').textContent = election.votesCount;
            document.getElementById('result-total-voters').textContent = election.voterCount;
            document.getElementById('result-candidate-count').textContent = candidates.length;
            
            // Get unique positions
            const positions = [...new Set(candidates.map(c => c.position))];
            document.getElementById('result-position-count').textContent = positions.length;
            
            // Election status
            const now = new Date();
            const endDate = new Date(election.endDate);
            const statusIndicator = document.getElementById('election-status-indicator');
            
            if (election.status === 'completed' || endDate < now) {
                statusIndicator.innerHTML = `
                <div class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md">
                    <span class="text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Election Completed
                    </span>
                </div>`;
            } else {
                const diffTime = Math.abs(endDate - now);
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                const diffHours = Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                
                statusIndicator.innerHTML = `
                <div class="px-3 py-2 bg-blue-100 dark:bg-blue-900 rounded-md">
                    <span class="text-blue-700 dark:text-blue-300">
                        <i class="fas fa-clock text-warning mr-2"></i>
                        ${diffDays} days, ${diffHours} hours remaining
                    </span>
                </div>`;
            }
            
            // Results by position
            const resultsPositions = document.getElementById('results-positions');
            let positionsHTML = '';
            
            positions.forEach(position => {
                const positionCandidates = candidates.filter(c => c.position === position);
                // Sort candidates by votes (descending)
                positionCandidates.sort((a, b) => b.votes - a.votes);
                
                let candidatesHTML = '';
                positionCandidates.forEach((candidate, index) => {
                    const totalVotes = positionCandidates.reduce((sum, c) => sum + c.votes, 0);
                    const percentage = totalVotes > 0 ? Math.round((candidate.votes / totalVotes) * 100) : 0;
                    
                    // Color for winner
                    const isWinner = index === 0 && candidate.votes > 0;
                    const winnerClass = isWinner ? 'bg-green-50 dark:bg-green-900 border-green-200 dark:border-green-800' : '';
                    
                    candidatesHTML += `
                    <div class="p-3 border ${winnerClass} dark:border-gray-700 rounded-md mb-2 flex items-center">
                        <div class="mr-3">
                            ${candidate.photo ? 
                                `<img src="${candidate.photo}" alt="${candidate.name}" class="h-10 w-10 rounded-full">` : 
                                `<div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>`
                            }
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-medium">${candidate.name}</span>
                                <span class="text-sm">${candidate.votes} votes (${percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                        ${isWinner ? '<div class="ml-2 text-success"><i class="fas fa-trophy"></i></div>' : ''}
                    </div>
                    `;
                });
                
                positionsHTML += `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium mb-4">${position}</h3>
                    ${candidatesHTML}
                </div>
                `;
            });
            
            resultsPositions.innerHTML = positionsHTML;
        }
        
        // Load settings data
        function loadSettingsData() {
            const settings = mockAPI.getSettings();
            
            // Populate form
            document.getElementById('site-title').value = settings.siteTitle;
            document.getElementById('voting-hours').value = settings.votingHours;
            document.getElementById('session-timeout').value = settings.sessionTimeout;
            document.getElementById('require-email-verification').checked = settings.requireEmailVerification;
            document.getElementById('allow-guest-access').checked = settings.allowGuestAccess;
            
            // Setup form submission
            const settingsForm = document.getElementById('settings-form');
            settingsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    siteTitle: document.getElementById('site-title').value,
                    votingHours: document.getElementById('voting-hours').value,
                    sessionTimeout: parseInt(document.getElementById('session-timeout').value),
                    requireEmailVerification: document.getElementById('require-email-verification').checked,
                    allowGuestAccess: document.getElementById('allow-guest-access').checked
                };
                
                // Update settings
                mockAPI.updateSettings(formData);
                
                showNotification('Settings updated successfully');
            });
            
            // Setup backup button
            document.getElementById('backup-data-btn').addEventListener('click', function() {
                // Simulate backup
                document.getElementById('last-backup-date').textContent = new Date().toLocaleString();
                showNotification('Data backup completed successfully');
            });
            
            // Setup clear cache button
            document.getElementById('clear-cache-btn').addEventListener('click', function() {
                showNotification('Cache cleared successfully');
            });
            
            // Setup reset settings button
            document.getElementById('reset-settings-btn').addEventListener('click', function() {
                showConfirmation('This will reset all settings to default values. Are you sure?', function() {
                    // Reset to default values
                    document.getElementById('site-title').value = 'E-Voting Administration';
                    document.getElementById('voting-hours').value = '6:00 AM - 8:00 PM';
                    document.getElementById('session-timeout').value = '30';
                    document.getElementById('require-email-verification').checked = true;
                    document.getElementById('allow-guest-access').checked = false;
                    
                    showNotification('Settings reset to default values');
                });
            });
        }
        
        // Update election dropdowns across the app
        function updateElectionDropdowns() {
            const elections = mockAPI.getElections();
            
            // For candidate form
            const candidateElection = document.getElementById('candidate-election');
            if (candidateElection) {
                let options = '<option value="">Select Election</option>';
                elections.forEach(election => {
                    options += `<option value="${election.id}">${election.title}</option>`;
                });
                candidateElection.innerHTML = options;
            }
            
            // For results page
            const resultElectionSelect = document.getElementById('results-election-select');
            if (resultElectionSelect) {
                let options = '<option value="">Choose an election...</option>';
                elections.forEach(election => {
                    options += `<option value="${election.id}">${election.title}</option>`;
                });
                resultElectionSelect.innerHTML = options;
            }
        }
        
        // CRUD Operations for Elections
        
        // View election details
        function viewElection(id) {
            const election = mockAPI.getElectionById(id);
            if (!election) return;
            
            showNotification(`
                <h4 class="font-bold mb-2">${election.title}</h4>
                <p class="mb-1">${election.description}</p>
                <p class="mb-1"><strong>Period:</strong> ${election.startDate} - ${election.endDate}</p>
                <p class="mb-1"><strong>Status:</strong> ${election.status}</p>
                <p class="mb-1"><strong>Candidates:</strong> ${election.candidateCount}</p>
                <p><strong>Voter Turnout:</strong> ${election.votesCount}/${election.voterCount} (${Math.round((election.votesCount / election.voterCount) * 100)}%)</p>
            `);
        }
        
        // Edit election
        function editElection(id) {
            const election = mockAPI.getElectionById(id);
            if (!election) return;
            
            // Populate form
            document.getElementById('election-id').value = election.id;
            document.getElementById('election-title').value = election.title;
            document.getElementById('election-description').value = election.description;
            document.getElementById('election-start-date').value = election.startDate;
            document.getElementById('election-end-date').value = election.endDate;
            document.getElementById('election-status').value = election.status;
            
            // Update modal title
            document.getElementById('election-modal-title').textContent = 'Edit Election';
            
            // Show modal
            document.getElementById('election-modal').classList.remove('hidden');
        }
        
        // Delete election
        function deleteElection(id) {
            const election = mockAPI.getElectionById(id);
            if (!election) return;
            
            showConfirmation(`Are you sure you want to delete the election "${election.title}"?`, function() {
                mockAPI.deleteElection(id);
                
                // Reload current page data
                loadPageData(currentPage);
                
                showNotification('Election deleted successfully');
            });
        }
        
        // CRUD Operations for Candidates
        
        // View candidate details
        function viewCandidate(id) {
            const candidate = mockAPI.getCandidateById(id);
            if (!candidate) return;
            
            const election = mockAPI.getElectionById(candidate.election_id);
            
            showNotification(`
                <h4 class="font-bold mb-2">${candidate.name}</h4>
                <p class="mb-1"><strong>Position:</strong> ${candidate.position}</p>
                <p class="mb-1"><strong>Election:</strong> ${election ? election.title : 'Unknown'}</p>
                <p><strong>Votes:</strong> ${candidate.votes}</p>
            `);
        }
        
        // Edit candidate
        function editCandidate(id) {
            const candidate = mockAPI.getCandidateById(id);
            if (!candidate) return;
            
            // Populate form
            document.getElementById('candidate-id').value = candidate.id;
            document.getElementById('candidate-name').value = candidate.name;
            document.getElementById('candidate-position').value = candidate.position;
            document.getElementById('candidate-election').value = candidate.election_id;
            
            // Update modal title
            document.getElementById('candidate-modal-title').textContent = 'Edit Candidate';
            
            // Show modal
            document.getElementById('candidate-modal').classList.remove('hidden');
        }
        
        // Delete candidate
        function deleteCandidate(id) {
            const candidate = mockAPI.getCandidateById(id);
            if (!candidate) return;
            
            showConfirmation(`Are you sure you want to delete the candidate "${candidate.name}"?`, function() {
                mockAPI.deleteCandidate(id);
                
                // Reload current page data
                loadPageData(currentPage);
                
                showNotification('Candidate deleted successfully');
            });
        }
        
        // CRUD Operations for Voters
        
        // View voter details
        function viewVoter(id) {
            const voter = mockAPI.getVoterById(id);
            if (!voter) return;
            
            showNotification(`
                <h4 class="font-bold mb-2">${voter.name}</h4>
                <p class="mb-1"><strong>Email:</strong> ${voter.email}</p>
                <p class="mb-1"><strong>Type:</strong> ${voter.type}</p>
                <p class="mb-1"><strong>Registered:</strong> ${voter.registered_date}</p>
                <p><strong>Status:</strong> ${voter.has_voted ? 'Has voted' : 'Has not voted'}</p>
            `);
        }
        
        // Edit voter
        function editVoter(id) {
            const voter = mockAPI.getVoterById(id);
            if (!voter) return;
            
            // Populate form
            document.getElementById('voter-id').value = voter.id;
            document.getElementById('voter-name').value = voter.name;
            document.getElementById('voter-email').value = voter.email;
            document.getElementById('voter-type').value = voter.type;
            document.getElementById('voter-has-voted').checked = voter.has_voted;
            
            // Update modal title
            document.getElementById('voter-modal-title').textContent = 'Edit Voter';
            
            // Show modal
            document.getElementById('voter-modal').classList.remove('hidden');
        }
        
        // Delete voter
        function deleteVoter(id) {
            const voter = mockAPI.getVoterById(id);
            if (!voter) return;
            
            showConfirmation(`Are you sure you want to delete the voter "${voter.name}"?`, function() {
                mockAPI.deleteVoter(id);
                
                // Reload current page data
                loadPageData(currentPage);
                
                showNotification('Voter deleted successfully');
            });
        }
        
        // Setup Form Handling
        
        // Elections form
        document.getElementById('create-election-btn').addEventListener('click', function() {
            // Clear form
            document.getElementById('election-id').value = '';
            document.getElementById('election-title').value = '';
            document.getElementById('election-description').value = '';
            document.getElementById('election-start-date').value = '';
            document.getElementById('election-end-date').value = '';
            document.getElementById('election-status').value = 'upcoming';
            
            // Update modal title
            document.getElementById('election-modal-title').textContent = 'Create Election';
            
            // Show modal
            document.getElementById('election-modal').classList.remove('hidden');
        });
        
        document.getElementById('elections-create-btn').addEventListener('click', function() {
            // Clear form
            document.getElementById('election-id').value = '';
            document.getElementById('election-title').value = '';
            document.getElementById('election-description').value = '';
            document.getElementById('election-start-date').value = '';
            document.getElementById('election-end-date').value = '';
            document.getElementById('election-status').value = 'upcoming';
            
            // Update modal title
            document.getElementById('election-modal-title').textContent = 'Create Election';
            
            // Show modal
            document.getElementById('election-modal').classList.remove('hidden');
        });
        
        document.getElementById('election-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                title: document.getElementById('election-title').value,
                description: document.getElementById('election-description').value,
                startDate: document.getElementById('election-start-date').value,
                endDate: document.getElementById('election-end-date').value,
                status: document.getElementById('election-status').value,
                candidateCount: 0,
                voterCount: 0,
                votesCount: 0
            };
            
            const electionId = document.getElementById('election-id').value;
            
            if (electionId) {
                // Update existing election
                mockAPI.updateElection(electionId, formData);
                showNotification('Election updated successfully');
            } else {
                // Create new election
                mockAPI.createElection(formData);
                showNotification('Election created successfully');
            }
            
            // Close modal
            document.getElementById('election-modal').classList.add('hidden');
            
            // Reload elections data
            loadPageData(currentPage);
        });
        
        // Candidates form
        document.getElementById('candidates-create-btn').addEventListener('click', function() {
            // Clear form
            document.getElementById('candidate-id').value = '';
            document.getElementById('candidate-name').value = '';
            document.getElementById('candidate-position').value = '';
            document.getElementById('candidate-election').value = '';
            
            // Update modal title
            document.getElementById('candidate-modal-title').textContent = 'Add Candidate';
            
            // Show modal
            document.getElementById('candidate-modal').classList.remove('hidden');
        });
        
        document.getElementById('candidate-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('candidate-name').value,
                position: document.getElementById('candidate-position').value,
                election_id: parseInt(document.getElementById('candidate-election').value),
                photo: null
            };
            
            const candidateId = document.getElementById('candidate-id').value;
            
            if (candidateId) {
                // Update existing candidate
                mockAPI.updateCandidate(candidateId, formData);
                showNotification('Candidate updated successfully');
            } else {
                // Create new candidate
                mockAPI.createCandidate(formData);
                showNotification('Candidate added successfully');
            }
            
            // Close modal
            document.getElementById('candidate-modal').classList.add('hidden');
            
            // Reload candidates data
            loadPageData(currentPage);
        });
        
        // Voters form
        document.getElementById('voters-create-btn').addEventListener('click', function() {
            // Clear form
            document.getElementById('voter-id').value = '';
            document.getElementById('voter-name').value = '';
            document.getElementById('voter-email').value = '';
            document.getElementById('voter-type').value = 'Student';
            document.getElementById('voter-has-voted').checked = false;
            
            // Update modal title
            document.getElementById('voter-modal-title').textContent = 'Add Voter';
            
            // Show modal
            document.getElementById('voter-modal').classList.remove('hidden');
        });
        
        document.getElementById('voter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('voter-name').value,
                email: document.getElementById('voter-email').value,
                type: document.getElementById('voter-type').value,
                has_voted: document.getElementById('voter-has-voted').checked
            };
            
            const voterId = document.getElementById('voter-id').value;
            
            if (voterId) {
                // Update existing voter
                mockAPI.updateVoter(voterId, formData);
                showNotification('Voter updated successfully');
            } else {
                // Create new voter
                mockAPI.createVoter(formData);
                showNotification('Voter added successfully');
            }
            
            // Close modal
            document.getElementById('voter-modal').classList.add('hidden');
            
            // Reload voters data
            loadPageData(currentPage);
        });
        
        // Setup modal close buttons
        document.querySelectorAll('.modal-close, .modal-cancel').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#election-modal, #candidate-modal, #voter-modal').forEach(modal => {
                    modal.classList.add('hidden');
                });
            });
        });
        
        // Setup notifications button
        document.getElementById('notification-button').addEventListener('click', function() {
            const activities = mockAPI.getActivities(5);
            let notificationsHtml = '<ul class="space-y-2">';
            
            activities.forEach(activity => {
                notificationsHtml += `
                <li class="text-sm">
                    <p class="font-medium">${activity.description}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${activity.time}</p>
                </li>`;
            });
            
            notificationsHtml += '</ul>';
            
            showNotification(notificationsHtml);
        });
        
        // Initialize the application when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            // Create charts
            createCharts();
            
            // Load initial dashboard data
            loadDashboardData();
            
            // Setup filter buttons
            document.getElementById('election-filter-btn')?.addEventListener('click', function() {
                loadElectionsData();
            });
            
            document.getElementById('candidate-filter-btn')?.addEventListener('click', function() {
                loadCandidatesData();
            });
            
            document.getElementById('voter-filter-btn')?.addEventListener('click', function() {
                loadVotersData();
            });
            
            // Export results button
            document.getElementById('export-results-btn')?.addEventListener('click', function() {
                showNotification('Results would be exported as CSV or PDF in a real application. This is a demo simulation.');
            });
        });
        
        // Make functions available globally
        window.viewElection = viewElection;
        window.editElection = editElection;
        window.deleteElection = deleteElection;
        window.viewCandidate = viewCandidate;
        window.editCandidate = editCandidate;
        window.deleteCandidate = deleteCandidate;
        window.viewVoter = viewVoter;
        window.editVoter = editVoter;
        window.deleteVoter = deleteVoter;
    </script>
</body>
</html>