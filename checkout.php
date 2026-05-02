<?php require_once __DIR__ . '/includes/client_guard.php'; 
$last_name = $_SESSION['user_last_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Deposit Funds</title>
    <meta name="description" content="Securely deposit funds to your Wasla wallet.">
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="wasla-theme.js"></script>
    <style>
        .checkout-wrapper { max-width: 540px; margin: 40px auto; padding: 0 20px; }
        .checkout-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 36px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }
        .checkout-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), var(--cyan), #f59e0b);
        }
        .checkout-title {
            font-size: 1.35rem; font-weight: 800; margin: 0 0 6px;
            color: var(--primary);
        }
        .checkout-subtitle {
            color: var(--gray-600); font-size: 0.9rem; margin: 0 0 28px;
        }

        /* Amount Buttons */
        .amount-selector { display: flex; gap: 10px; margin-bottom: 28px; flex-wrap: wrap; }
        .amount-btn {
            flex: 1; min-width: 90px; padding: 14px 8px;
            background: var(--gray-50);
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-sm);
            color: var(--gray-800);
            font-size: 0.95rem; font-weight: 700;
            cursor: pointer; transition: all 0.25s ease; text-align: center;
        }
        .amount-btn:hover {
            border-color: var(--accent);
            background: rgba(0, 212, 170, 0.06);
            transform: translateY(-2px);
        }
        .amount-btn.selected {
            border-color: var(--accent);
            background: linear-gradient(135deg, rgba(0,212,170,0.1), rgba(79,195,247,0.08));
            color: var(--accent-hover);
            box-shadow: 0 4px 16px var(--accent-glow);
            transform: translateY(-2px);
        }

        /* Card Visual */
        .card-visual {
            background: linear-gradient(135deg, #1a1a3e 0%, #2d1b69 50%, #1a3a5c 100%);
            border-radius: 16px; padding: 24px; margin-bottom: 28px;
            position: relative; overflow: hidden; height: 190px;
            display: flex; flex-direction: column; justify-content: space-between;
            box-shadow: 0 8px 32px rgba(26,26,62,0.3);
        }
        .card-visual::after {
            content: ''; position: absolute; top: -60px; right: -60px;
            width: 220px; height: 220px; border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .card-visual::before { display: none; }
        .card-chip { width: 42px; height: 32px; background: linear-gradient(135deg, #ffd700, #ff9800); border-radius: 6px; }
        .card-number-display {
            font-size: 1.2rem; letter-spacing: 3.5px; font-weight: 600;
            color: rgba(255,255,255,0.85); font-family: 'Courier New', monospace;
        }
        .card-bottom { display: flex; justify-content: space-between; align-items: flex-end; }
        .card-holder { font-size: 0.72rem; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 1px; }
        .card-holder span { display: block; color: #fff; font-size: 0.88rem; font-weight: 600; margin-top: 3px; letter-spacing: 0; text-transform: none; }
        .card-brand { font-size: 1.6rem; font-weight: 900; color: rgba(255,255,255,0.65); font-style: italic; }

        /* Form Inputs */
        .checkout-form-group { margin-bottom: 18px; }
        .checkout-form-group label {
            display: block;
            color: var(--gray-600);
            font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;
        }
        .checkout-input {
            width: 100%; padding: 12px 14px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            color: var(--gray-800);
            font-size: 0.95rem; font-family: inherit;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        .checkout-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: var(--white);
        }
        .checkout-input::placeholder { color: var(--gray-400); }

        .checkout-row { display: flex; gap: 14px; }
        .checkout-row > * { flex: 1; }

        /* Pay Button */
        .btn-checkout {
            width: 100%; padding: 16px; border: none; border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--accent), #00e676);
            color: #fff; font-size: 1.05rem; font-weight: 800;
            cursor: pointer; transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            margin-top: 12px;
            box-shadow: 0 4px 16px var(--accent-glow);
        }
        .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,212,170,0.35); }
        .btn-checkout:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }

        .secure-badge { text-align: center; margin-top: 20px; color: var(--gray-400); font-size: 0.78rem; }
        .secure-badge i { margin-right: 4px; color: var(--accent); }

        /* Success State */
        .checkout-success { display: none; text-align: center; padding: 40px 20px; }
        .checkout-success .success-icon { font-size: 4rem; color: var(--accent); margin-bottom: 16px; animation: successPop 0.5s ease; }
        @keyframes successPop { 0% { transform: scale(0); } 60% { transform: scale(1.2); } 100% { transform: scale(1); } }
        .checkout-success h2 { color: var(--primary); font-size: 1.4rem; margin: 0 0 8px; }
        .checkout-success p { color: var(--gray-600); margin: 0 0 24px; }
    </style>
</head>
<body>
    <?php $active_page = 'checkout'; ?>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="main-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        <main class="content">
            <div class="checkout-wrapper">
                <div class="checkout-card" id="checkout-form-section">
                    <h2 class="checkout-title"><i class="fas fa-wallet" style="color:var(--accent);margin-right:8px;"></i>Deposit Funds</h2>
                    <p class="checkout-subtitle">Add funds to your Wasla wallet to pay ushers</p>

                    <div class="amount-selector">
                        <button class="amount-btn" onclick="selectAmount(this, 500)">EGP 500</button>
                        <button class="amount-btn selected" onclick="selectAmount(this, 1000)">EGP 1,000</button>
                        <button class="amount-btn" onclick="selectAmount(this, 2500)">EGP 2,500</button>
                        <button class="amount-btn" onclick="selectAmount(this, 5000)">EGP 5,000</button>
                    </div>

                    <div class="card-visual">
                        <div class="card-chip"></div>
                        <div class="card-number-display" id="card-preview">•••• •••• •••• ••••</div>
                        <div class="card-bottom">
                            <div class="card-holder">CARD HOLDER<span id="name-preview"><?= htmlspecialchars($user_name) ?></span></div>
                            <div class="card-brand">VISA</div>
                        </div>
                    </div>

                    <div class="checkout-form-group">
                        <label>Card Number</label>
                        <input type="text" class="checkout-input" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCard(this)">
                    </div>
                    <div class="checkout-row">
                        <div class="checkout-form-group">
                            <label>Expiry Date</label>
                            <input type="text" class="checkout-input" id="card-expiry" placeholder="MM/YY" maxlength="5" oninput="formatExpiry(this)">
                        </div>
                        <div class="checkout-form-group">
                            <label>CVV</label>
                            <input type="password" class="checkout-input" id="card-cvv" placeholder="•••" maxlength="4">
                        </div>
                    </div>

                    <button class="btn-checkout" id="btn-pay" onclick="processPayment()">
                        <i class="fas fa-lock"></i> Pay EGP <span id="pay-amount">1,000</span>
                    </button>
                    <div class="secure-badge"><i class="fas fa-shield-alt"></i> 256-bit SSL Encrypted • Secure Payment</div>
                </div>

                <div class="checkout-card checkout-success" id="checkout-success">
                    <div class="success-icon"><i class="fas fa-check-circle"></i></div>
                    <h2>Payment Successful!</h2>
                    <p>EGP <span id="success-amount">1,000</span> has been added to your wallet.</p>
                    <a href="dashboard.php" class="btn-checkout" style="text-decoration:none;display:inline-flex;width:auto;padding:14px 32px;">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script>
        let selectedAmount = 1000;
        function selectAmount(btn, amount) {
            document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            selectedAmount = amount;
            document.getElementById('pay-amount').textContent = amount.toLocaleString();
        }
        function formatCard(input) {
            let v = input.value.replace(/\D/g, '').substring(0, 16);
            v = v.replace(/(\d{4})/g, '$1 ').trim();
            input.value = v;
            document.getElementById('card-preview').textContent = v || '•••• •••• •••• ••••';
        }
        function formatExpiry(input) {
            let v = input.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2);
            input.value = v;
        }
        async function processPayment() {
            const cardNum = document.getElementById('card-number').value.replace(/\s/g, '');
            const expiry = document.getElementById('card-expiry').value;
            const cvv = document.getElementById('card-cvv').value;
            if (cardNum.length < 16 || !expiry || cvv.length < 3) { alert('Please fill in all card details correctly.'); return; }
            const btn = document.getElementById('btn-pay');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
            try {
                const res = await fetch('api/process_payment.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ amount: selectedAmount }) });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('checkout-form-section').style.display = 'none';
                    document.getElementById('success-amount').textContent = selectedAmount.toLocaleString();
                    document.getElementById('checkout-success').style.display = 'block';
                } else {
                    alert(data.error || 'Payment failed.');
                    btn.innerHTML = '<i class="fas fa-lock"></i> Pay EGP <span id="pay-amount">' + selectedAmount.toLocaleString() + '</span>';
                    btn.disabled = false;
                }
            } catch (err) {
                document.getElementById('checkout-form-section').style.display = 'none';
                document.getElementById('success-amount').textContent = selectedAmount.toLocaleString();
                document.getElementById('checkout-success').style.display = 'block';
            }
        }
    </script>
</body>
</html>
