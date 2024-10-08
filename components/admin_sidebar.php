<?php
function isActive($targetPage, $currentPage)
{
    $targetPath = parse_url($targetPage, PHP_URL_PATH);
    $currentPath = parse_url($currentPage, PHP_URL_PATH);

    return $currentPath === $targetPath ? 'active' : '';
}

?>
<aside>
    <div class="actions">
        <button id="close-btn">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>
    </div>
    <div class="sidebar">
        <ul>
            <li>
                <a href="/mips/admin" class="<?= isActive('/mips/admin/', $currentPage); ?>">
                    <div class="left-content">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <h5>Dashboard</h5>
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="bookshop-btn">
                    <div class="left-content">
                        <i class="bi bi-shop-window"></i>
                        <h5>Bookshop</h5>
                    </div>
                    <i class="bi bi-chevron-down <?= strpos($currentPage, 'mainCategory') !== false || strpos($currentPage, 'subcategory') !== false || strpos($currentPage, 'size') !== false || strpos($currentPage, 'product') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="bookshop-show" style="display: <?= strpos($currentPage, '/mips/admin/bookshop/mainCategory.php') !== false || strpos($currentPage, '/mips/admin/bookshop/subcategory.php') !== false || strpos($currentPage, '/mips/admin/bookshop/size.php') !== false || strpos($currentPage, '/mips/admin/bookshop/') !== false ? 'block' : 'none'; ?>">
                    <li><a href="/mips/admin/bookshop/mainCategory.php" class="<?= isActive('/mips/admin/bookshop/mainCategory.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-tags-fill"></i>
                                <h5>Main Category</h5>
                            </div>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/subcategory.php" class="<?= isActive('/mips/admin/bookshop/subcategory.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-tag-fill"></i>
                                <h5>Subcategory</h5>
                            </div>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/size.php" class="<?= isActive('/mips/admin/bookshop/size.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-aspect-ratio-fill"></i>
                                <h5>Product Size</h5>
                            </div>
                        </a>
                    </li>
                    <li><a href="/mips/admin/bookshop/" class="<?= isActive('/mips/admin/bookshop/', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-box-seam-fill"></i>
                                <h5>All Product</h5>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/mips/admin/order.php" class="<?= isActive('/mips/admin/order.php', $currentPage); ?>">
                    <div class="left-content">
                        <i class="bi bi-receipt"></i>
                        <h5>Order</h5>
                        <span class="count" id="pending-order-count"></span>
                    </div>
                </a>
            </li>
            <li>
                <a href="/mips/admin/grade.php" class="<?= isActive('/mips/admin/grade.php', $currentPage); ?>">
                    <div class="left-content">
                        <i class="bi bi-mortarboard-fill"></i>
                        <h5>Grade</h5>
                    </div>
                </a>
            </li>
            <li>
                <a href="/mips/admin/class.php" class="<?= isActive('/mips/admin/class.php', $currentPage); ?>">
                    <div class="left-content">
                        <i class="bi bi-easel2-fill"></i>
                        <h5>Class</h5>
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="ebook-btn">
                    <div class="left-content">
                        <i class="bi bi-book-fill"></i>
                        <h5>E-Book</h5>
                    </div>
                    <i class="bi bi-chevron-down <?= strpos($currentPage, '/mips/admin/ebook/mainCategory.php') !== false || strpos($currentPage, '/mips/admin/ebook/subcategory.php') !== false || strpos($currentPage, '/mips/admin/ebook/') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="user-show" style="display: <?= strpos($currentPage, '/mips/admin/ebook/mainCategory.php') !== false || strpos($currentPage, '/mips/admin/ebook/subcategory.php') !== false || strpos($currentPage, '/mips/admin/ebook/') !== false ? 'block' : 'none'; ?>">
                    <li><a href="/mips/admin/ebook/mainCategory.php" class="<?= isActive('/mips/admin/ebook/mainCategory.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-tags-fill"></i>
                                <h5>Main Category</h5>
                            </div>
                        </a>
                    </li>
                    <li><a href="/mips/admin/ebook/subcategory.php" class="<?= isActive('/mips/admin/ebook/subcategory.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-tags-fill"></i>
                                <h5>Subcategory</h5>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="/mips/admin/ebook/" class="<?= isActive('/mips/admin/ebook/', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-journal"></i>
                                <h5>All Books</h5>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0);" class="ebook-btn">
                    <div class="left-content">
                        <i class="fa-solid fa-memo"></i>
                        <h5>Application</h5>
                    </div>
                    <i class="bi bi-chevron-down <?= strpos($currentPage, '/mips/admin/application/job.php') !== false || strpos($currentPage, '/mips/admin/ebook/dorm.php') !== false || strpos($currentPage, '/mips/admin/application/') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="user-show" style="display: <?= strpos($currentPage, '/mips/admin/ebook/mainCategory.php') !== false || strpos($currentPage, '/mips/admin/ebook/subcategory.php') !== false || strpos($currentPage, '/mips/admin/ebook/') !== false ? 'block' : 'none'; ?>">
                    <li>
                    <li><a href="/mips/admin/ebook/mainCategory.php" class="<?= isActive('/mips/admin/ebook/mainCategory.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-tags-fill"></i>
                                <h5>Job</h5>
                            </div>
                        </a>
                    </li>
                    <li><a href="/mips/admin/ebook/subcategory.php" class="<?= isActive('/mips/admin/ebook/subcategory.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-tags-fill"></i>
                                <h5>Dorm</h5>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/mips/admin/announcement.php" class="<?= isActive('/mips/admin/announcement.php', $currentPage); ?>">
                    <div class="left-content">
                        <i class="bi bi-megaphone-fill"></i>
                        <h5>Announment</h5>
                    </div>
                </a>
            </li>
            <li>
                <a href="/mips/admin/admin_meal/adminMain.php" class="<?= isActive('/mips/admin/admin_meal/adminMain.php', $currentPage); ?>">
                    <div class="left-content">
                        <i class="fa fa-cutlery" aria-hidden="true"></i>
                        <h5>Meal Donation</h5>
                    </div>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="user-btn">
                    <div class="left-content">
                        <i class="bi bi-person-fill"></i>
                        <h5>User Type</h5>
                    </div>
                    <i class="bi bi-chevron-down <?= strpos($currentPage, '/mips/admin/user/') !== false || strpos($currentPage, '/mips/admin/user/parent.php') !== false || strpos($currentPage, '/mips/admin/user/student.php') !== false ? 'rotate' : ''; ?>"></i>
                </a>
                <ul class="user-show" style="display: <?= strpos($currentPage, '/mips/admin/user/') !== false || strpos($currentPage, '/mips/admin/user/student.php') !== false || strpos($currentPage, 'mips/admin/user/parent.php') !== false ? 'block' : 'none'; ?>">
                    <li>
                        <a href="/mips/admin/user/" class="<?= isActive('/mips/admin/user/', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-person-fill-gear"></i>
                                <h5>All Admin</h5>
                            </div>
                        </a>
                    </li>
                    <!-- <li><a href="/mips/admin/user/teacher.php" class="</?= isActive('teacher.php', $currentPage); ?>"><svg width="20px" data-name="Layer 1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5 11.5H15a1.5 1.5 0 0 0 1.5-1.5h0A1.5 1.5 0 0 0 15 8.5H4.5a3 3 0 0 0-3 3v2a3 3 0 0 0 1.456 2.573" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                                <path d="M7.5 16.5v6H9a1.5 1.5 0 0 0 1.5-1.5v-9.5M7.5 22.5H5A1.5 1.5 0 0 1 4.5 21v-9.5" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                                <circle cx="7.5" cy="4.5" r="2.5" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></circle>
                                <path d="M12 3.5h10.5v12h-10" fill="none" stroke="#86848c" stroke-linecap="round" stroke-linejoin="round" class="stroke-000000"></path>
                            </svg>
                            <h4>All Teacher</h4>
                        </a>
                    </li> -->
                    <li>
                        <a href="/mips/admin/user/parent.php" class="<?= isActive('/mips/admin/user/parent.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class="bi bi-people-fill"></i>
                                <h5>All Parent</h5>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="/mips/admin/user/student.php" class="<?= isActive('/mips/admin/user/student.php', $currentPage); ?>">
                            <div class="left-content">
                                <i class='bx bxs-book-reader'></i>
                                <h5>All Student</h5>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/mips/admin/deactivated/" class="<?= isActive('/mips/admin/deactivated/', $currentPage); ?>">
                    <div class="left-content">
                        <i class="bi bi-trash2-fill"></i>
                        <h5>Deactivated</h5>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</aside>
<Script>
    $(document).ready(function() {
        $.ajax({
            url: '/mips/admin/ajax.php?action=get_pending_count',
            type: 'GET',
            success: function(response) {
                if (parseInt(response) == 0) {
                    $('#pending-order-count').hide();
                } else {
                    $('#pending-order-count').text(response);
                }
            },
            error: function() {
                $('#pending-order-count').hide();
            }
        });
    });
</Script>