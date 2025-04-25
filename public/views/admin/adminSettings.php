        <!DOCTYPE html>

        <html lang="en">
        <?php
        require_once "../../config/rolePermissions.php";
        require_once '../../model/dbconnection.php';
        require_once '../../controller/admin/documentController.php';

        require_once '../../config/sessionCheck.php';

        // checkAccess('edit_documents', ['Guest', 'Resident'], $conn);

        // if (!isset($_SESSION['username'])) {
        //     // Generate a CSRF token if not already set
        //     if (empty($_SESSION['csrf_token'])) {
        //         $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        //     }   

        //     // Redirect to the login page
        //     header('Location: ../login.php');
        //     exit(); // Ensure the script stops executing after redirection
        // }
        ?>

        <head>
            <meta charset="UTF-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Settings</title>
            <script src="https://kit.fontawesome.com/4907458c0c.js" crossorigin="anonymous"></script>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Metrophobic&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="../../css/admin/adminSettings.css">
            <link rel="stylesheet" href="../../css/main.css">   
            <script src="../../js/main.js" defer></script>
            <script src="../../js/dashBoard.js" defer></script>
            <script src="../../js/settings.js" defer></script>
            <script src="../../js/upload.js" defer></script>
            <script src="../../js/imageModal.js" defer></script>
        </head>
        <style>/* Template Item (exact match to payment with even closer button spacing) */
    .template-item {
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .template-item .template-header {
        margin-bottom: 12px;
    }

    .template-item h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-color);
        margin: 0;
    }

    .template-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin: 12px 0;
    }

    .template-item .document-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 12px;
    }

    .template-item .document-container a {
        padding: 0;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        height: var(--button-height);
        line-height: var(--button-height);
        text-align: center;
        text-decoration: none;
        color: white;
    }

    .template-item .view-document-btn {
        background: var(--accent-color);
    }

    .template-item .download-document-btn {
        background: var(--accent-color);
    }

    .template-item .view-document-btn:hover {
        background: #2563EB;
    }

    .template-item .download-document-btn:hover {
        background: #059669;
    }

    .template-item .btn-container {
        display: flex;
        gap: 2px; /* Reduced from 4px for even closer button spacing */
        justify-content: center;
    }

    .template-item .btn-container button {
        flex: 1;
        padding: 0;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        height: var(--button-height);
        line-height: var(--button-height);
        color: white;
    }

    .template-item .edit-template-btn {
        background: var(--success-color);
    }

    .template-item .delete-template-btn {
        background: var(--danger-color);
    }

    .template-item .edit-template-btn:hover {
        background: #2563EB;
    }

    .template-item .delete-template-btn:hover {
        background: #DC2626;
    }</style>

        <body>
            <main class="main-wrap">
            <header class="main-head active">
                    <div class="main-nav">
                        <button class="hamburger">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <nav class="navbar">
                            <div class="navbar-nav">
                                <div class="title">
                                    <h1>BRGY. 752</h1>
                                </div>
                                <ul class="nav-list">

                                    <!-- HOME -->
                                    <a href="../../index.php" class="nav-link">
                                        <li class="nav-list-item">
                                            <i class="fa-solid fa-house"></i>
                                            <span class="link-text">HOME</span>
                                        </li>
                                    </a>

                                    <!-- DASHBOARD -->
                                    <a href="adminDashboardController.php" class="nav-link">
                                        <li class="nav-list-item">
                                            <i class="fa-solid fa-gauge"></i>
                                            <span class="link-text">DASHBOARD</span>
                                        </li>
                                    </a>


                                    <!-- PEOPLE DROPDOWN -->
                                    <li class="nav-list-item" id="resident-link">
                                        <a href="#" class="nav-link">
                                            <i class="fa-solid fa-user"></i>
                                            <span class="link-text">PEOPLE</span>
                                            <span class="link-text">
                                                <i class="fa-solid fa-caret-down" id="resident-dropdown-logo"></i>
                                            </span>
                                            <ul class="resident-dropdown">
                                            <?php if (!empty($_SESSION['permissions']['can_manage_residents'])) : ?>
                                                <li class="dropdown-item"><a href="adminResidentController.php">Resident</a></li>
                                            <?php endif; ?>
                                            <?php if (!empty($_SESSION['permissions']['can_manage_officials'])) : ?>
                                                <li class="dropdown-item"><a href="adminOfficialController.php">Officials</a></li>
                                            <?php endif; ?>
                                                <li class="dropdown-item"><a href="adminFamilyController.php">Family</a></li>     
                                            </ul>
                                        </a>
                                    </li>

                                    <!-- ACCOUNTS DROPDOWN -->
                                    <a href="#" class="nav-link">
                                        <li class="nav-list-item" id="accounts-link">
                                            <i class="fa-solid fa-user-pen"></i>
                                            <span class="link-text">ACCOUNTS</span><span class="link-text"><i
                                                    class="fa-solid fa-caret-down" id="accounts-dropdown-logo"></i></span>
                                            <ul class="accounts-dropdown">
                                                <?php if (!empty($_SESSION['permissions']['can_manage_residents'])) : ?>
                                                <li class="dropdown-item"><a href="adminResidentAccountsController.php">Resident Accounts</a></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['permissions']['can_manage_officials'])) : ?>
                                                <li class="dropdown-item"><a href="adminOfficialAccountsController.php">Official Accounts</a></li>   
                                                <?php endif; ?>                                 
                                            </ul>
                                        </li>
                                    </a>

                                    <!-- REQUESTS DROPDOWN -->
                                    <a href="#" class="nav-link">
                                        <li class="nav-list-item" id="request-link">
                                            <i class="fa-solid fa-print"></i>
                                            <span class="link-text">REQUEST</span><span class="link-text"><i
                                                    class="fa-solid fa-caret-down" id="request-dropdown-logo"></i></span>
                                            <ul class="request-dropdown">
                                                <?php if (!empty($_SESSION['permissions']['can_process_clearances'])) : ?>
                                                <li class="dropdown-item"><a href="adminClearanceController.php">Clearance</a></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['permissions']['can_process_certificates'])) : ?>
                                                <li class="dropdown-item"><a href="adminCertificateController.php">Certificate</a></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['permissions']['can_process_permits'])) : ?>
                                                <li class="dropdown-item"><a href="adminPermitController.php">Permits</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </li>
                                    </a>

                                <!-- REPORTS DROPDOWN -->
                                <a href="#" class="nav-link">
                                    <li class="nav-list-item" id="report-link">
                                        <i class="fa-solid fa-flag"></i>
                                        <span class="link-text">REPORT</span><span class="link-text"><i
                                                class="fa-solid fa-caret-down" id="report-dropdown-logo"></i></span>
                                        <ul class="report-dropdown">
                                            <?php if (!empty($_SESSION['permissions']['can_manage_blotters'])) : ?>
                                            <li class="dropdown-item"><a href="adminBlotterController.php">Blotters</a></li>
                                            <?php endif; ?>
                                            <?php if (!empty($_SESSION['permissions']['can_manage_complaints'])) : ?>
                                            <li class="dropdown-item"><a href="adminComplaintsController.php">Complaints</a></li>
                                            <?php endif; ?>
                                            <?php if (!empty($_SESSION['permissions']['can_manage_incidents'])) : ?>
                                            <li class="dropdown-item"><a href="adminIncidentController.php">Incident</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </li>
                                </a>

                                <!-- ANNOUNCEMENT -->
                                <?php if (!empty($_SESSION['permissions']['can_create_announcements'])) : ?>
                                <a href="adminAnnouncementController.php" class="nav-link">
                                    <li class="nav-list-item">
                                        <i class="fa-solid fa-bullhorn"></i>
                                        <span class="link-text">ANNOUNCEMENT</span>
                                    </li>
                                </a>
                                <?php endif; ?>

                                <!-- ACTIVITY LOG -->
                                <?php if (!empty($_SESSION['permissions']['can_view_activity_log'])) : ?>
                                <a href="adminActivityLogController.php" class="nav-link">
                                    <li class="nav-list-item">
                                        <i class="fa-regular fa-clock"></i>
                                        <span class="link-text">ACTIVITY LOG</span>
                                    </li>
                                </a>
                                <?php endif; ?>
                            </ul>

                            <!-- SETTINGS -->
                        <div class="footer-nav-bar"></div>  
                            <ul class="nav-list">
                                <li class="nav-list-item">
                                        <a href="/Barangay752ManagementSystem/controller/admin/adminSettingsController.php" class="nav-link">
                                        <i class="fa-solid fa-gear"></i>
                                        <span class="link-text">SETTINGS</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </header>
            <section class="showcase">
                <div class="overlay">
                    <nav class="header-nav">
                        <div class="datetime">
                            <div class="day">DAY</div>
                            <div class="date">Date</div>
                            <div class="time">Time</div>
                        </div>
                        <div class="head">
                            <button class="account">
                                <p>
                                    <i class="fa-solid fa-user"></i> &nbsp;&nbsp;&nbsp;
                                    <?php
                                    if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
                                        echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']);
                                    } else {
                                        echo "Guest!";
                                    } ?>
                                </p>
                            </button>
                            <div class="floating-menu">
                                <p>You're logged in as</p>
                                <?php echo '<h3>' . ($_SESSION['username']) . '</h3>' ?>
                                <a href="index.php">View Profile</a>
                                <a href="../../controller/logoutController.php">Logout</a>
                            </div>
                        </div>
                    </nav>
                </div>
                <div class="content" id="settings-content">
                    <div class="settings-div">
                        <div class="settings-nav">
                            <h3><u>Template Settings</u></h3>
                            <button onclick="showSection('certificates')">
                                <p>Certificate Templates</p>
                            </button>
                            <button onclick="showSection('permits')">
                                <p>Permit Templates</p>
                            </button>
                            <button onclick="showSection('clearances')">
                                <p>Clearance Templates</p>
                            </button>
                            <button onclick="showSection('payment')">
                                <p>Payment Settings</p>
                            </button>
                            <h3><u>Admin Controls</u></h3>
                            <button onclick="showSection('permissions')">
                                <p>Roles and Permissions</p>
                            </button>
                        </div>

                        <div class="settings-main-content">
                            <?php
                            $defaultSections = ['Certificates', 'Permits', 'Clearances'];
                            foreach ($defaultSections as $section) {
                                $lowerSection = strtolower($section);
                                if (!isset($templatesByType[$section])) {
                                    $templatesByType[$section] = [];
                                }
                            }
                            ?>
                            <?php foreach ($templatesByType as $table => $templates): ?>
                                <div id="<?= strtolower(htmlspecialchars($table)) ?>" class="section">
                                    <div class="template-header">
                                        <h3><?= ucfirst($table) ?> Templates</h3>
                                        <button class="add-template-btn" data-template-type="<?= htmlspecialchars($table) ?>">+ Add Template</button>
                                    </div>
                                    <div class="template-container">
                                        <?php if (!empty($templates)): ?>
                                            <?php foreach ($templates as $certificateId => $templateData): ?>
                                                <div class="template-item" id="template-<?= htmlspecialchars($certificateId) ?>">
                                                    <p id="template-name-<?= htmlspecialchars($certificateId) ?>">
                                                        <?= htmlspecialchars($templateData['doc_name']) ?>
                                                    </p>
                                                    <img src="<?= htmlspecialchars($templateData['image']) ?>" class="template-img" onerror="this.src='../../img/uploads/image.png'">
                                                    <div class="btn-container">
                                                        <button class="edit-template-btn" 
                                                                data-template-id="<?= htmlspecialchars($certificateId) ?>" 
                                                                data-template-name="<?= htmlspecialchars($templateData['doc_name']) ?>"
                                                                data-template-type="<?= htmlspecialchars($table) ?>"
                                                                data-price-enabled="<?= $templateData['price_enabled'] ?>"
                                                                data-price="<?= htmlspecialchars($templateData['price']) ?>">
                                                            Edit    
                                                        </button>
                                                        <button class="delete-template-btn" data-template-id="<?= htmlspecialchars($certificateId) ?>">Delete</button>
                                                    </div>
                                                    <div class="document-container">
                                                        <?php if (!empty($templateData['document'])): ?>
                                                            <a href="<?= htmlspecialchars($templateData['document']) ?>" target="_blank" class="view-document-btn">View</a>
                                                            <a href="<?= htmlspecialchars($templateData['document']) ?>" download class="download-document-btn">Download</a>
                                                        <?php else: ?>
                                                            <p class="no-document">No template added yet</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="no-templates">No templates available. Click "Add Template" to create one.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div id="editModal" class="modal">
                                <div class="modal-content">
                                    <span class="close" id="cancel-edit-btn">×</span>
                                    <h2>Edit Template</h2>
                                    
                                    <form id="editTemplateForm" enctype="multipart/form-data" method="POST">
                                        <input type="hidden" name="templateId" id="edit-template-id">

                                        <label for="edit-template-name">Template Name:</label>
                                        <input type="text" name="templateName" id="edit-template-name" required>

                                        <label for="edit-template-file">Upload Document:</label>
                                        <input type="file" name="templateFile" id="edit-template-file" accept=".pdf">

                                        <div class="price-container">
                                            <input type="checkbox" id="edit-price-enabled" name="price_enabled" value="1">
                                            <label for="edit-price-enabled">Enable Price</label>
                                        </div>
                                        <div class="input-price" id="edit-price-input">
                                            <label for="edit-price">Price:</label>
                                            <input type="number" id="edit-price" name="price" min="0" step="0.01" required>
                                        </div>
