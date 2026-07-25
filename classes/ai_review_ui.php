<?php
/**
 * Shared "AI Review" column UI for the admin enrollment tables.
 * Include this once near the top of each admn_*_search.php file, then call
 * render_ai_review_cell() inside the row loop.
 */

/** Badge + "View" button for the table cell. Call once per row. */
function render_ai_review_cell($aiAnalysisJson, $grade, $recordId) {
    $ai = $aiAnalysisJson ? json_decode($aiAnalysisJson, true) : null;
    $modalId = "aiModal_{$grade}_{$recordId}";

    if (!$ai) {
        echo '<span class="ai-badge ai-badge-pending" title="No AI review yet">
                <i class="fas fa-robot"></i> Not analyzed
              </span><br>
              <button type="button" class="btn btn-outline-secondary btn-sm mt-1" onclick="aiReanalyze(\'' . $grade . '\',' . (int)$recordId . ',this)">
                <i class="fas fa-magic mr-1"></i>Analyze
              </button>';
        return;
    }

    if (empty($ai['success'])) {
        echo '<span class="ai-badge ai-badge-error" title="' . htmlspecialchars($ai['error'] ?? 'AI review failed') . '">
                <i class="fas fa-exclamation-triangle"></i> Unavailable
              </span><br>
              <button type="button" class="btn btn-outline-secondary btn-sm mt-1" onclick="aiReanalyze(\'' . $grade . '\',' . (int)$recordId . ',this)">
                <i class="fas fa-redo mr-1"></i>Retry
              </button>';
        return;
    }

    $flag = $ai['overall_flag'] ?? 'needs_review';
    $map = [
        'ok'           => ['ai-badge-ok', 'fa-check-circle', 'Matched'],
        'needs_review' => ['ai-badge-warn', 'fa-eye', 'Needs Review'],
        'mismatch'     => ['ai-badge-mismatch', 'fa-times-circle', 'Mismatch'],
    ];
    [$cls, $icon, $label] = $map[$flag] ?? $map['needs_review'];

    echo '<span class="ai-badge ' . $cls . '" style="cursor:pointer;" data-toggle="modal" data-target="#' . $modalId . '">
            <i class="fas ' . $icon . '"></i> ' . htmlspecialchars($label) . '
          </span><br>
          <button type="button" class="btn btn-link btn-sm p-0 mt-1" data-toggle="modal" data-target="#' . $modalId . '">View details</button>';

    render_ai_review_modal($ai, $grade, $recordId, $modalId);
}

