<?php
    require 'classes/conn.php';
?>

<!-- ===== DOCUMENT VIEWER MODAL ===== -->
<div class="modal fade" id="docViewerModal" tabindex="-1" role="dialog" aria-labelledby="docViewerTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0b2b5c,#1f5a9e); color:white;">
                <h5 class="modal-title" id="docViewerTitle">
                    <i class="fas fa-file"></i>&nbsp;<span id="docViewerTitleText">Document Preview</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white; opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3" id="docViewerBody" style="min-height:300px; background:#f8f9fa;">
                <p class="text-muted pt-5">Loading...</p>
            </div>
            <div class="modal-footer">
                <a id="docViewerNewTab" href="#" target="_blank" class="btn btn-primary btn-sm" style="border-radius:20px;">
                    <i class="fas fa-external-link-alt"></i>&nbsp;Open in New Tab
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius:20px;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden relay button -->
<button id="docViewerRelay" data-toggle="modal" data-target="#docViewerModal" style="display:none;"></button>

<style>
#docViewerBody img {
    max-width: 100%; max-height: 68vh; border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15); object-fit: contain;
}
#docViewerBody iframe { width: 100%; height: 68vh; border: none; border-radius: 8px; }
.doc-preview-btn {
    display: inline-block; max-width: 130px; overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap; vertical-align: middle;
    cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
}
.doc-preview-btn:hover { transform: scale(1.04); box-shadow: 0 3px 10px rgba(42,111,156,0.3); }
.doc-unsupported { padding: 50px 20px; color:#6c757d; }
.doc-unsupported .big-icon { font-size:3rem; display:block; margin-bottom:12px; color:#adb5bd; }
</style>

<?php if(isset($_POST['search_eleven'])): $keyword = $_POST['keyword']; ?>

<div class="table-responsive" style="width:100%; overflow-x:auto;">
    <table class="table table-hover text-center table-bordered" style="min-width:1000px;">
        <thead class="alert-info">
            <tr>
                <th>LRN</th><th>Course</th><th>Full Name</th><th>Birthday</th>
                <th>Age</th><th>Contact</th><th>Email</th><th>Documents</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $kw   = "%$keyword%";
            $stmt = $conn->prepare("SELECT * FROM `tbl_eleven` WHERE `lname` LIKE ? OR `fname` LIKE ? OR `id_resident` LIKE ? OR `lrn` LIKE ?");
            $stmt->execute([$kw, $kw, $kw, $kw]);
            while($view = $stmt->fetch()):
        ?>
        <tr>
            <td><?= htmlspecialchars($view['lrn']) ?></td>
            <td><?= htmlspecialchars($view['course']) ?></td>
            <td><?= htmlspecialchars($view['lname']) ?>, <?= htmlspecialchars($view['fname']) ?> <?= htmlspecialchars($view['mi']) ?></td>
            <td><?= htmlspecialchars($view['bdate']) ?></td>
            <td><?= htmlspecialchars($view['age']) ?></td>
            <td><?= htmlspecialchars($view['contact']) ?></td>
            <td><?= htmlspecialchars($view['email']) ?></td>
            <td style="min-width:145px;">
                <?php
                    $docs = json_decode($view['documents'] ?? '[]', true);
                    if (!empty($docs)):
                        foreach ($docs as $docPath):
                            $fileName = basename($docPath);
                            $ext   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                            $isPdf = $ext === 'pdf';
                            $icon  = $isImg ? 'fa-image' : ($isPdf ? 'fa-file-pdf' : 'fa-file-word');
                            $type  = $isImg ? 'image' : ($isPdf ? 'pdf' : 'doc');
                ?>
                    <button type="button"
                        class="btn btn-outline-primary btn-sm mb-1 doc-preview-btn"
                        onclick="openDocViewer('<?= addslashes(htmlspecialchars($docPath)) ?>','<?= addslashes(htmlspecialchars($fileName)) ?>','<?= $type ?>')"
                        title="<?= htmlspecialchars($fileName) ?>">
                        <i class="fas <?= $icon ?>"></i> <?= htmlspecialchars($fileName) ?>
                    </button><br>
                <?php endforeach; else: ?>
                    <span class="text-muted small">No documents</span>
                <?php endif; ?>
            </td>
            <td>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#viewModalSearch<?= $view['id_resident'] ?>">
                    <i class="fa fa-eye"></i> View
                </button>
                <form action="" method="post" style="display:inline;">
                    <input type="hidden" name="id_eleven" value="<?= $view['id_eleven'] ?>">
                    <button class="btn btn-danger btn-sm" type="submit" name="delete_eleven" style="border-radius:30px;">Archive</button>
                </form>
            </td>
        </tr>

        <div class="modal fade" id="viewModalSearch<?= $view['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Student Profile: <?= htmlspecialchars($view['fname']) ?></h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-left">
                        <p><strong>LRN:</strong> <?= htmlspecialchars($view['lrn']) ?></p>
                        <p><strong>Full Name:</strong> <?= htmlspecialchars($view['lname']) ?>, <?= htmlspecialchars($view['fname']) ?> <?= htmlspecialchars($view['mi']) ?></p>
                        <p><strong>Age:</strong> <?= htmlspecialchars($view['age']) ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php else: // DEFAULT VIEW ?>

<div class="table-responsive" style="width:100%; overflow-x:auto;">
    <table class="table table-hover text-center table-bordered" style="min-width:1000px;">
        <thead class="alert-info">
            <tr>
                <th>LRN</th><th>Course</th><th>Full Name</th><th>Birthday</th>
                <th>Age</th><th>Contact</th><th>Email</th><th>Documents</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php if(is_array($view)): foreach($view as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['lrn']) ?></td>
            <td><?= htmlspecialchars($row['course']) ?></td>
            <td><?= htmlspecialchars($row['lname']) ?>, <?= htmlspecialchars($row['fname']) ?> <?= htmlspecialchars($row['mi']) ?></td>
            <td><?= htmlspecialchars($row['bdate']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td style="min-width:145px;">
                <?php
                    $docs = json_decode($row['documents'] ?? '[]', true);
                    if (!empty($docs)):
                        foreach ($docs as $docPath):
                            $fileName = basename($docPath);
                            $ext   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                            $isPdf = $ext === 'pdf';
                            $icon  = $isImg ? 'fa-image' : ($isPdf ? 'fa-file-pdf' : 'fa-file-word');
                            $type  = $isImg ? 'image' : ($isPdf ? 'pdf' : 'doc');
                ?>
                    <button type="button"
                        class="btn btn-outline-primary btn-sm mb-1 doc-preview-btn"
                        onclick="openDocViewer('<?= addslashes(htmlspecialchars($docPath)) ?>','<?= addslashes(htmlspecialchars($fileName)) ?>','<?= $type ?>')"
                        title="<?= htmlspecialchars($fileName) ?>">
                        <i class="fas <?= $icon ?>"></i> <?= htmlspecialchars($fileName) ?>
                    </button><br>
                <?php endforeach; else: ?>
                    <span class="text-muted small">No documents</span>
                <?php endif; ?>
            </td>
            <td>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#viewModal<?= $row['id_resident'] ?>">
                    <i class="fa fa-eye"></i> View
                </button>
                <form action="" method="post" style="display:inline;">
                    <input type="hidden" name="id_eleven" value="<?= $row['id_eleven'] ?>">
                    <button class="btn btn-danger btn-sm" type="submit" name="delete_eleven" style="border-radius:30px;">Archive</button>
                </form>

                <div class="modal fade" id="viewModal<?= $row['id_resident'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Student Information</h5>
                                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body text-left">
                                <p><strong>School Year:</strong> <?= htmlspecialchars($row['sy']) ?></p>
                                <p><strong>Course:</strong> <?= htmlspecialchars($row['course']) ?></p>
                                <p><strong>LRN:</strong> <?= htmlspecialchars($row['lrn']) ?></p>
                                <hr style="border:2px solid black;opacity:1;">
                                <h5><strong>Personal Information</strong></h5>
                                <p><strong>Full Name:</strong> <?= htmlspecialchars($row['lname']) ?>, <?= htmlspecialchars($row['fname']) ?> <?= htmlspecialchars($row['mi']) ?></p>
                                <p><strong>Birthday:</strong> <?= htmlspecialchars($row['bdate']) ?></p>
                                <p><strong>Age:</strong> <?= htmlspecialchars($row['age']) ?></p>
                                <p><strong>Contact Number:</strong> <?= htmlspecialchars($row['contact']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                                <p><strong>Current Address:</strong> <?= htmlspecialchars($row['current_address']) ?></p>
                                <p><strong>Permanent Address:</strong> <?= htmlspecialchars($row['perm_address']) ?></p>
                                <hr style="border:2px solid black;opacity:1;">
                                <h5><strong>Father's Information</strong></h5>
                                <p><strong>Name:</strong> <?= htmlspecialchars($row['flname']) ?>, <?= htmlspecialchars($row['ffname']) ?> <?= htmlspecialchars($row['fmi']) ?></p>
                                <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact_f']) ?></p>
                                <hr style="border:2px solid black;opacity:1;">
                                <h5><strong>Mother's Information</strong></h5>
                                <p><strong>Name:</strong> <?= htmlspecialchars($row['mlname']) ?>, <?= htmlspecialchars($row['mfname']) ?> <?= htmlspecialchars($row['mmi']) ?></p>
                                <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact_m']) ?></p>
                                <hr style="border:2px solid black;opacity:1;">
                                <h5><strong>For Returning Learner:</strong></h5>
                                <p><strong>Last Grade Level Completed:</strong> <?= htmlspecialchars($row['lglc']) ?></p>
                                <p><strong>Last School Attended:</strong> <?= htmlspecialchars($row['lsa']) ?></p>
                                <p><strong>Last School Year Completed:</strong> <?= htmlspecialchars($row['lysc']) ?></p>
                                <p><strong>School Id:</strong> <?= htmlspecialchars($row['school_id']) ?></p>
                                <hr style="border:2px solid black;opacity:1;">
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php endif; $conn = null; ?>

<script>
function openDocViewer(path, name, type) {
    document.getElementById('docViewerTitleText').textContent = name;
    document.getElementById('docViewerNewTab').setAttribute('href', path);

    var body = document.getElementById('docViewerBody');
    if (type === 'image') {
        body.innerHTML = '<img src="' + path + '" alt="' + name + '">';
    } else if (type === 'pdf') {
        body.innerHTML = '<iframe src="' + path + '" title="' + name + '"></iframe>';
    } else {
        body.innerHTML =
            '<div class="doc-unsupported">' +
            '<i class="fas fa-file-word big-icon"></i>' +
            '<strong>' + name + '</strong>' +
            '<p class="mt-2 text-muted">This file type cannot be previewed here.<br>' +
            'Use <strong>Open in New Tab</strong> to download and view it.</p>' +
            '</div>';
    }

    document.getElementById('docViewerRelay').click();
}

document.getElementById('docViewerModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('docViewerBody').innerHTML = '<p class="text-muted pt-5">Loading...</p>';
    document.getElementById('docViewerTitleText').textContent = 'Document Preview';
    document.getElementById('docViewerNewTab').setAttribute('href', '#');
});
</script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>