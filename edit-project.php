<?php 
require_once __DIR__ . '/includes/client_guard.php'; 
require_once __DIR__ . '/db/connection.php';

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$client_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $project_id, $client_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    die("Project not found or unauthorized.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Edit Project</title>
    <meta name="description" content="Edit your project on Wasla.">
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="wasla-theme.js"></script>
</head>
<body>
    <?php $active_page = 'projects'; ?>
    <?php include __DIR__ . '/includes/navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        <main class="content" id="main-content">
            <div class="page-header">
                <h1 class="section-title">Edit Project</h1>
                <a href="projects.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Projects</a>
            </div>
            <div class="create-project-form">
                <!-- Step Indicator -->
                <div class="form-steps">
                    <div class="form-step active" data-step="1"><span class="step-number">1</span><span class="step-label">Details</span></div>
                    <div class="form-step-line"></div>
                    <div class="form-step" data-step="2"><span class="step-number">2</span><span class="step-label">Ushers</span></div>
                    <div class="form-step-line"></div>
                    <div class="form-step" data-step="3"><span class="step-number">3</span><span class="step-label">Review</span></div>
                </div>
                <!-- Step 1: Details -->
                <div class="step-panel active" id="step-1">
                    <div class="settings-card">
                        <h2 class="settings-card-title">Project Details</h2>
                        <div class="settings-form">
                            <div class="form-group">
                                <label class="form-label">Project Name *</label>
                                <input type="text" class="form-input" id="project-name" value="<?= htmlspecialchars($project['title']) ?>">
                            </div>
                            <div class="form-row">
                                <div class="form-group"><label class="form-label">Event Date *</label><input type="date" class="form-input" id="project-date" value="<?= htmlspecialchars($project['event_date']) ?>"></div>
                                <div class="form-group"><label class="form-label">End Date</label><input type="date" class="form-input" id="project-end-date" value="<?= htmlspecialchars($project['end_date'] ?? '') ?>"></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group"><label class="form-label">Location *</label><input type="text" class="form-input" id="project-location" value="<?= htmlspecialchars($project['location']) ?>"></div>
                                <div class="form-group"><label class="form-label">City *</label>
                                    <select class="form-input" id="project-city">
                                        <option value="">Select City</option>
                                        <option value="Cairo" <?= $project['city'] === 'Cairo' ? 'selected' : '' ?>>Cairo</option>
                                        <option value="Riyadh" <?= $project['city'] === 'Riyadh' ? 'selected' : '' ?>>Riyadh</option>
                                        <option value="Jeddah" <?= $project['city'] === 'Jeddah' ? 'selected' : '' ?>>Jeddah</option>
                                        <option value="Dubai" <?= $project['city'] === 'Dubai' ? 'selected' : '' ?>>Dubai</option>
                                        <option value="Alexandria" <?= $project['city'] === 'Alexandria' ? 'selected' : '' ?>>Alexandria</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea class="form-input form-textarea" id="project-desc" rows="4"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Cover Image</label>
                                <div class="file-upload-area" id="file-upload-area">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Drag & drop an image or <span class="file-upload-link">browse</span></p>
                                    <span class="file-upload-hint">PNG, JPG up to 5MB</span>
                                    <input type="file" id="file-input" accept="image/*" hidden>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button class="btn-save btn-next" onclick="goToStep(2)">Next: Ushers <i class="fas fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Step 2: Ushers -->
                <div class="step-panel" id="step-2">
                    <div class="settings-card">
                        <h2 class="settings-card-title">Usher Requirements</h2>
                        <div class="settings-form">
                            <div class="form-row">
                                <div class="form-group"><label class="form-label">Number of Ushers Needed *</label><input type="number" class="form-input" id="ushers-count" min="1" value="<?= htmlspecialchars($project['ushers_needed']) ?>"></div>
                                <div class="form-group"><label class="form-label">Budget per Usher (EGP)</label><input type="number" class="form-input" id="ushers-budget" value="<?= htmlspecialchars($project['pay_per_usher'] ?? '') ?>"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Preferred Usher Category *</label>
                                <select class="form-input" id="usher-category">
                                    <option value="">All Categories</option>
                                    <option value="Entertainment" <?= ($project['category'] ?? '') === 'Entertainment' ? 'selected' : '' ?>>Entertainment</option>
                                    <option value="Event organizers" <?= ($project['category'] ?? '') === 'Event organizers' ? 'selected' : '' ?>>Event Organizers</option>
                                    <option value="Quality control" <?= ($project['category'] ?? '') === 'Quality control' ? 'selected' : '' ?>>Quality Control</option>
                                    <option value="Models" <?= ($project['category'] ?? '') === 'Models' ? 'selected' : '' ?>>Models</option>
                                    <option value="Operation" <?= ($project['category'] ?? '') === 'Operation' ? 'selected' : '' ?>>Operation</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Requirements</label>
                                <textarea class="form-input form-textarea" rows="3" placeholder="e.g. Must speak Arabic and English, professional attire..."></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Skills Needed</label>
                                <div class="skills-tags">
                                    <span class="skill-tag">Communication <i class="fas fa-times" onclick="this.parentElement.remove()"></i></span>
                                    <span class="skill-tag">Customer Service <i class="fas fa-times" onclick="this.parentElement.remove()"></i></span>
                                    <input type="text" class="skill-input" placeholder="Add skill..." id="skill-input">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button class="btn-back-step" onclick="goToStep(1)"><i class="fas fa-arrow-left"></i> Back</button>
                                <button class="btn-save btn-next" onclick="goToStep(3)">Next: Review <i class="fas fa-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Step 3: Review -->
                <div class="step-panel" id="step-3">
                    <div class="settings-card">
                        <h2 class="settings-card-title">Review & Submit</h2>
                        <div class="review-summary" id="review-summary">
                            <div class="review-row"><span class="review-label">Project Name</span><span class="review-value" id="rev-name">—</span></div>
                            <div class="review-row"><span class="review-label">Date</span><span class="review-value" id="rev-date">—</span></div>
                            <div class="review-row"><span class="review-label">Location</span><span class="review-value" id="rev-location">—</span></div>
                            <div class="review-row"><span class="review-label">Ushers</span><span class="review-value" id="rev-ushers">—</span></div>
                        </div>
                        <div class="form-actions">
                            <button class="btn-back-step" onclick="goToStep(2)"><i class="fas fa-arrow-left"></i> Back</button>
                            <button class="btn-save btn-submit" onclick="submitProject()"><i class="fas fa-check"></i> Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script>
        const projectId = <?= $project_id ?>;
        
        function goToStep(n){
            document.querySelectorAll('.step-panel').forEach(p=>p.classList.remove('active'));
            document.getElementById('step-'+n).classList.add('active');
            document.querySelectorAll('.form-step').forEach(s=>{
                const sn=parseInt(s.dataset.step);
                s.classList.toggle('active',sn<=n);
                s.classList.toggle('completed',sn<n);
            });
            if(n===3){
                document.getElementById('rev-name').textContent=document.getElementById('project-name').value||'—';
                document.getElementById('rev-date').textContent=document.getElementById('project-date').value||'—';
                document.getElementById('rev-location').textContent=document.getElementById('project-location').value||'—';
                document.getElementById('rev-ushers').textContent=document.getElementById('ushers-count').value||'—';
            }
        }
        function submitProject(){
            const btn=document.querySelector('.btn-submit');
            const name = document.getElementById('project-name').value.trim();
            const date = document.getElementById('project-date').value;
            const endDate = document.getElementById('project-end-date').value;
            const location = document.getElementById('project-location').value.trim();
            const city = document.getElementById('project-city').value;
            const desc = document.getElementById('project-desc').value.trim();
            const ushers = document.getElementById('ushers-count').value;
            const budget = document.getElementById('ushers-budget').value;
            const category = document.getElementById('usher-category').value;

            // Validate
            if (!name || !date || !location || !city || !ushers) {
                alert('Please fill in all required fields (Name, Date, Location, City, Ushers).');
                return;
            }

            btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;

            fetch('api/update_project.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    project_id: projectId,
                    title: name,
                    description: desc,
                    event_date: date,
                    end_date: endDate || null,
                    location: location,
                    city: city,
                    ushers_needed: parseInt(ushers),
                    pay_per_usher: budget ? parseFloat(budget) : null,
                    category: category
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML='<i class="fas fa-check"></i> Saved Successfully!';
                    btn.style.background='var(--accent)';
                    setTimeout(()=>{window.location.href='projects.php';},1500);
                } else {
                    btn.innerHTML='<i class="fas fa-check"></i> Save Changes';
                    btn.disabled = false;
                    alert(data.error || 'Failed to update project.');
                }
            })
            .catch(() => {
                btn.innerHTML='<i class="fas fa-check"></i> Save Changes';
                btn.disabled = false;
                alert('Network error.');
            });
        }
        document.getElementById('file-upload-area').addEventListener('click',()=>document.getElementById('file-input').click());
        document.getElementById('skill-input').addEventListener('keypress',e=>{
            if(e.key==='Enter'&&e.target.value.trim()){
                const tag=document.createElement('span');tag.className='skill-tag';tag.innerHTML=e.target.value.trim()+' <i class="fas fa-times" onclick="this.parentElement.remove()"></i>';
                e.target.parentElement.insertBefore(tag,e.target);e.target.value='';
            }
        });
    </script>
</body>
</html>
