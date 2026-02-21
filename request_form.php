<?php 
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}
include 'db.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Request Form - Barangay Igpit Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-bg: #f4f7f6;
            --accent-color: #1e3799;
            --glass-white: rgba(255, 255, 255, 0.95);
            --text-dark: #2d3436;
        }

        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background: var(--primary-bg); 
            margin: 0; padding: 40px 20px;
            color: var(--text-dark);
        }

        .container { 
            background: var(--glass-white); 
            padding: 50px; border-radius: 30px; 
            max-width: 850px; margin: auto; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            animation: slideIn 0.7s ease-out;
            border: 1px solid rgba(255,255,255,0.3);
        }

        @keyframes slideIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .back-btn { 
            text-decoration: none; color: #636e72; display: inline-flex; 
            align-items: center; gap: 8px; margin-bottom: 25px; 
            font-weight: 600; transition: 0.3s; background: #fff;
            padding: 10px 15px; border-radius: 12px;
        }
        .back-btn:hover { color: var(--accent-color); transform: translateX(-5px); }

        .form-section-label {
            display: block; font-size: 0.85rem; text-transform: uppercase;
            letter-spacing: 1px; color: var(--accent-color);
            margin-bottom: 15px; font-weight: 800; border-bottom: 2px solid #f0f0f0;
            padding-bottom: 5px;
        }

        .form-group { margin-bottom: 25px; position: relative; }
        label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.95rem; }

        /* --- CUSTOM CURVED DROPDOWN --- */
        .custom-select-wrapper { position: relative; user-select: none; width: 100%; }
        .custom-select-trigger {
            padding: 15px; background: #f8f9fa; border-radius: 15px;
            border: 2px solid #edf2f7; cursor: pointer; display: flex;
            justify-content: space-between; align-items: center;
            transition: 0.3s; font-size: 1rem;
        }
        .custom-select-trigger:after {
            content: "\f107"; font-family: "Font Awesome 6 Free"; font-weight: 900; transition: 0.3s;
        }
        .custom-select-wrapper.open .custom-select-trigger { border-color: var(--accent-color); background: #fff; }
        .custom-select-wrapper.open .custom-select-trigger:after { transform: rotate(180deg); }

        .custom-options {
            position: absolute; display: block; top: 105%; left: 0; right: 0;
            background: #fff; border-radius: 15px; border: 1px solid #edf2f7;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 100;
            opacity: 0; visibility: hidden; transform: translateY(-10px);
            transition: all 0.3s ease; overflow: hidden;
        }
        .custom-select-wrapper.open .custom-options { opacity: 1; visibility: visible; transform: translateY(0); }
        .custom-option { padding: 12px 15px; cursor: pointer; transition: 0.3s; font-size: 14px; }
        .custom-option:hover { background: #f0f4f8; color: var(--accent-color); padding-left: 20px; }
        .custom-option.selected { background: var(--accent-color); color: #fff; }

        /* --- INPUTS & TEXTAREA --- */
        input, textarea { 
            width: 100%; padding: 15px; border-radius: 15px; border: 2px solid #edf2f7;
            background: #f8f9fa; box-sizing: border-box; font-size: 1rem; transition: 0.3s; font-family: inherit;
        }
        input:focus, textarea:focus { outline: none; border-color: var(--accent-color); background: #fff; box-shadow: 0 0 15px rgba(30, 55, 153, 0.1); }

        .row { display: flex; gap: 20px; }
        .row > div { flex: 1; }

        .footer-btns { display: flex; justify-content: center; gap: 20px; margin-top: 40px; }
        .btn { padding: 16px 50px; border-radius: 15px; border: none; font-weight: 700; cursor: pointer; font-size: 1rem; transition: 0.3s; }
        .btn-submit { background: var(--accent-color); color: white; box-shadow: 0 10px 20px rgba(30, 55, 153, 0.2); }
        .btn-submit:hover { background: #152b7a; transform: translateY(-5px); }
        .btn-cancel { background: #fff; color: #636e72; border: 1px solid #ddd; }
    </style>
</head>
<body>

<div style="max-width: 850px; margin: auto;">
    <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
</div>

<div class="container">
    <h2 style="display:flex; align-items:center; gap:12px; color: var(--accent-color);">
        <i class="fa-solid fa-file-signature"></i> Document Request Form
    </h2>

    <form action="save_request.php" method="POST" id="requestForm">
        <span class="form-section-label">Request Details</span>
        
        <div class="form-group">
            <label>Select Document Type *</label>
            <div class="custom-select-wrapper">
                <input type="hidden" name="document_type" id="realSelect" required>
                <div class="custom-select-trigger">-- Choose One --</div>
                <div class="custom-options">
                    <div class="custom-option" data-value="Barangay Clearance">Barangay Clearance</div>
                    <div class="custom-option" data-value="Business Permit">Business Permit</div>
                    <div class="custom-option" data-value="Indigency Certificate">Certificate of Indigency</div>
                    <div class="custom-option" data-value="Barangay Residency">Certificate of Residency</div>
                </div>
            </div>
        </div>

        <span class="form-section-label">Personal Information</span>
        <div class="row form-group">
            <div>
                <label>First Name *</label>
                <input type="text" name="first_name" placeholder="Juan" required>
            </div>
            <div>
                <label>Last Name *</label>
                <input type="text" name="last_name" placeholder="Dela Cruz" required>
            </div>
        </div>

        <div class="row form-group">
            <div style="flex: 2;">
                <label>Home Address *</label>
                <input type="text" name="address" placeholder="Purok, Street, Brgy Igpit" required>
            </div>
            <div style="flex: 1;">
                <label>Contact No. *</label>
                <input type="tel" name="contact_number" maxlength="11" placeholder="09XXXXXXXXX" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
            </div>
        </div>

        <span class="form-section-label">Additional Details</span>
        <div class="form-group">
            <label>Purpose of Request *</label>
            <textarea name="purpose" rows="4" required></textarea>
        </div>

        <div class="footer-btns">
            <button type="button" class="btn btn-cancel" onclick="window.location.href='dashboard.php'">Cancel</button>
            <button type="submit" name="submit_request" class="btn btn-submit">
                <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i> Submit Request
            </button>
        </div>
    </form>
</div>

<script>
    // Custom Dropdown JavaScript
    document.querySelector('.custom-select-trigger').addEventListener('click', function() {
        this.parentElement.classList.toggle('open');
    });

    document.querySelectorAll('.custom-option').forEach(option => {
        option.addEventListener('click', function() {
            let value = this.getAttribute('data-value');
            let trigger = this.closest('.custom-select-wrapper').querySelector('.custom-select-trigger');
            let hiddenInput = document.querySelector('#realSelect');

            trigger.textContent = this.textContent;
            hiddenInput.value = value;
            
            document.querySelectorAll('.custom-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.closest('.custom-select-wrapper').classList.remove('open');
        });
    });

    window.addEventListener('click', function(e) {
        const select = document.querySelector('.custom-select-wrapper');
        if (!select.contains(e.target)) { select.classList.remove('open'); }
    });
</script>

</body>
</html>