/** The detail modal — document-by-document breakdown + field match table. */
function render_ai_review_modal($ai, $grade, $recordId, $modalId) {
    ?>
    <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:14px;overflow:hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg,#0b2b5c,#1f5a9e);color:white;">
                    <h5 class="modal-title"><i class="fas fa-robot mr-2"></i>AI Document Review</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:1;"><span>&times;</span></button>
                </div>
                <div class="modal-body text-left" id="<?= $modalId ?>_body">
                    <?php render_ai_review_body($ai); ?>
                </div>
                <div class="modal-footer">
                    <small class="text-muted mr-auto">This is an AI-assisted check &mdash; please confirm important flags yourself before deciding.</small>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="aiReanalyze('<?= $grade ?>',<?= (int)$recordId ?>,this)">
                        <i class="fas fa-redo mr-1"></i>Re-analyze
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/** Renders just the inner content of the modal body (reused by the JS refresh after re-analyze). */
function render_ai_review_body($ai) {
    if (empty($ai['success'])) {
        echo '<p class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>' . htmlspecialchars($ai['error'] ?? 'AI review failed.') . '</p>';
        if (!empty($ai['skipped'])) {
            echo '<p class="text-muted small">Skipped: ' . htmlspecialchars(implode('; ', $ai['skipped'])) . '</p>';
        }
        return;
    }

    if (!empty($ai['summary'])) {
        echo '<p><strong>Summary:</strong> ' . htmlspecialchars($ai['summary']) . '</p>';
    }

    if (!empty($ai['required_docs_detected']) || !empty($ai['required_docs_not_detected'])) {
        echo '<p><strong>Commonly-required documents:</strong><br>';
        foreach (($ai['required_docs_detected'] ?? []) as $d) {
            echo '<span class="badge badge-success mr-1 mb-1"><i class="fas fa-check mr-1"></i>' . htmlspecialchars($d) . '</span>';
        }
        foreach (($ai['required_docs_not_detected'] ?? []) as $d) {
            echo '<span class="badge badge-secondary mr-1 mb-1">' . htmlspecialchars($d) . ' &mdash; not detected</span>';
        }
        echo '</p>';
    }

    if (!empty($ai['documents'])) {
        echo '<p class="mb-1"><strong>Documents reviewed:</strong></p>
              <div class="table-responsive mb-3"><table class="table table-sm table-bordered">
              <thead class="thead-light"><tr><th>File</th><th>Detected Type</th><th>Confidence</th><th>Notes</th></tr></thead><tbody>';
        foreach ($ai['documents'] as $doc) {
            echo '<tr>
                    <td>' . htmlspecialchars($doc['file'] ?? '') . '</td>
                    <td>' . htmlspecialchars($doc['detected_type'] ?? '') . '</td>
                    <td>' . htmlspecialchars($doc['confidence'] ?? '') . '</td>
                    <td class="small text-muted">' . htmlspecialchars($doc['notes'] ?? '') . '</td>
                  </tr>';
        }
        echo '</tbody></table></div>';
    }

    if (!empty($ai['field_matches'])) {
        echo '<p class="mb-1"><strong>Form vs. document check:</strong></p>
              <div class="table-responsive mb-2"><table class="table table-sm table-bordered">
              <thead class="thead-light"><tr><th>Field</th><th>Form says</th><th>Document says</th><th></th></tr></thead><tbody>';
        foreach ($ai['field_matches'] as $fm) {
            $match = !empty($fm['match']);
            $rowCls = $match ? '' : 'table-warning';
            $icon = $match ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-exclamation-triangle text-warning"></i>';
            echo '<tr class="' . $rowCls . '">
                    <td>' . htmlspecialchars($fm['field'] ?? '') . '</td>
                    <td>' . htmlspecialchars($fm['form_value'] ?? '') . '</td>
                    <td>' . htmlspecialchars($fm['document_value'] ?? '') . '</td>
                    <td>' . $icon . '</td>
                  </tr>';
        }
        echo '</tbody></table></div>';
    }

    if (!empty($ai['analyzed_at'])) {
        echo '<p class="text-muted small mb-0">Analyzed ' . htmlspecialchars($ai['analyzed_at']) . (!empty($ai['model']) ? ' &middot; ' . htmlspecialchars($ai['model']) : '') . '</p>';
    }
}
?>
<style>
.ai-badge { font-size:12px;padding:4px 10px;border-radius:20px;font-weight:600;display:inline-block; }
.ai-badge-ok       { background:#d4edda;color:#155724;border:1px solid #28a745; }
.ai-badge-warn     { background:#fff3cd;color:#856404;border:1px solid #ffc107; }
.ai-badge-mismatch { background:#f8d7da;color:#721c24;border:1px solid #dc3545; }
.ai-badge-error    { background:#e2e3e5;color:#383d41;border:1px solid #6c757d; }
.ai-badge-pending  { background:#e2e3e5;color:#383d41;border:1px solid #adb5bd; }
</style>
<script>
function aiReanalyze(grade, id, btn) {
    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';

    fetch('ai_reanalyze.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'grade=' + encodeURIComponent(grade) + '&id=' + encodeURIComponent(id)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            // Simplest reliable way to reflect the new badge/modal content everywhere.
            window.location.reload();
        } else {
            alert('AI analysis failed: ' + (data.message || 'Unknown error'));
            btn.disabled = false;
            btn.innerHTML = original;
        }
    })
    .catch(function() {
        alert('Could not reach the server. Please try again.');
        btn.disabled = false;
        btn.innerHTML = original;
    });
}
</script>