\
                                        <button type="submit" id="save-edit-btn">Save Changes</button>
                                    </form>
                                </div>
                            </div>

                            <div id="addTemplateModal" class="modal">
                                <div class="modal-content">
                                    <span class="close">&times;</span>
                                    <h2>Add New Template</h2>
                                    <form id="addTemplateForm" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="templateType" id="modalTemplateType">
                                        <label for="templateName">Template Name:</label>
                                        <input type="text" name="templateName" id="templateName" required>
                                        <input type="hidden" name="templateId" value="<?= htmlspecialchars($certificateId) ?>">
                                        <button type="submit">Add Template</button>
                                    </form>
                                </div>
                            </div>
                            <div id="payment" class="section">
                                <div class="template-header">
                                    <h3>Online Payment Settings</h3>
                                    <button id="add-payment-btn">+ Add Payment Type</button>
                                </div>
                                <div class="template-container">
                                <?php if (!empty($paymentData)): ?>
                                    <?php foreach ($paymentData as $paymentId => $payment): ?>
                                        <div class="payment">
                                            <div class="payment-header">
                                                <h3><?= htmlspecialchars($payment['name']) ?></h3>
                                                <img id="qr-image-<?= $paymentId ?>" src="<?= htmlspecialchars($payment['qr_photo']) ?>" onerror="this.src='/Barangay752ManagementSystem/img/uploads/image.png'" alt="QR Code Image" />
                                            </div>
                                            <form id="payment-form-<?= $paymentId ?>" class="payment-form" enctype="multipart/form-data">
                                                <input type="hidden" name="paymentId" value="<?= $paymentId ?>">
                                                <label>Account Name</label>
                                                <input type="text" name="account_name" value="<?= htmlspecialchars($payment['account_name']) ?>" required>
                                                <label>Upload QR Code</label>
                                                <input type="file" name="payment_image" accept="image/*">
                                                <div id="status-message-<?= $paymentId ?>"></div>
                                                <div style="display: flex; flex-direction:row;gap:5px" class="btn-container ">
                                                    <button type="submit" class="update-btn">Update</button>
                                                    <button  type="submit" class="remove-qr-btn">Remove QR</button>
                                                    <button class="delete-payment-btn" data-payment-id="<?= $paymentId ?>">Delete</button>
                                                </div>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="no-templates">No payment types available. Click "Add Payment Type" to create one.</p>
                                <?php endif; ?>
                                    
                                    <div id="add-payment-modal" style="display:none;">
                                        <div class="modal-content">
                                            <span id="close-modal-btn">&times;</span>
                                            <h2>Add Payment Type</h2>
                                            <form id="add-payment-form">
                                                <label for="payment-name">Payment Type Name:</label>
                                                <input type="text" id="payment-name" name="payment-name" required>
                                                <button type="submit">Add</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="permissions" class="section">
                                <div class="template-header">
                                    <h3>Roles and Permissions</h3>
                                </div>
                                <div class="settings-nav">
                                    <?php foreach ($roles as $role): ?>
                                        <button onclick="showPermission('<?= htmlspecialchars($role) ?>')">
                                            <p><?= htmlspecialchars($role) ?></p>
                                        </button>
                                    <?php endforeach; ?>
                                    </div>
                                        <div>
                                            <?php foreach ($roles as $role): ?>
                                                <div class="permission" id="<?= htmlspecialchars($role) ?>" style="display: none;">
                                                    <h3><?= htmlspecialchars($role) ?> Permissions</h3>
                                                    <?php foreach ($permissions as $key => $label): ?>
                                                        <label>
                                                            <input type="checkbox"
                                                                class="permission-checkbox"
                                                                data-role="<?= htmlspecialchars($role) ?>"
                                                                data-permission="<?= htmlspecialchars($key) ?>"
                                                                <?= !empty($permissions_data[$role][$key]) && $permissions_data[$role][$key] == 1 ? 'checked' : '' ?>>
                                                            <?= htmlspecialchars($label) ?>
                                                        </label><br>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                </div>
                            </div>
                        </div>
            </section>
            <div id="imageModal" class="modal">
                <span class="close">&times;</span>
                <img class="modal-content" id="fullImage">
            </div>
        </main>
    </body>

    </html>