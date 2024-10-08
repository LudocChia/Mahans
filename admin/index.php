<?php

include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
function getTotalParentsAndStudents($pdo)
{
    $sqlParents = "SELECT COUNT(*) as total_parents FROM Parent WHERE status = 0";
    $stmtParents = $pdo->query($sqlParents);
    $totalParents = $stmtParents->fetch(PDO::FETCH_ASSOC)['total_parents'];

    $sqlStudents = "SELECT COUNT(*) as total_students FROM Student WHERE status = 0";
    $stmtStudents = $pdo->query($sqlStudents);
    $totalStudents = $stmtStudents->fetch(PDO::FETCH_ASSOC)['total_students'];

    return $totalParents + $totalStudents;
}

$totalParentsAndStudents = getTotalParentsAndStudents($pdo);

function getTotalAdmins($pdo)
{
    $sqlAdmins = "SELECT COUNT(*) as total_admins FROM Admin WHERE status = 0";
    $stmtAdmins = $pdo->query($sqlAdmins);
    return $stmtAdmins->fetch(PDO::FETCH_ASSOC)['total_admins'];
}

$totalAdmins = getTotalAdmins($pdo);

function getTotalCashInAmount($pdo)
{
    $sqlCashIn = "SELECT SUM(payment_amount) as total_cash_in FROM Payment WHERE payment_status = 'completed'";
    $stmtCashIn = $pdo->query($sqlCashIn);
    return $stmtCashIn->fetch(PDO::FETCH_ASSOC)['total_cash_in'];
}

$totalCashIn = getTotalCashInAmount($pdo);

function getRecentOrders($pdo, $limit = 5)
{
    $sqlRecentOrders = "SELECT 
                            o.order_id, 
                            p.parent_name, 
                            py.payment_amount AS total_price,
                            py.payment_status
                        FROM 
                            Orders o
                        JOIN 
                            Parent p ON o.parent_id = p.parent_id
                        JOIN 
                            Payment py ON o.order_id = py.order_id
                        ORDER BY 
                            o.order_datetime DESC
                        LIMIT :limit";

    $stmtRecentOrders = $pdo->prepare($sqlRecentOrders);
    $stmtRecentOrders->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmtRecentOrders->execute();
    return $stmtRecentOrders->fetchAll(PDO::FETCH_ASSOC);
}

$recentOrders = getRecentOrders($pdo, 5);

