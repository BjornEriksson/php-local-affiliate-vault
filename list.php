<?php $openFile = basename($_GET['open'] ?? ''); ?>

<div class="toolbar" style="margin-bottom: 10px;">
    <div class="toolbar-group">
        <strong>Filter:</strong>
        <a href="?filter=all&sort=<?= h($sort) ?>&letter=<?= h($letter) ?>" class="btn <?= $filter === 'all' ? 'btn-active' : 'btn-secondary' ?>">List All (<?= count($files) ?>)</a>
        <a href="?filter=fav&sort=<?= h($sort) ?>&letter=<?= h($letter) ?>" class="btn <?= $filter === 'fav' ? 'btn-active' : 'btn-secondary' ?>">★ Favourites</a>
        <a href="?filter=active&sort=<?= h($sort) ?>&letter=<?= h($letter) ?>" class="btn <?= $filter === 'active' ? 'btn-active' : 'btn-secondary' ?>">● Active</a>
        <a href="?filter=inactive&sort=<?= h($sort) ?>&letter=<?= h($letter) ?>" class="btn <?= $filter === 'inactive' ? 'btn-active' : 'btn-secondary' ?>">○ Non Active</a>
    </div>
    <div class="toolbar-group">
        <strong>Sort by:</strong>
        <a href="?filter=<?= h($filter) ?>&sort=alpha&letter=<?= h($letter) ?>" class="btn <?= $sort === 'alpha' ? 'btn-active' : 'btn-secondary' ?>">0–9 / A–Z</a>
        <a href="?filter=<?= h($filter) ?>&sort=earnings&letter=<?= h($letter) ?>" class="btn <?= $sort === 'earnings' ? 'btn-active' : 'btn-secondary' ?>">Highest Earnings</a>
    </div>
</div>

<!-- Letter row -->
<div class="alphabet-bar">
    <a href="?filter=<?= h($filter) ?>&sort=<?= h($sort) ?>&letter=all" class="btn-alpha <?= $letter === 'ALL' ? 'active' : '' ?>">All</a>
    <?php
    $alphaKeys = array_merge(['0-9'], range('A', 'Z'));
    foreach ($alphaKeys as $key):
        $hasItems = isset($availableLetters[$key]);
        $isActive = ($letter === $key);
    ?>
        <?php if ($hasItems || $isActive): ?>
            <a href="?filter=<?= h($filter) ?>&sort=<?= h($sort) ?>&letter=<?= urlencode($key) ?>" class="btn-alpha <?= $isActive ? 'active' : '' ?>">
                <?= h($key) ?>
            </a>
        <?php else: ?>
            <span class="btn-alpha disabled"><?= h($key) ?></span>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<h2>Showing <?= count($programs) ?> Program(s)</h2>

<?php if (empty($programs)): ?>
    <div class="box"><p>No affiliate programs match the selected filter.</p></div>
<?php endif; ?>

<?php foreach ($programs as $idx => $p):
    $fname = $p['_file'];
    $clients = $p['clients'] ?? [];

	// Build the client name list and apply strikethrough to inactive casinos
    $clientHtmlList = [];
    foreach ($clients as $c) {
        $cName = h($c['casino_name'] ?? '');
        $isClientActive = $c['is_active'] ?? true;
        if ($isClientActive) {
            $clientHtmlList[] = $cName;
        } else {
            $clientHtmlList[] = '<span class="client-inactive">' . $cName . '</span>';
        }
    }
