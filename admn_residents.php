<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 0);
    require('classes/resident.class.php');
    $userdetails = $eusebia->get_userdata();
    $eusebia->validate_admin();

    // Fetch all registered resident accounts
    $connection = $eusebia->openConn();
    $stmt = $connection->prepare("SELECT * FROM tbl_resident ORDER BY lname ASC, fname ASC");
    $stmt->execute();
    $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include('dashboard_sidebar_start.php'); ?>

<style>
.res-header {
    background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
    color: #fff;
    padding: 18px 24px;
    border-radius: 8px;
    margin-bottom: 24px;
}
.res-header h4 { margin-bottom: 2px; }
.total-badge {
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff;
    font-weight: 700;
    padding: 6px 18px;
    border-radius: 20px;
    font-size: 14px;
}
.table thead th {
    background: #0b2b5c;
    color: #fff;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    vertical-align: middle;
}
.table tbody tr:hover { background-color: #eaf0fb; }
.table tbody tr.row-selected { background-color: #d6e8f7 !important; }
.search-wrap input {
    border: 1px solid #c5d5e8;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 14px;
    width: 260px;
}
.search-wrap input:focus {
    outline: none;
    border-color: #0b2b5c;
    box-shadow: 0 0 0 2px rgba(11,43,92,0.1);
}
#entriesCount { font-size: 13px; color: #6c757d; }
#btnDeleteSelected {
    display: none;
    background: #c0392b;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 7px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
#btnDeleteSelected:hover { background: #a93226; }
#selectedCount {
    font-size: 13px;
    font-weight: 600;
    color: #c0392b;
    margin-right: 8px;
    display: none;
}
.cb-row { cursor: pointer; width: 18px; height: 18px; accent-color: #0b2b5c; }
.th-check { width: 42px; text-align: center; }
</style>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Header -->
    <div class="res-header d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <h4 class="mb-1"><i class="fas fa-users mr-2"></i>Registered Residents</h4>
            <small class="opacity-75">All accounts registered in the portal.</small>
        </div>
        <span class="total-badge mt-2 mt-md-0">
            <i class="fas fa-user-check mr-1"></i>
            Total: <?= number_format(count($residents)) ?>
        </span>
    </div>

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="m-0 font-weight-bold" style="color:#0b2b5c;">
                <i class="fas fa-table mr-1"></i> Resident List
            </h6>
            <div class="d-flex align-items-center flex-wrap mt-2 mt-md-0" style="gap:10px;">
                <span id="selectedCount">0 selected</span>
                <button id="btnDeleteSelected" onclick="confirmBulkDelete()">
                    <i class="fas fa-trash-alt mr-1"></i> Delete Selected
                </button>
                <div class="search-wrap">
                    <input type="text" id="residentSearch" placeholder="&#128269; Search name, email...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="residentTable">
                    <thead>
                        <tr>
                            <th class="th-check">
                                <input type="checkbox" id="selectAll" class="cb-row" title="Select All">
                            </th>
                            <th style="width:45px;">#</th>
                            <th>Full Name</th>
                            <th>Birthdate</th>
                            <th>Email / Phone</th>
                            <th>Registered By</th>
                        </tr>
                    </thead>
                    <tbody id="residentTbody">
                        <?php if (empty($residents)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No registered residents found.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($residents as $i => $r):
                            $mi       = !empty($r['mi']) ? ' ' . strtoupper($r['mi']) . '.' : '';
                            $fullname = strtoupper($r['lname']) . ', ' . ucwords(strtolower($r['fname'])) . $mi;
                            $contact  = !empty($r['email']) ? $r['email'] : ($r['phone_number'] ?? '—');
                            $bdate    = !empty($r['bdate']) ? date('M d, Y', strtotime($r['bdate'])) : '—';
                        ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="cb-row cb-resident" value="<?= $r['id_resident'] ?>">
                            </td>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td class="font-weight-bold" style="color:#0b2b5c;"><?= htmlspecialchars($fullname) ?></td>
                            <td><?= $bdate ?></td>
                            <td><?= htmlspecialchars($contact) ?></td>
                            <td><?= htmlspecialchars($r['addedby'] ?? '—') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted d-flex justify-content-between align-items-center py-2">
            <small id="entriesCount">Showing <strong id="visibleCount"><?= count($residents) ?></strong> of <strong><?= count($residents) ?></strong> entries</small>
            <small>Sorted by last name A–Z</small>
        </div>
    </div>

</div><!-- /.container-fluid -->

<script>
// ── Select All ──────────────────────────────────────────────────────────────
document.getElementById('selectAll').addEventListener('change', function () {
    const visibleCheckboxes = [...document.querySelectorAll('#residentTbody tr')]
        .filter(r => r.style.display !== 'none')
        .map(r => r.querySelector('.cb-resident'))
        .filter(Boolean);

    visibleCheckboxes.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('row-selected', this.checked);
    });
    updateDeleteBar();
});

// ── Per-row checkbox ─────────────────────────────────────────────────────────
document.getElementById('residentTbody').addEventListener('change', function (e) {
    if (e.target.classList.contains('cb-resident')) {
        e.target.closest('tr').classList.toggle('row-selected', e.target.checked);
        syncSelectAll();
        updateDeleteBar();
    }
});

function syncSelectAll() {
    const all     = [...document.querySelectorAll('.cb-resident')].filter(cb => cb.closest('tr').style.display !== 'none');
    const checked = all.filter(cb => cb.checked);
    const sa      = document.getElementById('selectAll');
    sa.checked       = all.length > 0 && checked.length === all.length;
    sa.indeterminate = checked.length > 0 && checked.length < all.length;
}

function updateDeleteBar() {
    const count = document.querySelectorAll('.cb-resident:checked').length;
    const btn   = document.getElementById('btnDeleteSelected');
    const lbl   = document.getElementById('selectedCount');
    if (count > 0) {
        btn.style.display = 'inline-block';
        lbl.style.display = 'inline';
        lbl.textContent   = count + ' selected';
    } else {
        btn.style.display = 'none';
        lbl.style.display = 'none';
    }
}

// ── Bulk delete ──────────────────────────────────────────────────────────────
function confirmBulkDelete() {
    const ids = [...document.querySelectorAll('.cb-resident:checked')].map(cb => cb.value);
    if (ids.length === 0) return;

    Swal.fire({
        title: 'Delete ' + ids.length + ' account' + (ids.length > 1 ? 's' : '') + '?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c0392b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (!result.isConfirmed) return;

        fetch('delete_bulk_residents.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: ids })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: data.deleted + ' account(s) removed.',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Something went wrong.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'Request failed. Please try again.', 'error'));
    });
}

// ── Live search ──────────────────────────────────────────────────────────────
document.getElementById('residentSearch').addEventListener('keyup', function () {
    const query = this.value.toLowerCase().trim();
    const rows  = document.querySelectorAll('#residentTbody tr');
    let visible = 0;

    rows.forEach(row => {
        const match = row.innerText.toLowerCase().includes(query);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('visibleCount').textContent = visible;
    syncSelectAll();
});
</script>

<?php include('dashboard_sidebar_end.php'); ?>