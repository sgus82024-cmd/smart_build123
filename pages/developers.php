<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Распределение задач в команде</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: rgba(0, 0, 0, 0.5);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        
        .modal-pattern {
            background-image: url(https://media.licdn.com/dms/image/v2/C511BAQHwyhgChF32-w/company-background_10000/company-background_10000/0/1583854866040/amplify_social_media_agency_cover?e=2147483647&v=beta&t=BeoB3BUheC_ljJf9UwIHh7-_1fmxBNae1SnMnKJY7tc);
            border-radius: 12px;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            width: 100%;
            max-width: 1000px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            color: white;
            position: relative;
        }
        
        .modal-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 0;
        }
        
        .back {
            width: 95%;
    display: flex
;
    justify-content: flex-end;
    padding: 20px
        }
        
        button.close-btn {
                position: relative;
    text-align: center;
    color: #ff5b4d;
    background: transparent;
    border: none;
    width: 32px;
    height: 32px;
    display: flex
;
    font-size: 72px;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    z-index: 1;
        }
        
        .section {
            padding: 0 32px 32px 32px;
            position: relative;
        }
        
        .section-title {
            font-family: 'Roboto';
            font-style: normal;
            font-weight: 300;
            font-size: 28px;
            line-height: 41px;
            display: flex;
            align-items: center;
            text-align: center;
            color: #F9FAFB;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .team-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            max-width: 600px;
            margin: 0 auto;
        }
        
    .team-section {
    /* background-color: rgba(255, 255, 255, 0.1); */
    border-radius: 8px;
    padding: 20px;
    display: flex
;
    align-items: flex-start;
    gap: 22px;
}
        .team-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7A1E4C;
            margin-right: 16px;
            font-size: 24px;
        }
        
        .team-content {
            flex: 1;
        }
        
        .team-title {
               font-family: 'Inter';
    font-weight: 700;
    font-size: 18px;
    color: #7A1E4C;
    text-align: center;
    margin-bottom: 50px;
        }
        
        .team-members {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        
        .team-member {
                font-family: 'Roboto';
    font-weight: 300;
    font-size: 16px;
    color: #FFFFFF;
    margin-bottom: 5px;
    text-align: center;
        }
    </style>
</head>
<body>
    <div class="modal-pattern">
        <!-- Close button -->
        <div class="back">
            <button class="close-btn" onclick="window.history.go(-1)">×</button>
        </div>
        
        <!-- Team distribution section -->
        <div class="section">
            <h2 class="section-title">Распределение задач в команде</h2>
            
            <div class="team-container">
                <!-- Design Team -->
                <div class="team-section">
                    <div class="team-icon">
                        <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="50" height="50" fill="url(#pattern0_316_596)"/>
<defs>
<pattern id="pattern0_316_596" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_316_596" transform="scale(0.01)"/>
</pattern>
<image id="image0_316_596" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>

                    </div>
                    <div class="team-content">
                        <h3 class="team-title">ДИЗАЙН</h3>
                        <ul class="team-members">
                            <li class="team-member">1. Пушкарёв Иван</li>
                            <li class="team-member">2. Демидов Валерий</li>
                            <li class="team-member">3. Орлов Дима</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Frontend Team -->
                <div class="team-section">
                    <div class="team-icon">
                       <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="50" height="50" fill="url(#pattern0_316_599)"/>
<defs>
<pattern id="pattern0_316_599" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_316_599" transform="scale(0.02)"/>
</pattern>
<image id="image0_316_599" width="50" height="50" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAABfklEQVRoBe1UQW7CQAzkAb32AsnyHsRGPfUrFU0ufIO+A7UPaL9TvkBlqZai2NZ6lQ1xwEgo0dhjz4wRq5V/PAFPwBNYZALH59enLsSPNsRLG+LV+PfShuYEmknY/yasGxjoa07EyHuIv8avMDARr6CZGFmaCdSbNHKomnNXxc3bZlcd6viJRAmHOhlaGEAN/SdZ0S/COxjApm79UmNdwqGO/VM9UUP/SXb1i/AuCZZw4JChhYGhRnbnsAl+TiAartFtmy+sSzg71IIRFG79SbKyLljS50akZObCyUUcsJbAXD+NsXtJjmMHzsV3I3MlL+1VX4Q0jgQ4QZqRHA8wwlU3EmYewO3RTOB4bkSTXKqHSzbFgTrHe8yLSEmUxG9ykZKCpVluJOfvV0qxJH6Ti2iW5PRwAWj4HA8wwlU3EmYewO3RTOB4bkSTXKqHSzbFgTrHe8yLSElYwck1rQjL1eFGchObuv+OL1Lvf6ZOr/z8/Te5iAOegCfgCSwigT9F2PSVbncCiAAAAABJRU5ErkJggg=="/>
</defs>
</svg>

                    </div>
                    <div class="team-content">
                        <h3 class="team-title">FRONTEND</h3>
                        <ul class="team-members">
                            <li class="team-member">1. Орлов Дима</li>
                            <li class="team-member">2. Пушкарёв Иван</li>
                            <li class="team-member">3. Демидов Валерий</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Backend Team -->
                <div class="team-section">
                    <div class="team-icon">
                       <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="50" height="50" fill="url(#pattern0_316_602)"/>
<defs>
<pattern id="pattern0_316_602" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_316_602" transform="scale(0.01)"/>
</pattern>
<image id="image0_316_602" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>

                    </div>
                    <div class="team-content">
                        <h3 class="team-title">BACKEND</h3>
                        <ul class="team-members">
                            <li class="team-member">1. Демидов Валерий</li>
                            <li class="team-member">2. Орлов Дима</li>
                            <li class="team-member">3. Пушкарёв Иван</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>