$pageTitle = "Dashboard - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="two-columns">
            <section class="middle">
                <div class="insights">
                    <div class="wrapper">
                        <div class="sales">
                            <i class="bi bi-person-plus"></i>
                            <div class="middle">
                                <div class="left">
                                    <h4>Total Registered Parents and Students</h4>
                                    <h1><?php echo $totalParentsAndStudents; ?></h1>
                                </div>
                                <!-- <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div> -->
                            </div>
                            <small class="text-muted">Last 12 Months</small>
                        </div>
                    </div>
                    <!-- END OF SALES -->
                    <div class="wrapper">
                        <div class="sales">
                            <i class="bi bi-person-gear"></i>
                            <div class="middle">
                                <div class="left">
                                    <h4>Total Registered Admin and Staff</h4>
                                    <h1><?php echo $totalAdmins; ?></h1>
                                </div>
                                <!-- <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div> -->
                            </div>
                            <small class="text-muted">Last 12 Months</small>
                        </div>
                    </div>
                    <!-- END OF EXPENSE -->
                    <div class="wrapper">
                        <div class="sales">
                            <i class="bi bi-credit-card"></i>
                            <div class="middle">
                                <div class="left">
                                    <h4>Total Cash in Amount</h4>
                                    <h1>MYR <?php echo number_format($totalCashIn, 2); ?></h1>
                                </div>
                                <!-- <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div> -->
                            </div>
                            <small class="text-muted">Last 12 Months</small>
                        </div>
                    </div>
                    <!-- END OF SALES -->
                </div>
                <!-- END OF INSIGHTS -->
                <div class="recent-orders">
                    <div class="wrapper">
                        <!-- <div class="recent-orders"> -->
                        <div class="title">
                            <div class="left">
                                <h2>Recent Orders</h2>
                            </div>
                            <div class="right">
                                <a href="/mips/admin/order.php" class="more">View All<i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                        <div class="table-container">
                            <?php if (!empty($recentOrders)) : ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Parent Name</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order) : ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                                <td><?php echo htmlspecialchars($order['parent_name']); ?></td>
                                                <td>MYR <?php echo number_format($order['total_price'], 2); ?></td>
                                                <td><span class="<?php echo $order['payment_status'] == 'completed' ? 'success' : 'pending'; ?>">
                                                        <?php echo ucfirst($order['payment_status']); ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else : ?>
                                <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
            <section class="right">
                <div class="recent-updates">
                    <div class="wrapper">
                        <div class="title">
                            <div class="left">
                                <h2>Recent Updates</h2>
                            </div>
                            <div class="right">
                                <a href="javascript:void(0)" class="more">View All<i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                        </title>
                        <div class="updates">
                            <div class="update">
                                <div class="profile-photo">
                                    <!-- <img src="../uploads/wangbingbing(2).jpg"> -->
                                </div>
                                <div class="message">
                                    <p style="color: white"><b>Admin</b> received a new order</p>
                                    <small style="color: white" class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="update">
                                <div class="profile-photo">
                                    <!-- <img src="../uploads/wangbingbing(2).jpg"> -->
                                </div>
                                <div class="message">
                                    <p style="color: white"><b>Admin</b> received a new order</p>
                                    <small style="color: white" class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="update">
                                <div class="profile-photo">
                                    <!-- <img src="../uploads/wangbingbing(2).jpg"> -->
                                </div>
                                <div class="message">
                                    <p style="color: white"><b>Admin</b> received a new order</p>
                                    <small style="color: white" class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="update">
                                <div class="profile-photo">
                                    <!-- <img src="../uploads/wangbingbing(3).jpg"> -->
                                </div>
                                <div class="message">
                                    <p style="color: white"><b>Admin</b> received a new order</p>
                                    <small style="color: white" class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="update">
                                <div class="profile-photo">
                                    <!-- <img src="../uploads/wangbingbing(4).png"> -->
                                </div>
                                <div class="message">
                                    <p style="color: white"><b>Admin</b> received a new order</p>
                                    <small style="color: white" class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="web-analytics">
                    <div class="wrapper">
                        <div class="title">
                            <div class="left">
                                <h2>Website Analytics</h2>
                            </div>
                        </div>
                        <div class="item">
                            <div class="icon">
                                <span class="material-symbols-outlined">travel_explore</span>
                            </div>
                            <div class="right">
                                <div class="info">
                                    <h4>Page Views</h4>
                                    <small class="text-muted">Last 24 Hours</small>
                                </div>
                                <h5 style="color: white" class="success">+1%</h5>
                                <h4 style="color: white">1</h4>
                            </div>
                        </div>
                        <div class="item">
                            <div class="icon">
                                <span class="material-symbols-outlined">co_present</span>
                            </div>
                            <div class="right">
                                <div class="info">
                                    <h4>Unique Visitors</h4>
                                    <small class="text-muted">Last 24 Hours</small>
                                </div>
                                <h5 style="color: white" class="danger">0%</h5>
                                <h4 style="color: white">1</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'ajax.php?action=get_pending_count',
                type: 'GET',
                success: function(response) {
                    if (parseInt(response) > 0) {
                        $('#pending-order-count').text(response);
                    } else {
                        $('#pending-order-count').hide();
                    }
                },
                error: function() {
                    $('#pending-order-count').hide();
                }
            });
        });
    </script>
</body>

</html>