?>
    <details id="prog_<?= h($fname) ?>" class="program-row" <?= $fname === $openFile ? 'open' : '' ?>>
        <summary class="row-header" style="display: block; padding: 12px 16px; cursor: pointer;">
			<!-- Row 1: Program name on the left + fixed badges on the right -->
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; width: 100%;">
                <div class="row-title" style="flex: 1; min-width: 0;">
                    <?= h($p['program_name']) ?>
                </div>

                <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    <span style="width: 96px; display: inline-flex; justify-content: flex-end;">
                        <?php if (!empty($p['is_favourite'])): ?>
                            <span class="badge badge-fav" style="width: 100%; justify-content: center;">★ Favourite</span>
                        <?php endif; ?>
                    </span>

                    <span class="badge badge-earn" style="width: 85px; justify-content: center;">
                        <?= number_format((float)($p['earnings'] ?? 0), 2) ?>
                    </span>

                    <span class="badge <?= !empty($p['is_active']) ? 'badge-act' : 'badge-inact' ?>" style="width: 72px; justify-content: center;">
                        <?= !empty($p['is_active']) ? 'Active' : 'Inactive' ?>
                    </span>

                    <span class="row-arrow" style="width: 16px; text-align: center;">▼</span>
                </div>
            </div>

			<!-- Row 2: Clients across full width below the title -->
            <div class="row-clients" style="margin-top: 6px; width: 100%;">
				<?= !empty($clientHtmlList) ? implode(' • ', $clientHtmlList) : '<em>No casinos added yet</em>' ?>
            </div>
        </summary>

        <div class="row-details">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
                <div>
                    <strong>Login URL:</strong>
                    <?= !empty($p['login_url']) ? '<a href="'.h($p['login_url']).'" target="_blank">'.h($p['login_url']).'</a>' : '<em>None</em>' ?>
                </div>
                <a href="?edit=<?= urlencode($fname) ?>" class="btn btn-secondary btn-sm">Edit Program / Notes</a>
            </div>

            <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:center; margin-bottom:10px;">
                <div>
                    <strong>User:</strong>
                    <input type="text" class="cred-input" value="<?= h($p['username'] ?? '') ?>" readonly onclick="this.select();">
                </div>
                <div>
                    <strong>Pass:</strong>
                    <input type="password" id="pw_<?= $idx ?>" class="cred-input" value="<?= h($p['password'] ?? '') ?>" readonly onclick="this.select();">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="togglePw('pw_<?= $idx ?>', this)">Show</button>
                </div>
            </div>

            <?php if (!empty($p['comments'])): ?>
                <div class="comments"><?= h($p['comments']) ?></div>
            <?php endif; ?>

            <h4 style="margin:12px 0 6px;">Casinos / Clients (<?= count($clients) ?>):</h4>
            <?php if (!empty($clients)): ?>
                <table>
                    <tr>
                        <th style="width:24%;">Casino Name</th>
                        <th>Affiliate URL</th>
                        <th style="width:175px;">Action</th>
                    </tr>
                    <?php foreach ($clients as $cIndex => $c): 
                        $cActive = $c['is_active'] ?? true;
                    ?>
                        <tr style="<?= !$cActive ? 'opacity: 0.65; background: #f9fafb;' : '' ?>">
                            <td class="<?= !$cActive ? 'client-inactive' : '' ?>"><?= h($c['casino_name'] ?? '') ?></td>
								<td>
									<form method="POST" style="display:flex; gap:6px; align-items:center; margin:0;">
										<input type="hidden" name="program_file" value="<?= h($fname) ?>">
										<input type="hidden" name="client_index" value="<?= $cIndex ?>">
										<input type="text" name="affiliate_url" style="margin:0; font-family:monospace; height:30px;" value="<?= h($c['affiliate_url'] ?? '') ?>" onclick="this.select();">
										<button type="button" class="btn-copy" onclick="copyUrl(this)">Copy</button>
										<button type="submit" name="update_client_url" class="btn-update">Update</button>
									</form>
								</td>
                            <td>
                                <div style="display:flex; gap:6px; align-items:center;">
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="program_file" value="<?= h($fname) ?>">
                                        <input type="hidden" name="client_index" value="<?= $cIndex ?>">
                                        <button type="submit" name="toggle_client_active" class="<?= $cActive ? 'btn-client-active' : 'btn-client-inactive' ?>">
                                            <?= $cActive ? 'Active' : 'Non active' ?>
                                        </button>
                                    </form>

                                    <form method="POST" style="margin:0;" onsubmit="return confirm('Remove this casino?');">
                                        <input type="hidden" name="program_file" value="<?= h($fname) ?>">
                                        <input type="hidden" name="client_index" value="<?= $cIndex ?>">
                                        <button type="submit" name="delete_client" class="btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p style="color:#666; margin:4px 0;"><em>No casinos added yet.</em></p>
            <?php endif; ?>
        </div>
    </details>
<?php endforeach; ?>