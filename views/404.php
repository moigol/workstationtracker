<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - RFID Logging System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 70vh;
            text-align: center;
            padding: 2rem;
        }
        
        .error-graphic {
            width: 200px;
            height: 200px;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .error-graphic::before {
            content: "404";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 5rem;
            font-weight: bold;
            color: var(--primary-color);
            opacity: 0.2;
            z-index: 1;
        }
        
        .error-graphic svg {
            width: 100%;
            height: 100%;
            fill: var(--primary-color);
        }
        
        .error-message {
            font-size: 1.8rem;
            margin: 0 0 1rem;
            color: var(--dark-color);
        }
        
        .error-description {
            max-width: 600px;
            margin-bottom: 2rem;
            color: #666;
            line-height: 1.6;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            color: white;
        }
        
        .btn-secondary {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-secondary:hover {
            background-color: #e8e8e8;
        }
        
        .suggested-links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
            width: 100%;
            max-width: 600px;
        }
        
        .suggested-links h3 {
            margin-bottom: 1rem;
            color: #666;
            font-size: 1.1rem;
        }
        
        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        
        .link-item {
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .link-item:hover {
            background: #f0f0f0;
        }
        
        .link-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        /* Animation */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .error-graphic {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .error-graphic {
                width: 150px;
                height: 150px;
            }
            
            .error-graphic::before {
                font-size: 3rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>RFID Logging System</h1>
            <nav>
                <ul>
                    <li><a href="/">Dashboard</a></li>
                    <li><a href="/scanners">Scanners</a></li>
                    <li><a href="/rfid-tags">RFID Tags</a></li>
                    <li><a href="/items">Items</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="error-container">
                <div class="error-graphic">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M50 10C28.1 10 10 28.1 10 50s18.1 40 40 40 40-18.1 40-40S71.9 10 50 10zm0 70c-16.6 0-30-13.4-30-30s13.4-30 30-30 30 13.4 30 30-13.4 30-30 30z"/>
                        <path d="M65.7 34.3c-.8-.8-2-.8-2.8 0L50 47.2 37.1 34.3c-.8-.8-2-.8-2.8 0s-.8 2 0 2.8L47.2 50 34.3 62.9c-.8.8-.8 2 0 2.8.4.4.9.6 1.4.6s1-.2 1.4-.6L50 52.8l12.9 12.9c.4.4.9.6 1.4.6s1-.2 1.4-.6c.8-.8.8-2 0-2.8L52.8 50l12.9-12.9c.8-.8.8-2 0-2.8z"/>
                    </svg>
                </div>
                
                <h2 class="error-message">Page Not Found</h2>
                <p class="error-description">
                    The page you're looking for doesn't exist or may have been moved. 
                    Please check the URL or try one of the links below.
                </p>
                
                <div class="action-buttons">
                    <a href="/" class="btn btn-primary">Go to Dashboard</a>
                    <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
                </div>
                
                <div class="suggested-links">
                    <h3>Popular Pages</h3>
                    <div class="link-grid">
                        <div class="link-item">
                            <a href="/scanners">Scanners</a>
                            <p>Manage your RFID scanners</p>
                        </div>
                        <div class="link-item">
                            <a href="/rfid-tags">RFID Tags</a>
                            <p>Manage RFID tags</p>
                        </div>
                        <div class="link-item">
                            <a href="/items">Items</a>
                            <p>Manage inventory items</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add interactive elements
            const errorGraphic = document.querySelector('.error-graphic');
            
            errorGraphic.addEventListener('mouseover', function() {
                this.style.transform = 'scale(1.1)';
            });
            
            errorGraphic.addEventListener('mouseout', function() {
                this.style.transform = '';
            });
            
            // Add click effect
            errorGraphic.addEventListener('click', function() {
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'shake 0.5s ease-in-out';
                }, 10);
            });
        });
    </script>
</body>
</html>