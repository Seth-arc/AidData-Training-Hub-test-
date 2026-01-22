<?php
if (!defined('ABSPATH')) exit;
// $recipientName, $courseName, $completionDate, $certificateId are expected to be available
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Certificate - <?php echo esc_html($recipientName); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #115740;
            --primary-dark: #0a3b2b;
            --accent-color: #1a805f;
            --accent-light: #2ea384;
            --gold: #d4af37;
            --gold-light: #f2e9af;
            --dark-text: #111111;
            --light-text: #555555;
            --border-radius: 10px;
            --transition-speed: 0.3s;
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            --gradient-gold: linear-gradient(135deg, var(--gold), #f8e08e);
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #f5f5f5;
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 6px;
            border: 3px solid #f5f5f5;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-color);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
            color: var(--dark-text);
            letter-spacing: -0.02em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            box-sizing: border-box;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(17, 87, 64, 0.03) 0%, transparent 150px),
                radial-gradient(circle at 70% 60%, rgba(212, 175, 55, 0.03) 0%, transparent 150px);
        }
        
        * {
            box-sizing: border-box;
        }

        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .certificate-container {
                width: 297mm; /* A4 landscape width */
                height: 210mm; /* A4 landscape height */
                margin: 0 auto;
                padding: 0;
                box-shadow: none;
                page-break-inside: avoid;
                overflow: hidden;
            }

            .certificate {
                padding: 30px 40px;
                margin: 0;
                box-shadow: none;
                page-break-inside: avoid;
                width: calc(100% - 60px); /* Account for padding */
                height: calc(100% - 60px); /* Account for padding */
            }

            .controls {
                display: none;
            }
            
            .certificate-id {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }

        .certificate-container {
            width: 297mm; /* A4 landscape width */
            height: 210mm; /* A4 landscape height */
            position: relative;
            margin: 0 auto;
            max-width: 100%;
        }

        .certificate {
            background-color: #ffffff;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            width: calc(100% - 80px); /* Account for padding */
            height: calc(100% - 80px); /* Account for padding */
            box-sizing: border-box;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(17, 87, 64, 0.02) 0%, transparent 200px),
                radial-gradient(circle at 90% 80%, rgba(212, 175, 55, 0.02) 0%, transparent 200px);
        }

        .border-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 15px solid transparent;
            border-image: var(--gradient-primary) 1;
            pointer-events: none;
            opacity: 0.8;
            box-shadow: inset 0 0 20px rgba(17, 87, 64, 0.1);
        }
        
        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
            pointer-events: none;
        }
        
        .corner-tl {
            top: 20px;
            left: 20px;
            border-top: 3px solid var(--gold);
            border-left: 3px solid var(--gold);
            border-radius: 10px 0 0 0;
        }
        
        .corner-tr {
            top: 20px;
            right: 20px;
            border-top: 3px solid var(--gold);
            border-right: 3px solid var(--gold);
            border-radius: 0 10px 0 0;
        }
        
        .corner-bl {
            bottom: 20px;
            left: 20px;
            border-bottom: 3px solid var(--gold);
            border-left: 3px solid var(--gold);
            border-radius: 0 0 0 10px;
        }
        
        .corner-br {
            bottom: 20px;
            right: 20px;
            border-bottom: 3px solid var(--gold);
            border-right: 3px solid var(--gold);
            border-radius: 0 0 10px 0;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.04;
            font-size: 150px;
            font-weight: bold;
            color: var(--primary-color);
            white-space: nowrap;
            pointer-events: none;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .logo {
            max-width: 200px;
            margin-bottom: 25px;
            margin-top: 25px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .certificate-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 40px;
            color: var(--primary-color);
            margin: 12px 0;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        .certificate-subtitle {
            font-size: 16px;
            color: var(--light-text);
            margin-bottom: 15px;
            font-weight: 400;
            letter-spacing: 1px;
        }

        .recipient-section {
            text-align: center;
            margin: 40px 0;
            position: relative;
        }

        .recipient-title {
            font-size: 20px;
            color: var(--light-text);
            margin-bottom: 15px;
            font-weight: 400;
        }

        .recipient-name {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 10px 0;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
            font-family: 'Cormorant Garamond', serif;
        }

        .recipient-name::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 2px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .certificate-text {
            text-align: center;
            font-size: 16px;
            line-height: 1.8;
            color: var(--dark-text);
            margin: 30px 0;
            padding: 0 30px;
        }

        .completion-date {
            text-align: center;
            font-size: 16px;
            color: var(--light-text);
            margin: 30px 0;
            font-weight: 500;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            position: relative;
            padding: 0 30px;
        }

        .signature {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            width: 80%;
            height: 1px;
            background: var(--gradient-primary);
            margin: 10px auto;
            position: relative;
        }

        .signature-line::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--gradient-primary);
            border-radius: 2px;
            transform: scaleX(0.7);
            opacity: 0.7;
        }

        .signature-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--dark-text);
            margin-top: 10px;
        }

        .signature-title {
            font-size: 12px;
            color: var(--light-text);
            margin-top: 5px;
        }

        .certificate-footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: var(--light-text);
            padding: 0 30px;
            position: relative;
        }

        .certificate-id {
            font-size: 14px;
            color: var(--primary-dark);
            font-weight: 500;
            letter-spacing: 0.5px;
            margin-top: 20px;
            display: block;
            background-color: rgba(17, 87, 64, 0.05);
            padding: 4px 12px;
            border-radius: 4px;
            width: max-content;
            margin-left: auto;
            margin-right: auto;
        }

        .controls {
            margin-top: 30px;
            text-align: center;
        }

        button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            margin: 0 5px;
            cursor: pointer;
            font-size: 14px;
            border-radius: var(--border-radius);
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all var(--transition-speed);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:hover {
            background: var(--accent-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(17, 87, 64, 0.15);
        }

        /* Floating shapes animation */
        .shape {
            position: absolute;
            background: linear-gradient(135deg, rgba(17, 87, 64, 0.05), rgba(26, 128, 95, 0.08));
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: morphShape 15s ease-in-out infinite;
            z-index: 0;
            pointer-events: none;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation-delay: -2s;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            right: -50px;
            animation-delay: -4s;
        }
        
        .shape-3 {
            width: 160px;
            height: 160px;
            top: 30%;
            right: -30px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), rgba(248, 224, 142, 0.08));
            animation-delay: -7s;
        }
        
        .shape-4 {
            width: 120px;
            height: 120px;
            bottom: 20%;
            left: -20px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), rgba(248, 224, 142, 0.08));
            animation-delay: -10s;
        }

        @keyframes morphShape {
            0%, 100% {
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            }
            25% {
                border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%;
            }
            50% {
                border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%;
            }
            75% {
                border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate" id="certificate">
            <div class="border-pattern"></div>
            <div class="watermark">AIDDATA</div>
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            
            <div class="header">
                <img src="<?php echo esc_url($logoUrl); ?>" alt="AidData Logo" class="logo">
                <h1 class="certificate-title">Certificate of Completion</h1>
                <div class="certificate-subtitle"><?php echo esc_html($courseName); ?></div>
            </div>
            
            <div class="recipient-section">
                <div class="recipient-title">Presented to</div>
                <div class="recipient-name" id="recipient-name"><?php echo esc_html($recipientName); ?></div>
                <div class="certificate-id" id="certificate-id"><?php echo esc_html($certificateId); ?></div>
            </div>
            
            <div class="certificate-text">
                For successfully completing the <span id="course-name"><?php echo esc_html($courseName); ?></span> simulation, demonstrating proficiency in analyzing financing packages and strategic decision-making for development projects.
            </div>
            
            <div class="completion-date">
                Completed on <span id="completion-date"><?php echo esc_html($completionDate); ?></span>
            </div>
            
            <div class="certificate-footer">
                This certificate verifies completion of an AidData professional development program. 
                AidData is a research lab at William & Mary that uses data and rigorous methods to create policy-relevant analysis.
            </div>
        </div>
        
        <div class="controls">
            <button id="print-btn">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                    <path d="M6 14h12v8H6z"/>
                </svg>
                Print PDF
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Print button event listener
            document.getElementById('print-btn').addEventListener('click', generatePDF);
        });
        
        function generatePDF() {
            // Get the certificate element
            const certificate = document.getElementById('certificate');
            
            // Get recipient name for the filename
            const recipientName = document.getElementById('recipient-name').textContent;
            // Create a filename-safe version of the name
            const safeRecipientName = recipientName.replace(/[^a-zA-Z0-9]/g, '_');
            
            console.log('Generating PDF for:', safeRecipientName);
            
            // Configure PDF options
            const opt = {
                margin: [10, 0, 10, 0], // Adjusted margins
                filename: `AidData_Certificate_${safeRecipientName}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 3,
                    useCORS: true,
                    letterRendering: true,
                    // logging: true, 
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'landscape',
                    compress: true,
                    precision: 16
                }
            };

            // Remove any existing transforms during PDF generation
            const originalTransform = certificate.style.transform;
            certificate.style.transform = 'none';

            // Generate PDF
            html2pdf().set(opt).from(certificate).save().then(() => {
                // Restore original transform after PDF generation
                certificate.style.transform = originalTransform;
                console.log('PDF generation completed');
            }).catch(error => {
                console.error('PDF generation error:', error);
            });
        }
    </script>
</body>
</html>
