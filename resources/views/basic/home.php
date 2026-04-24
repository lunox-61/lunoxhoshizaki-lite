<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment Successful</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #0a0a0a;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            color: #ffffff;
        }

        .container {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            transform: translateY(-5vh); /* visually center perfectly */
        }

        .icon-circle {
            width: 100px;
            height: 100px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }

        .icon-circle svg {
            width: 50px;
            height: 50px;
            color: #0a0a0a;
        }

        h1 {
            font-size: 52px;
            font-weight: 500;
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .footer {
            position: absolute;
            bottom: 30px;
            left: 40px;
            right: 40px;
            display: flex;
            justify-content: space-between;
            color: #666666; /* Subdued text color for minimal aesthetic */
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 36px;
            }
            .icon-circle {
                width: 80px;
                height: 80px;
            }
            .icon-circle svg {
                width: 40px;
                height: 40px;
            }
            .footer {
                left: 20px;
                right: 20px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>
        <h1>Deployment Successful</h1>
    </div>

    <div class="footer">
        <div>LunoxHoshizaki Framework Lite</div>
        <div>v2.1.0</div>
    </div>
</body>
</html>